<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdGuzzle\Tests;

use GuzzleHttp\HandlerStack;
use ManoManoTech\CorrelationIdGuzzle\GuzzleClientFactory;
use ManoManoTech\CorrelationIdGuzzle\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

/** @covers \ManoManoTech\CorrelationIdGuzzle\GuzzleClientFactory */
final class GuzzleClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $middlewareMocked = $this->createMock(MiddlewareInterface::class);
        $object = new GuzzleClientFactory($middlewareMocked);
        $result = $object->create();
        static::assertArrayHasKey('handler', $result->getConfig());
    }

    public function testCreateWithoutHandler(): void
    {
        $middlewareMocked = $this->createMock(MiddlewareInterface::class);
        $object = new GuzzleClientFactory($middlewareMocked);
        $result = $object->create(['handler' => HandlerStack::create()]);
        static::assertArrayHasKey('handler', $result->getConfig());
    }
}
