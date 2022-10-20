<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Symfony;

use Symfony\Component\HttpClient\Psr18Client;

/**
 * @internal
 */
class_alias(Psr18Client::class, SymfonyPsr18Client::class);

if (false) {
    /**
     * @internal
     */
    class SymfonyPsr18Client extends Psr18Client
    {
        public function __construct()
        {
        }
    }
}
