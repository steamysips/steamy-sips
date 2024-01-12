<?php
/**
 * Display any data in a formatted block. Use this function
 * for debugging.
 * @param $stuff
 * @return void
 */
function show($stuff): void
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}


/** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
/**
 * Redirects website to a page.
 * @param $path string relative URL of page
 * @return void
 */
function redirect(string $path): void
{
    header("Location: " . ROOT . "/" . $path);
    die;
}
