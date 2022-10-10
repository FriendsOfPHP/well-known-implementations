<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7ServerRequest;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class WellKnownPsr7ServerRequestTest extends TestCase
{
    public function testServerRequest()
    {
        $request = new WellKnownPsr7ServerRequest('GET', '/foo');

        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testServerRequestUri()
    {
        $request = new WellKnownPsr7ServerRequest('GET', new WellKnownPsr7Uri('/foo'));

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testServerParam()
    {
        $request = new WellKnownPsr7ServerRequest('POST', '/foo', ['FOO' => 'bar']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
        $this->assertSame(['FOO' => 'bar'], $request->getServerParams());
    }
}
