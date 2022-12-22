<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SearchEngine
{
    private array $index = [];

    public function __construct(array $docs)
    {
        $this->index = $this->buildIndex($docs);
    }

    public function search(string $text): array
    {

        return [];
    }

    private function buildIndex(array $docs): Collection
    {
        $docsCount = count($docs);

        $index = collect($docs)
        ->keyBy('id')
        ->map(function (array $doc, string $docId): Collection {
            $terms = Str::of($doc['text'])
            ->explode(' ')
            ->map(fn($token) => normalize($token))
            ->filter();

            return $terms->countBy()
                ->map(function (int $count) use ($docId, $terms): array {
                    return [
                        'docId' => $docId,
                        'termFrequency' => $count / $terms->count(),
                        'count' => $count,
                    ];
                });

            return $terms;
        })
        ->reduce(function (Collection $acc, Collection $docTerms): Collection {
            $keys = $docTerms->keys();

            $keys->each(function ($term) use ($acc, $docTerms) {
                if ($acc->has($term)) {
                    $acc[$term] = array_merge($acc[$term], [$docTerms[$term]]);
                } else {
                    $acc[$term] = [$docTerms[$term]];
                }
            });

            return $acc;
        }, collect([]))
        ->map(function (array $termDocs) use ($docsCount) {
            $termDocsCount = count($termDocs);
            return collect($termDocs)
                ->map(function (array $termDoc) use ($docsCount, $termDocsCount) {
                    $docIdf = calcIDF($docsCount, $termDocsCount);
                    $tfIDF = $termDoc['termFrequency'] * $docIdf;
                    return [
                        ...$termDoc,
                        'tfIDF' => $tfIDF,
                    ];
                });
        });

        dump($index);

        return $index;
    }
}

function buildSearchEngine(array $docs)
{
    return new SearchEngine($docs);
}

function normalize(string $token): ?string
{
    preg_match_all('/\w+/', $token, $matches);
    $result = collect($matches)->flatten()->join('');

    return is_null($result) ? null : Str::lower($result);
}

function calcIDF(int $docsCount, int $termCount): float
{
    dump("$docsCount, $termCount");
    $num = 1 + ($docsCount - $termCount + 1) / ($termCount + 0.50);

    return log($num, 2 );
}
