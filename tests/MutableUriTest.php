<?php
declare(strict_types=1);

namespace UriInterop\Impl;

class MutableUriTest extends \PHPUnit\Framework\TestCase
{
    public function test() : void
    {
        $uri = new MutableUri(
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
        $uri->queryParams = ['zim' => 'gir'];
        $uri->queryParams['irk'] = 'doom';

        $expect = 'http://boshag@example.net/path/to/file?zim=gir&irk=doom#results';
        $this->assertSame($expect, (string) $uri);

        $expect = ['zim' => 'gir', 'irk' => 'doom'];
        $this->assertSame($expect, $uri->queryParams);

        $expect = 'boshag';
        $this->assertSame($expect, $uri->userInfo);

        $expect = 'boshag@example.net';
        $this->assertSame($expect, $uri->authority);
    }
}
