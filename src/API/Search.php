<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Delayed;
use Amp\Iterator;
use Amp\Producer;

class Search extends HttpAPI implements SearchInterface
{
    private const VALUES_KEY = 'issues';
    private const WORKLOG_CHUNK_SIZE = 100;
    private const DELAY_AFTER_PAGINATION = 150;

    /**
     * @inheritDoc
     */
    public function query(string $jql, array $fields = [], int $maxResults = 100): Iterator
    {
        return $this->httpPaginate($maxResults, self::VALUES_KEY, function($next, $offset) use ($jql, $fields) {
            return $this->httpPost('/rest/api/2/search', [], [
                'jql' => $jql,
                'startAt' => $offset,
                'maxResults' => $next,
                'fields' => $fields,
            ]);
        });
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function worklogs(string $jql, array $issueFields = []): Iterator
    {
        return new Producer(function (callable $emit) use ($jql, $issueFields) {
            $query = $this->query($jql, $issueFields);

            $issues = [];
            while (yield $query->advance()) {
                $issues[] = $query->getCurrent();
            }

            new Delayed(self::DELAY_AFTER_PAGINATION);

            $tasks = [];
            $promises = [];

            foreach (array_chunk($issues, self::WORKLOG_CHUNK_SIZE) as $chunk) {
                foreach ($chunk as $issue) {
                    $tasks[$issue['key']] = $issue;
                    $promises[$issue['key']] = $this->httpGet("/rest/api/2/issue/{$issue['key']}/worklog");
                }

                foreach (yield $promises as $promiseIssue => $promiseItem) {
                    $emit([
                        'issue' => $tasks[$promiseIssue],
                        'worklogs' => $promiseItem['worklogs'],
                    ]);
                }

                $tasks = [];
                $promises = [];
            }
        });
    }
}
