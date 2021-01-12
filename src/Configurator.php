<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira;

use Amp\Http\Client\DelegateHttpClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spacetab\SDK\Jira\Exception;

final class Configurator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private Request $request;
    private Cache $cache;
    private InterceptBuilder $interceptBuilder;
    private DelegateHttpClient $httpClient;

    private function __construct() { /* nothing here */ }

    public static function fromBasicAuth(string $endpoint, string $basicUsername, string $basicPassword): self
    {
        $configurator = new Configurator();
        $configurator->setRequest(new Request\Jira($endpoint));
        $configurator->setCache(Cache::disabled());
        $configurator->setLogger(new NullLogger());
        $configurator->setInterceptBuilder(new InterceptBuilder\Jira($basicUsername, $basicPassword));

        return $configurator;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function setCache(Cache $cache): void
    {
        $this->cache = $cache;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getInterceptBuilder(): InterceptBuilder
    {
        return $this->interceptBuilder;
    }

    public function setInterceptBuilder(InterceptBuilder $builder): void
    {
        $this->interceptBuilder = $builder;
    }

    /**
     * @return DelegateHttpClient
     * @throws Exception\Configurator
     */
    public function getHttpClient(): DelegateHttpClient
    {
        if (!isset($this->httpClient)) {
            throw Exception\Configurator::forgotCallMethod();
        }

        return $this->httpClient;
    }

    public function configurate(): void
    {
        $this->httpClient = $this->getInterceptBuilder()->build();
    }
}
