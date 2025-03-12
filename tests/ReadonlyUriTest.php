<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\Uri;

class ReadonlyUriTest extends UriTestCase
{
    /**
     * @return ReadonlyUri
     */
    public function newUri(
        ?string $scheme = null,
        ?string $user = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        string $path = '',
        ?string $query = null,
        ?string $fragment = null,
    ) : Uri
    {
        return new ReadonlyUri(
            scheme: $scheme,
            user: $user,
            password: $password,
            host: $host,
            port: $port,
            path: $path,
            query: $query,
            fragment: $fragment,
        );
    }

    public function test() : void
    {
        $uri = $this->newUri(
            scheme: 'https',
            user: 'boshag',
            password: 'bopass',
            host: 'example.net',
            port: 443,
            path: '/path/to/file',
            query: 'foo=bar&baz=dib',
            fragment: 'results',
        );

        $expect = 'https://boshag:bopass@example.net:443/path/to/file?foo=bar&baz=dib#results';
        $this->assertSame($expect, (string) $uri);

        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->assertSame($expect, $uri->queryParams);

        $expect = 'boshag:bopass';
        $this->assertSame($expect, $uri->userInfo);

        $expect = 'boshag:bopass@example.net:443';
        $this->assertSame($expect, $uri->authority);
    }
}
