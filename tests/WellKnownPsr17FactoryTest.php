<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Stream;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class WellKnownPsr17FactoryTest extends TestCase
{
    public function testRequest()
    {
        $request = (new WellKnownPsr17Factory())->createRequest('GET', '/foo');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testRequestUri()
    {
        $request = (new WellKnownPsr17Factory())->createRequest('GET', new WellKnownPsr7Uri('/foo'));

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testResponse()
    {
        $response = (new WellKnownPsr17Factory())->createResponse(202);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(202, $response->getStatusCode());
    }

    public function testReasonPhrase()
    {
        $response = (new WellKnownPsr17Factory())->createResponse(202, 'Hello');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame('Hello', $response->getReasonPhrase());
    }

    public function testServerRequest()
    {
        $request = (new WellKnownPsr17Factory())->createServerRequest('GET', '/foo');

        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testServerRequestUri()
    {
        $request = (new WellKnownPsr17Factory())->createServerRequest('GET', new WellKnownPsr7Uri('/foo'));

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testServerParam()
    {
        $request = (new WellKnownPsr17Factory())->createServerRequest('POST', '/foo', ['FOO' => 'bar']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
        $this->assertSame(['FOO' => 'bar'], $request->getServerParams());
    }

    public function testStreamString()
    {
        $stream = (new WellKnownPsr17Factory())->createStream('Hello');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('Hello', (string) $stream);
    }

    public function testStreamFile()
    {
        $stream = (new WellKnownPsr17Factory())->createStreamFromFile(__FILE__, 'r');

        $this->assertStringEqualsFile(__FILE__, (string) $stream);
    }

    public function testStreamResource()
    {
        $stream = (new WellKnownPsr17Factory())->createStreamFromResource(fopen(__FILE__, 'r'));

        $this->assertStringEqualsFile(__FILE__, (string) $stream);
    }

    public function testUploadedFile()
    {
        $file = (new WellKnownPsr17Factory())->createUploadedFile(new WellKnownPsr7Stream('Hello'), null, \UPLOAD_ERR_PARTIAL, 'client.name', 'client/type');

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame(5, $file->getSize());
        $this->assertSame(\UPLOAD_ERR_PARTIAL, $file->getError());
        $this->assertSame('client.name', $file->getClientFilename());
        $this->assertSame('client/type', $file->getClientMediaType());
    }

    public function testUri()
    {
        $uri = (new WellKnownPsr17Factory())->createUri('/hello');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('/hello', (string) $uri);
    }

    public function testUriEmpty()
    {
        $uri = (new WellKnownPsr17Factory())->createUri();

        $this->assertSame('', $uri->getPath());
    }
}
