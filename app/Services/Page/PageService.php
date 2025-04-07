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

class PageService extends BaseService
{
    use RoleTrait;

    private Page $page;

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

        return $this->createBlockBatch($page, $blocks);
    }

    public function updatePage(Page $page, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
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

        if (empty($data['blocks'])) {
            return true;
        }

        $this->detachPageBlocks($page);

        return $this->createBlockBatch($page, $blocks);
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
            if (!$this->createPageBlock($page, $blockData)) {
                throw new \Exception('Error creating page block');
            }
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function createPageBlock(Page $page, array $data)
    {
        $block = Block::where('type', $data['type'])->first();
        if (!$block) {
            throw new \Exception('Block not found');
        }
        return $this->attachPageBlock($page, $block, $data);
    }

    public function attachPageBlock(Page $page, Block $block, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (array_key_exists('type', $data)) {
            unset($data['type']);
        }
        $atts = $data;
        if (!empty($data['properties']) && is_array($data['properties'])) {
            $atts['properties'] = json_encode($data['properties']);
        }
        if (!empty($data['sidebar_widgets']) && is_array($data['sidebar_widgets'])) {
            $atts['sidebar_widgets'] = json_encode($data['sidebar_widgets']);
        }

        $page->blocks()->attach($block->id, $atts);

        if (is_array($roles)) {
            $this->syncRoles($page->blocks()->where('block_id', $block->id)->first()->roles(), $roles);
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

    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    /**
     * @param Page $menu
     */
    public function setPage(Page $menu): void
    {
        $this->page = $menu;
    }
}
