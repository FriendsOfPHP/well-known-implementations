<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class WellKnownPsr7UriTest extends TestCase
{
    public function testUri()
    {
        $uri = new WellKnownPsr7Uri('/hello');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame('/hello', (string) $uri);
    }

    public function testUriEmpty()
    {
        $uri = new WellKnownPsr7Uri();

        $this->assertSame('', $uri->getPath());
    }
}
