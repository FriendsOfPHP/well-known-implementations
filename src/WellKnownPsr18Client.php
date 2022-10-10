<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use FriendsOfPHP\WellKnownImplementations\Internal\ConcreteImplementation;
use Psr\Http\Client\ClientInterface;

if (null !== $vendor = ConcreteImplementation::PSR18_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}Psr18Client", WellKnownPsr18Client::class);
} else {
    throw new \LogicException('Supported PSR-18 implementation not found, try running "composer require symfony/http-client".');
}

if (false) {
    final class WellKnownPsr18Client implements ClientInterface
    {
        public function __construct()
        {
        }
    }
}
