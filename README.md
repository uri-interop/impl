# Uri-Interop Implementation Package

Reference implementations of [uri-interop/interface][].

## Installation

Install this package via [Composer][]:

```
$ composer require uri-interop/impl
```

## Implementations

Each implementation provides a different kind of mutability, ranging from readonly to fully-mutable, as well as a utility class that provides a factory, parser, normalizer, and resolver for that kind of URI.

### _ReadonlyUri_

```php
use UriInterop\Impl\Readonly\ReadonlyUri;
use UriInterop\Impl\Readonly\ReadonlyUriUtility;

$utility = new ReadonlyUriUtility();

$base = $utility->parseUri('https://example.com/foo/bar?zim=gir');

$relative = $utility->newUri(
    path: '/baz/dib',
);

$resolved = $utilty->resolveUri($relative, $base);

$normalized = $utility->normalizeUri($resolved);
```

### _ImmutableUri_

```php
use UriInterop\Impl\Immutable\ImmutableUri;
use UriInterop\Impl\Immutable\ImmutableUriUtility;

$utility = new ImmutableUriUtility();

$base = $utility
    ->parseUri('https://example.com')
    ->withPath('/foo/bar');

$relative = $utility->newUri(
    path: '/baz/dib',
);

$resolved = $utilty->resolveUri($relative, $base);

$normalized = $utility->normalizeUri($resolved);
```

### _MutableUri_

```php
use UriInterop\Impl\Mutable\MutableUri;
use UriInterop\Impl\Mutable\MutableUriUtility;

$utility = new ImmutableUriUtility();

$base = $utility->parseUri('https://example.com');
$base->path = '/foo/bar';

$relative = $utility->newUri(
    path: '/baz/dib',
);

$resolved = $utilty->resolveUri($relative, $base);

$normalized = $utility->normalizeUri($resolved);
```

## Abstract Classes

#### _Uri_

All of the URI classes descend from an abstract _Uri_ class. It can serve as a base for your own URI implementations as well.

#### _UriUtility_

All of the utility classes descend from an abstract _UriUtility_ class. It can serve as a base for your own utility implementations as well.

Note that you can use any kind of utility to normalize and resolve any other kind of URI. For example:

```php
/** @var ReadonlyUriUtility $readonlyUtility */
$baseReadonlyUri = $readonlyUtility->parseUri('https://example.com');
assert($baseReadonlyUri instanceof ReadonlyUri);

/** @var ImmutableUriUtility $immutableUtility */
$relativeImmutableUri = $immutableUtility->newUri(
    path: '/foo/bar'
);

assert($relativeImmutableUri instanceof ImmutableUri);

/** @var MutableUriUtility $mutableUtility */
$resolvedMutableUri = $mutableUtility->resolveUri(
    $relativeReadonlyUri,
    $baseReadonlyUri,
);

assert($resolvedMutableUri instanceof MutableUri);

$normalizedReadonlyUri = $readonlyUtility->normalizeUri(
    $resolvedMutableUri
);

assert($normalizedReadonlyUri instanceof ReadonlyUri);
```

## Support Classes

### _UriComponents_

The _UriComponents_ class is composed of static methods with all of the core logic for parsing, normalizing, and resolving. You can call these static methods from your own implementations.

* * *

[uri-interop/interface]: https://packagist.org/packages/uri-interop/interface
[Composer]: https://getcomposer.org
