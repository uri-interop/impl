<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface;

class MutableUri extends Uri implements Interface\MutableUri
{
    /**
     * @inheritdoc
     */
    public array $queryParams = [];

    /**
     * @inheritdoc
     */
    public string $query {
        get {
            return $this->queryParams
                ? http_build_query($this->queryParams)
                : '';
        }

        set (string $query) {
            $this->queryParams = $this->parseQuery($query);
        }
    }

    public function __construct(
        public string $scheme = '',
        public string $user = '',
        public string $password = '',
        public string $host = '',
        public ?int $port = null,
        public string $path = '',
        string $query = '',
        public string $fragment = '',
    ) {
        $this->queryParams = $this->parseQuery($query);
    }
}
