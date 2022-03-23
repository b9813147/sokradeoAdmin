<?php


namespace App\Helpers\Custom;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class  GlobalArr extends Arr
{
    /**
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function rsort(array $array, callable $callback): array
    {
        return Collection::make($array)->sortByDesc($callback)->all();
    }

    public static function mergeUpdateArr(string $uniqueKey, string $aggregateKey, array $originalArr, array $newArr): ?array
    {
        if (is_null($uniqueKey) || is_null($aggregateKey) || is_null($originalArr) || is_null($newArr))
            return null;

        // Create Hash
        $originalArrHash = array();
        foreach ($originalArr as $originalData) {
            $curYear                   = $originalData[$uniqueKey];
            $originalArrHash[$curYear] = $originalData;
        }

        // Update
        foreach ($newArr as $newData) {
            $curYear = $newData[$uniqueKey];
            if (array_key_exists($curYear, $originalArrHash)) {
                $originalArrHash[$curYear][$aggregateKey] += $newData[$aggregateKey];
            } else {
                $originalArrHash[$curYear] = $newData;
            }
        }
        return $originalArrHash;
    }

}
