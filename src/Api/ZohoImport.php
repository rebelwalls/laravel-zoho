<?php

namespace RebelWalls\Zoho\Api;

class ZohoImport extends ZohoApi
{
    private $importType = false;

    /**
     * @param bool $clearTable
     *
     * @return ZohoImport
     */
    public function clearTableBeforeImport($clearTable = true)
    {
        $this->importType = $clearTable ? 'TRUNCATEADD' : 'APPEND';

        return $this;
    }

    /**
     * @param $table
     * @param array $data
     * @param null $workspace
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function import($table, array $data, $workspace = null)
    {
        if (! $workspace) {
            $workspace = config('zoho.default_workspace');
        }

        $params = [
            'ZOHO_ACTION' => 'IMPORT',
            'ZOHO_IMPORT_FILETYPE' => 'JSON',
            'ZOHO_OUTPUT_FORMAT' => 'JSON',
            'ZOHO_ERROR_FORMAT' => 'JSON',
            'ZOHO_API_VERSION' => '1.0',
            'ZOHO_IMPORT_TYPE' => $this->importType,
            'ZOHO_AUTO_IDENTIFY' => 'TRUE',
            'ZOHO_ON_IMPORT_ERROR' => 'ABORT',
            'ZOHO_CREATE_TABLE' => 'TRUE'
        ];

        $this->call($table, $params, $data, $workspace);
    }
}
