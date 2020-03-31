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

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
