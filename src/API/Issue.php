<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

class Issue extends HttpAPI implements IssueInterface
{
    /**
     * @param string $issueIdOrKey
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(string $issueIdOrKey, array $params = []): Promise
    {
        return $this->httpGet("/rest/api/2/issue/{$issueIdOrKey}");
    }

    /**
     * @inheritDoc
     */
    public function getWorklog(string $issueIdOrKey): Promise
    {
        return $this->httpGet("/rest/api/2/issue/{$issueIdOrKey}/worklog");
    }
}
