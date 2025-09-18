<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;
    protected $query;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->query = $model->newQuery();
    }

    /**
     * Find a model by its ID
     */
    public function findById(string $id): ?Model
    {
        return $this->query->find($id);
    }

    /**
     * Find all models with optional filters
     */
    public function findAll(array $filters = []): Collection
    {
        $query = $this->getQuery();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($filters)->getQuery();
        }

        return $query->get();
    }

    /**
     * Find all models with pagination
     */
    public function findAllPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->getQuery();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($filters)->getQuery();
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing model
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete a model
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Soft delete a model
     */
    public function softDelete(Model $model): bool
    {
        if (method_exists($model, 'delete')) {
            return $model->delete();
        }
        
        return $this->delete($model);
    }

    /**
     * Restore a soft deleted model
     */
    public function restore(Model $model): bool
    {
        if (method_exists($model, 'restore')) {
            return $model->restore();
        }
        
        return false;
    }

    /**
     * Search models with criteria
     */
    public function search(array $criteria): Collection
    {
        $query = $this->getQuery();
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (is_string($value) && str_contains($value, '%')) {
                $query->where($field, 'like', $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get the query builder for the model
     */
    public function getQuery(): Builder
    {
        return $this->query->clone();
    }

    /**
     * Count models with optional filters
     */
    public function count(array $filters = []): int
    {
        $query = $this->getQuery();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($filters)->getQuery();
        }

        return $query->count();
    }

    /**
     * Check if a model exists
     */
    public function exists(array $criteria): bool
    {
        $query = $this->getQuery();
        
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->exists();
    }

    /**
     * Get models with relationships
     */
    public function with(array $relationships): self
    {
        $this->query->with($relationships);
        return $this;
    }

    /**
     * Apply filters to query
     */
    public function applyFilters(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $this->query->where($field, $value);
            }
        }

        return $this;
    }

    /**
     * Reset the query builder
     */
    protected function resetQuery(): self
    {
        $this->query = $this->model->newQuery();
        return $this;
    }

    /**
     * Execute a transaction
     */
    protected function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }
}
