<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\StringableComponents;
use UriInterop\Interface\UriEncoded;

/**
 * @method MutableUri parseUri(string $uriString)
 */
class MutableUriUtility extends UriUtility
{
    /**
     * @inheritdoc
     * @return MutableUri
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
        return new MutableUri(
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
