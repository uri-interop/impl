<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\Uri;

/**
 * @property ReadonlyUriUtility $uriUtility
 */
class ReadonlyUriTest extends UriTestCase
{
    protected function setUp() : void
    {
        $this->uriUtility = new ReadonlyUriUtility();
    }

    public function test() : void
    {
        $uri = $this->uriUtility->newUri(
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
