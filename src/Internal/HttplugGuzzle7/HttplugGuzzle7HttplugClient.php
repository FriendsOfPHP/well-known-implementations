<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\HttplugGuzzle7;

use Http\Adapter\Guzzle7\Client;

class_alias(Client::class, HttplugGuzzle7HttplugClient::class);

if (false) {
    /**
     * @internal
     */
    class HttplugGuzzle7HttplugClient extends Client
    {
        public function __construct()
        {
        }
    }
}
