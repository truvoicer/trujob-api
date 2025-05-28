<?php

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\UserDiscountUsage;
use App\Models\User;
use App\Services\BaseService;

class UserDiscountUsageService extends BaseService
{
    public function getUserDiscountUsage(User $user, Discount $discount): ?UserDiscountUsage
    {
        return $user->discountUsages()->where('discount_id', $discount->id)->first();
    }

    public function createUsageTrack(User $user, Discount $discount)
    {

        // Increment global usage count
        $discount->increment('usage_count');
        
        // Track per-user usage
        $usage = UserDiscountUsage::firstOrCreate(
            [
                'discount_id' => $discount->id,
                'user_id' => $user->id,
            ],
            ['usage_count' => 0]
        );

        $usage->increment('usage_count');
        if (!$usage->update(['last_used_at' => now()])) {
            throw new \Exception('Error updating user discount usage');
        }

        return true;
    }

    public function updateUserDiscountUsage(UserDiscountUsage $usage, array $data)
    {
        if (!$usage->update($data)) {
            throw new \Exception('Error updating user discount usage');
        }
        return $usage;
    }

    public function deleteUserDiscountUsage(UserDiscountUsage $usage)
    {
        if (!$usage->delete()) {
            throw new \Exception('Error deleting user discount usage');
        }
        return true;
    }
}
