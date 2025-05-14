<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Stringable;
use UriInterop\Interface\UriStruct;
use UriInterop\Interface\UriTypeAliases;

/**
 * @phpstan-import-type composed_string from UriTypeAliases
 * @phpstan-import-type percent_composed_string from UriTypeAliases
 * @phpstan-import-type percent_encoded_string from UriTypeAliases
 */
class UriComponents
{
    protected const array DECODE_UNRESERVED_CHARS = [
        '%2D' => '-',
        '%2E' => '.',
        '%30' => '0',
        '%31' => '1',
        '%32' => '2',
        '%33' => '3',
        '%34' => '4',
        '%35' => '5',
        '%36' => '6',
        '%37' => '7',
        '%38' => '8',
        '%39' => '9',
        '%41' => 'A',
        '%42' => 'B',
        '%43' => 'C',
        '%44' => 'D',
        '%45' => 'E',
        '%46' => 'F',
        '%47' => 'G',
        '%48' => 'H',
        '%49' => 'I',
        '%4A' => 'J',
        '%4B' => 'K',
        '%4C' => 'L',
        '%4D' => 'M',
        '%4E' => 'N',
        '%4F' => 'O',
        '%50' => 'P',
        '%51' => 'Q',
        '%52' => 'R',
        '%53' => 'S',
        '%54' => 'T',
        '%55' => 'U',
        '%56' => 'V',
        '%57' => 'W',
        '%58' => 'X',
        '%59' => 'Y',
        '%5A' => 'Z',
        '%5F' => '_',
        '%61' => 'a',
        '%62' => 'b',
        '%63' => 'c',
        '%64' => 'd',
        '%65' => 'e',
        '%66' => 'f',
        '%67' => 'g',
        '%68' => 'h',
        '%69' => 'i',
        '%6A' => 'j',
        '%6B' => 'k',
        '%6C' => 'l',
        '%6D' => 'm',
        '%6E' => 'n',
        '%6F' => 'o',
        '%70' => 'p',
        '%71' => 'q',
        '%72' => 'r',
        '%73' => 's',
        '%74' => 't',
        '%75' => 'u',
        '%76' => 'v',
        '%77' => 'w',
        '%78' => 'x',
        '%79' => 'y',
        '%7A' => 'z',
        '%7E' => '~',
    ];

    /**
     * @param ?percent_encoded_string $username
     * @param ?percent_encoded_string $password
     * @param ?percent_composed_string $host
     * @param percent_composed_string $path
     * @param ?composed_string $query
     * @param ?percent_composed_string $fragment
     */
    public function __construct(
        public ?string $scheme = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $host = null,
        public ?int $port = null,
        public string $path = '',
        public ?string $query = null,
        public ?string $fragment = null,
    ) {
    }

    /**
     * @return array{
     *     scheme: ?string,
     *     username: ?percent_encoded_string,
     *     password: ?percent_encoded_string,
     *     host: ?percent_composed_string,
     *     port: ?int,
     *     path: percent_composed_string,
     *     query: ?composed_string,
     *     fragment: ?percent_composed_string,
     * }
     */
    public function asArray()
    {
        return (array) $this;
    }

    /**
     * The algorithm herein is taken directly from
     * cf. <https://datatracker.ietf.org/doc/html/rfc3986/#appendix-B>.
     */
    public static function newFromParsed(string|Stringable $uriString) : UriComponents
    {
        $components = new UriComponents();

        preg_match(
            // 12            3  4          5       6  7        8 9
            '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~',
            //           2 1            43        5         76      98
            (string) $uriString,
            $matches,
            PREG_UNMATCHED_AS_NULL,
        );

        $matches += array_fill(1, 9, null);
        $components->scheme = $matches[2];
        static::parseAuthority($matches[4], $components);
        $components->path = $matches[5] ?? '';
        $components->query = $matches[7];
        $components->fragment = $matches[9];
        return $components;
    }

