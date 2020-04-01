<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\InterceptedHttpClient;
use Amp\Http\Client\Interceptor\DecompressResponse;
use Amp\Http\Client\Interceptor\FollowRedirects;
use Amp\Http\Client\Interceptor\RetryRequests;
use Amp\Http\Client\Interceptor\SetRequestHeaderIfUnset;
use Amp\Http\Client\PooledHttpClient;

final class HttpClientConfigurator
{
    /**
     * Retry requests count.
     */
    private const RETRY_REQUESTS   = 5;
    private const FOLLOW_REDIRECTS = 10;

    /**
     * @var string
     */
    private string $basicAuthUsername;

    /**
     * @var string
     */
    private string $basicAuthPassword;

    /**
     * @var string
     */
    private string $endpoint;

    /**
     * @var \Amp\Http\Client\Connection\UnlimitedConnectionPool
     */
    private UnlimitedConnectionPool $pool;

    /**
     * HttpClientConfigurator constructor.
     */
    public function __construct()
    {
        $this->pool = new UnlimitedConnectionPool;
    }

    /**
     * @return \Amp\Http\Client\HttpClient
     */
    public function createConfiguredHttpClient(): HttpClient
    {
        /** @var PooledHttpClient $client */
        $client = new PooledHttpClient($this->pool);

        $interceptors = [
            new SetRequestHeaderIfUnset('Accept', 'application/json'),
            new SetRequestHeaderIfUnset('Content-Type', 'application/json'),
            new SetRequestHeaderIfUnset('Authorization', $this->getAuthorizationHeaderValue()),
            new SetRequestHeaderIfUnset('User-Agent', sprintf('Jira PHP SDK / v%s', JiraSDK::VERSION)),
            new DecompressResponse()
        ];

        foreach ($interceptors as $interceptor) {
            $client = $client->intercept($interceptor);
        }

        $client = new InterceptedHttpClient($client, new RetryRequests(self::RETRY_REQUESTS));
        $client = new InterceptedHttpClient($client, new FollowRedirects(self::FOLLOW_REDIRECTS));

        return new HttpClient($client);
    }

    /**
     * @return \Spacetab\JiraSDK\ConfiguredRequest
     */
    public function createConfiguredHttpRequest(): ConfiguredRequest
    {
        return (new ConfiguredRequest($this->endpoint));
    }

    /**
     * @param string $endpoint
     * @return \Spacetab\JiraSDK\HttpClientConfigurator
     */
    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setBasicUsername(string $username): self
    {
        $this->basicAuthUsername = $username;

        return $this;
    }

    /**
     * @param string $password
     * @return \Spacetab\JiraSDK\HttpClientConfigurator
     */
    public function setBasicPassword(string $password): self
    {
        $this->basicAuthPassword = $password;

        return $this;
    }

    /**
     * @return string
     */
    private function getAuthorizationHeaderValue(): string
    {
        return 'Basic ' . base64_encode("$this->basicAuthUsername:$this->basicAuthPassword");
    }
}
