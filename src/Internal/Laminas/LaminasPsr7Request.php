<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Laminas;

use Laminas\Diactoros\Request;

/**
 * @internal
 */
class LaminasPsr7Request extends Request
{
    /**
     * @param UriInterface|string $uri
     */
    public function __construct(string $method, $uri)
    {
        parent::__construct($uri, $method);
    }
}
