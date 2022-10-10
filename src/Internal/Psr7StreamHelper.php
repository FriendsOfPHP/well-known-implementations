<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal;

/**
 * @internal
 */
class Psr7StreamHelper
{
    /**
     * @param string|resource $content
     *
     * @return resource
     */
    public static function toResource($content, ?string $mode)
    {
        if (\is_resource($content)) {
            return $content;
        }

        if (null === $mode) {
            $h = fopen('php://temp', 'r+');
            fwrite($h, $content);
            rewind($h);

            return $h;
        }

        if ('' === $content) {
            throw new \RuntimeException('Path cannot be empty');
        }

        if (false === $h = @fopen($content, $mode)) {
            if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], true)) {
                throw new \InvalidArgumentException(sprintf('The mode "%s" is invalid.', $mode));
            }

            throw new \RuntimeException(sprintf('The file "%s" cannot be opened: %s', $content, error_get_last()['message'] ?? ''));
        }

        return $h;
    }
}
