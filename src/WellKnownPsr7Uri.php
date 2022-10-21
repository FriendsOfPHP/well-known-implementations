<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Psr\Http\Message\UriInterface;

if (null !== $vendor = ConcreteImplementation::PSR7_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}Psr7Uri", WellKnownPsr7Uri::class);
} else {
    throw new \LogicException('Cannot find any of the well-known PSR-7 implementations; please "composer require" one of them.');
}

if (false) {
    final class WellKnownPsr7Uri implements UriInterface
    {
        public function __construct(string $uri = '')
        {
        }
    }
}
