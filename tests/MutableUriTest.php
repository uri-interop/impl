<?php
declare(strict_types=1);

namespace UriInterop\Impl;

/**
 * @property MutableUriUtility $uriUtility
 */
class MutableUriTest extends UriTestCase
{
    protected function setUp() : void
    {
        $this->uriUtility = new MutableUriUtility();
    }

    public function test() : void
    {
        $uri = $this->uriUtility->newUri(
            scheme: 'https',
            username: 'boshag',
            password: 'bopass',
            host: 'example.net',
            port: 443,
            path: '/path/to/file',
            query: 'foo=bar&baz=dib',
            fragment: 'results',
        );

        $uri->scheme = 'http';
        $uri->password = null;
        $uri->host = 'example.net';
        $uri->port = null;
        $uri->path .= '.ext';
        $uri->queryParams = ['zim' => 'gir'];
        $uri->queryParams['irk'] = 'doom';

        $expect = 'http://boshag@example.net/path/to/file.ext?zim=gir&irk=doom#results';
        $this->assertSame($expect, (string) $uri);

        $expect = '/path/to/file.ext';
        $this->assertSame($expect, $uri->path);

        $expect = ['zim' => 'gir', 'irk' => 'doom'];
        $this->assertSame($expect, $uri->queryParams);

        $expect = 'boshag';
        $this->assertSame($expect, $uri->userinfo);

        $expect = 'boshag@example.net';
        $this->assertSame($expect, $uri->authority);

        $uri->username = null;
        $uri->password = null;
        $uri->path = '/';
        $uri->query = null;
        $uri->fragment = null;
        $expect = 'http://example.net/';
        $this->assertSame($expect, (string) $uri);
    }
}
