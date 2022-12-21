<?php

use Illuminate\Support\Str;

class SearchEngine
{
  public function __construct(public array $docs)
  {}

  public function search(string $text): array
  {
    return collect($this->docs)
      ->filter(function ($doc) use ($text) {
        $terms = Str::of($doc['text'])->explode(' ');
        return $terms->map('normalize')->filter();
      })
      ->pluck('id')
      ->values()
      ->toArray();
  }
}

function buildSearchEngine(array $docs)
{
  return new SearchEngine($docs);
}

function normalize(string $token): ?string
{
  preg_match('/\w+/', $token, $matches);

  return Str::lower($matches[0]) ?? null;
}
