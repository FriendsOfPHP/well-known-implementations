<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use Slim\Psr7\Headers;
use Slim\Psr7\Request;

/**
 * @internal
 */
class SlimPsr7Request extends Request
{
    /**
     * @param UriInterface|string $uri
     */
    public function __construct(string $method, $uri)
    {
        parent::__construct($method, \is_string($uri) ? new SlimPsr7Uri($uri) : $uri, new Headers(), [], [], new SlimPsr7Stream());
    }
}
