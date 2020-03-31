<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;
use function Amp\call;

class Issue extends HttpAPI implements IssueInterface
{
    /**
     * @param string $issueIdOrKey
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(string $issueIdOrKey, array $params = []): Promise
    {
        $this->logger->info("Issue: Get one issue: {$issueIdOrKey}", $params);

        return call(function () use ($issueIdOrKey) {
            /** @var \Amp\Http\Client\Response $response */
            $response = yield $this->httpClient->request(
                $this->configuredRequest->makeRequest("/rest/api/2/issue/{$issueIdOrKey}")
            );

            return $this->handleResponse($response);
        });
    }
}
