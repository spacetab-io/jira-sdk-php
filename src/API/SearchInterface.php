<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;

interface SearchInterface
{
    public function query(string $jql, array $fields = [], int $maxResults = 100): Iterator;
    public function worklogs(string $jql, array $issueFields = []): Iterator;
}
