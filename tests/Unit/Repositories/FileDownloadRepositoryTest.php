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
        FileDownload::factory()->count(3)->create();

        $results = $this->fileDownloadRepository->findByParams('id', 'asc');
        $this->assertCount(3, $results);
        $this->assertInstanceOf(FileDownload::class, $results->first());

        $resultsLimited = $this->fileDownloadRepository->findByParams('id', 'asc', 2);
        $this->assertCount(2, $resultsLimited);
    }

    public function testFindByQuery(): void
    {
        $fileDownload1 = FileDownload::factory()->create(['download_key' => 'test_key_123']);
        FileDownload::factory()->create(['download_key' => 'another_key']);

        $results = $this->fileDownloadRepository->findByQuery('test_key');
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
        $fileDownload = FileDownload::factory()->create();

        $result = $this->fileDownloadRepository->deleteFileDownload($fileDownload);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('file_downloads', ['id' => $fileDownload->id]);
    }
}
