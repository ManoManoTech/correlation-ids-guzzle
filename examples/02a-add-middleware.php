<?php

declare(strict_types=1);
require '01-generate-correlation-id-container.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;

// create the middleware
$correlationIdMiddleware = new CorrelationIdMiddleware($correlationIdContainer);

$stack = HandlerStack::create();
$stack->push(Middleware::mapRequest($correlationIdMiddleware));

$client = new Client(['handler' => $stack]);
