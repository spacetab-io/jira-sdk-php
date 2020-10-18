<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;
use Amp\Promise;

class Board extends HttpAPI implements BoardInterface
{
    public function all(?string $name = null, ?string $type = null, int $maxResults = 100): Iterator
    {
        $query = [];

        if ($name !== null) {
            $query['name'] = $name;
        }

        if ($type !== null) {
            $query['type'] = $type;
        }

        return $this->httpPaginate($maxResults, 'values', function($next, $offset) use ($query) {
            return $this->httpGet('/rest/agile/1.0/board', array_merge($query, [
                'startAt' => $offset,
                'maxResults' => $next,
            ]));
        });
    }

    public function get(int $boardId, array $params = []): Promise
    {
        return $this->httpGet(sprintf('/rest/agile/1.0/board/%d', $boardId), $params);
    }

    /**
     * @param int $boardId
     * @param array|string[] $state
     * @param int $maxResults
     * @return \Amp\Iterator
     */
    public function getBoardSprints(int $boardId, array $state = ['active'], int $maxResults = 100): Iterator
    {
        return $this->httpPaginate($maxResults, 'values', function($next, $offset) use ($boardId, $state) {
            return $this->httpGet(sprintf('/rest/agile/1.0/board/%d/sprint', $boardId), [
                'startAt' => $offset,
                'maxResults' => $next,
                'state' => join(',', $state)
            ]);
        });
    }
}
