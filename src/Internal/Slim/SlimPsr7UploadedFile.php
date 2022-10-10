<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Slim;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Factory\UploadedFileFactory;

/**
 * @internal
 */
class SlimPsr7UploadedFile implements UploadedFileInterface
{
    private $file;

    public function __construct(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null)
    {
        $this->file = (new UploadedFileFactory())->createUploadedFile(...\func_get_args());
    }

    public function getStream()
    {
        return $this->file->getStream();
    }

    public function moveTo($targetPath): void
    {
        $this->file->moveTo($targetPath);
    }

    public function getError(): int
    {
        return $this->file->getError();
    }

    public function getClientFilename(): ?string
    {
        return $this->file->getClientFilename();
    }

    public function getClientMediaType(): ?string
    {
        return $this->file->getClientMediaType();
    }

    public function getSize(): ?int
    {
        return $this->file->getSize();
    }

    public function getFilePath(): string
    {
        return $this->getFilePath();
    }
}
