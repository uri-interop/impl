<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriStruct;

/**
 * @method MutableUri parseUri(string $uriString)
 * @method MutableUri normalizeUri(string $uriString)
 * @method MutableUri resolveUri(UriStruct $base, UriStruct $relative)
 */
class MutableUriUtility extends UriUtility
{
    /**
     * @inheritdoc
     * @return MutableUri
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
        return new MutableUri(
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
