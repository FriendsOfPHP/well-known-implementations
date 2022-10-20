<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations\Internal;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Package\Locker;
use Composer\Package\Version\VersionParser;
use Composer\Package\Version\VersionSelector;
use Composer\Plugin\PluginInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RepositorySet;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

/**
 * @internal
 */
class ComposerPlugin implements PluginInterface, EventSubscriberInterface
{
    private const PROVIDE_RULES = [
        'php-http/async-client-implementation' => [
            'symfony/http-client' => ['guzzlehttp/promises', 'php-http/message-factory', 'psr/http-factory-implementation'],
            'php-http/guzzle7-adapter' => [],
            'php-http/guzzle6-adapter' => [],
            'php-http/curl-client' => [],
            'php-http/react-adapter' => [],
        ],
        'php-http/client-implementation' => [
            'symfony/http-client' => ['php-http/message-factory', 'psr/http-factory-implementation'],
            'php-http/guzzle7-adapter' => [],
            'php-http/guzzle6-adapter' => [],
            'php-http/curl-client' => [],
            'php-http/react-adapter' => [],
        ],
        'psr/http-client-implementation' => [
            'symfony/http-client' => ['psr/http-factory-implementation'],
            'guzzlehttp/guzzle' => [],
        ],
        'psr/http-factory-implementation' => [
            'nyholm/psr7' => [],
            'guzzlehttp/psr7' => [],
            'slim/psr7' => [],
            'laminas/laminas-diactoros' => [],
        ],
        'psr/http-message-implementation' => [
            'nyholm/psr7' => [],
            'guzzlehttp/psr7' => [],
            'slim/psr7' => [],
            'laminas/laminas-diactoros' => [],
        ],
    ];

    private const STICKYNESS_RULES = [
        'symfony/http-client' => 'symfony/framework-bundle',
        'php-http/guzzle7-adapter' => 'guzzlehttp/guzzle:^7',
        'php-http/guzzle6-adapter' => 'guzzlehttp/guzzle:^6',
        'php-http/react-adapter' => 'react/event-loop',
        'slim/psr7' => 'slim/slim',
    ];

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        $repo = $composer->getRepositoryManager()->getLocalRepository();
        $requires = [
            $composer->getPackage()->getRequires(),
            $composer->getPackage()->getDevRequires(),
        ];

        $missingRequires = $this->getMissingRequires($repo, $requires);
        $missingRequires = [
            'require' => array_fill_keys(array_merge([], ...array_values($missingRequires[0])), '*'),
            'require-dev' => array_fill_keys(array_merge([], ...array_values($missingRequires[1])), '*'),
        ];

        if (!$missingRequires = array_filter($missingRequires)) {
            return;
        }

        $composerJsonContents = file_get_contents(Factory::getComposerFile());
        $this->updateComposerJson($missingRequires, $composer->getConfig()->get('sort-packages'));

