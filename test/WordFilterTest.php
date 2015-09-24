<?php

namespace WordFilter;

class ListTest extends \PHPUnit_Framework_TestCase
{
    public $words;

    public function setUp()
    {
        $wordListFile = __DIR__ . '/../lib/badwords.json';
        $this->words = json_decode(
            file_get_contents($wordListFile),
            true
        );
    }

    public function testCreateBuildsList()
    {
        $wordFilter = WordFilter::create();
        $this->assertInstanceOf('\WordFilter\WordFilter', $wordFilter);
        $this->assertEquals($this->words, $wordFilter->words);
    }

    public function testMissingWordlistSetsEmptyList()
    {
        $wordFilter = WordFilter::create("file_does_not_exist.json");
        $this->assertEquals(array(), $wordFilter->words);
    }

    public function testWordlistBuildsRegex()
    {
        $wordFilter = new WordFilter(array("elephant", "zebra"));
        $this->assertEquals('/elephant|zebra/', $wordFilter->regEx);
    }

    public function testBlacklistedReturnsExpectedResult()
    {
        $wordFilter = new WordFilter(array("zebra"));
        $this->assertTrue($wordFilter->blacklisted("this string contains zebra"));
        $this->assertFalse($wordFilter->blacklisted("this string contains elephant"));
    }

    public function testAddWordsToListAddsWords()
    {
        $wordFilter = new WordFilter(array("octopus", "zebra"));
        $wordFilter->addWords(array("turkey", "zebra"));
        $this->assertEquals(array("turkey", "zebra", "octopus"), $wordFilter->words);
    }

    public function testRemoveWordRemovesWord()
    {
        $wordFilter = new WordFilter(array("zebra", "elephant"));
        $wordFilter->removeWord("zebra");
        $this->assertEquals(array("elephant"), $wordFilter->words);
    }

    public function testClearListClearsList()
    {
        $wordFilter = new WordFilter(array("zebra", "elephant"));
        $wordFilter->clearList();
        $this->assertEquals(array(), $wordFilter->words);
    }
}
