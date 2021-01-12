<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira;

use Amp\Http\Client\DelegateHttpClient;

interface InterceptBuilder
{
    public function build(): DelegateHttpClient;
}
