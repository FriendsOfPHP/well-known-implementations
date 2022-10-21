<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\HttplugCurl;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use Http\Client\Curl\Client;

/**
 * @internal
 */
class HttplugCurlHttplugClient extends Client
{
    public function __construct()
    {
        $factory = new WellKnownPsr17Factory();

        parent::__construct($factory, $factory);
    }
}
