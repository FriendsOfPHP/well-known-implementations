<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal\Phalcon;

use Phalcon\Http\Message\StreamFactory;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
class PhalconPsr7Stream implements StreamInterface
{
    private $stream;

    /**
     * @param string|resource $content
     */
    public function __construct($content = '', string $mode = null)
    {
        $factory = new StreamFactory();

        if (null !== $mode) {
            $this->stream = $factory->createStreamFromFile($content, $mode);
        } elseif (\is_string($content)) {
            $this->stream = $factory->createStream($content);
        } else {
            $this->stream = $factory->createStreamFromResource($content);
        }
    }

    public function __toString()
    {
        return $this->stream->__toString();
    }

    public function close(): void
    {
        $this->stream->close();
    }

    public function detach()
    {
        return $this->stream->detach();
    }

    public function getSize(): ?int
    {
        return $this->stream->getSize();
    }

    public function tell(): int
    {
        return $this->stream->tell();
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function isSeekable(): bool
    {
        return $this->stream->isSeekable();
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->stream->seek(...\func_get_args());
    }

    public function rewind(): void
    {
        $this->stream->rewind();
    }

    public function isWritable(): bool
    {
        return $this->stream->isWritable();
    }

    public function write($string): int
    {
        return $this->stream->write(...\func_get_args());
    }

    public function isReadable(): bool
    {
        return $this->stream->isReadable();
    }

    public function read($length): string
    {
        return $this->stream->read(...\func_get_args());
    }

    public function getContents(): string
    {
        return $this->stream->getContents();
    }

    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata(...\func_get_args());
    }
}
