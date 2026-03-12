<?php

namespace Prolyfix\BankingBundle\Importer;

use Prolyfix\BankingBundle\Entity\Account;

interface BankImporterInterface
{
    public const IMPORT_MODE_FILE = 'file';
    public const IMPORT_MODE_API = 'api';

    /**
     * Returns a human-readable name for this importer (used in the import type selector).
     */
    public function getName(): string;

    /**
     * Returns the import mode: 'file' for file-upload-based importers,
     * 'api' for importers that pull data from an external API.
     */
    public function getImportMode(): string;

    /**
     * Checks whether the given file format (extension) is supported by this importer.
     */
    public function isFormatAllowed(string $format): bool;

    /**
     * Performs a quick sanity check on the uploaded file to verify it matches
     * the expected structure for this importer.
     */
    public function isFileRight(mixed $file): bool;

    /**
     * Deserializes the source (file or API response) into an array of raw data rows.
     */
    public function deserialize(mixed $source): array;

    /**
     * Imports data from the given source into the specified bank account.
     *
     * @param mixed   $source  For file-based importers: the uploaded file (UploadedFile).
     *                         For API-based importers: optional configuration array (e.g. date
     *                         range, credentials) or null to use the importer's own defaults.
     * @param Account $account The target bank account.
     * @param bool    $write   When true the entries are persisted to the database.
     *
     * @return array The imported Entry objects.
     */
    public function import(mixed $source, Account $account, bool $write = true): array;
}
