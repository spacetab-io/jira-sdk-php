<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK;

use Amp\Http\Client\Request;
use League\Uri\Uri;

class ConfiguredRequest
{
    /**
     * @var \Psr\Http\Message\UriInterface|Uri
     */
    private $baseUri = null;

    /**
     * ConfiguredRequest constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = Uri::createFromString($baseUri);
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param string $method
     * @param string|null $body
     * @return \Amp\Http\Client\Request
     */
    public function makeRequest($uri, string $method = 'GET', ?string $body = null)
    {
        if (is_string($uri)) {
            $uri = Uri::createFromString($uri);
        }

        $total = $this->baseUri
            ->withPath($uri->getPath())
            ->withQuery($uri->getQuery());

        return new Request($total, $method, $body);
    }
}
