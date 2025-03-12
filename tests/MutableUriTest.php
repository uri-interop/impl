<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\Uri;

class MutableUriTest extends UriTestCase
{
    /**
     * @return MutableUri
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
        return new MutableUri(
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

        $uri->scheme = 'http';
        $uri->password = '';
        $uri->host = 'example.net';
        $uri->port = null;
        $uri->pathSegments[2] = 'file.ext';
        $uri->queryParams = ['zim' => 'gir'];
        $uri->queryParams['irk'] = 'doom';

        $expect = 'http://boshag@example.net/path/to/file.ext?zim=gir&irk=doom#results';
        $this->assertSame($expect, (string) $uri);

        $expect = ['path', 'to', 'file.ext'];
        $this->assertSame($expect, $uri->pathSegments);

        $expect = ['zim' => 'gir', 'irk' => 'doom'];
        $this->assertSame($expect, $uri->queryParams);

        $expect = 'boshag';
        $this->assertSame($expect, $uri->userInfo);

        $expect = 'boshag@example.net';
        $this->assertSame($expect, $uri->authority);

        $uri->user = '';
        $uri->password = '';
        $uri->path = '/';
        $uri->query = null;
        $uri->fragment = null;
        $expect = 'http://example.net/';
        $this->assertSame($expect, (string) $uri);
    }
}
