<?php

namespace App\Models;

use App\Repositories\PermissionRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    public const TABLE_NAME = 'permissions';
    public const REPOSITORY = PermissionRepository::class;

}
