<?php

namespace App\Http\Controllers\Api\Page\Block;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Page;
use App\Models\PageBlock;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

class PageBlockRoleController extends Controller
{

    public function __construct(
        private PageService $pageService
    ) {}

    public function index(
        Page $page,
        PageBlock $pageBlock,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->pageService->getPageRepository()
                ->getRoles($pageBlock)

        );
    }
    public function create(
        Page $page,
        PageBlock $pageBlock,
        Role $role,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        
        $this->pageService->assignRoles(
            $pageBlock->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to page block.",
        ]);
    }
    public function destroy(
        Page $page,
        PageBlock $pageBlock,
        Role $role,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService->getPageRepository()->detachRoles(
            $pageBlock->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from page block.",
        ]);
    }
}
