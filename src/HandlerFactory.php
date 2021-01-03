<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Amp\Http\Client\HttpClient;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spacetab\JiraSDK\API\Board;
use Spacetab\JiraSDK\API\Issue;
use Spacetab\JiraSDK\API\Project;
use Spacetab\JiraSDK\API\Search;
use Spacetab\JiraSDK\API\Sprint;
use Spacetab\JiraSDK\Exception\SdkErrorException;

class HandlerFactory
{
    private HttpClient $httpClient;
    private ConfiguredRequest $configuredRequest;
    private LoggerInterface $logger;

    /**
     * HttpAPI constructor.
     *
     * @param HttpClient $httpClient
     * @param ConfiguredRequest $configuredRequest
     * @param ?LoggerInterface $logger
     */
    public function __construct(HttpClient $httpClient, ConfiguredRequest $configuredRequest, ?LoggerInterface $logger = null)
    {
        $this->httpClient        = $httpClient;
        $this->configuredRequest = $configuredRequest;
        $this->logger            = $logger ?: new NullLogger();
    }

    /**
     * @param string $name
     * @return mixed
     * @throws SdkErrorException
     */
    public function create(string $name)
    {
        $arguments = [$this->httpClient, $this->configuredRequest, $this->logger];

        switch ($name) {
            case 'issue':
            case 'issues':
                return new Issue(...$arguments);
            case 'search':
                return new Search(...$arguments);
            case 'project':
                return new Project(...$arguments);
            case 'board':
                return new Board(...$arguments);
            case 'sprint':
                return new Sprint(...$arguments);
            default:
                throw new SdkErrorException("Http handler `{$name}` not supported.");
        }
    }
}
