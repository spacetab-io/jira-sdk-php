<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface IssueInterface
{
    public function get(string $issueIdOrKey, array $params = []): Promise;
    public function getWorklog(string $issueIdOrKey): Promise;
}
