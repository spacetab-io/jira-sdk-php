<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira;

use Amp\Http\Client\Request as AmpRequest;

interface Request
{
    public function makeRequest(string $uri, string $method = 'GET', ?string $body = null): AmpRequest;
}
