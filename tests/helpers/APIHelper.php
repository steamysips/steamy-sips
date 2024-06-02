<?php

declare(strict_types=1);

namespace Steamy\Tests\helpers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

trait APIHelper
{
    private static ?GuzzleClient $guzzle;

    public static function initGuzzle(): void
    {
        // Create a handler stack
        $handlerStack = HandlerStack::create();

        // Add middleware to the handler stack
        $handlerStack->push(Middleware::mapRequest(function ($request) {
            // Add custom header to each request
            return $request->withHeader('X-Test-Env', 'testing');
        }));

        self::$guzzle = new GuzzleClient([
            'base_uri' => $_ENV['API_BASE_URI'],
            'http_errors' => false, // Optionally disable throwing exceptions for HTTP errors
            'handler' => $handlerStack,

        ]);
    }
}