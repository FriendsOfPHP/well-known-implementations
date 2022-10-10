<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Guzzle;

use GuzzleHttp\Psr7\Uri;

class_alias(Uri::class, GuzzlePsr7Uri::class);

if (false) {
    /**
     * @internal
     */
    class GuzzlePsr7Uri extends Uri
    {
        public function __construct(string $uri = '')
        {
        }
    }
}
