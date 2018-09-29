Guzzle Request Correlation
==========================

Injects request correlation headers in guzzle requests

Installation
------------

```bash
composer require manomano-tech/correlation-ids-guzzle
```

Usage
-----

First, generate a CorrelationIdContainer:

```php
use ManoManoTech\CorrelationId\Factory\CorrelationIdContainerFactory;
use ManoManoTech\CorrelationId\Generator\RamseyUuidGenerator;

// We specify which generator will be responsible for generating the
// identification of the current process
$generator = new RamseyUuidGenerator();

$factory = new CorrelationIdContainerFactory($generator);
$correlationIdContainer = $factory->create(
    '3fc044d9-90fa-4b50-b6d9-3423f567155f',
    '3b5263fa-1644-4750-8f11-aaf61e58cd9e'
);
```

Then, you have two options:

### Add Middleware to your Guzzle client
 
```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;

// create the middleware
$correlationIdMiddleware = new CorrelationIdMiddleware($correlationIdContainer);

$stack = HandlerStack::create();
$stack->push(Middleware::mapRequest($correlationIdMiddleware));

$client = new Client(['handler' => $stack]);
```

### Use the factory to create a guzzle client

```php
use ManoManoTech\CorrelationIdGuzzle\GuzzleClientFactory;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;

// create the middleware
$correlationIdMiddleware = new CorrelationIdMiddleware($correlationIdContainer);

$factory = new GuzzleClientFactory($correlationIdMiddleware);
// return an instance of GuzzleHttp\Client
$client = $factory->create();
```

Customizing header names
------------------------

By default, request headers will look something like this:

```http
GET / HTTP/1.1
Host: example.com
parent-correlation-id: 3fc044d9-90fa-4b50-b6d9-3423f567155f
root-correlation-id: 3b5263fa-1644-4750-8f11-aaf61e58cd9e
```

You can change this by providing a second argument to the constructor:

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use ManoManoTech\CorrelationId\CorrelationEntryName;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;

$correlationHeaderName = new CorrelationEntryName(
    'current-id', // not used in this context
    'parent-id',
    'root-id'
);
$correlationIdMiddleware = new CorrelationIdMiddleware(
    $correlationIdContainer,
    $correlationHeaderName
);

$stack = HandlerStack::create();
$stack->push(Middleware::mapRequest($correlationIdMiddleware));

$client = new Client(['handler' => $stack]);
```

will produce:

```http
GET / HTTP/1.1
Host: example.com
parent-id: 3fc044d9-90fa-4b50-b6d9-3423f567155f
root-id: 3b5263fa-1644-4750-8f11-aaf61e58cd9e
```
