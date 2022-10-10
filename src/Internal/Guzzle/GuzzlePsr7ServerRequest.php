<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Guzzle;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
class GuzzlePsr7ServerRequest extends ServerRequest
{
    /**
     * @param UriInterface|string $uri
     */
    public function __construct(string $method, $uri, array $serverParams = [])
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }

        parent::__construct($method, $uri, [], null, '1.1', $serverParams);
    }
}
