<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Laminas;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
class LaminasPsr7ServerRequest extends ServerRequest
{
    /**
     * @param UriInterface|string $uri
     */
    public function __construct(string $method, $uri, array $serverParams = [])
    {
        parent::__construct($serverParams, [], $uri, $method, 'php://temp');
    }
}
