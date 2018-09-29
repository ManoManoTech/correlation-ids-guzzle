<?php

declare(strict_types=1);
require '01-generate-correlation-id-container.php';

use ManoManoTech\CorrelationIdGuzzle\GuzzleClientFactory;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;

// create the middleware
$correlationIdMiddleware = new CorrelationIdMiddleware($correlationIdContainer);

$factory = new GuzzleClientFactory($correlationIdMiddleware);
// return an instance of GuzzleHttp\Client
$client = $factory->create();
