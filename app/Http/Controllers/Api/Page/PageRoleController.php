<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Page;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

class PageRoleController extends Controller
{

    public function __construct(
        private PageService $pageService
    ) {}

    public function index(
        Page $page,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->pageService->getPageRepository()
                ->getRoles($page)

        );
    }
    public function store(
        Page $page,
        Role $role,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        
        $this->pageService->assignRoles(
            $page->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to page.",
        ]);
    }
    public function destroy(
        Page $page,
        Role $role,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService->getPageRepository()->detachRoles(
            $page->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from page.",
        ]);
    }
}
