<?php

namespace Tests\Unit\Services\Payment\Stripe\Middleware;

use App\Services\Payment\Stripe\Middleware\StripeResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StripeResponseTest extends TestCase
{
    private StripeResponse $stripeResponse;
    private array $testData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testData = ['key1' => 'value1', 'key2' => 'value2'];
        $this->stripeResponse = new StripeResponse($this->testData);
    }

    public function testSetResponse(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->assertInstanceOf(StripeResponse::class, $this->stripeResponse->setResponse($response));
    }

    public function testGetResponse(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->stripeResponse->setResponse($response);
        $this->assertInstanceOf(Response::class, $this->stripeResponse->getResponse());
    }

    public function testGetResponseData(): void
    {
        $this->assertEquals($this->testData, $this->stripeResponse->getResponseData());
    }

    public function testSetResponseData(): void
    {
        $newData = ['key3' => 'value3', 'key4' => 'value4'];
        $this->stripeResponse->setResponseData($newData);
        $this->assertEquals($newData, $this->stripeResponse->getResponseData());
    }

    public function testIsSuccessWithResponseSuccessful(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->stripeResponse->setResponse($response);
        $this->assertTrue($this->stripeResponse->isSuccess());
    }

    public function testIsSuccessWithResponseNotSuccessful(): void
    {
        $response = Http::response(['status' => 'error'], 400);
        $this->stripeResponse->setResponse($response);
        $this->assertFalse($this->stripeResponse->isSuccess());
    }

    public function testIsSuccessWithoutResponse(): void
    {
        $this->assertFalse($this->stripeResponse->isSuccess());
    }

    public function testIsFailedWithSuccess(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->stripeResponse->setResponse($response);
        $this->assertFalse($this->stripeResponse->isFailed());
    }

    public function testIsFailedWithFailure(): void
    {
        $response = Http::response(['status' => 'error'], 400);
        $this->stripeResponse->setResponse($response);
        $this->assertTrue($this->stripeResponse->isFailed());
    }

    public function testGetErrorMessageWhenSuccess(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->stripeResponse->setResponse($response);
        $this->assertNull($this->stripeResponse->getErrorMessage());
    }

    public function testGetErrorMessageWhenFailed(): void
    {
        $response = Http::response(['error' => 'Test error message'], 400);
        $this->stripeResponse->setResponse($response);
        $this->assertNull($this->stripeResponse->getErrorMessage());
    }

    public function testGetErrorDetailsWhenSuccess(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->stripeResponse->setResponse($response);
        $this->assertIsArray($this->stripeResponse->getErrorDetails());
        $this->assertEmpty($this->stripeResponse->getErrorDetails());
    }

    public function testGetErrorDetailsWhenFailed(): void
    {
        $response = Http::response(['error' => ['message' => 'Test error details']], 400);
        $this->stripeResponse->setResponse($response);
        $this->assertIsArray($this->stripeResponse->getErrorDetails());
        $this->assertEmpty($this->stripeResponse->getErrorDetails());
    }
}