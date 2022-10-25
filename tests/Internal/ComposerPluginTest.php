<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Tests\Internal;

use Composer\Package\Link;
use Composer\Repository\InstalledArrayRepository;
use Composer\Semver\Constraint\Constraint;
use FriendsOfPHP\WellKnownImplementations\Internal\ComposerPlugin;
use PHPUnit\Framework\TestCase;

class ComposerPluginTest extends TestCase
{
    /**
     * @dataProvider provideMissingRequires
     */
    public function testMissingRequires(array $expected, InstalledArrayRepository $repo, array $rootRequires, array $rootDevRequires)
    {
        $plugin = new ComposerPlugin();

        $this->assertSame($expected, $plugin->getMissingRequires($repo, [$rootRequires, $rootDevRequires], true));
    }

    public function provideMissingRequires()
    {
        $link = new Link('source', 'target', new Constraint(Constraint::STR_OP_GE, '1'));
        $repo = new InstalledArrayRepository([]);

        yield 'empty' => [[[], [], []], $repo, [], []];

        $rootRequires = [
            'php-http/async-client-implementation' => $link,
        ];
        $expected = [[
            'php-http/async-client-implementation' => [
                'symfony/http-client',
                'guzzlehttp/promises',
                'php-http/message-factory',
            ],
        ], [], []];

        if (!\extension_loaded('phalcon')) {
            $expected[0]['psr/http-factory-implementation'] = ['nyholm/psr7'];
        }

        yield 'async-httplug' => [$expected, $repo, $rootRequires, []];
    }
}
