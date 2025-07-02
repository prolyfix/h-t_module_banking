<?php

namespace Prolyfix\BankingBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Prolyfix\BankingBundle\Entity\Account;
use Prolyfix\BankingBundle\Entity\Entry;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApobankXlsImporter
{

    private const ALLOW_FORMAT = ['csv'];
    private const TRANSLATION = [
        'Datum' => ['function'=>'setDate','transform' => 'date'],
        'Name' => ['function'=>'setCounterpart','transform' => 'text'],
        'Text' =>['function'=>'setTitle','transform' => 'text'], 
        'Betrag (EUR)' => ['function'=>'setAmount', 'transform' => 'float'],
    ];

    public function __construct(private EntityManagerInterface $em, SerializerInterface $serializer)
    {
        // Constructor to inject dependencies
    }

    public function isFormatAllowed(string $format): bool
    {
        return true;
    }

    public function isFileRight($file): bool
    {
        return true;
    }

    public function deserialize($file): array
    {

        $output = [];
        $csvData = $this->readCsvFile($file);
        $deserializedData = $this->deserializeCsv($csvData);
        return $deserializedData;
    }

    private function createEntity($input, Account $account): Entry
    {
        $entry = new Entry();
        $entry->setBank($account);
        foreach (self::TRANSLATION as $key => $values) {
            if (isset($input[$key])) {
                switch($values['transform']) {
                    case 'date':
                        $date = \DateTime::createFromFormat('d.m.Y', $input[$key]);
                        if ($date) {
                            $entry->{$values['function']}($date);
                        }
                        break;
                    case 'text':
                        $entry->{$values['function']}($input[$key]);
                        break;
                    case 'float':
                        $amount = str_replace(',', '.', $input[$key]);
                        $entry->{$values['function']}((float)$amount);
                        break;
                    default:
                        $entry->{$values['function']}($input[$key]);
                }
            }
        }
        return $entry;
    }

    public function import($file,Account $account, bool $write = true): array
    {
        $output = [];
        $normalized = $this->deserialize($file);
        foreach ($normalized as $input) {
            $entity = $this->createEntity($input, $account);
            $output[] = $entity;
            if ($write)
                $this->em->persist($entity);
        }
        if ($write)
            $this->em->flush();
        return $output;
    }
    public function readCsvFile($file)
    {
        $filePath = $file->getPathname();
        $csvData = [];

        if (($handle = fopen($filePath, "r")) !== FALSE) {

            while (($line = fgets($handle)) !== FALSE) {
                // Assume the original encoding is Windows-1252 (or try ISO-8859-1)
                // Convert the line to UTF-8 before fgetcsv processes it
                $convertedLine = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');

                // Now, use str_getcsv to parse the converted line
                // You might need to specify the delimiter and enclosure if they're not default
                $data = str_getcsv($convertedLine, ';', '"'); // Example: semicolon delimiter, double quote enclosure
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }
    public function deserializeCsv(array $csvData)
    {
        $encoder = new CsvEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $keys = $csvData[0]; // Assuming the first row contains the headers
        foreach ($csvData as $index => $row) {
            if ($index === 0) continue; // Skip the header row
            $data[] = array_combine($keys, $row);
        }
        return $data;
    }
}
