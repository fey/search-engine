<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function buildSearchEngine;

final class FinalSearchTest extends TestCase
{
    public function testSearch(): void
    {
        $searchText = 'trash island';
        $docs = collect([
        ['id' => 'garbage_patch_NG'],
        ['id' => 'garbage_patch_ocean_clean'],
        ['id' => 'garbage_patch_wiki'],
        ])
        ->map(function ($doc) {
            return [
            ...$doc,
            'text' => $this->readFixture($doc['id']),
            ];
        });
        $searchEngine = buildSearchEngine($docs->toArray());
        $expected = $docs->pluck('id')->toArray();

        $this->assertSame($expected, $searchEngine->search($searchText));
    }

    public function testSearchWithSpam(): void
    {
        $searchText = 'the trash island is a';
        $docs = collect([
            ['id' => 'garbage_patch_NG'],
            ['id' => 'garbage_patch_ocean_clean'],
            ['id' => 'garbage_patch_wiki'],
            ['id' => 'garbage_patch_spam'],
        ])->map(fn ($doc) => [...$doc, 'text' => $this->readFixture($doc['id'])]);
        $searchEngine = buildSearchEngine($docs->toArray());
        $expected = $docs->pluck('id')->toArray();

        $this->assertSame($expected, $searchEngine->search($searchText));
    }

    public function testSearchEmpty(): void
    {
        $searchText = '';
        $docs = [];
        $searchEngine = buildSearchEngine($docs);
        $expected = [];

        $this->assertSame($expected, $searchEngine->search($searchText));
    }


    public function testSearchShortStrings(): void
    {
        $doc1 = "I can't shoot straight unless I've had a pint!";
        $doc2 = "Don't shoot shoot shoot that thing at me.";
        $doc3 = "I'm your shooter.";
        $docs = [
            ['id' => 'doc1', 'text' => $doc1],
            ['id' => 'doc2', 'text' => $doc2],
            ['id' => 'doc3', 'text' => $doc3],
        ];
        $searchEngine = buildSearchEngine($docs);
        $expected = ['doc2', 'doc1'];

        $this->assertSame($expected, $searchEngine->search('shoot at me, nerd'));
    }

    private function readFixture($name): string
    {
        return file_get_contents(implode('/', [__DIR__, 'fixtures', $name]));
    }
}