        $installer = null;
        foreach (debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT) as $trace) {
            if (isset($trace['object']) && $trace['object'] instanceof Installer) {
                $installer = $trace['object'];
                break;
            }
        }

        if (!$installer) {
            return;
        }

        $event->stopPropagation();

        $ed = $composer->getEventDispatcher();
        $disableScripts = !method_exists($ed, 'setRunScripts') || !((array) $ed)["\0*\0runScripts"];
        $composer = Factory::create($event->getIO(), null, false, $disableScripts);

        /** @var Installer $installer */
        $installer = clone $installer;
        $installer->__construct(
            $event->getIO(),
            $composer->getConfig(),
            $composer->getPackage(),
            $composer->getDownloadManager(),
            $composer->getRepositoryManager(),
            $composer->getLocker(),
            $composer->getInstallationManager(),
            $composer->getEventDispatcher(),
            $composer->getAutoloadGenerator()
        );

        if (0 !== $installer->run()) {
            file_put_contents(Factory::getComposerFile(), $composerJsonContents);

            return;
        }

        $versionSelector = new VersionSelector(new RepositorySet());
        $updateComposerJson = false;

        foreach ($composer->getRepositoryManager()->getLocalRepository()->getPackages() as $package) {
            foreach (['require', 'require-dev'] as $key) {
                if (!isset($missingRequires[$key][$package->getName()])) {
                    continue;
                }
                $updateComposerJson = true;
                $missingRequires[$key][$package->getName()] = $versionSelector->findRecommendedRequireVersion($package);
            }
        }

        if ($updateComposerJson) {
            $this->updateComposerJson($missingRequires, $composer->getConfig()->get('sort-packages'));
            $this->updateComposerLock($composer, $event->getIO());
        }
    }

    public function getMissingRequires(InstalledRepositoryInterface $repo, array $requires): array
    {
        $allPackages = [];
        $devPackages = array_flip($repo->getDevPackageNames());

        foreach ($repo->getPackages() as $package) {
            $allPackages[$package->getName()] = $package;
            $requires[(int) isset($devPackages[$package->getName()])] += $package->getRequires();
        }


        $abstractions = [];
        $missingRequires = [[], []];
        $versionParser = new VersionParser();

        foreach ($requires as $dev => $rules) {
            $rules = array_intersect_key(self::PROVIDE_RULES, $rules);

            while ($rules) {
                $abstractions[] = $abstraction = key($rules);

                foreach (array_shift($rules) as $candidate => $deps) {
                    if (!isset($allPackages[$candidate])) {
                        continue;
                    }
                    $missingRequires[$dev][$abstraction] = !$dev && isset($devPackages[$candidate]) ? [$candidate] : [];

                    foreach ($deps as $dep) {
                        if (isset(self::PROVIDE_RULES[$dep])) {
                            $rules[$dep] = self::PROVIDE_RULES[$dep];
                        } elseif (!isset($allPackages[$dep]) || (!$dev && isset($devPackages[$dep]))) {
                            $missingRequires[$dev][$abstraction][] = $dep;
                        }
                    }
                }
            }

            while ($abstractions) {
                $abstraction = array_shift($abstractions);

                if (isset($missingRequires[$dev][$abstraction])) {
                    continue;
                }
                $candidates = self::PROVIDE_RULES[$abstraction];

                foreach ($candidates as $candidate => $deps) {
                    if (isset($allPackages[$candidate]) && (!$dev || isset($devPackages[$candidate]))) {
                        continue 2;
                    }
                }

                foreach (array_intersect_key(self::STICKYNESS_RULES, $candidates) as $candidate => $stickyRule) {
                    [$stickyName, $stickyVersion] = explode(':', $stickyRule, 2) + [1 => null];
                    if (!isset($allPackages[$stickyName]) || (!$dev && isset($devPackages[$stickyName]))) {
                        continue;
                    }
                    if (null !== $stickyVersion && !$repo->findPackage($stickyName, $versionParser->parseConstraints($stickyVersion))) {
                        continue;
                    }

                    $candidates = [$candidate => $candidates[$candidate]];
                    break;
                }

                $missingRequires[$dev][$abstraction] = [key($candidates)];

                foreach (current($candidates) as $dep) {
                    if (isset(self::PROVIDE_RULES[$dep])) {
                        $abstractions[] = $dep;
                    } elseif (!isset($allPackages[$dep]) || (!$dev && isset($devPackages[$dep]))) {
                        $missingRequires[$dev][$abstraction][] = $dep;
                    }
                }
            }
        }

        if (!isset($allPackages['nyholm/psr7'])) {
            foreach ($missingRequires as $dev => $abstractions) {
                if (\in_array('nyholm/psr7', $missingRequires[$dev]['psr/http-factory-implementation'] ?? [], true)) {
                    continue;
                }
                foreach ($abstractions as $abstraction => $deps) {
                    if (\in_array('symfony/http-client', $deps, true)) {
                        $missingRequires[$dev][$abstraction][] = 'php-http/discovery';
                        continue 2;
                    }
                }
            }
        }

        $missingRequires[1] = array_diff_key($missingRequires[1], $missingRequires[0]);

        return $missingRequires;
    }

    public function updateComposerJson(array $missingRequires, bool $sortPackages)
    {
        $file = Factory::getComposerFile();
        $contents = file_get_contents($file);

        $manipulator = new JsonManipulator($contents);

        foreach ($missingRequires as $key => $packages) {
            foreach ($packages as $package => $constraint) {
                $manipulator->addLink($key, $package, $constraint, $sortPackages);
            }
        }

        file_put_contents($file, $manipulator->getContents());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_UPDATE_CMD => 'postUpdate',
        ];
    }

    private function updateComposerLock(Composer $composer, IOInterface $io)
    {
        $lock = substr(Factory::getComposerFile(), 0, -4).'lock';
        $composerJson = file_get_contents(Factory::getComposerFile());
        $lockFile = new JsonFile($lock, null, $io);
        $locker = new Locker($io, $lockFile, $composer->getInstallationManager(), $composerJson);
        $lockData = $locker->getLockData();
        $lockData['content-hash'] = Locker::getContentHash($composerJson);
        $lockFile->write($lockData);
    }
}
