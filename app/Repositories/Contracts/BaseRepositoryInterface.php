<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Find a model by its ID
     */
    public function findById(string $id): ?Model;

    /**
     * Find all models with optional filters
     */
    public function findAll(array $filters = []): Collection;

    /**
     * Find all models with pagination
     */
    public function findAllPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Create a new model
     */
    public function create(array $data): Model;

    /**
     * Update an existing model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete a model
     */
    public function delete(Model $model): bool;

    /**
     * Soft delete a model
     */
    public function softDelete(Model $model): bool;

    /**
     * Restore a soft deleted model
     */
    public function restore(Model $model): bool;

    /**
     * Search models with criteria
     */
    public function search(array $criteria): Collection;

    /**
     * Get the query builder for the model
     */
    public function getQuery();

    /**
     * Count models with optional filters
     */
    public function count(array $filters = []): int;

    /**
     * Check if a model exists
     */
    public function exists(array $criteria): bool;

    /**
     * Get models with relationships
     */
    public function with(array $relationships): self;

    /**
     * Apply filters to query
     */
    public function applyFilters(array $filters): self;
}
