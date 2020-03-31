<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface SearchInterface
{
    /**
     * @param string $jql
     * @param array $params
     * @return \Amp\Promise
     */
    public function query(string $jql, array $params = []): Promise;
}
