<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Core\Utility;

class FuzzyTest extends TestCase
{
    public function testFuzzySearch(): void
    {
        $strings = ['Espresso', 'Cappuccino', 'Latte', 'Americano', 'Mocha'];

        // Test with normal data (correct spelling)
        $searchTerm = 'Espresso';
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertContains('Espresso', $result);
        $this->assertNotContains('Mocha', $result);

        // Test with misspelling within threshold
        $searchTerm = 'Espreso'; // Missing 's'
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertContains('Espresso', $result);

        // Test with misspelling exceeding threshold
        $searchTerm = 'Espressso'; // Extra 's'
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertContains('Espresso', $result);

        // Test with empty search term
        $searchTerm = '';
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertEquals([], $result); // Expect no matches

        // Test with non-string search term (integer)
        $searchTerm = (string) 123;
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertEquals([], $result); // Expect no matches (type mismatch)

        // Test with search term containing special characters
        $searchTerm = 'Latte!';
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertContains('Latte', $result); // Should still match 'Latte'

        // Test with case sensitivity (optional, depending on your needs)
        $searchTerm = 'eSPRESSO'; // All uppercase
        $threshold = 1;
        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);
        // Depending on your implementation, this might match (case-insensitive)
        // or not match (case-sensitive). Update assertions accordingly.

    }

    public function testLevenshteinDistance(): void
    {
        $str1 = 'Almond';
        $str2 = 'Coconut';

        $result = Utility::levenshteinDistance($str1, $str2);

        $this->assertEquals(5, $result);
    }
}
