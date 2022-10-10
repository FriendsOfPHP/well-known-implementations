<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use FriendsOfPHP\WellKnownImplementations\Internal\Psr7StreamHelper;
use Slim\Psr7\Stream;

/**
 * @internal
 */
class SlimPsr7Stream extends Stream
{
    /**
     * @param string|resource $content
     */
    public function __construct($content = '', string $mode = null)
    {
        parent::__construct(Psr7StreamHelper::toResource($content, $mode));
    }
}
