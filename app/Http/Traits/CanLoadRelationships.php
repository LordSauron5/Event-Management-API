<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

trait CanLoadRelationships
{
    /**
     * Load specified relationships into the given query or model.
     *
     * @param Model|QueryBuilder|EloquentBuilder $for The query or model to load relationships into.
     * @param array|null $relations The array of relationships to be loaded.
     *
     * @return Model|QueryBuilder|EloquentBuilder The modified query or model with loaded relationships.
     */
    public function loadRelationships(
        Model|QueryBuilder|EloquentBuilder $for,
        ?array $relations = null
        ): Model|QueryBuilder|EloquentBuilder {

            $relations = $relations ?? $this->relations ?? [];

            // loop - Load allowed relations into the query or model.
            foreach ($relations as $relation) {
                $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }
    
        return $for;
    }

    /**
     * Determine if the given relation should be included based on the 'include' query parameter.
     *
     * @param string $relation The relation to check for inclusion.
     *
     * @return bool Whether the relation should be included.
     */
    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');

        // check - if include parameter is empty, return
        if (!$include) {
            return false;
        }

        // set relations - santize and convert to array
        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }
}

