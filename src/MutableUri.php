<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\MutableUriComponents;

class MutableUri extends Uri implements MutableUriComponents
{
    /**
     * @inheritdoc
     */
    public ?string $query {
        get {
            return $this->composeQuery($this->queryParams);
        }

        set (?string $query) {
            $this->queryParams = $this->parseQuery($query);
        }
    }

    /**
     * @inheritdoc
     */
    public ?array $queryParams = [];

    public function __construct(
        public ?string $scheme = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $host = null,
        public ?int $port = null,
        public string $path = '',
        ?string $query = null,
        public ?string $fragment = null,
    ) {
        $this->queryParams = $this->parseQuery($query);
        $this->query = $this->composeQuery($this->queryParams);
    }
}
