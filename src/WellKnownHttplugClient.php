<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;

if (null !== $vendor = ConcreteImplementation::HTTPLUG_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}HttplugClient", WellKnownHttplugClient::class);
} else {
    throw new \LogicException('Cannot find any of the well-known HTTPlug implementations; please "composer require" one of them.');
}

if (false) {
    final class WellKnownHttplugClient implements HttpClient, HttpAsyncClient
    {
        public function __construct()
        {
        }
    }
}
