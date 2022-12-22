<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function buildSearchEngine;

final class SearchEngineTest extends TestCase
{
    // public function testSimpleSearch(): void
    // {
    //     $docs = [
    //         ['id' => 'doc1', 'text' => "I can't shoot straight unless I've had a pint!"],
    //         ['id' => 'doc2', 'text' => "Don't shoot shoot shoot that thing at me."],
    //         ['id' => 'doc3', 'text' => "I'm your shooter."],
    //     ];
    //     $searchEngine = buildSearchEngine($docs);

    //     $this->assertSame(['doc2', 'doc1'], $searchEngine->search('shoot'));
    // }

    // public function testSearchWord(): void
    // {
    //     $docs = [
    //         ['id' => 'doc1', 'text' => "I can't shoot straight unless I've had a pint!"],
    //     ];
    //     $searchEngine = buildSearchEngine($docs);

    //     $this->assertSame(['doc1'], $searchEngine->search('pint'));
    //     $this->assertSame(['doc1'], $searchEngine->search('pint!'));
    // }

    public function testSearchMultiple(): void
    {
        $docs = [
            ['id' => 'doc1', 'text' => "I can't shoot straight unless I've had a pint!"],
            ['id' => 'doc2', 'text' => "Don't shoot shoot shoot that thing at me."],
            ['id' => 'doc3', 'text' => "I'm your shooter."],
        ];
        $searchEngine = buildSearchEngine($docs);
        $actual = $searchEngine->search('shoot at me');
        echo json_encode($actual, JSON_PRETTY_PRINT);
        // $this->assertSame(['doc2', 'doc1'], $searchEngine->search('shoot at me'));
    }
}
