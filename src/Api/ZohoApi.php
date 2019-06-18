<?php

namespace RebelWalls\Zoho\Api;

use App\Services\IntegrationLog\FortnoxIntegrationLogger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use stdClass;

abstract class ZohoApi
{
    /**
     * Responsible for making the final API call
     *
     * @param $email
     * @param $workspace
     * @param $tableName
     * @param null $params
     * @param array $payload
     *
     * @return mixed|stdClass
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call($tableName, $params = null, array $payload = [], $workspace = null)
    {
        try {
            $uri = $this->createBaseUri($tableName, $workspace);
            $parameters = $this->createParameterString($params);

            $options['form_params'] = ['ZOHO_IMPORT_DATA' => json_encode($payload)];

            $client = new Client();
            $response = $client->request('POST', $uri . '?' . $parameters, $options);

            return $response;
        } catch (ClientException $exception) {
            $responseContent = $exception->getResponse()->getBody()->getContents();
            $errorObject = json_decode($responseContent);

            dd($errorObject->response->error->message);
        }
    }

    /**
     * Compiles the API uri with the endpoint and optional parameters
     *
     * @param $workspace
     * @param $tableName
     *
     * @return mixed|string
     */
    private function createBaseUri($tableName, $workspace)
    {
        $baseUri = config('zoho.base_uri');
        $email = config('zoho.email');

        return concat_uri($baseUri, $email, $workspace, $tableName);
    }

    /**
     * @param $params
     *
     * @return string
     */
    private function createParameterString($params)
    {
        $params['authtoken'] = config('zoho.authtoken');

        return http_build_query($params);
    }
}
