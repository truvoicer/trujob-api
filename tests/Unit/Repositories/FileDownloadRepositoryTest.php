<?php

namespace Tests\Unit\Repositories;

use App\Models\File;
use App\Models\FileDownload;
use App\Repositories\FileDownloadRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileDownloadRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FileDownloadRepository $fileDownloadRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileDownloadRepository = new FileDownloadRepository();
    }

    public function testGetModel(): void
    {
        $model = $this->fileDownloadRepository->getModel();
        $this->assertInstanceOf(FileDownload::class, $model);
    }

    public function testFindByParams(): void
    {
        File::factory()
        ->has(
            FileDownload::factory()->count(3)
        )
        ->create();

        $results = $this->fileDownloadRepository->findByParams('id', 'asc');
        $this->assertCount(3, $results);
        $this->assertInstanceOf(FileDownload::class, $results->first());

        $resultsLimited = $this->fileDownloadRepository->findByParams('id', 'asc', 2);
        $this->assertCount(2, $resultsLimited);
    }

    public function testFindByQuery(): void
    {

        File::factory()
        ->has(
            FileDownload::factory()->state([
                'download_key' => 'test_key_123'
            ])
        )
        ->create();
        File::factory()
        ->has(
            FileDownload::factory()->state([
                'download_key' => 'another_key'
            ])
        )
        ->create();
        $fileDownload1 = FileDownload::where('download_key', 'test_key_123')->first();

        $results = $this->fileDownloadRepository->findByQuery(
            FileDownload::query()->where('download_key', 'LIKE', '%test_key%')
        );
        $this->assertCount(1, $results);
        $this->assertEquals($fileDownload1->id, $results->first()->id);
    }

    public function testSaveFileDownload(): void
    {
        $file = File::factory()->create();
        $downloadKey = 'unique_download_key';
        $clientIp = '127.0.0.1';
        $userAgent = 'Test User Agent';

        $result = $this->fileDownloadRepository->saveFileDownload($file, $downloadKey, $clientIp, $userAgent);

        $this->assertTrue($result);
        $this->assertDatabaseHas('file_downloads', [
            'file_id' => $file->id,
            'download_key' => $downloadKey,
            'client_ip' => $clientIp,
            'user_agent' => $userAgent,
        ]);
    }

    public function testDeleteFileDownload(): void
    {
        File::factory()
        ->has(
            FileDownload::factory()->state([
                'download_key' => 'test_key_123'
            ])
        )
        ->create();
        $fileDownload = FileDownload::where('download_key', 'test_key_123')->first();

        $result = $this->fileDownloadRepository->deleteFileDownload($fileDownload);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('file_downloads', ['id' => $fileDownload->id]);
    }
}
