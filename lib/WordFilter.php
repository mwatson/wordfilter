<?php

namespace WordFilter;

class WordFilter
{
    public static $badWordsFile = "badwords.json";

    public $regEx;
    public $words = array();

    public static function create($wordsFile = "badwords.json")
    {
        $words = @file_get_contents(__DIR__ . "/{$wordsFile}");
        if ($words === false) {
            $words = "[]";
        }
        $words = json_decode($words, true);

        return new Static($words);
    }

    // pass in array of words
    public function __construct($wordlist)
    {
        if (is_array($wordlist)) {
            $this->words = $wordlist;
        }
        $this->rebuildRegex();
    }

    public function rebuildRegex()
    {
        $this->regEx = '/' . strtolower(implode('|', $this->words)) . '/';
    }

    public function blacklisted($string)
    {
        return !!preg_match($this->regEx, $string);
    }

    public function addWords($words)
    {
        $this->words = array_keys(
            array_merge(
                array_flip($words),
                array_flip($this->words)
            )
        );
        $this->rebuildRegex();
    }

    public function removeWord($word)
    {
        $wordLookup = array_flip($this->words);
        if (isset($wordLookup[$word])) {
            unset($wordLookup[$word]);
            $this->words = array_keys($wordLookup);
            $this->rebuildRegex();
        }
    }

    public function clearList()
    {
        $this->words = array();
        $this->rebuildRegex();
    }
}
