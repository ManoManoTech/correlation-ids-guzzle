<?php

declare(strict_types=1);

namespace ManoManoTech\CorrelationIdGuzzle;

use Psr\Http\Message\RequestInterface;

interface MiddlewareInterface
{
    /** @param array $options */
    public function __invoke(RequestInterface $request, array $options = []): RequestInterface;
}
