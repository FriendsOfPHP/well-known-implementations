<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Nyholm;

use Nyholm\Psr7\Uri;

class_alias(Uri::class, NyholmPsr7Uri::class);

if (false) {
    /**
     * @internal
     */
    class NyholmPsr7Uri extends Uri
    {
        public function __construct(string $uri = '')
        {
        }
    }
}
