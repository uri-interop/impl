<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use UriInterop\Interface\UriTypeAliases;
use UriInterop\Interface\Uri;

/**
 * @phpstan-import-type parse_url_array from UriTypeAliases
 */
abstract class UriTestCase extends \PHPUnit\Framework\TestCase
{
    protected UriUtility $uriUtility;

    #[\PHPUnit\Framework\Attributes\DataProvider('provideRecomposition')]
    public function testRecomposition(string $expect) : void
    {
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    public function testNoAuthority() : void
    {
        $expect = '/path/to/file.txt';
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    public function testNoCredentials() : void
    {
        $expect = 'http://example.com/path/to/file.txt';
        $actual = (string) $this->uriUtility->parseUri($expect);
        $this->assertSame($expect, $actual);
    }

    /**
     * @return array<int, array{string}>
     */
    public static function provideRecomposition() : array
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
}
