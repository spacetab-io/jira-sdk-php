<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Amp\Http\Client\HttpClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spacetab\JiraSDK\API\Issue;
use Spacetab\JiraSDK\API\IssueInterface;
use Spacetab\JiraSDK\API\Project;
use Spacetab\JiraSDK\API\ProjectInterface;
use Spacetab\JiraSDK\API\Search;
use Spacetab\JiraSDK\API\SearchInterface;

class JiraSDK implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const VERSION = '1.0.0b';

    /**
     * @var \Spacetab\JiraSDK\HttpClientConfigurator
     */
    private HttpClientConfigurator $clientConfigurator;

    /**
     * @var \Amp\Http\Client\HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var \Spacetab\JiraSDK\ConfiguredRequest
     */
    private ConfiguredRequest $httpRequest;

    /**
     * JiraSDK constructor.
     *
     * @param \Spacetab\JiraSDK\HttpClientConfigurator $clientConfigurator
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(HttpClientConfigurator $clientConfigurator, ?LoggerInterface $logger = null)
    {
        $this->clientConfigurator = $clientConfigurator;
        $this->httpClient         = $clientConfigurator->createConfiguredHttpClient();
        $this->httpRequest        = $clientConfigurator->createConfiguredHttpRequest();
        $this->logger             = $logger ?: new NullLogger();
    }

    /**
     * @param string $endpoint
     * @param string $basicUsername
     * @param string $basicPassword
     *
     * @return static
     */
    public static function new(string $endpoint, string $basicUsername, string $basicPassword): self
    {
        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint)
            ->setBasicUsername($basicUsername)
            ->setBasicPassword($basicPassword);

        return new JiraSDK($configurator);
    }

    public function issues(): IssueInterface
    {
        return new Issue($this->httpClient, $this->httpRequest, $this->logger);
    }

    public function search(): SearchInterface
    {
        return new Search($this->httpClient, $this->httpRequest, $this->logger);
    }

    public function project(): ProjectInterface
    {
        return new Project($this->httpClient, $this->httpRequest, $this->logger);
    }
}