    protected static function parseAuthority(
        ?string $authority,
        UriComponents $components
    ) : void
    {
        if ($authority === null) {
            return;
        }

        if ($authority === '') {
            $components->host = $authority;
            return;
        }

        preg_match(
            // 12         3    4 5
            '~^(([^@]*)@)?(.+?)(:(\d*))?$~',
            //        2 1     3      54
            $authority,
            $matches,
            PREG_UNMATCHED_AS_NULL
        );

        $matches += array_fill(1, 5, null);

        if ($matches[1]) {
            $userinfo = explode(':', (string) $matches[2]);
            $components->username = $userinfo[0];
            $components->password = $userinfo[1] ?? null;
        }

        $components->host = $matches[3];

        if (! empty($matches[5])) {
            $components->port = (int) $matches[5];
        }
    }

    /**
     * The algorithm herein is taken directly from
     * <https://datatracker.ietf.org/doc/html/rfc3986/#section-5.2>.
     */
    public static function newFromResolved(
        UriStruct $relative,
        UriStruct $base
    ) : UriComponents
    {
        // <https://datatracker.ietf.org/doc/html/rfc3986/#section-5.2.1>:
        // Note that only the scheme component is required to be
        // present in a base URI; the other components may be empty or
        // undefined.
        if (trim((string) $base->scheme) === '') {
            throw new UriException('Expected scheme in base UriStruct, actually missing.');
        }

        $target = new UriComponents();

        if ($relative->scheme !== null) {
            $target->scheme = $relative->scheme;
            $target->username = $relative->username;
            $target->password = $relative->password;
            $target->host = $relative->host;
            $target->port = $relative->port;
            $target->path = static::removeDotSegments($relative->path);
            $target->query = $relative->query;
        } else {
            if ($relative->authority !== null) {
                $target->username = $relative->username;
                $target->password = $relative->password;
                $target->host = $relative->host;
                $target->port = $relative->port;
                $target->path = static::removeDotSegments($relative->path);
                $target->query = $relative->query;
            } else {
                if ($relative->path === '') {
                    $target->path = $base->path;
                    if ($relative->query !== null) {
                        $target->query = $relative->query;
                    } else {
                        $target->query = $base->query;
                    }
                } else {
                    if (str_starts_with($relative->path, '/')) {
                        $target->path = static::removeDotSegments($relative->path);
                    } else {
                        $target->path = static::mergePaths($relative, $base);
                        $target->path = static::removeDotSegments($target->path);
                    }

                    $target->query = $relative->query;
                }

                $target->username = $base->username;
                $target->password = $base->password;
                $target->host = $base->host;
                $target->port = $base->port;
            }

            $target->scheme = $base->scheme;
        }

        $target->fragment = $relative->fragment;
        return $target;
    }

    /**
     * The algorithm herein is taken directly from
     * <https://datatracker.ietf.org/doc/html/rfc3986/#section-5.2.3>.
     */
    protected static function mergePaths(UriStruct $relative, UriStruct $base) : string
    {
        // If the base URI has a defined authority component and an empty
        // path, then return a string consisting of "/" concatenated with the
        // reference's path; otherwise,
        if ($base->authority !== null && $base->path === '') {
            return '/' . $relative->path;
        }

        // return a string consisting of the reference's path component
        // appended to all but the last segment of the base URI's path (i.e.,
        // excluding any characters after the right-most "/" in the base URI
        // path, or excluding the entire base URI path if it does not contain
        // any "/" characters).
        $rightMostSlash = strrpos($base->path, '/');

        if ($rightMostSlash === false) {
            return $relative->path;
        }

        return substr($base->path, 0, $rightMostSlash) . '/' . $relative->path;
    }

