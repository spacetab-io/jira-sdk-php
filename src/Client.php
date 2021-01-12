<?php

declare(strict_types=1);

namespace Spacetab\SDK\Jira;

use Spacetab\SDK\Jira\Resource\Board;
use Spacetab\SDK\Jira\Resource\Issue;
use Spacetab\SDK\Jira\Resource\Project;
use Spacetab\SDK\Jira\Resource\Search;
use Spacetab\SDK\Jira\Resource\Sprint;

final class Client
{
    public const VERSION = '1.0.0-beta1';

    private Configurator $configurator;

    /**
     * Client constructor.
     *
     * @param Configurator $configurator
     */
    public function __construct(Configurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function issue(): Issue
    {
        return new Issue($this->configurator);
    }

    public function search(): Search
    {
        return new Search($this->configurator);
    }

    public function project(): Project
    {
        return new Project($this->configurator);
    }

    public function board(): Board
    {
        return new Board($this->configurator);
    }

    public function sprint(): Sprint
    {
        return new Sprint($this->configurator);
    }
}
