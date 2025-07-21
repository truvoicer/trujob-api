<?php

namespace Tests\Unit\Services\Site;

use App\Enums\Auth\ApiAbility;
use App\Enums\Auth\ApiTokenExpiry;
use App\Models\Site;
use App\Services\ResultsService;
use App\Services\Site\SiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Sanctum\NewAccessToken;
use Mockery;
use Tests\TestCase;

class SiteServiceTest extends TestCase
{
    use RefreshDatabase;

    private SiteService $siteService;
    private ResultsService $resultsService;
    private Site $site;
    private array $siteData;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a ResultsService instance
        $this->resultsService = new ResultsService();

        // Inject the ResultsService instance into the SiteService
        $this->siteService = new SiteService($this->resultsService);

        // Create a sample Site model for testing purposes
        $this->siteData = [
            'name' => 'Test Site',
            'url' => 'https://example.com',
        ];
        $this->site = Site::create($this->siteData);
    }

    protected function tearDown(): void
    {
        // Clean up any mock objects after each test
        Mockery::close();
        parent::tearDown();
    }

    public function testFindBy(): void
    {
        $sites = $this->siteService->findBy('name', $this->siteData['name']);
        $this->assertInstanceOf(Collection::class, $sites);
        $this->assertCount(1, $sites);
        $this->assertEquals($this->siteData['name'], $sites->first()->name);
    }

    public function testFindOneBy(): void
    {
        $site = $this->siteService->findOneBy('name', $this->siteData['name']);

        $this->assertInstanceOf(Site::class, $site);
        $this->assertEquals($this->siteData['name'], $site->name);
    }

    public function testCreateToken(): void
    {
        $token = $this->siteService->createToken($this->site);
        $this->assertInstanceOf(NewAccessToken::class, $token);
    }

    public function testCreateTokenWithExpiry(): void
    {
        $expiry = ApiTokenExpiry::ONE_DAY;
        $token = $this->siteService->createToken($this->site, $expiry);
        $this->assertInstanceOf(NewAccessToken::class, $token);
    }


    public function testCreateSite(): void
    {
        $data = ['name' => 'New Site', 'url' => 'https://newsite.com'];
        $result = $this->siteService->createSite($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sites', $data);
        $this->assertInstanceOf(Site::class, $this->siteService->site);
    }

    public function testCreateSiteFails(): void
    {
        $data = ['name' => null, 'url' => 'https://newsite.com']; // Invalid data will cause site creation to fail
        $result = $this->siteService->createSite($data);

        $this->assertFalse($result);
        $this->assertDatabaseMissing('sites', ['url' => 'https://newsite.com']);
        $this->assertNotEmpty($this->siteService->getResultsService()->getErrors());
    }


    public function testUpdateSite(): void
    {
        $data = ['name' => 'Updated Site'];
        $result = $this->siteService->updateSite($this->site, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('sites', ['id' => $this->site->id, 'name' => 'Updated Site']);
        $this->assertEquals('Updated Site', $this->siteService->site->name);
    }

    public function testUpdateSiteFails(): void
    {
        $data = ['name' => null];
        $result = $this->siteService->updateSite($this->site, $data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->siteService->getResultsService()->getErrors());
    }

    public function testDeleteSite(): void
    {
        $result = $this->siteService->deleteSite($this->site);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('sites', ['id' => $this->site->id]);
    }

    public function testDeleteSiteFails(): void
    {
        // Mock the delete method to return false to simulate a failure
        $siteMock = Mockery::mock(Site::class);
        $siteMock->shouldReceive('delete')->once()->andReturn(false);
        $siteMock->id = $this->site->id;

        $result = $this->siteService->deleteSite($siteMock);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->siteService->getResultsService()->getErrors());
    }


    public function testGetResultsService(): void
    {
        $this->assertInstanceOf(ResultsService::class, $this->siteService->getResultsService());
    }
}