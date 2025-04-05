<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriTypeAliases;

/**
 * @phpstan-import-type percent_encoded_string from UriTypeAliases
 * @phpstan-import-type encoded_string from UriTypeAliases
 */
class UriComponents
{
    /**
     * @param ?percent_encoded_string $username:
     * @param ?percent_encoded_string $password:
     * @param ?percent_encoded_string $host
     * @param percent_encoded_string $path
     * @param ?encoded_string $query
     * @param ?percent_encoded_string $fragment
     */
    public function __construct(
        public ?string $scheme = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $host = null,
        public ?int $port = null,
        public string $path = '',
        public ?string $query = null,
        public ?string $fragment = null,
    ) {
    }

    /**
     * @return array{
     *     scheme: ?string,
     *     username: ?percent_encoded_string,
     *     password: ?percent_encoded_string,
     *     host: ?percent_encoded_string,
     *     port: ?int,
     *     path: percent_encoded_string,
     *     query: ?encoded_string,
     *     fragment: ?percent_encoded_string,
     * }
     */
    public function asArray()
    {
        return (array) $this;
    }
}
