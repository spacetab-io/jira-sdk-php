<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Exception;

use JsonException;

final class Response extends Main
{
    public static function invalidBodyFormat(JsonException $e): self
    {
        return new self("Invalid response format: {$e->getMessage()}", 0, $e);
    }

    public static function badRequest(): self
    {
        return new self('Bad request.');
    }

    public static function unauthorized(): self
    {
        return new self('Unauthorized. Wrong credentials?');
    }

    public static function requestFailed(): self
    {
        return new self('MakeRequest failed.');
    }

    public static function forbidden(): self
    {
        return new self('Forbidden.');
    }

    public static function notFound(): self
    {
        return new self('Not found.');
    }

    public static function payloadTooLarge(): self
    {
        return new self('Payload too large.');
    }

    public static function serverError(): self
    {
        return new self('Server error.');
    }

    public static function unknownError(): self
    {
        return new self('Unknown error occurred.');
    }
}
