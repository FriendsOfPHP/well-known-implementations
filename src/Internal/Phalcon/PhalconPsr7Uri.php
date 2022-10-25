<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Phalcon;

use Phalcon\Http\Message\Uri;

class_alias(Uri::class, PhalconPsr7Uri::class);

if (false) {
    /**
     * @internal
     */
    class PhalconPsr7Uri extends Uri
    {
        public function __construct(string $uri = '')
        {
        }
    }
}
