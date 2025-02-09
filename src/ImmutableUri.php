<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface;

class ImmutableUri extends Uri implements Interface\ImmutableUri
{
    /**
     * @inheritdoc
     */
    public protected(set) array $queryParams;

    public function __construct(
        public protected(set) string $scheme = '',
        public protected(set) string $user = '',
        public protected(set) string $password = '',
        public protected(set) string $host = '',
        public protected(set) ?int $port = null,
        public protected(set) string $path = '',
        public protected(set) string $query = '',
        public protected(set) string $fragment = '',
    ) {
        $this->queryParams = $this->parseQuery($query);
    }

    /**
     * @inheritdoc
     */
    public function withScheme(string $scheme) : ImmutableUri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withUser(string $user) : ImmutableUri
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPassword(string $password) : ImmutableUri
    {
        $clone = clone $this;
        $clone->password = $password;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withHost(string $host) : ImmutableUri
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPort(?int $port) : ImmutableUri
    {
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPath(string $path) : ImmutableUri
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withQuery(string $query) : ImmutableUri
    {
        $clone = clone $this;
        $clone->query = $query;
        $clone->queryParams = $clone->parseQuery($query);
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withFragment(string $fragment) : ImmutableUri
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $queryParams) : ImmutableUri
    {
        $clone = clone $this;
        $clone->queryParams = $queryParams;
        $clone->query = http_build_query($queryParams);
        return $clone;
    }
}
