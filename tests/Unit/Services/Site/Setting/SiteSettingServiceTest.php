<?php

namespace Tests\Unit\Services\Site\Setting;

use App\Models\Site;
use App\Models\SiteSetting;
use App\Services\Site\Setting\SiteSettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingServiceTest extends TestCase
{
    use RefreshDatabase;

    private SiteSettingService $siteSettingService;
    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siteSettingService = new SiteSettingService();
        $this->site = Site::factory()->create();
        $this->siteSettingService->site = $this->site; //Set the site property for testing.
    }

    public function testCreateSiteSetting(): void
    {
        $data = ['key' => 'test_key', 'value' => 'test_value'];

        $result = $this->siteSettingService->createSiteSetting($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('site_settings', $data + ['site_id' => $this->site->id]);
    }

    public function testCreateSiteSettingThrowsExceptionOnFailure(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating Site setting');

        // Mock the SiteSetting model to simulate a save failure
        $mockSiteSetting = $this->getMockBuilder(SiteSetting::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSiteSetting->method('exists')->willReturn(false);

        // Replace the save method of the SiteSetting with our mock
        $this->site->settings()->save($mockSiteSetting);

        $this->siteSettingService->createSiteSetting(['key' => 'test', 'value' => 'test']);

    }


    public function testUpdateSiteSettingCreatesSettingIfNoneExists(): void
    {
        $data = ['key' => 'updated_key', 'value' => 'updated_value'];

        $result = $this->siteSettingService->updateSiteSetting($this->site, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('site_settings', $data + ['site_id' => $this->site->id]);
    }

    public function testUpdateSiteSettingUpdatesExistingSetting(): void
    {
        $siteSetting = SiteSetting::factory()->create(['site_id' => $this->site->id, 'key' => 'original_key', 'value' => 'original_value']);
        $data = ['key' => 'updated_key', 'value' => 'updated_value'];

        $result = $this->siteSettingService->updateSiteSetting($this->site, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('site_settings', $data + ['site_id' => $this->site->id]);
        $this->assertDatabaseMissing('site_settings', ['key' => 'original_key', 'value' => 'original_value']);
    }


    public function testUpdateSiteSettingThrowsExceptionOnFailure(): void
    {
        $siteSetting = SiteSetting::factory()->create(['site_id' => $this->site->id]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating Site setting');

        // Mock the SiteSetting model to simulate an update failure
        $mockSiteSetting = $this->getMockBuilder(SiteSetting::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSiteSetting->method('update')->willReturn(false);

        // Replace the find method of the SiteSetting with our mock
        $this->site->settings()->first()->update([]); // force the settings property on the site object to load first

        SiteSetting::where('id', $siteSetting->id)->update(['value' => 'test']);

        $data = ['key' => 'test', 'value' => 'test'];
        $this->siteSettingService->updateSiteSetting($this->site, $data);
    }

    public function testDeleteSiteSetting(): void
    {
        $siteSetting = SiteSetting::factory()->create(['site_id' => $this->site->id]);

        $result = $this->siteSettingService->deleteSiteSetting($siteSetting);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('site_settings', ['id' => $siteSetting->id]);
    }

    public function testDeleteSiteSettingThrowsExceptionOnFailure(): void
    {
        $siteSetting = SiteSetting::factory()->create(['site_id' => $this->site->id]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting Site setting');

        // Mock the SiteSetting model to simulate a delete failure
        $mockSiteSetting = $this->getMockBuilder(SiteSetting::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockSiteSetting->method('delete')->willReturn(false);

        $this->siteSettingService->deleteSiteSetting($mockSiteSetting);
    }

}