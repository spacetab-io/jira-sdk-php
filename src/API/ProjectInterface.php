<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface ProjectInterface
{
    public function all(array $params = []): Promise;
    public function get(string $projectIdOrKey, array $params = []): Promise;
}
