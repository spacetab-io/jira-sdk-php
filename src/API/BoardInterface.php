<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;
use Amp\Promise;

interface BoardInterface
{
    public function all(?string $name = null, ?string $type = null, int $maxResults = 100): Iterator;
    public function get(int $boardId, array $params = []): Promise;
    public function getBoardSprints(int $boardId, array $state = ['active'], int $maxResults = 100): Iterator;
}
