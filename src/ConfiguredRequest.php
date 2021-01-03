<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Amp\Http\Client\Request;

class ConfiguredRequest
{
    private string $baseUri;

    /**
     * ConfiguredRequest constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param string|null $body
     * @return Request
     */
    public function makeRequest(string $uri, string $method = 'GET', ?string $body = null): Request
    {
        return new Request($this->baseUri . $uri, $method, $body);
    }
}
