<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7Stream;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr7UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class WellKnownPsr7UploadedFileTest extends TestCase
{
    public function testUploadedFile()
    {
        $file = new WellKnownPsr7UploadedFile(new WellKnownPsr7Stream('Hello'), null, \UPLOAD_ERR_PARTIAL, 'client.name', 'client/type');

        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertSame(5, $file->getSize());
        $this->assertSame(\UPLOAD_ERR_PARTIAL, $file->getError());
        $this->assertSame('client.name', $file->getClientFilename());
        $this->assertSame('client/type', $file->getClientMediaType());
    }
}
