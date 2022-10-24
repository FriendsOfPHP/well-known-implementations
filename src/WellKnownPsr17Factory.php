<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use FriendsOfPHP\WellKnownImplementations\Internal\Psr7Helper;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

final class WellKnownPsr17Factory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{
    /**
     * @param UriInterface|string $uri
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new WellKnownPsr7Request(...\func_get_args());
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new WellKnownPsr7Response(...\func_get_args());
    }

    /**
     * @param UriInterface|string $uri
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new WellKnownPsr7ServerRequest(...\func_get_args());
    }

    public function createServerRequestFromGlobals(array $server = null, array $get = null, array $post = null, array $cookie = null, array $files = null, StreamInterface $body = null): ServerRequestInterface
    {
        $server = $server ?? $_SERVER;
        $request = new WellKnownPsr7ServerRequest($server['REQUEST_METHOD'] ?? 'GET', $this->createUriFromGlobals($server), $server);

        return Psr7Helper::buildServerRequestFromGlobals($request, $server, $files ?? $_FILES)
            ->withQueryParams($get ?? $_GET)
            ->withParsedBody($post ?? $_POST)
            ->withCookieParams($cookie ?? $_COOKIE)
            ->withBody($body ?? new WellKnownPsr7Stream('php://input', 'r+'));
        ;
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return new WellKnownPsr7Stream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new WellKnownPsr7Stream($filename, $mode);
    }

    /**
     * @param resource $resource
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new WellKnownPsr7Stream($resource);
    }

    public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface
    {
        return new WellKnownPsr7UploadedFile(...\func_get_args());
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return new WellKnownPsr7Uri(...\func_get_args());
    }

    public function createUriFromGlobals(array $server = null): UriInterface
    {
        return Psr7Helper::buildUriFromGlobals(new WellKnownPsr7Uri(''), $server ?? $_SERVER);
    }
}
