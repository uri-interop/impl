<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Stringable;
use UriInterop\Interface;

/**
 * @phpstan-import-type path_segments_array from Interface\UriTypeAliases
 * @phpstan-import-type percent_composed_string from Interface\UriTypeAliases
 * @phpstan-import-type query_params_array from Interface\UriTypeAliases
 */
abstract class Uri implements Interface\Uri
{
    /**
     * @inheritdoc
     */
    abstract public string $scheme { get; }

    /**
     * @inheritdoc
     */
    abstract public string $user { get; }

    /**
     * @inheritdoc
     */
    abstract public string $password { get; }

    /**
     * @inheritdoc
     */
    abstract public string $host { get; }

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
    abstract public string $query { get; }

    /**
     * @inheritdoc
     */
    abstract public string $fragment { get; }

    /**
     * @inheritdoc
     */
    abstract public array $pathSegments { get; }

    /**
     * @inheritdoc
     */
    abstract public array $queryParams { get; }

    /**
     * @inheritdoc
     */
    public string $userInfo {
        get {
            $userInfo = rawurlencode($this->user);
            $userInfo .= ($this->user && $this->password) ? ':' . rawurlencode($this->password) : '';
            return $userInfo;
        }
    }

    /**
     * @inheritdoc
     */
    public string $authority {
        get {
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
        return ($this->scheme ? "{$this->scheme}:" : "")
            . ($this->authority ? "//{$this->authority}" : "")
            . ($this->path ? $this->path : "")
            . (! $this->path && ($this->query || $this->fragment) ? "/" : "")
            . ($this->query ? "?{$this->query}" : "")
            . ($this->fragment ? "#{$this->fragment}" : "");
    }

    /**
     * @param path_segments_array $pathSegments
     * @return percent_composed_string
     */
    protected function composePath(array $pathSegments) : string
    {
        if (! $pathSegments) {
            return '';
        }

        array_walk($pathSegments, fn (string $segment) => rawurlencode($segment));
        return '/' . implode('/', $pathSegments);
    }

    /**
     * @return path_segments_array
     */
    protected function parsePath(string $path) : array
    {
        $path = trim($path);

        if (! $path || $path === '/') {
            return [];
        }

        $path = trim($path, '/');
        $pathSegments = explode('/', $path);
        array_walk($pathSegments, fn (string $segment) => urldecode($segment));
        return $pathSegments;
    }

    /**
     * @param query_params_array $queryParams
     * @return percent_composed_string
     */
    protected function composeQuery(array $queryParams) : string
    {
        if (! $queryParams) {
            return '';
        }

        return http_build_query(
            $queryParams,
            encoding_type: PHP_QUERY_RFC3986
        );
    }

    /**
     * @return query_params_array
     */
    protected function parseQuery(string $query) : array
    {
        parse_str($query, $queryParams);
        /** @var query_params_array $queryParams */
        return $queryParams;
    }
}
