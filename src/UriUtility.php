<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Stringable;
use UriInterop\Interface\UriComponents;
use UriInterop\Interface\UriComponentsFactory;
use UriInterop\Interface\UriStringParser;
use UriInterop\Interface\UriTypeAliases;

/**
 * @phpstan-import-type percent_encoded_string from UriTypeAliases
 *
 * @phpstan-import-type encoded_string from UriTypeAliases
 *
 * @phpstan-type uri_components_array array{
 *     scheme: ?string,
 *     username: ?percent_encoded_string,
 *     password: ?percent_encoded_string,
 *     host: ?percent_encoded_string,
 *     port: ?int,
 *     path: percent_encoded_string,
 *     query: ?encoded_string,
 *     fragment: ?percent_encoded_string,
 * }
 */
abstract class UriUtility implements UriComponentsFactory, UriStringParser
{
    /**
     * @inheritdoc
     */
    abstract public function newUri(
        ?string $scheme = null,
        ?string $username = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null,
    ) : UriComponents;

    /**
     * @inheritdoc
     */
    public function parseUri(string|Stringable $uriString) : UriComponents
    {
        $components = $this->parseComponents($uriString);
        return $this->newUri(...$components);
    }

    /**
     * @return uri_components_array
     */
    protected function parseComponents(string|Stringable $uriString) : array
    {
        // cf. https://datatracker.ietf.org/doc/html/rfc3986/#appendix-B
        preg_match(
            '(^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?)',
            (string) $uriString,
            $matches,
        );

        $matches += array_fill(1, 9, null);

        /** @var uri_components_array $components */
        $components = [
            'scheme' => ! empty($matches[1]) ? $matches[2] : null,
            'username' => null,
            'password' => null,
            'host' => null,
            'port' => null,
            'path' => $matches[5],
            'query' => ! empty($matches[6]) ? $matches[7] : null,
            'fragment' => ! empty($matches[8]) ? $matches[9] : null,
        ];

        if (empty($matches[3])) {
            return $components;
        }

        $authority = (string) $matches[4];

        if ($authority === '') {
            $components['host'] = $authority;
            return $components;
        }

        preg_match('(^(([^@]*)@)?(.+?)(:(\d*))?$)', $authority, $matches);
        $matches += array_fill(1, 5, null);

        if ($matches[1]) {
            $userinfo = explode(':', (string) $matches[2]);
            $components['username'] = $userinfo[0];
            $components['password'] = $userinfo[1] ?? null;
        }

        $components['host'] = $matches[3];
        $port = $matches[5] ?? null;

        if ($port !== null) {
            $components['port'] = (int) $port;
        }

        return $components;
    }
}
