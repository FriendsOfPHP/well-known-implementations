<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr18Client;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Request;
use FriendsOfPHP\WellKnownImplementations\Internal\ConcreteImplementation;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

class WellKnownPsr18ClientTest extends TestCase
{
    protected function setUp(): void
    {
        if (null === ConcreteImplementation::PSR18_VENDOR) {
            $this->markTestSkipped('PSR-18 implementation required');
        }
    }

    public function testSendRequest()
    {
        $client = new WellKnownPsr18Client();

        $this->assertInstanceOf(ClientInterface::class, $client);

        $response = $client->sendRequest(new WellKnownPsr7Request('GET', 'http://example.com/'));

        $this->assertSame(200, $response->getStatusCode());
    }
}
