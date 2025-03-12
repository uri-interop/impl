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

    /**
     * @return array<int, array{string}>
     */
    public static function provideRecomposition() : array
    {
        $users = [
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

        foreach ($users as $user) {
            foreach ($hosts as $host) {
                foreach ($paths as $path) {
                    foreach ($queries as $query) {
                        foreach ($fragments as $fragment) {
                            $expects[] = ["http://{$user}{$host}{$path}{$query}{$fragment}"];
                        }
                    }
                }
            }
        }

        $expects[] = ['foo@example.com'];
        $expects[] = ['mailto:foo@example.com'];
        $expects[] = ['file://path/fo/file.ext'];
        $expects[] = ['file:///path/fo/file.ext'];

        return $expects;
    }
}
