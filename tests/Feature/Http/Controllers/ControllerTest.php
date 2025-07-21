<?php

namespace Tests\Feature;

use App\Enums\JWT\EncryptedResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Json\JsonResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test sendJsonResponse method.
     *
     * @return void
     */
    public function testSendJsonResponse(): void
    {
        $message = $this->faker->sentence;
        $data = ['key' => 'value'];
        $statusCode = Response::HTTP_CREATED;

        $controller = new class extends Controller {
            public function callSendJsonResponse(bool $encryptedResponse, string $message, $data = [], int $statusCode = Response::HTTP_OK): \Illuminate\Http\JsonResponse
            {
                return $this->sendJsonResponse($encryptedResponse, $message, $data, $statusCode);
            }
        };

        $response = $controller->callSendJsonResponse(false, $message, $data, $statusCode);

        $response->assertStatus($statusCode);
        $response->assertJson([
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Test sendJsonResponse method with encryption.
     *
     * @return void
     */
    public function testSendJsonResponseWithEncryption(): void
    {
        $message = $this->faker->sentence;
        $data = ['key' => 'value'];
        $statusCode = Response::HTTP_OK;

        $controller = new class extends Controller {
            public function callSendJsonResponse(bool $encryptedResponse, string $message, $data = [], int $statusCode = Response::HTTP_OK): \Illuminate\Http\JsonResponse
            {
                return $this->sendJsonResponse($encryptedResponse, $message, $data, $statusCode);
            }
        };

        $response = $controller->callSendJsonResponse(true, $message, $data, $statusCode);
        $responseContent = json_decode($response->getContent(), true);

        $response->assertStatus($statusCode);
        $this->assertArrayHasKey('encrypted_data', $responseContent);
    }

    /**
     * Test sendResourceResponse method.
     *
     * @return void
     */
    public function testSendResourceResponse(): void
    {
        $statusCode = Response::HTTP_OK;

        $controller = new class extends Controller {
            public function callSendResourceResponse(bool $encryptedResponse, JsonResource $resource, int $statusCode = Response::HTTP_OK): JsonResource
            {
                return $this->sendResourceResponse($encryptedResponse, $resource, $statusCode);
            }
        };

        $user = User::factory()->create();

        $resource = new class($user) extends JsonResource {
            public function toArray($request): array
            {
                return [
                    'id' => $this->resource->id,
                    'name' => $this->resource->name,
                    'email' => $this->resource->email,
                ];
            }
        };

        $response = $controller->callSendResourceResponse(false, $resource, $statusCode);

        $this->assertEquals($statusCode, $response->response()->getStatusCode());
        $this->assertEquals($user->id, $resource->resource->id);
    }

    /**
     * Test sendResourceResponse method with encryption.
     *
     * @return void
     */
    public function testSendResourceResponseWithEncryption(): void
    {
        $statusCode = Response::HTTP_OK;

        $controller = new class extends Controller {
            public function callSendResourceResponse(bool $encryptedResponse, JsonResource $resource, int $statusCode = Response::HTTP_OK): JsonResource
            {
                return $this->sendResourceResponse($encryptedResponse, $resource, $statusCode);
            }
        };
        $user = User::factory()->create();

        $resource = new class($user) extends JsonResource {
            public function toArray($request): array
            {
                return [
                    'id' => $this->resource->id,
                    'name' => $this->resource->name,
                    'email' => $this->resource->email,
                ];
            }
        };

        $encryptedResource = $controller->callSendResourceResponse(true, $resource, $statusCode);

        $this->assertEquals($statusCode, $encryptedResource->response()->getStatusCode());
        $this->assertTrue($encryptedResource->additional[EncryptedResponse::ENCRYPTED_RESPONSE->value]);
    }
}