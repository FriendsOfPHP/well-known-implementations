<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownHttplugClient;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Request;
use FriendsOfPHP\WellKnownImplementations\Internal\ConcreteImplementation;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;

class WellKnownHttplugClientTest extends TestCase
{
    protected function setUp(): void
    {
        if (null === ConcreteImplementation::HTTPLUG_VENDOR) {
            $this->markTestSkipped('Httplug implementation required');
        }
    }

    public function testSendRequest()
    {
        $client = new WellKnownHttplugClient();

        $this->assertInstanceOf(HttpClient::class, $client);
        $this->assertInstanceOf(HttpAsyncClient::class, $client);

        $response = $client->sendRequest(new WellKnownPsr7Request('GET', 'http://example.com/'));

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testSendAsyncRequest()
    {
        $client = new WellKnownHttplugClient();

        $response = $client->sendAsyncRequest(new WellKnownPsr7Request('GET', 'http://example.com/'));

        $this->assertSame(200, $response->wait()->getStatusCode());
    }
}
