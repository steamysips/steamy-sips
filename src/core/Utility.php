<?php

declare(strict_types=1);

namespace Steamy\Core;

/**
 * Utility class containing various helper functions.
 */
class Utility
{
    /**
     * Displays data in a formatted block. Use this function
     * for debugging.
     * @param $stuff mixed some data
     * @return void
     */
    public static function show(mixed $stuff): void
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
     * @return string[] An array containing the URL segments where each segment is in lowercase.
     */
    public static function splitURL(): array
    {
        $URL = $_GET['url'] ?? 'home';
        return explode("/", trim($URL, '/'));
    }

    /** @noinspection PhpNoReturnAttributeCanBeAddedInspection */
    /**
     * Redirects user to a page and ends execution of script.
     * - `redirect('home')` redirects to `ROOT`.
     * - `redirect('shop/products/1')` redirects to `ROOT/shop/products/1`.
     *
     * @param $relative_url string root-relative URL of page. It must not start with /.
     * @return void
     */
    public static function redirect(string $relative_url): void
    {
        header("Location: " . ROOT . "/" . $relative_url);
        die();
    }

    /**
     * Perform fuzzy search on an array of strings.
     *
     * This function takes a search term and an array of strings, and returns
     * an array of strings from the input array that closely match the search term.
     * It uses the Levenshtein distance algorithm to determine the similarity between
     * the search term and each string in the array.
     *
     * @param string $searchTerm The term to search for.
     * @param array $strings The array of strings to search within.
     * @param int $threshold The maximum allowed Levenshtein distance.
     * @return array An array of matching strings.
     */
    public static function fuzzySearch(string $searchTerm, array $strings, int $threshold = 3): array
    {
        $matches = [];
        foreach ($strings as $string) {
            $distance = self::levenshteinDistance(strtolower($searchTerm), strtolower($string));
            if ($distance <= $threshold) {
                $matches[] = $string;
            }
        }
        return $matches;
    }


    /**
     * Calculates the Levenshtein distance between two strings.
     *
     * The Levenshtein distance is a metric to measure the difference between two strings.
     * It is the minimum number of single-character edits (insertions, deletions, or replaces)
     * required to change one word into the other.
     *
     * @param string $str1 The first string.
     * @param string $str2 The second string.
     * @return int The Levenshtein distance between the two strings.
     */
    public static function levenshteinDistance(string $str1, string $str2): int
    {
        $m = strlen($str1);
        $n = strlen($str2);

        // Initialize a 2D array to store the distances
        $dp = [];
        for ($i = 0; $i <= $m; $i++) {
            $dp[$i] = [];
            for ($j = 0; $j <= $n; $j++) {
                $dp[$i][$j] = 0;
            }
        }

        // Fill the first row and column of the array
        for ($i = 0; $i <= $m; $i++) {
            $dp[$i][0] = $i;
        }
        for ($j = 0; $j <= $n; $j++) {
            $dp[0][$j] = $j;
        }

        // Calculate distances using dynamic programming
        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $cost = $str1[$i - 1] === $str2[$j - 1] ? 0 : 1;
                $dp[$i][$j] = min(
                    $dp[$i - 1][$j] + 1, // deletion
                    $dp[$i][$j - 1] + 1, // insertion
                    $dp[$i - 1][$j - 1] + $cost // replace
                );
            }
        }

        // Return the final result, which is the distance between the two strings
        return $dp[$m][$n];
    }
}
