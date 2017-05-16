<?php

declare(strict_types = 1);

namespace Solcloud\RabbitMQ\Api;

use Solcloud\Http\Request;
use Solcloud\Http\Contract\IRequestDownloader;
use Solcloud\RabbitMQ\Api\Exception\ApiException;

class Client
{

    protected $apiUrl = 'http://change.me.localhost';
    protected $vhost = 'changeme';
    protected $apiUsername = 'changeme';
    protected $apiPassword = 'changeme';

    /**
     * @var IRequestDownloader
     */
    protected $requestDownloader;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(IRequestDownloader $requestDownloader, Request $request)
    {
        $this->requestDownloader = $requestDownloader;
        $this->request = $request;
    }

    protected function encode(string $param): string
    {
        return urlencode($param);
    }

    protected function fetchResponse(string $endpointUrl): array
    {
        $this->request->setBasicHTTPAuthentication($this->apiUsername, $this->apiPassword);
        $this->request->setUrl($this->getApiUrl() . $endpointUrl);

        $response = $this->requestDownloader->fetchResponse($this->request);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), TRUE);
        }

        throw new ApiException('Api ERROR! RAW response body: ' . $response->getBody());
    }

    protected function fetchQueueInfo(string $queueName): array
    {
        $endpoint = '/api/queues/%s/%s';

        return $this->fetchResponse(sprintf($endpoint, $this->encode($this->getVhost()), $this->encode($queueName)));
    }

    public function getTotalMessageCountInQueue(string $queueName): int
    {
        $response = $this->fetchQueueInfo($queueName);

        if (isset($response['messages'])) {
            return (int) $response['messages'];
        }

        throw new ApiException('Unable to get total message count (key "messages") from response');
    }

    public function getUnackedMessageCountInQueue(string $queueName): int
    {
        $response = $this->fetchQueueInfo($queueName);

        if (isset($response['messages_unacknowledged'])) {
            return (int) $response['messages_unacknowledged'];
        }

        throw new ApiException('Unable to get unacked message count (key "messages_unacknowledged") from response');
    }

    public function getReadyMessageCountInQueue(string $queueName): int
    {
        $response = $this->fetchQueueInfo($queueName);

        if (isset($response['messages_ready'])) {
            return (int) $response['messages_ready'];
        }

        throw new ApiException('Unable to get ready message count (key "messages_ready") from response');
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = rtrim($apiUrl, '/');
    }

    public function setApiUsername(string $apiUsername): void
    {
        $this->apiUsername = $apiUsername;
    }

    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    public function getVhost(): string
    {
        return $this->vhost;
    }

    public function setVhost(string $vhost): void
    {
        $this->vhost = $vhost;
    }

}
