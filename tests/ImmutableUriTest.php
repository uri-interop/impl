<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\Uri;

/**
 * @property ImmutableUriUtility $uriUtility
 */
class ImmutableUriTest extends UriTestCase
{
    protected function setUp() : void
    {
        $this->uriUtility = new ImmutableUriUtility();
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

        $uri = $uri
            ->withScheme('http')
            ->withUser(null)
            ->withPassword(null)
            ->withHost('example.net')
            ->withPort(null)
            ->withPath('/path/to/other')
            ->withQueryParams(['zim' => 'gir', 'irk' => 'doom'])
            ->withFragment(null);

        $expect = 'http://example.net/path/to/other?zim=gir&irk=doom';
        $this->assertSame($expect, (string) $uri);

        $uri = $uri->withQuery('foo=bar&baz=dib');
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->assertSame($expect, $uri->queryParams);
    }
}
