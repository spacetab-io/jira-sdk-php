<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Promise;
use function Amp\call;

class Search extends HttpAPI implements SearchInterface
{
    /**
     * @inheritDoc
     */
    public function query(string $jql, array $params = []): Promise
    {
        $request = array_merge($params, compact('jql'));

        $this->logger->info("Search: send a search request with params: ", $request);

        return call(function () use ($request) {
            /** @var \Amp\Http\Client\Response $response */
            $response = yield $this->httpClient->request(
                $this->configuredRequest->makeRequest('/rest/api/2/search', 'POST', json_encode($request))
            );

            return $this->handleResponse($response);
        });
    }
}
