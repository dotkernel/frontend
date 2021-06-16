<?php

declare(strict_types=1);

namespace Frontend\Slug\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\Exception\RuntimeException;
use Frontend\Slug\Slug;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;


/**
 * Class SlugService
 *
 * @package Frontend\Slug\Service
 */
class SlugService implements SlugServiceInterface
{
    protected const CONFIGURATION = [
        'table',
        'identifier',
        'exchangeColumn',
        'slugColumn'
    ];

    /** @var EntityManager $em */
    protected EntityManager $em;

    /**
     * SlugService constructor.
     * @Inject({EntityManager::class})
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param Slug $slug
     * @return bool|string
     * @throws \Doctrine\DBAL\Driver\Exception|MissingConfigurationException
     */
    public function slugManipulation(Slug $slug, string $attribute, string $value)
    {

        $exchange = $slug->getExchange();
        $exchange = array_reduce(
            $exchange,
            function ($matched, $exchange) use ($attribute) {
                if (isset($exchange[$attribute])) {
                    return $matched;
                }

                return $exchange;
            },
            false
        );

        if (is_array($exchange)) {
            $this->checkExchange($slug, $exchange, $attribute);

            $result = $this->proceedSlug($slug, $value, $exchange);
            if ($result) {
                if ($slug->getType() === Slug::REQUEST_TYPE) {
                    return $this->processUuidToString($result[$exchange['identifier']]);
                }
                if ($result[$exchange['exchangeColumn']]) {
                    if (isset($result[$exchange['slugColumn']]) && !is_null($result[$exchange['slugColumn']])) {
                        return $result[$exchange['slugColumn']];
                    } else {
                        return $this->generateSlug($result, $exchange);
                    }
                } else {
                    return $value;
                }

            }
        }
        return false;
    }

    /**
     * @param array $param
     * @param array  $exchange
     * @return string
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function generateSlug(array $param, array $exchange): string
    {
        $exchangeValue = $param[$exchange['exchangeColumn']];
        $exchangeValue = strtolower($exchangeValue);
        $exchangeValue = str_replace(' ', '-', $exchangeValue);
        $exchangeValue = preg_replace('/[^A-Za-z0-9\-]/', '', $exchangeValue);
        $exchangeValue = str_replace('.com', '', $exchangeValue);

        $response = $this->checkDuplicateSlug($exchangeValue, $exchange);

        if ($response) {
            $exchangeValue .= '-' . $this->getSlugSuffix($param, $exchange);
        }

        try {
            $stmt = $this->em->getConnection()->prepare(
                'UPDATE `' . $exchange['table'] . '` SET `' . $exchange['slugColumn'] .
                '` = :slug WHERE `' . $exchange['identifier'] . '` = :identifier'
            );
            $stmt->bindValue('slug', $this->clean($exchangeValue));
            $stmt->bindValue('identifier', $param[$exchange['identifier']]);
            $stmt->executeStatement();
            return $exchangeValue;
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param       $param
     * @param array $exchange
     * @return int
     */
    protected function getSlugSuffix($param, array $exchange): int
    {
        try {
            $stmt = $this->em->getConnection()->prepare(
                'SELECT ' . $exchange['exchangeColumn'] . ' FROM `' . $exchange['table'] . '` WHERE `' .
                $exchange['exchangeColumn'] . '` = :exchangeColumn AND `' .
                $exchange['slugColumn'] . '` IS NOT NULL'
            );
            $stmt->bindValue('exchangeColumn', $param[$exchange['exchangeColumn']]);
            return $stmt->executeQuery()->rowCount();
        } catch (Exception | \Doctrine\DBAL\Driver\Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param $input
     * @return string
     */
    public function clean($input): string
    {
        return preg_replace('/[^A-Za-z0-9. -]/', '', $input);
    }

    /**
     * @param string $slug
     * @param array $exchange
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function checkDuplicateSlug(string $slug, array $exchange): int
    {
        try {
            $stmt = $this->em->getConnection()->prepare(
                'SELECT ' . $exchange['slugColumn'] . ' FROM `' . $exchange['table'] . '` WHERE `' .
                $exchange['slugColumn'] . '` = :slug'
            );
            $stmt->bindValue('slug', $this->clean($slug));
            return $stmt->executeQuery()->rowCount();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param string $param
     * @param array $db
     * @param Slug $slug
     * @return false|array
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function proceedSlug(Slug $slug, string $param, array $db)
    {
        $searchParam = $db['identifier'];
        if ($slug->getType() === Slug::REQUEST_TYPE) {
            $searchParam = $db['slugColumn'];
        }
        try {
            $table = $db['table'];
            unset($db['table']);
            $collum = array_values($db);
            $collum = implode(',', $collum);

            $stmt = $this->em->getConnection()->prepare(
                'SELECT ' . $collum . ' FROM `' . $table . '` WHERE `' . $searchParam . '` = :searchParam'
            );
            if ($slug->getType() === Slug::REQUEST_TYPE) {
                $stmt->bindValue('searchParam', $this->escapeCharacter($param));
            } else {
                $stmt->bindValue('searchParam', $param, UuidBinaryOrderedTimeType::NAME);
            }
            return $stmt->executeQuery()->fetchAssociative();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param $input
     * @return string|string[]
     */
    public function escapeCharacter($input)
    {
        return str_replace(
            ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
            ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
            $input
        );
    }

    /**
     * @param string $attributeUuid
     * @return string
     */
    public function processUuidToString(string $attributeUuid): string
    {
        return $this->getUuidGenerator()->fromBytes($attributeUuid)->toString();
    }

    /**
     * @return UuidFactory
     */
    private function getUuidGenerator(): UuidFactory
    {
        /** @var UuidFactory $factory */
        $factory = clone Uuid::getFactory();
        $codec = new OrderedTimeCodec($factory->getUuidBuilder());
        $factory->setCodec($codec);

        return $factory;
    }

    /**
     * @param Slug $slug
     * @param array $exchange
     * @param string $attribute
     * @throws MissingConfigurationException
     */
    private function checkExchange(Slug $slug, array $exchange, string $attribute)
    {
        foreach (self::CONFIGURATION as $configuration) {
            if (!isset($exchange[$configuration])) {
                throw new MissingConfigurationException(
                    sprintf(
                        'Missing "%s" configuration , trace --> slug : "%s" , exchange attribute : "%s"',
                        $configuration,
                        $slug->getAlias(),
                        $attribute
                    )
                );
            }
        }
    }
}
