<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Nyholm;

use FriendsOfPHP\WellKnownImplementations\Internal\Psr7StreamHelper;
use Nyholm\Psr7\Stream;

/**
 * @internal
 */
class NyholmPsr7Stream extends Stream
{
    /**
     * @param string|resource $content
     */
    public function __construct($content = '', string $mode = null)
    {
        if (!\is_resource($content)) {
            $content = Psr7StreamHelper::toResource($content, $mode);
        }

        static $sync;

        $sync = $sync ?? (\is_callable([parent::class, '__construct']) ? false : \Closure::bind(static function ($from, $to) {
            foreach ($from as $k => $v) {
                $to->$k = $v;
            }
        }, null, parent::class));

        if ($sync) {
            $stream = parent::create($content);
            $sync($stream, $this);
            $stream->detach();
        } else {
            parent::__construct($content);
        }
    }
}
