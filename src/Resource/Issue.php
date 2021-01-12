<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Resource;

use Amp\Promise;

class Issue extends Resource
{
    public function get(string $issueIdOrKey, array $params = []): Promise
    {
        return $this->httpGet("/rest/api/2/issue/{$issueIdOrKey}", $params);
    }

    public function getWorklog(string $issueIdOrKey): Promise
    {
        return $this->httpGet("/rest/api/2/issue/{$issueIdOrKey}/worklog");
    }
}
