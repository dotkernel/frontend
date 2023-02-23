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
 * @package Frontend\Slug\Service
 */
final class SlugService implements SlugServiceInterface
{
    /**
     * @var string[]
     */
    protected const CONFIGURATION = [
        'table',
        'identifier',
        'exchangeColumn',
        'slugColumn'
    ];

    private readonly EntityManager $entityManager;

    /**
     * SlugService constructor.
     * @Inject({
     *     EntityManager::class
     * })
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws MissingConfigurationException
     */
    public function slugManipulation(Slug $slug, string $attribute, string $value): mixed
    {
        $exchange = $slug->getExchange();
        $exchange = array_reduce(
            $exchange,
            static function ($matched, $exchange) use ($attribute) {
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

                if (!$result[$exchange['exchangeColumn']]) {
                    return $value;
                }
                if (isset($result[$exchange['slugColumn']]) && !is_null($result[$exchange['slugColumn']])) {
                    return $result[$exchange['slugColumn']];
                }
                return $this->generateSlug($result, $exchange);
            }
        }

        return false;
    }

    private function generateSlug(array $param, array $exchange): string
    {
        $exchangeValue = $param[$exchange['exchangeColumn']];
        $exchangeValue = strtolower((string) $exchangeValue);
        $exchangeValue = str_replace(' ', '-', $exchangeValue);
        $exchangeValue = preg_replace('#[^A-Za-z0-9\-]#', '', $exchangeValue);
        $exchangeValue = str_replace('.com', '', $exchangeValue);

        $response = $this->checkDuplicateSlug($exchangeValue, $exchange);

        if ($response !== 0) {
            $exchangeValue .= '-' . $this->getSlugSuffix($param, $exchange);
        }

        try {
            $stmt = $this->entityManager->getConnection()->prepare(
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
     */
    private function getSlugSuffix(array $param, array $exchange): int
    {
        try {
            $stmt = $this->entityManager->getConnection()->prepare(
                'SELECT ' . $exchange['exchangeColumn'] . ' FROM `' . $exchange['table'] . '` WHERE `' .
                $exchange['exchangeColumn'] . '` = :exchangeColumn AND `' .
                $exchange['slugColumn'] . '` IS NOT NULL'
            );
            $stmt->bindValue('exchangeColumn', $param[$exchange['exchangeColumn']]);
            return $stmt->executeQuery()->rowCount();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    /**
     * @param $input
     */
    private function clean($input): string
    {
        return preg_replace('#[^A-Za-z0-9. -]#', '', (string) $input);
    }

    private function checkDuplicateSlug(string $slug, array $exchange): int
    {
        try {
            $stmt = $this->entityManager->getConnection()->prepare(
                'SELECT ' . $exchange['slugColumn'] . ' FROM `' . $exchange['table'] . '` WHERE `' .
                $exchange['slugColumn'] . '` = :slug'
            );
            $stmt->bindValue('slug', $this->clean($slug));
            return $stmt->executeQuery()->rowCount();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    private function proceedSlug(Slug $slug, string $param, array $db): bool|array
    {
        $searchParam = $db['identifier'];
        if ($slug->getType() === Slug::REQUEST_TYPE) {
            $searchParam = $db['slugColumn'];
        }

        try {
            $table = $db['table'];
            unset($db['table']);
            $column = array_values($db);
            $column = implode(',', $column);

            $stmt = $this->entityManager->getConnection()->prepare(
                'SELECT ' . $column . ' FROM `' . $table . '` WHERE `' . $searchParam . '` = :searchParam'
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
    private function escapeCharacter(string $input): array|string
    {
        return str_replace(
            ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
            ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
            $input
        );
    }

    private function processUuidToString(string $attributeUuid): string
    {
        return $this->getUuidGenerator()->fromBytes($attributeUuid)->toString();
    }

    private function getUuidGenerator(): UuidFactory
    {
        /** @var UuidFactory $factory */
        $factory = clone Uuid::getFactory();
        $orderedTimeCodec = new OrderedTimeCodec($factory->getUuidBuilder());
        $factory->setCodec($orderedTimeCodec);

        return $factory;
    }

    /**
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
