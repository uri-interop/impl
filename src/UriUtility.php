<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Stringable;
use UriInterop\Interface\UriStruct;
use UriInterop\Interface\UriStructFactory;
use UriInterop\Interface\UriStructNormalizer;
use UriInterop\Interface\UriStructResolver;
use UriInterop\Interface\UriStringParser;

abstract class UriUtility implements UriStructFactory, UriStructNormalizer, UriStructResolver, UriStringParser
{
    /**
     * @inheritdoc
     */
    abstract public function newUri(
        ?string $scheme = null,
        ?string $username = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null,
    ) : UriStruct;

    /**
     * @inheritdoc
     */
    public function parseUri(string|Stringable $uriString) : UriStruct
    {
        $components = UriComponents::newFromParsed($uriString);
        return $this->newUri(...$components->asArray());
    }

    /**
     * @inheritdoc
     */
    public function normalizeUri(UriStruct $uri) : UriStruct
    {
        $components = UriComponents::newFromNormalized($uri);
        return $this->newUri(...$components->asArray());
    }

    /**
     * @inheritdoc
     */
    public function resolveUri(
        UriStruct $relative,
        UriStruct $base
    ) : UriStruct
    {
        $components = UriComponents::newFromResolved($relative, $base);
        return $this->newUri(...$components->asArray());
    }
}