    /**
     * The algorithm herein is taken directly from
     * <https://datatracker.ietf.org/doc/html/rfc3986/#section-5.2.4>.
     */
    protected static function removeDotSegments(string $input) : string
    {
        // 1.  The input buffer is initialized with the now-appended path
        //     components and the output buffer is initialized to the empty
        //     string.
        $output = '';

        // 2.  While the input buffer is not empty, loop as follows:
        while ($input !== '') {
            // A.  If the input buffer begins with a prefix of "../" or "./",
            //     then remove that prefix from the input buffer; otherwise,
             if (str_starts_with($input, '../')) {
                $input = substr($input, 3);
                continue;
            } elseif (str_starts_with($input, './')) {
                $input = substr($input, 2);
                continue;
            }

            // B.  if the input buffer begins with a prefix of "/./" or "/.",
            //     where "." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer; otherwise,
            if (str_starts_with($input, '/./')) {
                $input = '/' . substr($input, 3);
                continue;
            } elseif ($input === '/.') {
                $input = '/';
                continue;
            }

            // C.  if the input buffer begins with a prefix of "/../" or "/..",
            //     where ".." is a complete path segment, then replace that
            //     prefix with "/" in the input buffer and remove the last
            //     segment and its preceding "/" (if any) from the output
            //     buffer; otherwise,
            if (str_starts_with($input, '/../')) {
                $input = '/' . substr($input, 4);
                static::removeLastSegment($output);
                continue;
            } elseif ($input === '/..') {
                $input = '/' . substr($input, 3);
                static::removeLastSegment($output);
                continue;
            }

            // D.  if the input buffer consists only of "." or "..", then remove
            //     that from the input buffer; otherwise,
            if ($input === '.' || $input === '..') {
                $input = '';
                continue;
            }

            // E.  move the first path segment in the input buffer to the end of
            //     the output buffer, including the initial "/" character (if
            //     any) and any subsequent characters up to, but not including,
            //     the next "/" character or the end of the input buffer.
            $pos = strpos($input, '/', 1);

            if ($pos === false) {
                $output .= $input;
                $input = '';
                continue;
            }

            $output .= substr($input, 0, $pos);
            $input = substr($input, $pos);
        }

        // 3.  Finally, the output buffer is returned as the result.
        return $output;
    }

    protected static function removeLastSegment(string &$output) : void
    {
        $pos = strrpos($output, '/');

        if ($pos === false) {
            $output = '';
            return;
        }

        $output = substr($output, 0, $pos);
    }

    /**
     * https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2
     */
    public static function newFromNormalized(UriStruct $uri) : UriComponents
    {
        return new UriComponents(
            scheme: static::normalizeScheme($uri->scheme),
            username: static::normalizeUsername($uri->username),
            password: static::normalizePassword($uri->password),
            host: static::normalizeHost($uri->host),
            port: $uri->port,
            path: static::normalizePath($uri->path),
            query: static::normalizeQuery($uri->query),
            fragment: static::normalizeFragment($uri->fragment),
        );
    }

    // https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2.1
    public static function normalizeScheme(?string $scheme) : ?string
    {
        return $scheme === null ? null : strtolower($scheme);
    }

    // https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2.1
    public static function normalizeHost(?string $host) : ?string
    {
        return $host === null ? null : strtolower($host);
    }

    public static function normalizeUsername(?string $username) : ?string
    {
        return $username === null ? null : static::normalizeEncodedChars($username);
    }

    public static function normalizePassword(?string $password) : ?string
    {
        return $password === null ? null : static::normalizeEncodedChars($password);
    }

    // https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2.3
    public static function normalizePath(string $path) : string
    {
        return (string) static::normalizeEncodedChars(static::removeDotSegments($path));
    }

    public static function normalizeQuery(?string $query) : ?string
    {
        return $query === null ? null : static::normalizeEncodedChars($query);
    }

    public static function normalizeFragment(?string $fragment) : ?string
    {
        return $fragment === null ? null : static::normalizeEncodedChars($fragment);
    }

    /**
     * https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2.1
     * https://datatracker.ietf.org/doc/html/rfc3986/#section-6.2.2.2
     */
    protected static function normalizeEncodedChars(?string $input) : ?string
    {
        if ($input === null) {
            return null;
        }

        $input = preg_replace_callback(
            '/%[0-9a-f][0-9a-f]/i',
            fn (array $matches) : string => strtoupper($matches[0]),
            $input,
        );

        assert(is_string($input));
        return strtr($input, static::DECODE_UNRESERVED_CHARS);
    }
}
