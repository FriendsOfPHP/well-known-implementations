<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Phalcon;

use Phalcon\Http\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
class PhalconPsr7Response implements ResponseInterface
{
    private $response;

    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        $this->response = (new ResponseFactory())->createResponse($code, $reasonPhrase);
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withStatus(...\func_get_args());

        return $clone;
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withProtocolVersion(...\func_get_args());

        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name): bool
    {
        return $this->response->hasHeader();
    }

    public function getHeader($name): array
    {
        return $this->response->getHeader(...\func_get_args());
    }

    public function getHeaderLine($name): string
    {
        return $this->response->getHeaderLine(...\func_get_args());
    }

    public function withHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withHeader(...\func_get_args());

        return $clone;
    }

    public function withAddedHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withAddedHeader(...\func_get_args());

        return $clone;
    }

    public function withoutHeader($name): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withoutHeader(...\func_get_args());

        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->response = $this->response->withBody(...\func_get_args());

        return $clone;
    }
}
