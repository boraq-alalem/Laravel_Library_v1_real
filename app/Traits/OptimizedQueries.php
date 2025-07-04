<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OptimizedQueries
{
    public function scopeWithOptimizedRelations(Builder $query)
    {
        return $query->with(['author', 'university', 'specialization', 'degree']);
    }
    
    public function scopePaginateOptimized(Builder $query, $perPage = 15)
    {
        return $query->paginate($perPage);
    }
    
    public function scopeLazyChunk(Builder $query, $chunkSize = 1000)
    {
        return $query->lazy($chunkSize);
    }
}