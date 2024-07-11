<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Service\Api;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Http\ClientFactory;
use Laminas\Http\HeadersFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;

class Adapter
{

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const ACCESS_TOKEN_URL = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token';
    public const SCOPE = 'https://%s.onmicrosoft.com/%s/%s/.default';

    /**
     * @var array
     */
    private $token = [];
    /**
     * @var int
     */
    private $storeId = 0;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var ConfigRepository
     */
    private $configProvider;
    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var HeadersFactory
     */
    private $headersFactory;
    /**
     * @var ClientFactory
     */
    private $clientFactory;
    /**
     * @var array
     */
    private $credentials;

    public function __construct(
        HeadersFactory $headersFactory,
        ClientFactory $clientFactory,
        Json $json,
        ConfigRepository $configProvider,
        LogRepository $logRepository
    ) {
        $this->headersFactory = $headersFactory;
        $this->clientFactory = $clientFactory;
        $this->json = $json;
        $this->configProvider = $configProvider;
        $this->logRepository = $logRepository;
    }

    /**
     * @param $entry
     * @param $method
     * @param $data
     * @param int $storeId
     * @return array|bool[]|mixed
     */
    public function execute($entry, $method, $data = [], int $storeId = 0)
    {
        $this->storeId = $storeId;
        if (isset($data['credentials'])) {
            $this->credentials = $data['credentials'];
        } else {
            $this->credentials = $this->configProvider->getCredentials($storeId);
        }

        $httpHeaders = $this->headersFactory->create();
        $httpHeaders->addHeaders([
            'Accept' => 'application/json',
        ]);

        $this->client = $this->clientFactory->create();
        $this->client->setHeaders($httpHeaders);

        $tokenResult = $this->getToken();
        if ($entry == 'CredentialsTest') {
            return $tokenResult;
        }

        $this->logRepository->addDebugLog(
            sprintf('API CALL: [%s]', $entry),
            $data
        );

        if ($this->getMethod($method) == self::METHOD_POST) {
            $this->client->setMethod('post');
            $this->client->setRawBody($this->json->serialize($data));
        } elseif ($this->getMethod($method) == self::METHOD_GET) {
            $this->client->setMethod('get');
        }

        $this->client->setOptions([
            'adapter' => Curl::class,
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 180,
        ]);

        $this->client->setUri($this->getRequestUrl($entry, $storeId));

        $response = $this->client->send();
        $status = $response->getStatusCode();

        $this->logRepository->addDebugLog(
            sprintf('API RESULT [%s => %s] (status: %s)', $method, $entry, $status),
            $response->getBody()
        );

        if ($status == 202) {
            return [
                'success' => true
            ];
        }
        if ($status >= 401 && $status <= 403) {
            return [
                'success' => false,
                'result' => 'forbidden'
            ];
        }

        if ($status == 504) {
            return [
                'success' => false,
                'result' => 'Gateway Time-out'
            ];
        }

        $result = $this->json->unserialize(
            $response->getBody()
        );

        if ($status >= 100 && $status < 300) {
            return [
                'success' => true,
                'result' => $result['Result']
            ];
        } else {
            return [
                'success' => false,
                'result' => $this->formatApiError($result, $status)
            ];
        }
    }

    /**
     * Token retriever
     *
     * @return mixed
     */
    private function getToken(): array
    {
        if (!$this->credentials) {
            $this->credentials = $this->configProvider->getCredentials($this->storeId);
        }

        $this->client->setMethod('post');
        $this->client->setUri($this->getTokenUrl());

        $this->client->setParameterPost(
            [
                'grant_type' => 'client_credentials',
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'scope' => $this->getScope()
            ]
        );

        $response = $this->client->send();

        $result = $this->json->unserialize(
            $response->getBody()
        );

        $status = $response->getStatusCode();

        if ($status >= 100 && $status < 300) {
            $token = $result['access_token'] ?? '';
            $this->token[$this->storeId] = (string)$token;
            $this->client->resetParameters();
            $this->client->setHeaders([
                'Accept' => 'application/json',
                "Authorization" => "Bearer " . $token,
                'Ocp-Apim-Subscription-Key' => $this->configProvider->getSubscriptionKey($this->storeId)
            ]);
            return [
                'success' => true,
                'token' => $this->token[$this->storeId]
            ];
        }

        return [
            'success' => false,
            'result' => $this->formatApiError($result, $status)
        ];
    }

    /**
     * @return string
     */
    private function getTokenUrl(): string
    {
        return sprintf(self::ACCESS_TOKEN_URL, $this->credentials['b2c_tenant_id']);
    }

    /**
     * @param string $entry
     * @param int $storeId
     * @return string
     */
    private function getRequestUrl(string $entry, int $storeId): string
    {
        return $this->configProvider->getCredentials($storeId)['api_url'] . '/' . $entry;
    }

    /**
     * @return string
     */
    private function getScope(): string
    {
        return sprintf(
            self::SCOPE,
            $this->credentials['b2c_tenant_name'],
            $this->credentials['client_id'],
            $this->credentials['tenant_id']
        );
    }

    /**
     * @param string $method
     *
     * @return string
     */
    private function getMethod(string $method): string
    {
        switch (strtoupper($method)) {
            case 'GET':
                return self::METHOD_GET;
            case 'POST':
                return self::METHOD_POST;
        }
        return '';
    }

    /**
     * @param $result
     * @param $status
     *
     * @return Phrase
     */
    private function formatApiError($result, $status): Phrase
    {
        $this->logRepository->addErrorLog(
            sprintf('API ERROR [%s]', $status),
            $result
        );

        if (is_array($result) && !empty($result['Fault']['Message'])) {
            return __('Unable to process request: %1.', $result['Fault']['Message']);
        }

        return __('Unable to process request.');
    }
}
