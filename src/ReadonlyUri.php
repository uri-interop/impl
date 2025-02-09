<?php
declare(strict_types=1);

namespace UriInterop\Impl;

class ReadonlyUri extends Uri
{
    /**
     * @inheritdoc
     */
    public private(set) array $queryParams;

    public function __construct(
        public private(set) string $scheme = '',
        public private(set) string $user = '',
        public private(set) string $password = '',
        public private(set) string $host = '',
        public private(set) ?int $port = null,
        public private(set) string $path = '',
        public private(set) string $query = '',
        public private(set) string $fragment = '',
    ) {
        $this->queryParams = $this->parseQuery($this->query);
    }
}
