<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Nyholm;

use Nyholm\Psr7\Request;

class_alias(Request::class, NyholmPsr7Request::class);

if (false) {
    /**
     * @internal
     */
    class NyholmPsr7Request extends Request
    {
        /**
         * @param UriInterface|string $uri
         */
        public function __construct(string $method, $uri)
        {
        }
    }
}
