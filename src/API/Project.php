<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

class Project extends HttpAPI implements ProjectInterface
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
