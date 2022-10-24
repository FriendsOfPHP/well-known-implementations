<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\HttplugMock;

use Http\Mock\Client;

class_alias(Client::class, HttplugMockHttplugClient::class);

if (false) {
    /**
     * @internal
     */
    class HttplugMockHttplugClient extends Client
    {
        public function __construct()
        {
        }
    }
}
