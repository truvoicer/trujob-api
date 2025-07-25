<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Trait Orderable
 *
 * This trait provides methods and scopes for models that have an 'order' column
 * and may be grouped by another column (e.g., 'page_id').
 *
 * Models using this trait should have an integer 'order' column.
 *
 * To use:
 * 1. Add `use Orderable;` to your Eloquent model.
 * 2. (Optional but recommended) Define a protected property `$groupingColumn`
 * in your model if your grouping column is not 'page_id' or 'page_block_id',
 * or if you want to explicitly set it. Example: `protected string $groupingColumn = 'category_id';`
 * 3. (Optional) Override `getOrderColumn()` or `getGroupingColumn()` methods
 * in your model if default behavior is not suitable.
 */
trait Orderable
{
    /**
     * Get the name of the order column.
     *
     * @return string The name of the column used for ordering (default: 'order').
     */
    public function getOrderColumn(): string
    {
        // You can override this method in your model if your order column is named differently.
        return 'order';
    }

    /**
     * Get the name of the grouping column.
     * Items will be ordered relative to others within the same group.
     * Return null if no grouping is needed (i.e., global ordering).
     *
     * This method tries to infer the grouping column based on common foreign key names
     * or a `$groupingColumn` property in the model. It's recommended to explicitly
     * define `$groupingColumn` in your model for clarity and performance.
     *
     * @return string|null The name of the column used for grouping, or null if no grouping.
     */
    public function getGroupingColumn(): ?string
    {
        // Check if the model explicitly defines a $groupingColumn property
        if (property_exists($this, 'groupingColumn') && is_string($this->groupingColumn)) {
            return $this->groupingColumn;
        }

        // Attempt to infer common foreign key names from the database schema.
        // NOTE: This can be less performant than explicitly defining $groupingColumn.
        // It's generally better to define 'protected string $groupingColumn = 'your_column_name';'
        // in your model for clarity and performance.
        $table = $this->getTable();
        if (Schema::hasColumn($table, 'page_id')) {
            return 'page_id';
        }
        if (Schema::hasColumn($table, 'page_block_id')) {
            return 'page_block_id';
        }
        // Add more common grouping column names here if needed.

        return null; // No specific grouping column found by default
    }

    /**
     * Get the value of the grouping column for the current model instance.
     *
     * @return mixed|null The value of the grouping column, or null if no grouping.
     */
    public function getGroupingValue()
    {
        $groupingColumn = $this->getGroupingColumn();
        return $groupingColumn ? $this->{$groupingColumn} : null;
    }

    /**
     * Apply the grouping scope to an Eloquent query.
     * This ensures that order operations are performed only within the relevant group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The Eloquent query builder instance.
     * @return \Illuminate\Database\Eloquent\Builder The query builder with the grouping scope applied.
     */
    public function scopeGrouped(Builder $query): Builder
    {
        $groupingColumn = $this->getGroupingColumn();
        $groupingValue = $this->getGroupingValue();

        // If a grouping column is defined and its value is not null, apply the 'where' clause.
        if ($groupingColumn && $groupingValue !== null) {
            return $query->where($groupingColumn, $groupingValue);
        }

        return $query; // No grouping applied if no grouping column or value.
    }
}

