<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use Psr\Http\Message\UriInterface;
use Slim\Psr7\Cookies;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;

/**
 * @internal
 */
class SlimPsr7ServerRequest extends Request
{
    /**
     * @param UriInterface|string $uri
     */
    public function __construct(string $method, $uri, array $serverParams = [])
    {
        if (\is_string($uri)) {
            $uri = new SlimPsr7Uri($uri);
        }

        if ($serverParams) {
            $headers = Headers::createFromGlobals();
            $cookies = Cookies::parseHeader($headers->getHeader('Cookie', []));
        } else {
            $headers = new Headers();
            $cookies = [];
        }

        parent::__construct($method, $uri, $headers, $cookies, $serverParams, new SlimPsr7Stream());
    }
}
