<?php
declare(strict_types=1);

namespace UriInterop\Impl;

class ReadonlyUri extends Uri
{
    /**
     * @inheritdoc
     */
    public readonly ?array $queryParams;

    public function __construct(
        public readonly ?string $scheme = null,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?string $host = null,
        public readonly ?int $port = null,
        public readonly string $path = '',
        public readonly ?string $query = null,
        public readonly ?string $fragment = null,
    ) {
        $this->queryParams = $this->parseQuery($this->query);
    }
}
