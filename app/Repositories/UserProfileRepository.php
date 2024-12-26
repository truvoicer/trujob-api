<?php

namespace App\Repositories;

use App\Models\UserProfile;

class UserProfileRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(UserProfile::class);
    }

    public function findByParams(string $sort,  string $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function getModel(): UserProfile
    {
        return parent::getModel();
    }

}
