<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

if (null !== $vendor = ConcreteImplementation::PSR7_VENDOR) {
    class_alias(Internal::class."\\{$vendor}\\{$vendor}Psr7UploadedFile", WellKnownPsr7UploadedFile::class);
} else {
    throw new \LogicException('Supported PSR-7 implementation not found, try running "composer require nyholm/psr7".');
}

if (false) {
    final class WellKnownPsr7UploadedFile implements UploadedFileInterface
    {
        public function __construct(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null)
        {
        }
    }
}
