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

                if (isset($result[$exchange['slugColumn']]) && !is_null($result[$exchange['slugColumn']])) {
                    return $result[$exchange['slugColumn']];
                } else {
                    return $this->generateSlug($result, $exchange);
                }
            }
        }
        return false;
    }

    /**
     * @param array $param
     * @param array $exchange
     * @return bool
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function generateSlug(array $param, array $exchange)
    {
        $exchangeValue = $param[$exchange['exchangeColumn']];
        $exchangeValue = strtolower($exchangeValue);
        $exchangeValue = preg_replace('/\s+/', '-', $exchangeValue);

        $response = $this->checkDuplicateSlug($exchangeValue, $exchange);

        if ($response) {
            $exchangeValue .= '-' . count($response);
        }

        try {
            $stmt = $this->em->getConnection()->prepare(
                'UPDATE `' . $exchange['table'] . '` SET `' . $exchange['slugColumn'] .
                '` = :slug WHERE `' . $exchange['identifier'] . '` = :identifier'
            );
            $stmt->bindValue('slug', $exchangeValue);
            $stmt->bindValue('identifier', $param[$exchange['identifier']]);
            $stmt->execute();
            return $exchangeValue;
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param string $slug
     * @param array $exchange
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function checkDuplicateSlug(string $slug, array $exchange): ?int
    {
        try {
            $stmt = $this->em->getConnection()->prepare(
                'SELECT * FROM `' . $exchange['table'] . '` WHERE `' .
                $exchange['slugColumn'] . '` = :slug'
            );
            $stmt->bindValue('slug', $slug);
            $stmt->execute();
            return $stmt->fetchAssociative();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }


    /**
     * @param string $param
     * @param array $db
     * @param Slug $slug
     * @return array|null
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function proceedSlug(Slug $slug, string $param, array $db): ?array
    {
        $searchParam = $db['identifier'];
        if ($slug->getType() === Slug::REQUEST_TYPE) {
            $searchParam = $db['slugColumn'];
        }
        try {
            $table = $db['table'];
            $stmt = $this->em->getConnection()->prepare(
                'SELECT * FROM `' . $table . '` WHERE `' . $searchParam . '` = :searchParam'
            );
            if ($slug->getType() === Slug::REQUEST_TYPE) {
                $stmt->bindValue('searchParam', $this->escapeCharacter($param));
            } else {
                $stmt->bindValue('searchParam', $param, UuidBinaryOrderedTimeType::NAME);
            }
            $stmt->execute();
            return $stmt->fetchAssociative();
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
