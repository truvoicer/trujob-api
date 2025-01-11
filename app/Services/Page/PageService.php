<?php

namespace App\Services\Page;

use App\Models\Page;
use App\Models\PageBlock;
use App\Services\BaseService;
use App\Services\ResultsService;

class PageService extends BaseService
{
    private ResultsService $resultsService;

    private Page $page;

    public function __construct(ResultsService $resultsService)
    {
        parent::__construct();
        $this->resultsService = $resultsService;
    }

    public function menuFetch(string $slug) {
        return Page::where('slug', $slug)->first();
    }

    public function createPage(array $data) {
        $this->page = new Page($data);
        if (!$this->page->save()) {
            $this->resultsService->addError('Error creating page', $data);
            return false;
        }
        return $this->createBlockBatch($this->page, !empty($data['blocks']) ? $data['blocks'] : []);
    }

    public function createBlockBatch(Page $page, array $data) {
        foreach ($data as $block) {
            $this->createPageBlock($page, $block);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updatePage(Page $page, array $data) {
        $this->page = $page;
        $this->page->fill($data);
        if (!$this->page->save()) {
            $this->resultsService->addError('Error updating page', $data);
            return false;
        }
        return $this->createBlockBatch($this->page, !empty($data['blocks']) ? $data['blocks'] : []);
    }

    public function deletePage(Page $page) {
        $this->page = $page;
        if (!$this->page->delete()) {
            $this->resultsService->addError('Error deleting page');
            return false;
        }
        return true;
    }
    public function createPageBlock(Page $page, array $data) {
        $block = $page->blocks()->create($data);
        return true;
    }

    public function updatePageBlock(PageBlock $block, array $data) {
        $block->fill($data);
        if (!$block->save()) {
            $this->resultsService->addError('Error updating page block', $data);
            return false;
        }
        return true;
    }

    public function deletePageBlock(PageBlock $block) {
        if (!$block->delete()) {
            $this->resultsService->addError('Error deleting page block');
            return false;
        }
        return true;
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

    /**
     * @param PageItem $menuItem
     */
    public function setPageItem(PageItem $menuItem): void
    {
        $this->pageItem = $menuItem;
    }


}
