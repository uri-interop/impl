<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use InvalidArgumentException;
use UriInterop\Interface\ImmutableUriComponents;

class ImmutableUri extends Uri implements ImmutableUriComponents
{
    /**
     * @inheritdoc
     */
    public protected(set) ?array $queryParams;

    public function __construct(
        public protected(set) ?string $scheme = null,
        public protected(set) ?string $username = null,
        public protected(set) ?string $password = null,
        public protected(set) ?string $host = null,
        public protected(set) ?int $port = null,
        public protected(set) string $path = '',
        public protected(set) ?string $query = null,
        public protected(set) ?string $fragment = null,
    ) {
        $this->queryParams = $this->parseQuery($this->query);
        $this->query = $this->composeQuery($this->queryParams);
    }

    /**
     * @inheritdoc
     */
    public function withScheme(?string $scheme) : ImmutableUri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withUsername(?string $username) : ImmutableUri
    {
        $clone = clone $this;
        $clone->username = $username;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withPassword(?string $password) : ImmutableUri
    {
        $clone = clone $this;
        $clone->password = $password;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withHost(?string $host) : ImmutableUri
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
    public function withQuery(?string $query) : ImmutableUri
    {
        $clone = clone $this;
        $clone->query = $query;
        $clone->queryParams = $clone->parseQuery($query);
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withFragment(?string $fragment) : ImmutableUri
    {
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(?array $queryParams) : ImmutableUri
    {
        if (is_array($queryParams)) {
            $queryParams = $this->immutable($queryParams);
        }

        $clone = clone $this;
        $clone->queryParams = $queryParams;
        $clone->query = $clone->composeQuery($queryParams);
        return $clone;
    }

    /**
     * @template T of array
     * @param T $orig
     * @return T
     */
    public function immutable(array $orig) : mixed
    {
        $copy = [];

        foreach ($orig as $key => $value) {
            if (is_null($value) || is_scalar($value)) {
                $copy[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $copy[$key] = $this->immutable($value);
                continue;
            }

            throw new InvalidArgumentException(
                "Immutable values must be null, scalar, or array."
            );
        }

        /** @var T */
        return $copy;
    }
}
