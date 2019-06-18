<?php

namespace RebelWalls\Zoho;

use RebelWalls\Zoho\Api\ZohoImport;

class ZohoService
{
    private $importApi;

    public function __construct()
    {
        $this->importApi = new ZohoImport();
    }

    /**
     * @param string $table
     * @param array $data
     * @param bool $clearTable
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import(string $table, array $data, $clearTable = false)
    {
        return $this->importApi->clearTableBeforeImport($clearTable)->import($table, $data);
    }
}
