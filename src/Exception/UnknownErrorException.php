<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\Exception;

class UnknownErrorException extends SdkErrorException
{
    public static function unknownError(): self
    {
        return new self('Unknown error occurred.');
    }
}
