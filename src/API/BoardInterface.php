<?php

declare(strict_types=1);

namespace Spacetab\JiraSDK\API;

use Amp\Iterator;
use Amp\Promise;

interface BoardInterface
{
    /**
     * @param string|null $name
     * @param string|null $type
     * @param int $maxResults
     * @return \Amp\Iterator
     */
    public function all(?string $name = null, ?string $type = null, int $maxResults = 100): Iterator;

    /**
     * @param int $boardId
     * @param array $params
     * @return \Amp\Promise
     */
    public function get(int $boardId, array $params = []): Promise;

    /**
     * @param int $boardId
     * @param array|string[] $state
     * @param int $maxResults
     * @return \Amp\Iterator
     */
    public function getBoardSprints(int $boardId, array $state = ['active'], int $maxResults = 100): Iterator;
}
