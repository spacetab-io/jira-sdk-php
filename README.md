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
use Spacetab\JiraSDK\JiraSDK;
use Spacetab\JiraSDK\Exception\SdkErrorException;

Loop::run(function () {
    $jira = JiraSDK::new('https://jira.server.com', 'username', 'jiraTokenStringOrPassword');

    try {
        $result = yield $jira->issues()->get('KEY-1');
    } catch (SdkErrorException $e) {
        dump($e->getMessage(), $e->getErrorMessages());
    }

    dump($result);
});
```

### Methods with pagination which returns an iterator 

```php
use Amp\Loop;
use Spacetab\JiraSDK\JiraSDK;

Loop::run(function () {
    $jira = JiraSDK::new('https://jira.server.com', 'username', 'jiraTokenStringOrPassword');

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

* Get issue
* Get worklog by issue
* JQL Search (with pagination support)
* Load worklogs by JQL request
* Get all projects
* Get one project

## License

The MIT License

Copyright © 2020 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
