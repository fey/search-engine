<?php

use Illuminate\Support\Str;

class SearchEngine
{
  public function __construct(public array $docs)
  {}

  public function search(string $text): array
  {
    $textTerm = normalize($text);
    return collect($this->docs)
      ->map(function ($doc) use ($textTerm) {
        $terms = Str::of($doc['text'])
          ->explode(' ')
          ->map(fn($token) => normalize($token))
          ->filter(fn($term) => $term === $textTerm);

        return [
          ...$doc,
          'terms' => $terms->toArray(),
          'termsCount' => $terms->count(),
        ];
      })
      ->filter(function ($doc) {

        return $doc['termsCount'] !== 0;
      })
      ->sortByDesc('termsCount')
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
