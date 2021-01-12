<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira\Exception;

final class Configurator extends Main
{
    public static function forgotCallMethod(): self
    {
        return new self('Do you not forgot call `configurate` method to configure SDK?');
    }
}
