<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface;

class MutableUri extends Uri implements Interface\MutableUri
{
    /**
     * @inheritdoc
     */
    public array $pathSegments = [];

    /**
     * @inheritdoc
     */
    public string $path {
        get {
            return $this->composePath($this->pathSegments);
        }

        set (string $path) {
            $this->pathSegments = $this->parsePath($path);
        }
    }

    /**
     * @inheritdoc
     */
    public array $queryParams = [];

    /**
     * @inheritdoc
     */
    public string $query {
        get {
            return $this->composeQuery($this->queryParams);
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
        string $path = '',
        string $query = '',
        public string $fragment = '',
    ) {
        $this->pathSegments = $this->parsePath($path);
        $this->queryParams = $this->parseQuery($query);
    }
}
