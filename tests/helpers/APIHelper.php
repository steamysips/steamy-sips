<?php

declare(strict_types=1);

namespace Steamy\Tests\helpers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

trait APIHelper
{
    private static ?GuzzleClient $guzzle;

    /**
     * Initializes static variable $guzzle so that API requests can be made.
     * @return void
     */
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
            'http_errors' => false, // disable throwing exceptions for HTTP errors
            'handler' => $handlerStack,
        ]);
    }

    /**
     * Logs data in JSON format in terminal. Use for debugging only.
     * @param $data
     * @return void
     */
    public static function log_json($data): void
    {
        error_log(json_encode($data, JSON_PRETTY_PRINT));
    }
}