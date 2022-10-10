<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Laminas;

use Laminas\Diactoros\Uri;

class_alias(Uri::class, LaminasPsr7Uri::class);

if (false) {
    /**
     * @internal
     */
    class LaminasPsr7Uri extends Uri
    {
        public function __construct(string $uri = '')
        {
        }
    }
}
