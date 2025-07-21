<?php

namespace Tests\Unit\Repositories;

use App\Models\Page;
use App\Models\Site;
use App\Repositories\PageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PageRepository $pageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageRepository = new PageRepository();
    }

    public function testGetModelReturnsPageModel(): void
    {
        $model = $this->pageRepository->getModel();

        $this->assertInstanceOf(Page::class, $model);
    }

    public function testFindByParamsReturnsCollection(): void
    {
        Page::factory()->count(3)->create();

        $result = $this->pageRepository->findByParams('id', 'asc');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testFindByParamsReturnsLimitedCollection(): void
    {
        Page::factory()->count(5)->create();

        $result = $this->pageRepository->findByParams('id', 'asc', 2);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testFindByQueryReturnsAllPages(): void
    {
        Page::factory()->count(2)->create();

        $result = $this->pageRepository->findByQuery('some_query');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGetSitePagesReturnsPagesForGivenSite(): void
    {
        $site = Site::factory()->create();
        $pages = Page::factory()->count(3)->create(['site_id' => $site->id]);
        Page::factory()->count(2)->create(); // Create pages for another site

        $result = $this->pageRepository->getSitePages($site);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);

        foreach ($result as $page) {
            $this->assertEquals($site->id, $page->site_id);
        }
    }
}