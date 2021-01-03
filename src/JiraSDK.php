<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spacetab\JiraSDK\API\BoardInterface;
use Spacetab\JiraSDK\API\IssueInterface;
use Spacetab\JiraSDK\API\ProjectInterface;
use Spacetab\JiraSDK\API\SearchInterface;
use Spacetab\JiraSDK\API\SprintInterface;

class JiraSDK implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const VERSION = '1.0.0b';

    private HttpClientConfigurator $clientConfigurator;
    private HandlerFactory $handlerFactory;

    /**
     * JiraSDK constructor.
     *
     * @param HttpClientConfigurator $clientConfigurator
     * @param LoggerInterface|null $logger
     */
    public function __construct(HttpClientConfigurator $clientConfigurator, ?LoggerInterface $logger = null)
    {
        $this->clientConfigurator = $clientConfigurator;
        $this->handlerFactory     = new HandlerFactory(
            $clientConfigurator->createConfiguredHttpClient(),
            $clientConfigurator->createConfiguredHttpRequest(),
            $logger ?: new NullLogger()
        );
    }

    public static function new(string $endpoint, string $basicUsername, string $basicPassword): self
    {
        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint)
            ->setBasicUsername($basicUsername)
            ->setBasicPassword($basicPassword);

        return new JiraSDK($configurator);
    }

    /**
     * @deprecated
     * @return IssueInterface
     * @throws Exception\SdkErrorException
     */
    public function issues(): IssueInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }

    public function issue(): IssueInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }

    public function search(): SearchInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }

    public function project(): ProjectInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }

    public function board(): BoardInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }

    public function sprint(): SprintInterface
    {
        return $this->handlerFactory->create(__FUNCTION__);
    }
}
