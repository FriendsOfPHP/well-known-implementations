<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\PhpHttpGuzzle6;

use Http\Adapter\Guzzle6\Client;

class_alias(Client::class, PhpHttpGuzzle6HttplugClient::class);

if (false) {
    /**
     * @internal
     */
    class PhpHttpGuzzle6HttplugClient extends Client
    {
        public function __construct()
        {
        }
    }
}
