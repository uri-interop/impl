<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriComponents;

/**
 * @method ReadonlyUri parseUri(string $uriString)
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
    ) : UriComponents
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
