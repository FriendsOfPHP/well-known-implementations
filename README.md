# FriendsOfPHP / Well-Known Implementations

This package helps reduce the proliferation of same-abstraction implementations
in vendor directories.

It is targeted at SDK maintainers that write their code in a decoupled way but
still need an actual implementation to provide a nice experience out of the box.

Without this package, one would e.g. require the "php-http/client-implementation"
virtual package to signal that a given SDK uses HTTPlug to make API calls, and
would also require "php-http/guzzle7-adapter" to install an actual
implementation in case none is wired by the consuming app when calling the SDK.

But imagine that the consuming app already has a dependency on another
"php-http/client-implementation": the SDK should ideally reuse that
implementation and "php-http/guzzle7-adapter" should be removed from vendor/ with
all its transitive dependencies. This would help with dependency-management and
might enable better integration in debugging panels for example.

By requiring "friendsofphp/well-known-implementations" instead of
"php-http/guzzle7-adapter", SDK maintainers can provide ideal experiences:
because this package is also a composer-plugin, it will auto-install an actual
implementation of the required abstraction when none is already installed, or
reuse it if one is found.

In their constructors, SDKs should then reference the provided "well-known"
classes and they will get whatever implementation is available:

```php
class MySdk
{
    public function __construct(
        private HttpClient $client = new WellKnownHttplugClient(),
    )
    {
        // ...
    }
}
```

All provided `WellKnown*` classes have standardized constructor signatures so
that you don't need to care about which exact implementation is available to
instantiate them.

Althought not required most of the time, you can check which implementation is
used by using the `ConcreteImplementation::*_VENDOR` constants.

The logic to decide which implementation should be installed relies on the
packages that are already found in a project. For example, if one is using
`react/event-loop`, the plugin will select `php-http/react-adapter` (the rules
are declared in `ComposerPlugin`; they're open for discussion.) The missing
packages will be added to the project's composer.json file. This makes it easy
to override the choices of the plugin by explicitly requiring the preferred
implementations.

As of now, the following abstractions are supported:
 - php-http/async-client-implementation
 - php-http/client-implementation
 - psr/http-client-implementation
 - psr/http-factory-implementation
 - psr/http-message-implementation

And the following vendors are supported:
 - Guzzle
 - HTTPlug
 - Laminas
 - Nyholm
 - React
 - Slim
 - Symfony

More abstractions / vendors can be added by contributions.

If your favorite SDK does not use this package yet, please let them know about it
or better: send them a PR!
