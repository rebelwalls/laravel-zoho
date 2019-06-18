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

            $options['headers'] = $this->createHeaders();
            $options['form_params'] = ['ZOHO_IMPORT_DATA' => json_encode($payload)];

            $client = new Client();
            $response = $client->request('POST', $uri . '?' . $parameters, $options);

            return $response;

//            dd($response);

//            $logger->reportSuccess($endpoint, $payload, $response);

//            return $this->handleResponse($response);
        } catch (ClientException $exception) {
//            $logger->reportFailure($endpoint, $payload, $exception->getMessage());

//            throw new FortnoxException($exception);

            
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
     * Compiles the header information with type, security etc
     *
     * @return array
     */
    private function createHeaders()
    {
        return [
//            'Content-Type' => 'multipart/form-data',
        ];
    }

    /**
     * Converts an API respons to a return object depending on content type
     *
     * @param Response $response
     *
     * @return FortnoxApiResponse
     */
    private function handleResponse(Response $response)
    {
        $return = new FortnoxApiResponse();
        $content_type = $response->getHeader('Content-Type');

        if (in_array('application/json', $content_type)) {
            $return = json_decode($response->getBody());
        } else if (in_array('application/pdf', $content_type)) {
            $return->PDFContents = $response->getBody()->getContents();
        }

        return $return;
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
