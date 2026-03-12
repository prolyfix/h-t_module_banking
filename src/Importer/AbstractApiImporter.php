<?php

namespace Prolyfix\BankingBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Prolyfix\BankingBundle\Entity\Account;
use Prolyfix\BankingBundle\Entity\Entry;

/**
 * Base class for bank importers that pull data from an external API.
 *
 * Concrete subclasses must implement:
 *  - getName()        – a human-readable label used in the import-type selector
 *  - fetchFromApi()   – fetch raw data rows from the remote API
 *  - createEntity()   – map a single raw row to a new Entry entity
 */
abstract class AbstractApiImporter implements BankImporterInterface
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function getImportMode(): string
    {
        return BankImporterInterface::IMPORT_MODE_API;
    }

    /**
     * File-related checks are not meaningful for API importers.
     */
    public function isFormatAllowed(string $format): bool
    {
        return true;
    }

    /**
     * File-related checks are not meaningful for API importers.
     */
    public function isFileRight(mixed $file): bool
    {
        return true;
    }

    /**
     * Fetches raw data rows from the remote API.
     *
     * @param mixed $source Optional configuration passed to the API (e.g. date range,
     *                      credentials). May be null when the importer uses its own
     *                      defaults or reads credentials from the environment.
     */
    abstract protected function fetchFromApi(mixed $source): array;

    /**
     * Maps a single raw data row to an Entry entity.
     */
    abstract protected function createEntity(array $row, Account $account): Entry;

    public function deserialize(mixed $source): array
    {
        return $this->fetchFromApi($source);
    }

    public function import(mixed $source, Account $account, bool $write = true): array
    {
        $output = [];
        foreach ($this->deserialize($source) as $row) {
            $entity = $this->createEntity($row, $account);
            $output[] = $entity;
            if ($write) {
                $this->em->persist($entity);
            }
        }
        if ($write) {
            $this->em->flush();
        }
        return $output;
    }
}
