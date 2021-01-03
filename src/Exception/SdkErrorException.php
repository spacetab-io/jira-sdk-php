<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\Exception;

use Exception;

class SdkErrorException extends Exception
{
    /**
     * @var array<string>
     */
    private array $errorMessages = [];

    public function setErrorMessage(string $message): void
    {
        $this->errorMessages[] = $message;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
