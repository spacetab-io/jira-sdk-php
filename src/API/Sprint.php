<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

class Sprint extends HttpAPI implements SprintInterface
{
    public function get(int $sprintId): Promise
    {
        return $this->httpGet(sprintf('/rest/agile/1.0/sprint/%d', $sprintId));
    }
}
