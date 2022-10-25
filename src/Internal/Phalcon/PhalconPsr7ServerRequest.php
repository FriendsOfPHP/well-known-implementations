<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Phalcon;

use Phalcon\Http\Message\ServerRequest;
use Psr\Http\Message\UriInterface;

class_alias(ServerRequest::class, PhalconPsr7ServerRequest::class);

if (false) {
    /**
     * @internal
     */
    class PhalconPsr7ServerRequest extends ServerRequest
    {
        /**
         * @param UriInterface|string $uri
         */
        public function __construct(string $method, $uri, array $serverParams = [])
        {
        }
    }
}
