<?php
declare(strict_types=1);

namespace UriInterop\Impl;

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
        ?string $user = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null,
    ) : Uri
    {
        return new ReadonlyUri(
            scheme: $scheme,
            user: $user,
            password: $password,
            host: $host,
            port: $port,
            path: $path,
            query: $query,
            fragment: $fragment,
        );
    }
}
