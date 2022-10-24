<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Stream;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Helps provide portability accross PSR-7 implementations.
 *
 * The code in this class is inspired by the "nyholm/psr7", "guzzlehttp/psr7"
 * and "symfony/http-foundation" packages, all licenced under MIT.
 *
 * Copyright (c) 2015 Michael Dowling <mtdowling@gmail.com>
 * Copyright (c) 2015 Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * Copyright (c) 2015 Graham Campbell <hello@gjcampbell.co.uk>
 * Copyright (c) 2016 Tobias Schultze <webmaster@tubo-world.de>
 * Copyright (c) 2016 George Mponos <gmponos@gmail.com>
 * Copyright (c) 2016-2018 Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
class Psr7Helper
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

    public static function buildServerRequestFromGlobals(ServerRequestInterface $request, array $server, array $files): ServerRequestInterface
    {
        $request = $request
            ->withProtocolVersion(isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1')
            ->withUploadedFiles(self::normalizeFiles($files));

        $headers = [];
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $key = substr($key, 5);
            } elseif (!\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                continue;
            }
            $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));

            $headers[$key] = $value;
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $headers['Authorization'] = 'Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].':'.($_SERVER['PHP_AUTH_PW'] ?? ''));
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        foreach ($headers as $key => $value) {
            try {
                $request = $request->withHeader($key, $value);
            } catch (\InvalidArgumentException $e) {
                // ignore invalid headers
            }

        }

        return $request;
    }

    public static function buildUriFromGlobals(UriInterface $uri, array $server): UriInterface
    {
        $uri = $uri->withScheme(!empty($server['HTTPS']) && 'off' !== strtolower($server['HTTPS']) ? 'https' : 'http');

        $hasPort = false;
        if (isset($server['HTTP_HOST'])) {
            $parts = parse_url('http://'.$server['HTTP_HOST']);

            if ($parts['host'] ?? false) {
                $uri = $uri->withHost($parts['host']);
            }

            if ($parts['port'] ?? false) {
                $hasPort = true;
                $uri = $uri->withPort($parts['port']);
            }
        } elseif (isset($server['SERVER_NAME'])) {
            $uri = $uri->withHost($server['SERVER_NAME']);
        } elseif (isset($server['SERVER_ADDR'])) {
            $uri = $uri->withHost($server['SERVER_ADDR']);
        }

        if (!$hasPort && isset($server['SERVER_PORT'])) {
            $uri = $uri->withPort($server['SERVER_PORT']);
        }

        $hasQuery = false;
        if (isset($server['REQUEST_URI'])) {
            $requestUriParts = explode('?', $server['REQUEST_URI'], 2);
            $uri = $uri->withPath($requestUriParts[0]);
            if (isset($requestUriParts[1])) {
                $hasQuery = true;
                $uri = $uri->withQuery($requestUriParts[1]);
            }
        }

        if (!$hasQuery && isset($server['QUERY_STRING'])) {
            $uri = $uri->withQuery($server['QUERY_STRING']);
        }

        return $uri;
    }

    public static function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } elseif (!\is_array($value)) {
                continue;
            } elseif (!isset($value['tmp_name'])) {
                $normalized[$key] = self::normalizeFiles($value);
            } elseif (\is_array($value['tmp_name'])) {
                foreach ($value['tmp_name'] as $k => $v) {
                    $file = new WellKnownPsr7Stream($value['tmp_name'][$k], 'r');
                    $normalized[$key][$k] = new WellKnownPsr7UploadedFile($file, $value['size'][$k], $value['error'][$k], $value['name'][$k], $value['type'][$k]);
                }
            } else {
                $file = new WellKnownPsr7Stream($value['tmp_name'], 'r');
                $normalized[$key] = new WellKnownPsr7UploadedFile($file, $value['size'], $value['error'], $value['name'], $value['type']);
            }
        }

        return $normalized;
    }
}
