<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relPath = 'app/public/dummy';
        $dummyPath = storage_path($relPath);
        if (!file_exists($dummyPath)) {
            throw new \Exception("Dummy path does not exist: $dummyPath");
        }

        $files = array_values(array_diff(scandir($dummyPath), ['..', '.']));
        if (empty($files)) {
            throw new \Exception("No files found in dummy path: $dummyPath");
        }
        $randomFile = $this->faker->randomElement($files);
        $filePath = $dummyPath . '/' . $randomFile;
        $relFilePath = $relPath . '/' . $randomFile;
        $file = new File($filePath);
        return [
            'filename' => $file->getFilename(),
            'full_path' => $file->getRealPath(),
            'rel_path' => $relFilePath,
            'extension' => $file->getExtension(),
            'type' => $file->getType(),
            'size' => $file->getSize(),
            'file_system' => 'dummy',
            'mime_type' => $file->getMimeType()
        ];
    }
}
