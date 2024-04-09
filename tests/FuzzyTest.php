<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Steamy\Core\Utility;

class FuzzyTest extends TestCase
{
    public function testFuzzySearch(): void
    {
        $strings = ['Espresso', 'Cappuccino', 'Latte', 'Americano', 'Mocha'];
        $searchTerm = 'Espreso';
        $threshold = 1;

        $result = Utility::fuzzySearch($searchTerm, $strings, $threshold);

        $this->assertContains('Espresso', $result);
        $this->assertNotContains('Mocha', $result);
    }

    public function testLevenshteinDistance(): void
    {
        $str1 = 'Almond';
        $str2 = 'Coconut';

        $result = Utility::levenshteinDistance($str1, $str2);

        $this->assertEquals(5, $result);
    }
}
