<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait SidebarTrait
{

    public function syncSidebars(BelongsToMany $sidebars, array $sidebarData): array
    {
        if (
            count(array_filter($sidebarData , fn($sidebarId) => is_numeric($sidebarId))) === count($sidebarData )
        ) {
            return $sidebars->sync($sidebarData );
        }

        if (
            count(array_filter($sidebarData , fn($sidebarId) => is_string($sidebarId))) === count($sidebarData )
        ) {
            $sidebarIds = array_map(
                fn($sidebar) => Role::where('name', $sidebar)->first()?->id,
                $sidebarData 
            );
            return $sidebars->sync($sidebarIds);
        }

        throw new \Exception("Error syncing sidebars");
    }
}
