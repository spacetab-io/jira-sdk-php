<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;

interface SearchInterface
{
    /**
     * @param string $jql
     * @param array $fields
     * @param int $maxResults
     *
     * @return \Amp\Iterator
     */
    public function query(string $jql, array $fields = [], int $maxResults = 100): Iterator;

    /**
     * @param string $jql
     * @param array $issueFields
     * @return \Amp\Iterator
     */
    public function worklogs(string $jql, array $issueFields = []): Iterator;
}
