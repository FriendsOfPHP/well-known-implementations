<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use Slim\Psr7\Uri;

/**
 * @internal
 */
class SlimPsr7Uri extends Uri
{
    public function __construct(string $uri = '')
    {
        if (false === $uri = parse_url($uri)) {
            throw new \InvalidArgumentException('URI cannot be parsed');
        }

        parent::__construct($uri['scheme'] ?? '', $uri['host'] ?? '', $uri['port'] ?? null, $uri['path'] ?? '', $uri['query'] ?? '', $uri['fragment'] ?? '', $uri['user'] ?? '', $uri['pass'] ?? '');
    }
}
