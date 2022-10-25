<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Phalcon;

use Phalcon\Http\Message\UploadedFile;
use Psr\Http\Message\StreamInterface;

class_alias(UploadedFile::class, PhalconPsr7UploadedFile::class);

if (false) {
    /**
     * @internal
     */
    class PhalconPsr7UploadedFile extends UploadedFile
    {
        public function __construct(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null)
        {
        }
    }
}
