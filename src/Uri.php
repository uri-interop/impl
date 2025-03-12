<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface;

/**
 * @phpstan-import-type percent_composed_string from Interface\UriTypeAliases
 * @phpstan-import-type query_params_array from Interface\UriTypeAliases
 */
abstract class Uri implements Interface\Uri
{
    /**
     * @inheritdoc
     */
    abstract public ?string $scheme { get; }

    /**
     * @inheritdoc
     */
    abstract public ?string $user { get; }

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
    public ?string $userInfo {
        get {
            if ($this->user === null && $this->password === null) {
                return null;
            }

            $userInfo = rawurlencode((string) $this->user);

            $userInfo .= ($this->user && $this->password)
                ? ':' . rawurlencode((string) $this->password)
                : '';

                return $userInfo;
        }
    }

    /**
     * @inheritdoc
     */
    public ?string $authority {
        get {
            if (
                $this->userInfo === null
                && $this->host === null
                && $this->port === null
            ) {
                return null;
            }

            $authority = ($this->userInfo) ? $this->userInfo : '';

            if ($authority && $this->host) {
                $authority .= "@";
            }

            if ($this->host) {
                $authority .= rawurlencode($this->host);

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
