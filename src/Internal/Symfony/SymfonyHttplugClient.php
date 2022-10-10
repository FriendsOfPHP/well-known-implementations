<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Symfony;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttplugClient;

/**
 * @internal
 */
class SymfonyHttplugClient implements HttpClient, HttpAsyncClient
{
    private $client;

    public function __construct()
    {
        $this->client = new HttplugClient(null, new WellKnownPsr17Factory());
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }

    public function sendAsyncRequest(RequestInterface $request): Promise
    {
        return $this->client->sendAsyncRequest($request);
    }
}
