<?php

namespace App\Services\Page;

use App\Models\Block;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Site;
use App\Repositories\PageRepository;
use App\Services\BaseService;
use App\Services\Block\BlockService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;
use App\Traits\SidebarTrait;

class PageService extends BaseService
{
    use RoleTrait, SidebarTrait;

    public function __construct(private ResultsService $resultsService, private PageRepository $pageRepository)
    {
        parent::__construct();
        $this->resultsService = $resultsService;
    }

    public function getSitePages(Site $site)
    {
        return $this->pageRepository->getSitePages($site);
    }

    public function getPageById(Site $site, int $id)
    {
        return $site->pages()->where('id', $id)
            ->first();
    }

    public function getPageByPermalink(Site $site, string $permalink): ?Page
    {
        return $site->pages()->where('permalink', $permalink)
            ->first();
    }

    public function getPageByName(Site $site, string $name)
    {
        return $site->pages()->where('name', $name)
            ->first();
    }

    public function menuFetch(string $name)
    {
        return Page::where('name', $name)->first();
    }

    public function createPage(Site $site, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        $sidebars = null;
        if (array_key_exists('sidebars', $data) && is_array($data['sidebars'])) {
            $sidebars = $data['sidebars'];
            unset($data['sidebars']);
        }

        $blocks = [];
        if (array_key_exists('blocks', $data) && is_array($data['blocks'])) {
            $blocks = $data['blocks'];
            unset($data['blocks']);
        }
        $page = $site->pages()->create($data);
        if (!$page->exists()) {
            throw new \Exception('Error creating page');
        }

        if (is_array($roles)) {
            $this->syncRoles($page->roles(), $roles);
        }

        if (is_array($sidebars)) {
            $this->syncSidebars($page->sidebars(), $sidebars);
        }
        
        return $this->createBlockBatch($page, $blocks);
    }

    public function updatePage(Page $page, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        $sidebars = null;
        if (array_key_exists('sidebars', $data) && is_array($data['sidebars'])) {
            $sidebars = $data['sidebars'];
            unset($data['sidebars']);
        }
        
        $blocks = [];
        if (array_key_exists('blocks', $data) && is_array($data['blocks'])) {
            $blocks = $data['blocks'];
            unset($data['blocks']);
        }

        if (!$page->update($data)) {
            $this->resultsService->addError('Error updating page', $data);
            return false;
        }

        if (is_array($roles)) {
            $this->syncRoles($page->roles(), $roles);
        }

        if (is_array($blocks) && count($blocks) > 0) {
            $this->detachPageBlocks($page);
            $this->createBlockBatch($page, $blocks);
        }

        if (is_array($sidebars)) {
            $this->syncSidebars($page->sidebars(), $sidebars);
        }

        return true;
    }

    public function deletePage(Page $page)
    {
        if (!$page->delete()) {
            $this->resultsService->addError('Error deleting page');
            return false;
        }
        return true;
    }

    public function createBlockBatch(Page $page, array $data)
    {
        foreach ($data as $blockData) {
            if (!array_key_exists('type', $blockData)) {
                throw new \Exception('Block type not found');
            }
            $block = Block::where('type', $blockData['type'])->first();
            if (!$block) {
                throw new \Exception('Block not found');
            }
            if (!$this->createPageBlock($page, $block, $blockData)) {
                throw new \Exception('Error creating page block');
            }
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }


    public function createPageBlock(Page $page, Block $block, array $data) {
        return $this->attachPageBlock($page, $block, $data);
    }


    public function attachPageBlock(Page $page, Block $block, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        $sidebars = null;
        if (array_key_exists('sidebars', $data) && is_array($data['sidebars'])) {
            $sidebars = $data['sidebars'];
            unset($data['sidebars']);
        }

        if (array_key_exists('type', $data)) {
            unset($data['type']);
        }
        $atts = $data;
        if (!empty($data['properties']) && is_array($data['properties'])) {
            $atts['properties'] = json_encode($data['properties']);
        }
        
        $pageBlock = new PageBlock();
        $pageBlock->fill($atts);
        $pageBlock->block_id = $block->id;
        $pageBlock->page_id = $page->id;
        $pageBlock->order = $this->pageRepository->getHighestOrder($page->blocks());
        if (!$pageBlock->save()) {
            throw new \Exception('Error creating page block');
        }
        if (is_array($roles)) {
            $this->syncRoles($pageBlock->roles(), $roles);
        }
        if (is_array($sidebars)) {
            $this->syncSidebars($pageBlock->sidebars(), $sidebars);
        }
        return true;
    }

    public function updatePageBlock(PageBlock $pageBlock, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        $sidebars = null;
        if (array_key_exists('sidebars', $data) && is_array($data['sidebars'])) {
            $sidebars = $data['sidebars'];
            unset($data['sidebars']);
        }

        $properties = $pageBlock->properties ?? [];
        if (!empty($data['properties']) && is_array($data['properties'])) {
            $data['properties'] = [
                ...$properties,
                ...$data['properties']
            ];
        }
        $data = BlockService::buildBlockUpdateData($pageBlock, $data);

        if (!$pageBlock->update($data)) {
            $this->resultsService->addError('Error updating page block', $data);
            return false;
        }

        if (is_array($roles)) {
            $this->syncRoles($pageBlock->roles(), $roles);
        }
        if (is_array($sidebars)) {
            $this->syncSidebars($pageBlock->sidebars(), $sidebars);
        }

        return true;
    }

    public function deleteBulkPages(array $ids)
    {
        if (empty($ids)) {
            return false;
        }
        $pages = $this->site->pages()->whereIn('id', $ids)->get();
        foreach ($pages as $page) {
            if (!$this->deletePage($page)) {
                throw new \Exception('Error deleting page');
            }
        }
        return true;
    }

    public function deletePageBlock(PageBlock $pageBlock)
    {
        if (!$pageBlock->exists()) {
            $this->resultsService->addError('Page block not found');
            return false;
        }
        return $pageBlock->delete();
    }

    public function deletePageBlocksByType(Page $page, string $type)
    {
        return $page->blocks()->where('type', $type)->delete();
    }

    public function detachPageBlocks(Page $page)
    {
        return $page->blocks()->detach();
    }

    public function detachPageSidebars(Page $page)
    {
        return $page->sidebars()->detach();
    }

    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    public function getPageRepository(): PageRepository
    {
        return $this->pageRepository;
    }
}
