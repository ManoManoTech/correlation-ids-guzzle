<?php

declare(strict_types=1);

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
