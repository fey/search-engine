<?php

use Illuminate\Support\Str;

class SearchEngine
{
  public function __construct(public array $docs)
  {}

  public function search(string $text): array
  {
    $textTerms = Str::of($text)->explode((' '))->map('normalize')->filter()->values();
    return collect($this->docs)
      ->map(function ($doc) use ($textTerms) {
        $terms = Str::of($doc['text'])
          ->explode(' ')
          ->map(fn($token) => normalize($token))
          ->filter()
          ->values();

        return [
          ...$doc,
          'terms' => $terms->toArray(),
        ];
      })
      ->map(function ($doc) use ($textTerms) {
        $matchedTerms = $textTerms->intersect($doc['terms']);
        $countsbyMatches = collect($doc['terms'])->countBy()->only($matchedTerms);

        return [
          ...$doc,
          'matchedTermsCount' => $matchedTerms->count(),
          'sumOfMatches' => $countsbyMatches->sum(),
        ];
      })
      ->filter(fn($doc) => $doc['matchedTermsCount'] !== 0)
      ->sortBy([['matchedTermsCount', 'desc'], ['sumOfMatches', 'desc']
      ])
      // ->each(fn($doc) => dump($doc))
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
