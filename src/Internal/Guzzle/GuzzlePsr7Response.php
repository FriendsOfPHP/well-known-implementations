<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Guzzle;

use GuzzleHttp\Psr7\Response;

/**
 * @internal
 */
class GuzzlePsr7Response extends Response
{
    public function __construct(int $code = 200, string $reasonPhrase = '')
    {
        parent::__construct($code, [], null, '1.1', 2 > \func_num_args() ? null : $reasonPhrase);
    }
}
