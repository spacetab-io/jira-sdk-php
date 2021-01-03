<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface SprintInterface
{
    public function get(int $sprintId): Promise;
}
