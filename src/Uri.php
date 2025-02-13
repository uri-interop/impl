<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Stringable;
use UriInterop\Interface;

/**
 * @phpstan-import-type QueryParamsArray from Interface\UriTypeAliases
 */
abstract class Uri implements Interface\Uri
{
    abstract public string $scheme { get; }

    abstract public string $user { get; }

    abstract public string $password { get; }

    abstract public string $host { get; }

    abstract public ?int $port { get; }

    abstract public string $path { get; }

    abstract public string $query { get; }

    abstract public string $fragment { get; }

    /**
     * @inheritdoc
     */
    abstract public array $queryParams { get; }

    public string $userInfo {
        get {
            $userInfo = $this->user;
            $userInfo .= ($this->user && $this->password) ? ":{$this->password}" : "";
            return $userInfo;
        }
    }

    public string $authority {
        get {
            $authority = ($this->userInfo) ? "{$this->userInfo}" : "";

            if ($authority && $this->host) {
                $authority .= "@";
            }

            if ($this->host) {
                $authority .= $this->host;

                if ($this->port !== null) {
                    $authority .= ":{$this->port}";
                }
            }

            return $authority;
        }
    }

    public function __toString() : string
    {
        return ($this->scheme ? "{$this->scheme}:" : "")
            . ($this->authority ? "//{$this->authority}" : "")
            . ($this->path ? $this->path : "")
            . ($this->query ? "?{$this->query}" : "")
            . ($this->fragment ? "#{$this->fragment}" : "");
    }

    /**
     * @return QueryParamsArray
     */
    protected function parseQuery(string $query) : array
    {
        parse_str($query, $queryParamsArray);
        /** @var QueryParamsArray $queryParamsArray */
        return $queryParamsArray;
    }
}
