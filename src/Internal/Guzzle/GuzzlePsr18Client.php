<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Guzzle;

use GuzzleHttp\Client;

class_alias(Client::class, GuzzlePsr18Client::class);

if (false) {
    /**
     * @internal
     */
    class GuzzlePsr18Client extends Client
    {
        public function __construct()
        {
        }
    }
}
