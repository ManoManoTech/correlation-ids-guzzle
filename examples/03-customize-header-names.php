<?php

declare(strict_types=1);
require '01-generate-correlation-id-container.php';

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
