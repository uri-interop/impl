<?php
declare(strict_types=1);

namespace UriInterop\Impl;

class ImmutableUriTest extends \PHPUnit\Framework\TestCase
{
    public function test() : void
    {
        $uri = new ImmutableUri(
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
            ->withUser('')
            ->withPassword('')
            ->withHost('example.net')
            ->withPort(null)
            ->withPath('/path/to/other')
            ->withQueryParams(['zim' => 'gir', 'irk' => 'doom'])
            ->withFragment('');

        $expect = 'http://example.net/path/to/other?zim=gir&irk=doom';
        $this->assertSame($expect, (string) $uri);

        $uri = $uri->withQuery('foo=bar&baz=dib');
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->assertSame($expect, $uri->queryParams);
    }
}
