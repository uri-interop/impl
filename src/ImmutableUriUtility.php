<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\StringableComponents;
use UriInterop\Interface\UriEncoded;

/**
 * @method ImmutableUri parseUri(string $uriString)
 */
class ImmutableUriUtility extends UriUtility
{
    /**
     * @inheritdoc
     * @return ImmutableUri
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
    ) : UriEncoded&StringableComponents
    {
        return new ImmutableUri(
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
