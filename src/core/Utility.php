<?php

declare(strict_types=1);

namespace Steamy\Core;

class Utility
{
    /**
     * Display any data in a formatted block. Use this function
     * for debugging.
     * @param $stuff
     * @return void
     */
    public static function show($stuff): void
    {
        echo "<pre>";
        print_r($stuff);
        echo "</pre>";
    }

    /**
     * Splits the URL into an array of segments.
     *
     * This function retrieves the 'url' parameter from the $_GET array or defaults to 'home',
     * trims leading and trailing slashes, and then explodes the URL into an array of segments.
     *
     * @return array An array containing the URL segments.
     */
    public static function splitURL(): array
    {
        $URL = $_GET['url'] ?? 'home';
        return explode("/", trim($URL, '/'));
    }

    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
    /**
     * Redirects website to a page.
     * @param $path string relative URL of page
     * @return void
     */
    public static function redirect(string $path): void
    {
        header("Location: " . ROOT . "/" . $path);
        die();
    }
}
