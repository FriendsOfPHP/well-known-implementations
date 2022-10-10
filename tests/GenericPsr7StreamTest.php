<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class WellKnownPsr7StreamTest extends TestCase
{
    public function testStreamString()
    {
        $stream = new WellKnownPsr7Stream('Hello');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('Hello', (string) $stream);
    }

    public function testStreamFile()
    {
        $stream = new WellKnownPsr7Stream(__FILE__, 'r');

        $this->assertStringEqualsFile(__FILE__, (string) $stream);
    }

    public function testStreamResource()
    {
        $stream = new WellKnownPsr7Stream(fopen(__FILE__, 'r'));

        $this->assertStringEqualsFile(__FILE__, (string) $stream);
    }
}
