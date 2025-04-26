<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriStruct;

/**
 * @method ReadonlyUri parseUri(string $uriString)
 * @method ReadonlyUri normalizeUri(string $uriString)
 * @method ReadonlyUri resolveUri(UriStruct $relative, UriStruct $base)
 */
class ReadonlyUriUtility extends UriUtility
{
    /**
     * @inheritdoc
     * @return ReadonlyUri
     */
    public function newUri(
        ?string $scheme = null,
        ?string $username = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null,
    ) : UriStruct
    {
        return new ReadonlyUri(
            scheme: $scheme,
            username: $username,
            password: $password,
            host: $host,
            port: $port,
            path: $path,
            query: $query,
            fragment: $fragment,
        );
    }
}
