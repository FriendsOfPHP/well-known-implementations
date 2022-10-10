<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Symfony;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\Psr18Client;

/**
 * @internal
 */
class SymfonyPsr18Client implements ClientInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new Psr18Client(null, new WellKnownPsr17Factory());
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
