<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Response;
use Amp\Promise;
use JsonException;
use Psr\Log\LoggerInterface;
use Spacetab\JiraSDK\ConfiguredRequest;
use Spacetab\JiraSDK\Exception\ResponseErrorException;
use Spacetab\JiraSDK\Exception\UnknownErrorException;
use function Amp\call;

abstract class HttpAPI
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var \Amp\Http\Client\HttpClient
     */
    protected HttpClient $httpClient;

    /**
     * @var \Spacetab\JiraSDK\ConfiguredRequest
     */
    protected ConfiguredRequest $configuredRequest;

    /**
     * Issue constructor.
     *
     * @param \Amp\Http\Client\HttpClient $httpClient
     * @param \Spacetab\JiraSDK\ConfiguredRequest $configuredRequest
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(HttpClient $httpClient, ConfiguredRequest $configuredRequest, LoggerInterface $logger)
    {
        $this->httpClient        = $httpClient;
        $this->configuredRequest = $configuredRequest;
        $this->logger            = $logger;
    }

    /**
     * @param \Amp\Http\Client\Response $response
     * @return \Amp\Promise
     */
    protected function handleResponse(Response $response): Promise
    {
        return call(function () use ($response) {
            $this->logger->info("HttpAPI: Received a response with status code: {$response->getStatus()}");

            $continue = false;
            switch (true) {
                case $response->getStatus() >= 200 && $response->getStatus() <= 299:
                    $continue = true;
                    break;
                case $response->getStatus() === 400:
                    $exception = ResponseErrorException::badRequest();
                    break;
                case $response->getStatus() === 401:
                    $exception = ResponseErrorException::unauthorized();
                    break;
                case $response->getStatus() === 402:
                    $exception = ResponseErrorException::requestFailed();
                    break;
                case $response->getStatus() === 403:
                    $exception = ResponseErrorException::forbidden();
                    break;
                case $response->getStatus() === 404:
                    $exception = ResponseErrorException::notFound();
                    break;
                case $response->getStatus() === 413:
                    $exception = ResponseErrorException::payloadTooLarge();
                    break;
                case $response->getStatus() >= 500 && $response->getStatus() <= 599:
                    $exception = ResponseErrorException::serverError();
                    break;
                default:
                    $exception = UnknownErrorException::unknownError();
            }

            $data = $this->parseJson(yield $response->getBody()->buffer());

            if ($continue) {
                $this->logger->info('HttpAPI: Response is correct, return a parsed server value...');
                return $data;
            }

            if (isset($data['errorMessages']) && count($data['errorMessages']) > 0) {
                foreach ($data['errorMessages'] as $message) {
                    $exception->setErrorMessage($message);
                }
            }

            $this->logger->info('HttpAPI: Response incorrect, stops the request...');

            throw $exception;
        });
    }

    /**
     * @param $body
     * @return array
     * @throws \Spacetab\JiraSDK\Exception\ResponseErrorException
     */
    protected function parseJson($body): array
    {
        $body = (string) $body;

        try {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->debug('HttpAPI: Parse body response failed, because it\'s not a json.', compact('body'));
            throw ResponseErrorException::invalidBodyFormat($e);
        }
    }
}
