<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait SidebarTrait
{

    public function buildSidebarsIds(array $sidebarData): array
    {
        if (
            count(array_filter($sidebarData , fn($sidebarId) => is_numeric($sidebarId))) === count($sidebarData )
        ) {
            return $sidebarData;
        }

        if (
            count(array_filter($sidebarData , fn($sidebarId) => is_string($sidebarId))) === count($sidebarData )
        ) {
            return array_map(
                fn($sidebar) => Role::where('name', $sidebar)->first()?->id,
                $sidebarData 
            );
        }

        throw new \Exception("Error building sidebar ids");
    }

    public function syncSidebars(BelongsToMany $sidebars, array $sidebarData): array
    {
        return $sidebars->sync(
            $this->buildSidebarsIds($sidebarData)
        );
    }

    public function assignSidebars(BelongsToMany $sidebars, array $sidebarData): void
    {
        $sidebars->attach(
            $this->buildSidebarsIds($sidebarData)
        );
    }
    
    public function detachSidebars(BelongsToMany $sidebars, array $sidebarData): void
    {
        $sidebars->detach(
            $this->buildSidebarsIds($sidebarData)
        );
    }
}
