<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface ProjectInterface
{
    /**
     * @param array $params
     * @return \Amp\Promise
     */
    public function all(array $params = []): Promise;

    /**
     * @param string $projectIdOrKey
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(string $projectIdOrKey, array $params = []): Promise;
}
