<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\Uri;

class ImmutableUriTest extends UriTestCase
{
    /**
     * @return ImmutableUri
     */
    public function newUri(
        ?string $scheme = null,
        ?string $user = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        ?string $path = null,
        ?string $query = null,
        ?string $fragment = null,
    ) : Uri
    {
        return new ImmutableUri(
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

        $uri = $uri
            ->withScheme('http')
            ->withUser(null)
            ->withPassword(null)
            ->withHost('example.net')
            ->withPort(null)
            ->withPathSegments(['path', 'to', 'other'])
            ->withQueryParams(['zim' => 'gir', 'irk' => 'doom'])
            ->withFragment(null);

        $expect = 'http://example.net/path/to/other?zim=gir&irk=doom';
        $this->assertSame($expect, (string) $uri);

        $uri = $uri->withPath('/yet/another/file');
        $expect = ['yet', 'another', 'file'];
        $this->assertSame($expect, $uri->pathSegments);

        $uri = $uri->withQuery('foo=bar&baz=dib');
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->assertSame($expect, $uri->queryParams);
    }
}
