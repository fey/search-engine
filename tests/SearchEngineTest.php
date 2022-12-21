<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

use function buildSearchEngine;

final class SearchEngineTest extends TestCase
{

  public function testSearch(): void
  {
    $docs = [
      ['id' => 'doc1', 'text' => "I can't shoot straight unless I've had a pint!"],
      ['id' => 'doc2', 'text' => "Don't shoot shoot shoot that thing at me."],
      ['id' => 'doc3', 'text' => "I'm your shooter."],
    ];
    $searchEngine = buildSearchEngine($docs);

    $this->assertSame(['doc2', 'doc1'], $searchEngine->search('shoot'));
  }

  public function testSearch2(): void
  {
    $docs = [
      ['id' => 'doc1', 'text' => "I can't shoot straight unless I've had a pint!"],
    ];
    $searchEngine = buildSearchEngine($docs);

    $this->assertSame(['doc1'], $searchEngine->search('pint'));
    $this->assertSame(['doc1'], $searchEngine->search('pint!'));
  }
}
