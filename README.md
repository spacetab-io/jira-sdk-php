Asynchronous PHP Jira SDK
-------------------------

Note: Until 1.0.0 version SDK is unstable and can be rewritten completely.

## Install

```bash
composer require spacetab-io/jira-sdk
```

## Usage example

### Simple methods which returns a promise

```php
use Amp\Loop;
use Psr\Log\LogLevel;
use Spacetab\Logger\Logger;
use Spacetab\SDK\Jira\Cache;
use Spacetab\SDK\Jira\Client;
use Spacetab\SDK\Jira\Configurator;
use Spacetab\SDK\Jira\Exception;

Loop::run(function () {
    $logger = Logger::default('Client', LogLevel::DEBUG);

    $configurator = Configurator::fromBasicAuth('https://jira.server.com', 'username', 'jiraTokenStringOrPassword');
    $configurator->setLogger($logger);
    $configurator->setCache(Cache::enabled());
    $configurator->configurate();

    $jira = new Client($configurator);

    try {
        $issue = yield $jira->issue()->get('KEY-1');
    } catch (Exception\Main $e) {
        //$e->getMessage();
        //$e->getErrorMessages();
    }

    dump($issue);
});
```

### Methods with pagination which returns an iterator 

```php
use Amp\Loop;
use Spacetab\SDK\Jira\Client;
use Spacetab\SDK\Jira\Configurator;

Loop::run(function () {
    $configurator = Configurator::fromBasicAuth('https://jira.server.com', 'username', 'jiraTokenStringOrPassword');
    $configurator->configurate();

    $jira = new Client($configurator);
    $iterator = $jira->search()->query('project = KEY', ['summary'], 20);

    $results = [];
    while (yield $iterator->advance()) {
        $results[] = $iterator->getCurrent();
    }

    dump($results);
});
```

## Supported methods

Jira REST API Docs: https://docs.atlassian.com/software/jira/docs/api/REST/8.5.4/ 
and https://docs.atlassian.com/jira-software/REST/7.0.4

* Get issue
* Get worklog by issue
* JQL Search (with pagination support)
* Load worklogs by JQL request
* Get all projects
* Get one project
* Get all boards (agile)
* Get one board (agile)
* Get board sprints (agile)
* Get one sprint (agile)

## License

The MIT License

Copyright © 2021 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
