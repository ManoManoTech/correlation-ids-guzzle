<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdGuzzle;

use ManoManoTech\CorrelationId\CorrelationEntryName;
use ManoManoTech\CorrelationId\CorrelationEntryNameInterface;
use ManoManoTech\CorrelationId\CorrelationIdContainerInterface;
use Psr\Http\Message\RequestInterface;

final class CorrelationIdMiddleware implements MiddlewareInterface
{
    /** @var CorrelationEntryNameInterface */
    private $CorrelationEntryName;
    /** @var CorrelationIdContainerInterface */
    private $correlationIdContainer;

    public function __construct(
        CorrelationIdContainerInterface $correlationIdContainer,
        CorrelationEntryNameInterface $CorrelationEntryName = null
    ) {
        $this->correlationIdContainer = $correlationIdContainer;
        $this->CorrelationEntryName = $CorrelationEntryName ?? CorrelationEntryName::suffixed();
    }

    public function __invoke(RequestInterface $request, array $options = []): RequestInterface
    {
        $headers = [
            $this->CorrelationEntryName->parent() => $this->correlationIdContainer->current(),
            $this->CorrelationEntryName->root() => $this->selectBestRootHeaderValue(),
        ];

        foreach ($headers as $headerName => $headerValue) {
            $request = $request->withHeader($headerName, $headerValue);
        }

        return $request;
    }

    private function selectBestRootHeaderValue(): string
    {
        // Best value is, in the following order:
        // - root value if set or else
        // - parent value if set or else
        // - current value
        $rootPossibleValues = array_filter(
            [
                $this->correlationIdContainer->root(),
                $this->correlationIdContainer->parent(),
                $this->correlationIdContainer->current(),
            ]
        );

        return array_shift($rootPossibleValues) ?? '';
    }
}
