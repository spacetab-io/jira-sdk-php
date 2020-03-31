<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\Exception;

class UnknownErrorException extends SdkErrorException
{
    public static function unknownError()
    {
        return new self('Unknown error occurred.');
    }
}
