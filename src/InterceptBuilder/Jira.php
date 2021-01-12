<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\InterceptBuilder;

use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\DelegateHttpClient;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\InterceptedHttpClient;
use Amp\Http\Client\Interceptor\DecompressResponse;
use Amp\Http\Client\Interceptor\FollowRedirects;
use Amp\Http\Client\Interceptor\RetryRequests;
use Amp\Http\Client\Interceptor\SetRequestHeaderIfUnset;
use Amp\Http\Client\PooledHttpClient;
use Spacetab\SDK\Jira\InterceptBuilder;
use Spacetab\SDK\Jira\Client;

final class Jira implements InterceptBuilder
{
    private const RETRY_REQUESTS   = 5;
    private const FOLLOW_REDIRECTS = 10;

    /** @var PooledHttpClient|InterceptedHttpClient  */
    private $client;
    private array $networkInterceptors;
    private array $applicationInterceptors;
    private string $basicUsername;
    private string $basicPassword;

    /**
     * Jira constructor.
     *
     * @param string $basicUsername
     * @param string $basicPassword
     */
    public function __construct(string $basicUsername, string $basicPassword)
    {
        $this->client = new PooledHttpClient(new UnlimitedConnectionPool);

        // Default network interceptors.
        $this->networkInterceptors = [
            new SetRequestHeaderIfUnset('Accept', 'application/json'),
            new SetRequestHeaderIfUnset('Content-Type', 'application/json'),
            new SetRequestHeaderIfUnset('User-Agent', sprintf('Spacetab Jira PHP SDK / v%s', Client::VERSION)),
            new DecompressResponse()
        ];

        // Default application interceptors.
        $this->applicationInterceptors = [
            new RetryRequests(self::RETRY_REQUESTS),
            new FollowRedirects(self::FOLLOW_REDIRECTS)
        ];

        $this->basicUsername = $basicUsername;
        $this->basicPassword = $basicPassword;
    }

    private function getAuthorizationHeaderValue(): string
    {
        return 'Basic ' . base64_encode("$this->basicUsername:$this->basicPassword");
    }

    public function build(): DelegateHttpClient
    {
        $this->networkInterceptors[] = new SetRequestHeaderIfUnset(
            'Authorization', $this->getAuthorizationHeaderValue()
        );

        foreach ($this->networkInterceptors as $interceptor) {
            $this->client = $this->client->intercept($interceptor);
        }

        foreach ($this->applicationInterceptors as $interceptor) {
            $this->client = new InterceptedHttpClient($this->client, $interceptor);
        }

        return new HttpClient($this->client);
    }
}
