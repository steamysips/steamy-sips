<?php

declare(strict_types=1);

namespace models;

use PHPUnit\Framework\TestCase;
use Steamy\Core\Utility;

class FuzzyTest extends TestCase
{
    /**
     * @dataProvider fuzzySearchDataProvider
     */
    public function testFuzzySearch(string $searchTerm, array $strings, int $threshold, array $expected): void
    {
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);
        $this->assertEquals($expected, $result);
    }

    public static function fuzzySearchDataProvider(): array
    {
        $strings = ['Espresso', 'Cappuccino', 'Latte', 'Americano', 'Mocha'];
        return [
            ['Espresso', $strings, 1, ['Espresso']],
            ['Espreso', $strings, 1, ['Espresso']], // Missing 's'
            ['Espressso', $strings, 1, ['Espresso']], // Extra 's'
            ['', $strings, 1, []], // Empty search term
            ["123", $strings, 1, []], // Non-string search term (integer)
            ['Latte!', $strings, 1, ['Latte']], // Search term containing special characters
            ['eSPRESSO', $strings, 1, ['Espresso']], // Case sensitivity test
        ];
    }

    /**
     * @dataProvider levenshteinDistanceDataProvider
     */
    public function testLevenshteinDistance(string $str1, string $str2, int $expected): void
    {
        $result = Utility::levenshteinDistance($str1, $str2);
        $this->assertEquals($expected, $result);
    }

    public static function levenshteinDistanceDataProvider(): array
    {
        return [
            ['Almond', 'Coconut', 5],
            ['Almond', 'Almond', 0], // Same strings
            ['Almond', 'Almon', 1], // Missing character
            ['Almond', 'Almondd', 1], // Extra character
            ['Almond', 'Almend', 1], // Different character
            ['Almond', '', 6], // One string is empty
            ['', '', 0], // Both strings are empty
        ];
    }
}
