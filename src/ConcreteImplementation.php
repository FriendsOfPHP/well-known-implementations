<?php

/*
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FriendsOfPHP\WellKnownImplementations;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Discovery\ClassDiscovery;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ConcreteImplementation extends ClassDiscovery
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
    public const VENDOR_SLIM3 = 'Slim3';
    public const VENDOR_SYMFONY = 'Symfony';

    private const IMPLEMENTATIONS = [
        'PSR7' => [
            // Phalcon
            // Diactoros
            // Slim3
            'Nyholm' => \Nyholm\Psr7\Request::class,
            'Guzzle' => \GuzzleHttp\Psr7\Request::class,
            'Slim' => \Slim\Psr7\Request::class,
            'Laminas' => \Laminas\Diactoros\Request::class,
        ],
        'PSR18' => [
            'Symfony' => \Symfony\Component\HttpClient\Psr18Client::class,
            'Guzzle' => \GuzzleHttp\Client::class,
            // buzz
        ],
        'HTTPLUG' => [
            'Symfony' => \Symfony\Component\HttpClient\HttplugClient::class,
            'HttplugGuzzle7' => \Http\Adapter\Guzzle7\Client::class,
            'HttplugGuzzle6' => \Http\Adapter\Guzzle6\Client::class,
            // Guzzle 5
            'HttplugCurl' => \Http\Client\Curl\Client::class,
            'HttplugReact' => \Http\Adapter\React\Client::class,
            'HttplugMock' => \Http\Mock\Client::class,

            //['class' => Socket::class, 'condition' => Socket::class],
            //['class' => Buzz::class, 'condition' => Buzz::class],
            //['class' => Cake::class, 'condition' => Cake::class],
            //['class' => Zend::class, 'condition' => Zend::class],
            //['class' => Artax::class, 'condition' => Artax::class],
            //Buzz\Client\FileGetContents

        ],
    ];

    /**
     * @internal
     */
    public static function initialize(): void
    {
        foreach (self::IMPLEMENTATIONS as $const => $vendors) {
            if (\defined(__NAMESPACE__."\\{$const}_VENDOR")) {
                continue;
            }
            foreach ($vendors as $vendor => $class) {
                if (!class_exists($class)) {
                    $vendor = null;
                }
                \define(__NAMESPACE__."\\{$const}_VENDOR", $vendor);
            }
        }
    }
}

ConcreteImplementation::initialize();
