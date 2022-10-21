<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Psr\Http\Client\ClientInterface;

if (null !== $vendor = ConcreteImplementation::PSR18_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}Psr18Client", WellKnownPsr18Client::class);
} else {
    throw new \LogicException('Cannot find any of the well-known PSR-18 implementations; please "composer require" one of them.');
}

if (false) {
    final class WellKnownPsr18Client implements ClientInterface
    {
        public function __construct()
        {
        }
    }
}
