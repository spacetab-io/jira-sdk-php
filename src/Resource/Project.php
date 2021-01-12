<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Resource;

use Amp\Promise;

class Project extends Resource
{

    public function all(array $params = []): Promise
    {
        return $this->httpGet('/rest/api/2/project', $params);
    }

    public function get(string $projectIdOrKey, array $params = []): Promise
    {
        return $this->httpGet("/rest/api/2/project/{$projectIdOrKey}", $params);
    }
}
