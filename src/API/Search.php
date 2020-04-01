<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;

class Search extends HttpAPI implements SearchInterface
{
    /**
     * @inheritDoc
     */
    public function query(string $jql, array $params = []): Promise
    {
        $request = array_merge($params, compact('jql'));

        return $this->httpPost('/rest/api/2/search', [], $request);
    }
}
