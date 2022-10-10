<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\PhpHttpReact;

use Http\Adapter\React\Client;

class_alias(Client::class, PhpHttpReactHttplugClient::class);

if (false) {
    /**
     * @internal
     */
    class PhpHttpReactHttplugClient extends Client
    {
        public function __construct()
        {
        }
    }
}
