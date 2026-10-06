<?php

declare(strict_types=1);

namespace JevPHP\Classification;

use JevPHP\Utility;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class LayaConfig
{
    const LATEST = 'jev-latest';

    public readonly string $apiKey;

    public readonly string $url;

    public readonly string $model;

    public function __construct(
        ?string $apiKey = null,
        ?string $url = null,
        public readonly ?ClientInterface $client = null,
        public readonly ?RequestFactoryInterface $requestFactory = null,
        public readonly ?StreamFactoryInterface $streamFactory = null,
    ) {
        $this->apiKey = $apiKey
            ?? (string) Utility::readEnvironment('LAYA_API_KEY', '');
        $this->url = $url
            ?? (string) Utility::readEnvironment('LAYA_URL', '');
        $this->model = self::LATEST;
    }
}
