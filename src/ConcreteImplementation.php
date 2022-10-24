<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Composer\InstalledVersions;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ConcreteImplementation
{
    public const PSR7_VENDOR = PSR7_VENDOR;
    public const PSR18_VENDOR = PSR18_VENDOR;
    public const HTTPLUG_VENDOR = HTTPLUG_VENDOR;

    public const VENDOR_GUZZLE = 'Guzzle';
    public const VENDOR_HTTPLUG_CURL = 'HttplugCurl';
    public const VENDOR_HTTPLUG_GUZZLE6 = 'HttplugGuzzle6';
    public const VENDOR_HTTPLUG_GUZZLE7 = 'HttplugGuzzle7';
    public const VENDOR_HTTPLUG_MOCK = 'HttplugMock';
    public const VENDOR_HTTPLUG_REACT = 'HttplugReact';
    public const VENDOR_LAMINAS = 'Laminas';
    public const VENDOR_NYHOLM = 'Nyholm';
    public const VENDOR_SLIM = 'Slim';
    public const VENDOR_SYMFONY = 'Symfony';

    /**
     * @internal
     */
    public const IMPLEMENTATIONS = [
        'PSR7' => [
            'Nyholm' => 'nyholm/psr7',
            'Guzzle' => ['guzzlehttp/psr7' => '1.4'],
            'Slim' => 'slim/psr7',
            'Laminas' => 'laminas/laminas-diactoros',
        ],
        'PSR18' => [
            'Symfony' => 'symfony/http-client',
            'Guzzle' => ['guzzlehttp/guzzle' => '7.0'],
        ],
        'HTTPLUG' => [
            'Symfony' => ['symfony/http-client' => '4.4'],
            'HttplugGuzzle7' => 'php-http/guzzle7-adapter',
            'HttplugGuzzle6' => 'php-http/guzzle6-adapter',
            'HttplugCurl' => 'php-http/curl-client',
            'HttplugReact' => 'php-http/react-adapter',
            'HttplugMock' => 'php-http/mock-client',
        ],
    ];

    /**
     * @internal
     */
    public static function initialize(): void
    {
        foreach (self::IMPLEMENTATIONS as $const => $packages) {
            if (\defined(__NAMESPACE__."\\{$const}_VENDOR")) {
                continue;
            }
            foreach ([false, true] as $includeDevRequirements) {
                foreach ($packages as $vendor => $versions) {
                    if ('HttplugMock' === $vendor && !$includeDevRequirements) {
                        continue;
                    }
                    foreach ((array) $versions as $package => $version) {
                        if (\is_int($package)) {
                            $package = $version;
                            $version = null;
                        }
                        if (!InstalledVersions::isInstalled($package, $includeDevRequirements)) {
                            continue 2;
                        }
                        if (null !== $version && version_compare($installed = InstalledVersions::getVersion($package) ?? $version, $version, '<') && 0 !== strpos($installed, 'dev-')) {
                            continue 2;
                        }
                    }
                    \define(__NAMESPACE__."\\{$const}_VENDOR", $vendor);
                    continue 3;
                }
            }
            \define(__NAMESPACE__."\\{$const}_VENDOR", null);
        }
    }
}

ConcreteImplementation::initialize();
