<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;

class Search extends HttpAPI implements SearchInterface
{
    /**
     * @inheritDoc
     */
    public function query(string $jql, array $fields = [], int $maxResults = 100): Iterator
    {
        return $this->httpPaginate($maxResults, function($next, $offset) use ($jql, $fields) {
            return $this->httpPost('/rest/api/2/search', [], [
                'jql' => $jql,
                'startAt' => $offset,
                'maxResults' => $next,
                'fields' => $fields,
            ]);
        });
    }
}
