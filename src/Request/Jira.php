<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Request;

use Amp\Http\Client\Request as AmpRequest;
use Spacetab\SDK\Jira\Request;

final class Jira implements Request
{
    private string $baseUri;

    /**
     * Jira constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = rtrim($baseUri, '/');
    }

    public function makeRequest(string $uri, string $method = 'GET', ?string $body = null): AmpRequest
    {
        return new AmpRequest($this->baseUri . $uri, $method, $body);
    }
}
