<?php

namespace App\Services\Media;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ImageUploadService
{

    private Request $request;
    private array $errors = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function requestImageUpload(string $key, string $path, string $name) {

        $mediaFile = $this->request->file($key);
        if (!$mediaFile) {
            return false;
        }
        $moveFile = $mediaFile->storePubliclyAs($path, "{$name}.{$mediaFile->extension()}");
        if (!$moveFile) {
            return false;
        }
        return $moveFile;
    }

    public function imageUpload(UploadedFile $uploadedFile, string $path, string $name) {
        $moveFile = $uploadedFile->storePubliclyAs("public/$path", "{$name}.{$uploadedFile->extension()}");
        if (!$moveFile) {
            return false;
        }
        return $moveFile;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

}
