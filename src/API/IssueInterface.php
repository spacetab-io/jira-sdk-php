<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface IssueInterface
{
    /**
     * @param string $issueIdOrKey
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(string $issueIdOrKey, array $params = []): Promise;
}
