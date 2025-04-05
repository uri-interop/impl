<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriRecord;

/**
 * @method ImmutableUri parseUri(string $uriString)
 * @method ImmutableUri normalizeUri(string $uriString)
 * @method ImmutableUri resolveUri(UriRecord $relative, UriRecord $base)
 */
class ImmutableUriUtility extends UriUtility
{
    /**
     * @inheritdoc
     * @return ImmutableUri
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
    ) : UriRecord
    {
        return new ImmutableUri(
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
