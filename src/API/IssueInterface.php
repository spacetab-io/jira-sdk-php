<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

interface IssueInterface extends JiraInterface
{
    /**
     * @param string $issueIdOrKey
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(string $issueIdOrKey, array $params = []): Promise;

    /**
     * @param string $issueIdOrKey
     * @return \Amp\Promise
     */
    public function getWorklog(string $issueIdOrKey): Promise;
}
