<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

if (null !== $vendor = ConcreteImplementation::PSR7_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}Psr7ServerRequest", WellKnownPsr7ServerRequest::class);
} else {
    throw new \LogicException('Supported PSR-7 implementation not found, try running "composer require nyholm/psr7".');
}

if (false) {
    final class WellKnownPsr7ServerRequest implements ServerRequestInterface
    {
        /**
         * @param UriInterface|string $uri
         */
        public function __construct(string $method, $uri, array $serverParams = [])
        {
        }
    }
}
