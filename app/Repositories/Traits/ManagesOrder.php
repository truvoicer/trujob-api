<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Models\Traits\Orderable; // Ensure this path is correct

/**
 * Trait ManagesOrder
 *
 * This trait provides methods for managing the 'order' column of Eloquent models
 * that use the `Orderable` trait. It's intended to be used within a repository class
 * to centralize ordering logic.
 */
trait ManagesOrder
{
    /**
     * Reorders a model instance up or down within its defined group.
     *
     * This method swaps the 'order' value of the given model with its adjacent sibling
     * in the specified direction ('up' or 'down').
     *
     * @param Model $model The model instance to reorder. Must use the Orderable trait.
     * @param string $direction The direction to move the model ('up' or 'down').
     * @throws InvalidArgumentException If the model does not use the Orderable trait or direction is invalid.
     * @return bool True if the model was successfully reordered, false otherwise (e.g., already at top/bottom).
     */
    public function reorderUpDown(Model $model, string $direction): bool
    {
        // Ensure the model uses the Orderable trait for expected behavior.
        if (!in_array(Orderable::class, class_uses($model))) {
            throw new InvalidArgumentException("The provided model must use the 'Orderable' trait.");
        }

        // Validate the provided direction.
        if (!in_array($direction, ['up', 'down'])) {
            throw new InvalidArgumentException("Direction must be 'up' or 'down'.");
        }

        $orderColumn = $model->getOrderColumn();
        $currentOrder = $model->{$orderColumn};

        // All database operations for reordering should be wrapped in a transaction
        // to ensure atomicity and prevent data inconsistencies.
        return DB::transaction(function () use ($model, $direction, $orderColumn, $currentOrder) {
            // Start a new query for the model, scoped to its group (if any), and ordered by the order column.
            $query = $model->newQuery()->grouped()->orderBy($orderColumn);

            $targetModel = null;

            if ($direction === 'up') {
                // Find the sibling model directly above the current one.
                // It should have the highest 'order' value that is less than the current model's order.
                $targetModel = $query->where($orderColumn, '<', $currentOrder)
                                     ->latest($orderColumn) // Orders descending to get the nearest smaller order
                                     ->first();
            } else { // $direction === 'down'
                // Find the sibling model directly below the current one.
                // It should have the lowest 'order' value that is greater than the current model's order.
                $targetModel = $query->where($orderColumn, '>', $currentOrder)
                                     ->oldest($orderColumn) // Orders ascending to get the nearest larger order
                                     ->first();
            }

            // If a target model is found, swap their order values.
            if ($targetModel) {
                $model->{$orderColumn} = $targetModel->{$orderColumn};
                $targetModel->{$orderColumn} = $currentOrder;

                // Save both models to persist the changes.
                $model->save();
                $targetModel->save();
                return true; // Reordering successful.
            }

            return false; // No target model found, meaning the item is already at the top or bottom.
        });
    }

    /**
     * Reorders a model instance to a specific integer position within its group.
     *
     * This method adjusts the 'order' values of other items in the group to accommodate
     * the target model's new position.
     *
     * @param Model $model The model instance to reorder. Must use the Orderable trait.
     * @param int $newPosition The target order position (0-indexed is common, but depends on your 'order' column usage).
     * @throws InvalidArgumentException If the model does not use the Orderable trait.
     * @return bool True if the model was successfully reordered, false otherwise (e.g., already at target position).
     */
    public function reorderToPosition(Model $model, int $newPosition): bool
    {
        // Ensure the model uses the Orderable trait.
        if (!in_array(Orderable::class, class_uses($model))) {
            throw new InvalidArgumentException("The provided model must use the 'Orderable' trait.");
        }

        $orderColumn = $model->getOrderColumn();
        $currentOrder = $model->{$orderColumn};

        // If the model is already at the target position, no action is needed.
        if ($currentOrder === $newPosition) {
            return false;
        }

        // All database operations for reordering should be wrapped in a transaction.
        return DB::transaction(function () use ($model, $newPosition, $orderColumn, $currentOrder) {
            $query = $model->newQuery()->grouped();

            // Determine the maximum possible order value within the group.
            // This helps in clamping the newPosition to a valid range.
            $maxOrder = $query->max($orderColumn) ?? 0;
            // Ensure newPosition is not negative and not greater than the current max order.
            $newPosition = max(0, min($newPosition, $maxOrder));

            if ($currentOrder < $newPosition) {
                // Case 1: Moving the item down (current order is less than new position).
                // All items between the current position (exclusive) and the new position (inclusive)
                // need to have their order decremented by 1.
                $query->where($orderColumn, '>', $currentOrder)
                      ->where($orderColumn, '<=', $newPosition)
                      ->decrement($orderColumn);
            } else { // $currentOrder > $newPosition
                // Case 2: Moving the item up (current order is greater than new position).
                // All items between the new position (inclusive) and the current position (exclusive)
                // need to have their order incremented by 1.
                $query->where($orderColumn, '<', $currentOrder)
                      ->where($orderColumn, '>=', $newPosition)
                      ->increment($orderColumn);
            }

            // Finally, update the target model with its new order position.
            $model->{$orderColumn} = $newPosition;
            $model->save();

            return true; // Reordering successful.
        });
    }

