<?php

declare(strict_types=1);

namespace Tests\Unit;

use JevPHP\Exception\FormatException;
use JevPHP\Utility;

it('can decode a valid json string', function () {
    $json = '{"name":"jev-php","version":1}';

    $decoded = Utility::decodeJson($json);

    expect($decoded)->toBe([
        'name' => 'jev-php',
        'version' => 1,
    ]);
});

it('throws a format exception when json is invalid', function () {
    expect(fn () => Utility::decodeJson('{"invalid": }'))
        ->toThrow(FormatException::class, 'JSON error decoding:');
});

it('reads environment values using getenv, then _ENV, then _SERVER', function () {
    $name = 'JEVPHP_TEST_ENV_'.uniqid();

    putenv($name);
    unset($_ENV[$name], $_SERVER[$name]);

    $_SERVER[$name] = 'server-value';
    expect(Utility::readEnvironment($name))->toBe('server-value');

    $_ENV[$name] = 'env-value';
    expect(Utility::readEnvironment($name))->toBe('env-value');

    putenv("$name=getenv-value");
    expect(Utility::readEnvironment($name))->toBe('getenv-value');

    putenv($name);
    unset($_ENV[$name], $_SERVER[$name]);
});

it('returns default value when environment values are missing or blank', function () {
    $name = 'JEVPHP_TEST_DEFAULT_'.uniqid();

    putenv($name);
    $_ENV[$name] = '   ';
    $_SERVER[$name] = '';

    expect(Utility::readEnvironment($name, 'fallback'))->toBe('fallback');

    unset($_ENV[$name], $_SERVER[$name]);
});
