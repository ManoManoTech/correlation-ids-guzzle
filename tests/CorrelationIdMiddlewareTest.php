<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdGuzzle\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use ManoManoTech\CorrelationId\CorrelationEntryNameInterface;
use ManoManoTech\CorrelationId\CorrelationIdContainerInterface;
use ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/** @covers \ManoManoTech\CorrelationIdGuzzle\CorrelationIdMiddleware */
final class CorrelationIdMiddlewareTest extends TestCase
{
    /** @dataProvider provider */
    public function testAddRequestIdentifier(
        string $currentRequestId,
        ?string $parentRequestId,
        ?string $rootRequestId,
        ?string $expectedParentHeaderValue,
        ?string $expectedRootHeaderValue
    ): void {
        $rootHeaderName = 'root';
        $parentHeaderName = 'parent';
        $correlationIdContainer = $this->createMock(CorrelationIdContainerInterface::class);
        $correlationIdContainer->expects(self::any())
                               ->method('current')
                               ->willReturn($currentRequestId);
        $correlationIdContainer->expects(self::any())
                               ->method('parent')
                               ->willReturn($parentRequestId);
        $correlationIdContainer->expects(self::any())
                               ->method('root')
                               ->willReturn($rootRequestId);

        $correlationEntryName = $this->createMock(CorrelationEntryNameInterface::class);
        $correlationEntryName->expects(self::once())
                             ->method('parent')
                             ->willReturn($parentHeaderName);
        $correlationEntryName->expects(self::once())
                             ->method('root')
                             ->willReturn($rootHeaderName);

        $handler = new MockHandler(
            [
                function (RequestInterface $request) use (
                    $rootHeaderName,
                    $parentHeaderName,
                    $expectedRootHeaderValue,
                    $expectedParentHeaderValue
                ) {
                    if (null !== $expectedRootHeaderValue) {
                        static::assertSame($expectedRootHeaderValue, $request->getHeaderLine($rootHeaderName));
                    } else {
                        static::assertFalse($request->hasHeader($rootHeaderName));
                    }
                    if (null !== $expectedParentHeaderValue) {
                        static::assertSame($expectedParentHeaderValue, $request->getHeaderLine($parentHeaderName));
                    } else {
                        static::assertFalse($request->hasHeader($parentHeaderName));
                    }

                    return new Response(200);
                },
            ]
        );

        $middleware = new CorrelationIdMiddleware($correlationIdContainer, $correlationEntryName);
        $stack = new HandlerStack($handler);
        $stack->push(Middleware::mapRequest($middleware));
        $comp = $stack->resolve();
        $promise = $comp(new Request('GET', 'http://www.google.com'), []);
        static::assertInstanceOf(PromiseInterface::class, $promise);
        $response = $promise->wait();
        static::assertSame(200, $response->getStatusCode());
    }

    public function provider(): array
    {
        return [
            'When the current process has no parent nor root correlation id, it should send the current correlation id as root and parent' => [
                'current_request_id',
                null,
                null,
                'current_request_id',
                'current_request_id',
            ],
            'When the current process has a parent but no root correlation id, it should send the parent correlation id as root and the current correlation id as parent' => [
                'current_request_id',
                'parent_request_id',
                null,
                'current_request_id',
                'parent_request_id',
            ],
            'When the current process has a root but no parent correlation id, it should send the root correlation id as root and the current correlation id as parent' => [
                'current_request_id',
                null,
                'root_request_id',
                'current_request_id',
                'root_request_id',
            ],
            'When the current process has both a root and a parent correlation id, it should send the root correlation id as root and the current correlation id as parent' => [
                'current_request_id',
                'parent_request_id',
                'root_request_id',
                'current_request_id',
                'root_request_id',
            ],
        ];
    }
}
