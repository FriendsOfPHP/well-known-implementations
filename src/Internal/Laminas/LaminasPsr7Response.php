<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Laminas;

use Laminas\Diactoros\Response;

/**
 * @internal
 */
class LaminasPsr7Response extends Response
{
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        parent::__construct('php://memory', $code);

        if ('' !== $reasonPhrase) {
            static $sync;

            $sync = $sync ?? \Closure::bind(static function ($response, $code, $reasonPhrase) {
                $response->setStatusCode($code, $reasonPhrase);
            }, null, parent::class);

            $sync($this, $code, $reasonPhrase);
        }
    }
}
