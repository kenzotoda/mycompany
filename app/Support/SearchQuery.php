<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class SearchQuery
{
    public static function whereLikeInsensitive(Builder $query, string $column, string $value): Builder
    {
        return $query->whereRaw(
            'LOWER('.$column.') LIKE ?',
            ['%'.mb_strtolower($value).'%']
        );
    }

    public static function orWhereLikeInsensitive(Builder $query, string $column, string $value): Builder
    {
        return $query->orWhereRaw(
            'LOWER('.$column.') LIKE ?',
            ['%'.mb_strtolower($value).'%']
        );
    }
}
