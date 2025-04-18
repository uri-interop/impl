<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriThrowable;
use UriInterop\Interface\UriTypeAliases;

/**
 * @phpstan-import-type query_params_array from UriTypeAliases
 *
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
            username: 'boshag',
            password: 'bopass',
            host: 'example.net',
            port: 443,
            path: '/path/to/file',
            query: 'foo=bar&baz=dib',
            fragment: 'results',
        );

        $uri = $uri
            ->withScheme('http')
            ->withUsername(null)
            ->withPassword(null)
            ->withHost('example.net')
            ->withPort(null)
            ->withPath('/path/to/other')
            ->withQueryParams(['zim' => 'gir', 'irk' => ['gaz' => 'doom']])
            ->withFragment(null);

        $expect = 'http://example.net/path/to/other?zim=gir&irk%5Bgaz%5D=doom';
        $this->assertSame($expect, (string) $uri);

        $uri = $uri->withQuery('foo=bar&baz=dib');
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->assertSame($expect, $uri->queryParams);
    }

    public function testMutableNotAllowed() : void
    {
        $uri = $this->uriUtility->newUri();
        $this->expectException(UriThrowable::class);
        $this->expectExceptionMessage('Immutable values must be null, scalar, or array.');

        /** @phpstan-ignore argument.type */
        $uri->withQueryParams([(object) []]);
    }
}
