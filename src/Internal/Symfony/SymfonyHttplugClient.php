<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Symfony;

use Symfony\Component\HttpClient\HttplugClient;

/**
 * @internal
 */
class_alias(HttplugClient::class, SymfonyHttplugClient::class);

if (false) {
    /**
     * @internal
     */
    class SymfonyHttplugClient extends HttplugClient
    {
        public function __construct()
        {
        }
    }
}
