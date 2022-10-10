<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use Slim\Psr7\Response;

/**
 * @internal
 */
class SlimPsr7Response extends Response
{
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        parent::__construct($code);

        if ('' !== $reasonPhrase) {
            static $sync;

            $sync = $sync ?? \Closure::bind(static function ($response, $reasonPhrase) {
                $response->reasonPhrase = $response->filterReasonPhrase($reasonPhrase);
            }, null, parent::class);

            $sync($this, $reasonPhrase);
        }
    }
}
