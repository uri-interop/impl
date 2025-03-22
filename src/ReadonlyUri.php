<?php
declare(strict_types=1);

namespace UriInterop\Impl;

class ReadonlyUri extends Uri
{
    /**
     * @inheritdoc
     */
    public private(set) ?array $queryParams;

    public function __construct(
        public private(set) ?string $scheme = null,
        public private(set) ?string $username = null,
        public private(set) ?string $password = null,
        public private(set) ?string $host = null,
        public private(set) ?int $port = null,
        public private(set) string $path = '',
        public private(set) ?string $query = null,
        public private(set) ?string $fragment = null,
    ) {
        $this->queryParams = $this->parseQuery($this->query);
    }
}
