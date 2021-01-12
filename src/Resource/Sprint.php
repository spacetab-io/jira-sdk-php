<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Resource;

use Amp\Promise;

class Sprint extends Resource
{
    public function get(int $sprintId): Promise
    {
        return $this->httpGet(sprintf('/rest/agile/1.0/sprint/%d', $sprintId));
    }
}
