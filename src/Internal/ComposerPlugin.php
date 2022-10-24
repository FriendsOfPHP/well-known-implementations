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
use Composer\Package\Package;
use Composer\Package\Version\VersionParser;
use Composer\Package\Version\VersionSelector;
use Composer\Plugin\PluginInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RepositorySet;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\Util\Filesystem;
use FriendsOfPHP\WellKnownImplementations\ConcreteImplementation;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
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

    public function preAutoloadDump(Event $event)
    {
        $filesystem = new Filesystem();
        $vendorDir = $filesystem->normalizePath(realpath(realpath($event->getComposer()->getConfig()->get('vendor-dir'))));
        $filesystem->ensureDirectoryExists($vendorDir.'/composer');

        $repo = $event->getComposer()->getRepositoryManager()->getLocalRepository();
        $vendors = self::initializeConcreteImplementation($repo);

        $psr7Vendor = str_replace(['NULL', '\\\\'], ['null', '\\'], var_export(PSR7_VENDOR, true));
        $psr18Vendor = str_replace(['NULL', '\\\\'], ['null', '\\'], var_export(PSR18_VENDOR, true));
        $httplugVendor = str_replace(['NULL', '\\\\'], ['null', '\\'], var_export(HTTPLUG_VENDOR, true));

        natcasesort($vendors);
        $vendorConsts = '';
        foreach ($vendors as $vendor) {
            $vendorConsts .= "\n    public const VENDOR".strtoupper(preg_replace('/[A-Z]++/', '_$0', $vendor)).' = '.var_export($vendor, true).';';
        }

        $filesystem->filePutContentsIfModified($vendorDir.'/composer/WellKnownConcreteImplementation.php', <<<EOPHP
<?php

namespace FriendsOfPHP\WellKnownImplementations;

class ConcreteImplementation
{
    public const PSR7_VENDOR = $psr7Vendor;
    public const PSR18_VENDOR = $psr18Vendor;
    public const HTTPLUG_VENDOR = $httplugVendor;
$vendorConsts
}

EOPHP
        );

        $rootPackage = $event->getComposer()->getPackage();
        $autoload = $rootPackage->getAutoload();
        $autoload['classmap'][] = $vendorDir.'/composer/WellKnownConcreteImplementation.php';
        $rootPackage->setAutoload($autoload);

        unset($vendors[PSR7_VENDOR], $vendors[PSR18_VENDOR], $vendors[HTTPLUG_VENDOR]);

        foreach ($repo->getPackages() as $package) {
            if ('friendsofphp/well-known-implementations' !== $package->getName() || !$package instanceof Package) {
                continue;
            }
            $autoload = $package->getAutoload();
            $autoload['exclude-from-classmap'][] = 'src/ConcreteImplementation.php';
            $autoload['exclude-from-classmap'][] = 'src/Internal/ComposerPlugin.php';
            foreach ($vendors as $vendor) {
                $autoload['exclude-from-classmap'][] = 'src/Internal/'.$vendor.'/';
            }
            $package->setAutoload($autoload);
        }
    }

    public function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        $repo = $composer->getRepositoryManager()->getLocalRepository();
        $requires = [
            $composer->getPackage()->getRequires(),
            $composer->getPackage()->getDevRequires(),
        ];

        $missingRequires = $this->getMissingRequires($repo, $requires, 'project' === $composer->getPackage()->getType());
        $missingRequires = [
            'require' => array_fill_keys(array_merge([], ...array_values($missingRequires[0])), '*'),
            'require-dev' => array_fill_keys(array_merge([], ...array_values($missingRequires[1])), '*'),
            'remove' => array_fill_keys(array_merge([], ...array_values($missingRequires[2])), '*'),
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
        $trace['object']->setAudit(false);
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

    public function getMissingRequires(InstalledRepositoryInterface $repo, array $requires, bool $isProject): array
    {
        $allPackages = [];
        $devPackages = array_flip($repo->getDevPackageNames());

        foreach ($repo->getPackages() as $package) {
            $allPackages[$package->getName()] = $package;
            $requires[(int) isset($devPackages[$package->getName()])] += $package->getRequires();
        }

        $abstractions = [];
        $missingRequires = [[], [], []];
        $versionParser = new VersionParser();

        foreach ($requires as $dev => $rules) {
            $rules = array_intersect_key(self::PROVIDE_RULES, $rules);

            while ($rules) {
                $abstractions[] = $abstraction = key($rules);

                foreach (array_shift($rules) as $candidate => $deps) {
                    if (!isset($allPackages[$candidate])) {
                        continue;
                    }

                    if ($isProject && !$dev && isset($devPackages[$candidate])) {
                        $missingRequires[0][$abstraction] = [$candidate];
                        $missingRequires[2][$abstraction] = [$candidate];
                    } else {
                        $missingRequires[$dev][$abstraction] = [];
                    }

                    foreach ($deps as $dep) {
                        if (isset(self::PROVIDE_RULES[$dep])) {
                            $rules[$dep] = self::PROVIDE_RULES[$dep];
                        } elseif (!isset($allPackages[$dep])) {
                            $missingRequires[$dev][$abstraction][] = $dep;
                        } elseif ($isProject && !$dev && isset($devPackages[$dep])) {
                            $missingRequires[0][$abstraction][] = $dep;
                            $missingRequires[2][$abstraction][] = $dep;
                        }
                    }
                    break;
                }
            }

            while ($abstractions) {
                $abstraction = array_shift($abstractions);

                if (isset($missingRequires[$dev][$abstraction])) {
                    continue;
                }
                $candidates = self::PROVIDE_RULES[$abstraction];

                foreach ($candidates as $candidate => $deps) {
                    if (isset($allPackages[$candidate]) && (!$isProject || $dev || !isset($devPackages[$candidate]))) {
                        continue 2;
                    }
                }

                foreach (array_intersect_key(self::STICKYNESS_RULES, $candidates) as $candidate => $stickyRule) {
                    [$stickyName, $stickyVersion] = explode(':', $stickyRule, 2) + [1 => null];
                    if (!isset($allPackages[$stickyName]) || ($isProject && !$dev && isset($devPackages[$stickyName]))) {
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
                    } elseif (!isset($allPackages[$dep])) {
                        $missingRequires[$dev][$abstraction][] = $dep;
                    } elseif ($isProject && !$dev && isset($devPackages[$dep])) {
                        $missingRequires[0][$abstraction][] = $dep;
                        $missingRequires[2][$abstraction][] = $dep;
                    }
                }
            }
        }

        if (!isset($allPackages['nyholm/psr7']) && !isset($allPackages['php-http/discovery'])) {
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
                if ('remove' === $key) {
                    $manipulator->removeSubNode('require-dev', $package);
                } else {
                    $manipulator->addLink($key, $package, $constraint, $sortPackages);
                }
            }
        }

        file_put_contents($file, $manipulator->getContents());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => 'preAutoloadDump',
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

    private static function initializeConcreteImplementation(InstalledRepositoryInterface $repo): array
    {
        $vendors = [];

        foreach (ConcreteImplementation::IMPLEMENTATIONS as $const => $packages) {
            $vendors += $packages;
            if (\defined(__NAMESPACE__."\\{$const}_VENDOR")) {
                continue;
            }
            foreach ([false, true] as $includeDevRequirements) {
                foreach ($packages as $namespace => $versions) {
                    foreach ((array) $versions as $package => $version) {
                        if (\is_int($package)) {
                            $package = $version;
                            $version = null;
                        }
                        if (!$includeDevRequirements && \in_array($package, $repo->getDevPackageNames(), true)) {
                            continue 2;
                        }
                        if (!$repo->findPackage($package, null !== $version ? '>='.$version : '*')) {
                            continue 2;
                        }
                    }
                    \define(__NAMESPACE__."\\{$const}_VENDOR", $namespace);
                    continue 3;
                }
            }
            \define(__NAMESPACE__."\\{$const}_VENDOR", null);
        }

        foreach ($vendors as $vendor => $versions) {
            $vendors[$vendor] = $vendor;
        }

        return $vendors;
    }
}
