<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Laminas;

use FriendsOfPHP\WellKnownImplementations\Internal\Psr7Helper;
use Laminas\Diactoros\Stream;

/**
 * @internal
 */
class LaminasPsr7Stream extends Stream
{
    /**
     * @param string|resource $content
     */
    public function __construct($content = '', string $mode = null)
    {
        if (null === $mode && \is_string($content)) {
            $content = Psr7Helper::toResource($content, $mode);
        }

        parent::__construct($content, $mode ?? 'r');
    }
}
