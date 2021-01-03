<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Response;
use Amp\Iterator;
use Amp\Producer;
use Amp\Promise;
use Exception;
use Generator;
use JsonException;
use Kelunik\Retry\ConstantBackoff;
use Psr\Log\LoggerInterface;
use Spacetab\JiraSDK\ConfiguredRequest;
use Spacetab\JiraSDK\Exception\ResponseErrorException;
use Spacetab\JiraSDK\Exception\UnknownErrorException;
use function Amp\call;
use function Kelunik\Retry\retry;

abstract class HttpAPI
{
    private const PAGINATE_CHUNK_SIZE    = 7;
    private const REQUEST_RETRY_ATTEMPTS = 10;
    private const REQUEST_RETRY_DELAY    = 500;

    protected LoggerInterface $logger;
    protected HttpClient $httpClient;
    protected ConfiguredRequest $configuredRequest;

    /**
     * HttpAPI constructor.
     *
     * @param HttpClient $httpClient
     * @param ConfiguredRequest $configuredRequest
     * @param LoggerInterface $logger
     */
    public function __construct(HttpClient $httpClient, ConfiguredRequest $configuredRequest, LoggerInterface $logger)
    {
        $this->httpClient        = $httpClient;
        $this->configuredRequest = $configuredRequest;
        $this->logger            = $logger;
    }

    protected function httpPaginate(int $maxResults, string $valuesKey, callable $callbackRequest): Iterator
    {
        return new Producer(function (callable $emit) use ($maxResults, $valuesKey, $callbackRequest): Generator {
            $this->logger->debug("Gets a paginated result; max: {$maxResults}; offset: 0");
            $firstItem = yield $callbackRequest($maxResults, 0);

            foreach ($firstItem[$valuesKey] as $item) {
                $emit($item);
            }

            // WORKAROUND
            // Jira Agile API does not have `total` key in response but should.
            // If it not exists I count all items in the response and then emulate it.
            $totalCount = $firstItem['total'] ?? count($firstItem[$valuesKey]);
            $page = $totalCount / $maxResults;

            $this->logger->debug("Total records is {$totalCount}");

            $promises = [];
            for ($startAt = 1; $startAt < $page; $startAt++) {
                $offset  = $startAt * $maxResults;
                $message = "Continues a get paginated results; max: {$maxResults}; offset: {$offset}";
                $this->logger->debug($message);
                $promises[] = $callbackRequest($maxResults, $offset);
            }

            $results = [];
            foreach (array_chunk($promises, self::PAGINATE_CHUNK_SIZE) as $group) {
                $results += yield $group;
            }

            foreach ($results as $item) {
                foreach ($item[$valuesKey] as $one) {
                    $emit($one);
                }
            }
        });
    }

    /**
     * Sends a GET HTTP Request with query parameters.
     *
     * @param string $path
     * @param array $query
     * @return Promise
     */
    protected function httpGet(string $path, array $query = []): Promise
    {
        if (count($query) > 0) {
            $path = sprintf('%s?%s', $path, http_build_query($query));
        }

        $this->logger->debug("Send GET request to {$path}", compact('query'));

        return $this->retry(function () use ($path) {
            /** @var Response $response */
            $response = yield $this->httpClient->request(
                $this->configuredRequest->makeRequest($path)
            );

            return $this->handleResponse($response, $path);
        });
    }

    /**
     * Sends a GET HTTP Request with query parameters.
     *
     * @param string $path
     * @param array $query
     * @param array $body
     * @return Promise
     */
    protected function httpPost(string $path, array $query = [], array $body = []): Promise
    {
        $payload = null;

        if (count($query) > 0) {
            $path = sprintf('%s?%s', $path, http_build_query($query));
        }

        if (count($body) > 0) {
            $payload = (string) json_encode($body);
        }

        $this->logger->debug("Send POST request to {$path}", compact('payload'));

        return $this->retry(function () use ($path, $payload) {
            /** @var Response $response */
            $response = yield $this->httpClient->request(
                $this->configuredRequest->makeRequest($path, 'POST', $payload)
            );

            return $this->handleResponse($response, $path, $payload);
        });
    }

    /**
     * Handle the response.
     *
     * @param Response $response
     * @param string $path
     * @param string $payload
     * @return Promise
     */
    protected function handleResponse(Response $response, string $path, string $payload = ''): Promise
    {
        return call(function () use ($response, $path, $payload) {
            $this->logger->debug("Received a response for {$path} with status code: {$response->getStatus()}");

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
                $this->logger->debug("Response for {$path} is correct, return a parsed server value...");
                return $data;
            }

            if (isset($data['errorMessages']) && count($data['errorMessages']) > 0) {
                foreach ($data['errorMessages'] as $message) {
                    // @phpstan-ignore-next-line
                    $exception->setErrorMessage($message);
                }
            }

            $this->logger->info("Response for {$path} incorrect, stops the request...", compact('payload'));

            // @phpstan-ignore-next-line
            throw $exception;
        });
    }

    protected function retry(callable $callback): Promise
    {
        return retry(self::REQUEST_RETRY_ATTEMPTS, $callback, Exception::class, new ConstantBackoff(self::REQUEST_RETRY_DELAY));
    }

    /**
     * Parses JSON and catch error if Jira responses HTML.
     *
     * @param mixed $body
     * @return array
     * @throws ResponseErrorException
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
