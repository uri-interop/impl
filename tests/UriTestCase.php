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
    abstract public function newUri(
        ?string $scheme = null,
        ?string $user = null,
        ?string $password = null,
        ?string $host = null,
        ?int $port = null,
        ?string $path = null,
        ?string $query = null,
        ?string $fragment = null,
    ) : Uri;

    #[\PHPUnit\Framework\Attributes\DataProvider('provideRecomposition')]
    public function testRecomposition(string $expect) : void
    {
        /** @var parse_url_array $parsed */
        $parsed = parse_url($expect);

        if (array_key_exists('pass', $parsed)) {
            $parsed['password'] = $parsed['pass'];
            unset($parsed['pass']);
        }

        $actual = $this->newUri(...$parsed);
        $this->assertSame($expect, (string) $actual);
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

        $urls = [];

        foreach ($users as $user) {
            foreach ($hosts as $host) {
                foreach ($paths as $path) {
                    foreach ($queries as $query) {
                        foreach ($fragments as $fragment) {
                            $urls[] = ["http://{$user}{$host}{$path}{$query}{$fragment}"];
                        }
                    }
                }
            }
        }

        return $urls;
    }
}
