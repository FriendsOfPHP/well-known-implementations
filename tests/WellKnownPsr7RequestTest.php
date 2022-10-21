<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Request;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class WellKnownPsr7RequestTest extends TestCase
{
    public function testRequest()
    {
        $request = new WellKnownPsr7Request('GET', '/foo');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }

    public function testRequestUri()
    {
        $request = new WellKnownPsr7Request('GET', new WellKnownPsr7Uri('/foo'));

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/foo', (string) $request->getUri());
    }
}
