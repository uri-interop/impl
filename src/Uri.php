<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriComponents;
use UriInterop\Interface\UriTypeAliases;

/**
 * @phpstan-import-type percent_composed_string from UriTypeAliases
 * @phpstan-import-type query_params_array from UriTypeAliases
 */
abstract class Uri implements UriComponents
{
    /**
     * @inheritdoc
     */
    abstract public ?string $scheme { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $username { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $password { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $host { get; }

    /**
     * @inheritdoc
     */
    abstract public ?int $port { get; }

    /**
     * @inheritdoc
     */
    abstract public string $path { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $query { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $fragment { get; }

    /**
     * @inheritdoc
     */
    abstract public ?array $queryParams { get; }

    /**
     * @inheritdoc
     */
    public ?string $userinfo {
        get {
            if ($this->username === null && $this->password === null) {
                return null;
            }

            $userinfo = (string) $this->username;

            $userinfo .= ($this->username && $this->password)
                ? ':' . (string) $this->password
                : '';

                return $userinfo;
        }
    }

    /**
     * @inheritdoc
     */
    public ?string $authority {
        get {
            if (
                $this->userinfo === null
                && $this->host === null
                && $this->port === null
            ) {
                return null;
            }

            $authority = ($this->userinfo) ? $this->userinfo : '';

            if ($authority && $this->host) {
                $authority .= "@";
            }

            if ($this->host) {
                $authority .= $this->host;

                if ($this->port !== null) {
                    $authority .= ":{$this->port}";
                }
            }

            return $authority;
        }
    }

    /**
     * @inheritdoc
     */
    public function __toString() : string
    {
        $uriString = '';

        if ($this->scheme !== null) {
           $uriString .= "{$this->scheme}:";
        }

        if ($this->authority !== null) {
            $uriString .= "//{$this->authority}";
        }

        $uriString .= $this->path;

        if ($this->query !== null) {
            $uriString .= "?{$this->query}";
        }

        if ($this->fragment !== null) {
            $uriString .= "#{$this->fragment}";
        }

        return $uriString;
    }

    /**
     * @param ?query_params_array $queryParams
     * @return ?percent_composed_string
     */
    protected function composeQuery(?array $queryParams) : ?string
    {
        if ($queryParams === null) {
            return null;
        }

        return http_build_query(
            $queryParams,
            encoding_type: PHP_QUERY_RFC3986
        );
    }

    /**
     * @return ?query_params_array
     */
    protected function parseQuery(?string $query) : ?array
    {
        if ($query === null) {
            return null;
        }

        parse_str($query, $queryParams);

        /** @var query_params_array $queryParams */
        return $queryParams;
    }
}