    /**
     * Re-indexes the 'order' column for all items within a specific group, starting from 0.
     * This is useful for cleaning up gaps or duplicate order numbers that might occur
     * due to deletions or manual manipulations.
     *
     * @param string $modelClass The fully qualified class name of the model (e.g., `App\Models\PageSidebar`).
     * @param mixed|null $groupValue The value of the grouping column (e.g., `page_id`). Pass `null` for global re-indexing if no grouping.
     * @param string|null $groupColumn The name of the grouping column. If `null`, it will be inferred from the model.
     * @throws InvalidArgumentException If the model does not use the Orderable trait.
     * @return void
     */
    public function reindexGroup(string $modelClass, $groupValue = null, ?string $groupColumn = null): void
    {
        // Instantiate the model to access its trait methods.
        $model = new $modelClass();
        if (!in_array(Orderable::class, class_uses($model))) {
            throw new InvalidArgumentException("The provided model class must use the 'Orderable' trait.");
        }

        $orderColumn = $model->getOrderColumn();
        // Use the provided groupColumn or infer from the model.
        $groupColumn = $groupColumn ?? $model->getGroupingColumn();

        DB::transaction(function () use ($model, $groupColumn, $groupValue, $orderColumn) {
            $query = $model->newQuery();

            // Apply the grouping condition if a group column and value are provided.
            if ($groupColumn && $groupValue !== null) {
                $query->where($groupColumn, $groupValue);
            }

            // Retrieve all items in the group, ordered by their current 'order' value.
            $items = $query->orderBy($orderColumn)->get();

            // Iterate through the items and assign sequential order numbers starting from 0.
            foreach ($items as $index => $item) {
                // Only save if the order actually needs to be changed to avoid unnecessary writes.
                if ($item->{$orderColumn} !== $index) {
                    $item->{$orderColumn} = $index;
                    $item->save();
                }
            }
        });
    }

    /**
     * Re-indexes the 'order' column for all orderable models, processing each group separately
     * if grouping is defined, or globally if no grouping.
     *
     * This method can be resource-intensive for very large tables with many groups.
     *
     * @param string $modelClass The fully qualified class name of the model (e.g., `App\Models\PageSidebar`).
     * @throws InvalidArgumentException If the model does not use the Orderable trait.
     * @return void
     */
    public function reindexAll(string $modelClass): void
    {
        // Instantiate the model to access its trait methods.
        $model = new $modelClass();
        if (!in_array(Orderable::class, class_uses($model))) {
            throw new InvalidArgumentException("The provided model class must use the 'Orderable' trait.");
        }

        $groupingColumn = $model->getGroupingColumn();

        if ($groupingColumn) {
            // If grouping is defined, get all unique group values and reindex each group.
            $groupValues = $model->newQuery()->distinct()->pluck($groupingColumn);

            foreach ($groupValues as $groupValue) {
                $this->reindexGroup($modelClass, $groupValue, $groupingColumn);
            }
        } else {
            // If no grouping, reindex all items globally.
            $this->reindexGroup($modelClass, null, null);
        }
    }

    /**
     * Reorders a collection of models based on a provided array of IDs.
     * The order of IDs in the array determines the new 'order' values (0-indexed).
     *
     * This method is ideal for drag-and-drop interfaces where the client sends
     * the new desired order of all items in a group.
     *
     * @param string $modelClass The fully qualified class name of the model (e.g., `App\Models\PageSidebar`).
     * @param array $orderedIds An array of model IDs in their desired new order.
     * @param mixed|null $groupValue Optional. The value of the grouping column (e.g., `page_id`).
     * If provided, ensures reordering only affects items within this group.
     * @param string|null $groupColumn Optional. The name of the grouping column. If null, it will be inferred from the model.
     * @throws InvalidArgumentException If the model does not use the Orderable trait.
     * @return bool True if the reordering was successful, false otherwise.
     */
    public function reorderCollection(string $modelClass, array $orderedIds, $groupValue = null, ?string $groupColumn = null): bool
    {
        // Instantiate the model to access its trait methods.
        $model = new $modelClass();
        if (!in_array(Orderable::class, class_uses($model))) {
            throw new InvalidArgumentException("The provided model class must use the 'Orderable' trait.");
        }

        // If the array of ordered IDs is empty, there's nothing to reorder.
        if (empty($orderedIds)) {
            return false;
        }

        $orderColumn = $model->getOrderColumn();
        $primaryKey = $model->getKeyName(); // Get the primary key name (usually 'id')

        // Use the provided groupColumn or infer from the model.
        $groupColumn = $groupColumn ?? $model->getGroupingColumn();

        return DB::transaction(function () use ($model, $orderedIds, $orderColumn, $primaryKey, $groupColumn, $groupValue) {
            $query = $model->newQuery();

            // Apply the grouping condition if a group column and value are provided.
            if ($groupColumn && $groupValue !== null) {
                $query->where($groupColumn, $groupValue);
            }

            // Fetch all models that are part of the orderedIds and the current group.
            // This ensures we only operate on relevant models and can detect missing IDs.
            $itemsToReorder = $query->whereIn($primaryKey, $orderedIds)->get()->keyBy($primaryKey);

            $successful = true;
            foreach ($orderedIds as $index => $id) {
                // Check if the model for the current ID exists in the fetched collection.
                if ($item = $itemsToReorder->get($id)) {
                    // Only update if the order needs to change to avoid unnecessary writes.
                    if ($item->{$orderColumn} !== $index) {
                        $item->{$orderColumn} = $index;
                        $item->save();
                    }
                } else {
                    // If an ID in the orderedIds array is not found in the database (or not in the specified group),
                    // you might want to log this or handle it based on your application's requirements.
                    // For now, we'll consider it a partial success if some items are reordered.
                    // If strict success (all IDs must be found) is required, you could set $successful = false;
                    // and break, or throw an exception.
                    error_log("Warning: Model with ID {$id} not found or not in specified group for reordering.");
                }
            }
            return $successful;
        });
    }
}
