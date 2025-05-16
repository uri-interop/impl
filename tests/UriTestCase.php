<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use PHPUnit\Framework\Attributes\DataProvider;
use UriInterop\Interface\UriThrowable;

abstract class UriTestCase extends \PHPUnit\Framework\TestCase
{
    protected UriUtility $uriUtility;

    #[DataProvider('provideParseUri')]
    public function testParseUri(string $expect) : void
    {
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    public function testParseUriWithoutAuthority() : void
    {
        $expect = '/path/to/file.txt';
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    public function testParseUriWithoutCredentials() : void
    {
        $expect = 'http://example.com/path/to/file.txt';
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    #[DataProvider('provideResolveUri')]
    public function testResolveUri(string $relative, string $expect) : void
    {
        $base = $this->uriUtility->parseUri('http://a/b/c/d;p?q');
        $relative = $this->uriUtility->parseUri($relative);
        $expect = $this->uriUtility->parseUri($expect);
        $actual = $this->uriUtility->resolveUri($base, $relative);
        $this->assertSame((string) $expect, (string) $actual);
    }

    public function testResolveUriWithoutScheme() : void
    {
        $base = $this->uriUtility->parseUri('//example.com');
        $relative = $this->uriUtility->parseUri('foo/bar');
        $this->expectException(UriThrowable::class);
        $this->expectExceptionMessage('Expected scheme in base UriStruct, actually missing.');
        $this->uriUtility->resolveUri($base, $relative);
    }

    public function testResolveUriWithAuthorityButNoBasePath() : void
    {
        $base = $this->uriUtility->parseUri('http://example.com');
        $relative = $this->uriUtility->parseUri('foo/bar');
        $expect = $this->uriUtility->parseUri('http://example.com/foo/bar');
        $actual = $this->uriUtility->resolveUri($base, $relative);
        $this->assertSame((string) $expect, (string) $actual);
    }

    public function testResolveUriWithoutAuthorityAndNoRightmostSlash() : void
    {
        $base = $this->uriUtility->parseUri('http:foo');
        $relative = $this->uriUtility->parseUri('bar/baz');
        $expect = $this->uriUtility->parseUri('http:bar/baz');
        $actual = $this->uriUtility->resolveUri($base, $relative);
        $this->assertSame((string) $expect, (string) $actual);
    }

    #[DataProvider('provideNormalizeUri')]
    public function testNormalizeUri(string $uri, string $expect) : void
    {
        $uri = $this->uriUtility->parseUri($uri);
        $expect = $this->uriUtility->parseUri($expect);
        $actual = $this->uriUtility->normalizeUri($uri);
        $this->assertSame((string) $expect, (string) $actual);
    }

    /**
     * @return array<int, array{string}>
     */
    public static function provideParseUri() : array
    {
        $userinfos = [
            '',
            'boshag@',
            'boshag:bopass@',
        ];

        $hosts = [
            'example.com',
            'example.com:8080',
        ];

        $paths = [
            '',
            '/',
            '/foo',
            '/foo/',
        ];

        $queries = [
            '',
            '?',
            '?bar=baz',
        ];

        $fragments = [
            '',
            '#',
            '#dib',
        ];

        $expects = [];

        foreach ($userinfos as $userinfo) {
            foreach ($hosts as $host) {
                foreach ($paths as $path) {
                    foreach ($queries as $query) {
                        foreach ($fragments as $fragment) {
                            $expects[] = ["http://{$userinfo}{$host}{$path}{$query}{$fragment}"];
                        }
                    }
                }
            }
        }

        $expects[] = ['foo@example.com'];
        $expects[] = ['mailto:foo@example.com'];
        $expects[] = ['file://path/to/file.ext'];
        $expects[] = ['file:///path/to/file.ext'];
        $expects[] = ['ftp://ftp.is.co.za/rfc/rfc1808.txt'];
        $expects[] = ['ldap://[2001:db8::7]/c=GB?objectClass=foo'];
        $expects[] = ['news:comp.infosystems.www.servers.unix'];
        $expects[] = ['tel:+1-816-555-1212'];
        $expects[] = ['telnet://192.0.2.16:80/'];
        $expects[] = ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2'];

        return $expects;
    }

    /**
     * @return array<array{string, string}>
     */
    public static function provideResolveUri() : array
    {
        return [
            // normal examples
            ['g:h'           ,  'g:h'],
            ['g'             ,  'http://a/b/c/g'],
            ['./g'           ,  'http://a/b/c/g'],
            ['g/'            ,  'http://a/b/c/g/'],
            ['/g'            ,  'http://a/g'],
            ['//g'           ,  'http://g'],
            ['?y'            ,  'http://a/b/c/d;p?y'],
            ['g?y'           ,  'http://a/b/c/g?y'],
            ['#s'            ,  'http://a/b/c/d;p?q#s'],
            ['g#s'           ,  'http://a/b/c/g#s'],
            ['g?y#s'         ,  'http://a/b/c/g?y#s'],
            [';x'            ,  'http://a/b/c/;x'],
            ['g;x'           ,  'http://a/b/c/g;x'],
            ['g;x?y#s'       ,  'http://a/b/c/g;x?y#s'],
            [''              ,  'http://a/b/c/d;p?q'],
            ['.'             ,  'http://a/b/c/'],
            ['./'            ,  'http://a/b/c/'],
            ['..'            ,  'http://a/b/'],
            ['../'           ,  'http://a/b/'],
            ['../g'          ,  'http://a/b/g'],
            ['../..'         ,  'http://a/'],
            ['../../'        ,  'http://a/'],
            ['../../g'       ,  'http://a/g'],

            // abnormal examples
            ['../../../g'    ,  'http://a/g'],
            ['../../../../g' ,  'http://a/g'],
            ['/./g'          ,  'http://a/g'],
            ['/../g'         ,  'http://a/g'],
            ['g.'            ,  'http://a/b/c/g.'],
            ['.g'            ,  'http://a/b/c/.g'],
            ['g..'           ,  'http://a/b/c/g..'],
            ['..g'           ,  'http://a/b/c/..g'],
            ['./../g'        ,  'http://a/b/g'],
            ['./g/.'         ,  'http://a/b/c/g/'],
            ['g/./h'         ,  'http://a/b/c/g/h'],
            ['g/../h'        ,  'http://a/b/c/h'],
            ['g;x=1/./y'     ,  'http://a/b/c/g;x=1/y'],
            ['g;x=1/../y'    ,  'http://a/b/c/y'],
            ['g?y/./x'       ,  'http://a/b/c/g?y/./x'],
            ['g?y/../x'      ,  'http://a/b/c/g?y/../x'],
            ['g#s/./x'       ,  'http://a/b/c/g#s/./x'],
            ['g#s/../x'      ,  'http://a/b/c/g#s/../x'],
        ];
    }

    /**
     * @return array<array{string, string}>
     */
    public static function provideNormalizeUri() : array
    {
        return [
            ['hTtp://example.com', 'http://example.com'],
            ['hTtp://example.com/', 'http://example.com/'],
            ['https://EXAMPLE.COM/FOO/BAR', 'https://example.com/FOO/BAR'],
            ['/path/%68%65%6c%6c%6f/world', '/path/hello/world'],
            ['/urlencoded/params?chars=' . urlencode('+&=;%20#'), '/urlencoded/params?chars=%2B%26%3D%3B%2520%23'],
            ['File:///SitePages/fi%6ce%20has%20spaces', 'file:///SitePages/file%20has%20spaces'],
            ['/foo/bar/../baz?do=action#showFragment', '/foo/baz?do=action#showFragment'],
            ['http://www.example.com/a%c2%b1b', 'http://www.example.com/a%C2%B1b'],
            ['http://example.com:80/file?query=bar', 'http://example.com:80/file?query=bar'],
            [
                'HtTpS://MaStEr.B%c3%A9b%c3%a9.eXaMpLe.CoM:/%7ejohndoe/%a1/in+dex.php?f%C3%A0o.%bar=v%61lue#fragment',
                'https://master.b%c3%a9b%c3%a9.example.com/~johndoe/%A1/in+dex.php?f%C3%A0o.%BAr=value#fragment',
            ],

            // unusual
            ['http:../', 'http:'],
            ['http:./', 'http:'],
            ['http:..', 'http:'],
            ['http:.', 'http:'],
        ];
    }
}
