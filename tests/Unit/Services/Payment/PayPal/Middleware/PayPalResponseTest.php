<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware;

use App\Services\Payment\PayPal\Middleware\PayPalResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalResponseTest extends TestCase
{
    /**
     * @var PayPalResponse
     */
    private $payPalResponse;

    private $responseData;

    public function setUp(): void
    {
        parent::setUp();

        $this->responseData = ['key' => 'value'];
        $this->payPalResponse = new PayPalResponse($this->responseData);
    }

    public function tearDown(): void
    {
        unset($this->payPalResponse);
        parent::tearDown();
    }

    public function testSetResponse(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->assertInstanceOf(PayPalResponse::class, $this->payPalResponse->setResponse($response));
    }

    public function testGetResponse(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->payPalResponse->setResponse($response);
        $this->assertInstanceOf(Response::class, $this->payPalResponse->getResponse());
    }

    public function testGetResponseData(): void
    {
        $this->assertEquals($this->responseData, $this->payPalResponse->getResponseData());
    }

    public function testSetResponseData(): void
    {
        $newData = ['new_key' => 'new_value'];
        $this->payPalResponse->setResponseData($newData);
        $this->assertEquals($newData, $this->payPalResponse->getResponseData());
    }

    public function testIsSuccessWhenResponseIsSetAndSuccessful(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->payPalResponse->setResponse($response);
        $this->assertTrue($this->payPalResponse->isSuccess());
    }

    public function testIsSuccessWhenResponseIsNotSet(): void
    {
        $this->assertFalse($this->payPalResponse->isSuccess());
    }

    public function testIsSuccessWhenResponseIsSetAndNotSuccessful(): void
    {
        $response = Http::response(['status' => 'error'], 400);
        $this->payPalResponse->setResponse($response);
        $this->assertFalse($this->payPalResponse->isSuccess());
    }

    public function testIsFailedWhenResponseIsSuccessful(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->payPalResponse->setResponse($response);
        $this->assertFalse($this->payPalResponse->isFailed());
    }

    public function testIsFailedWhenResponseIsNotSuccessful(): void
    {
        $response = Http::response(['status' => 'error'], 400);
        $this->payPalResponse->setResponse($response);
        $this->assertTrue($this->payPalResponse->isFailed());
    }

    public function testGetErrorMessageWhenSuccessful(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->payPalResponse->setResponse($response);
        $this->assertNull($this->payPalResponse->getErrorMessage());
    }

    public function testGetErrorMessageWhenNotSuccessful(): void
    {
        $response = Http::response(['status' => 'error', 'message' => 'Something went wrong'], 400);
        $this->payPalResponse->setResponse($response);
        $this->assertNull($this->payPalResponse->getErrorMessage());
    }

    public function testGetErrorDetailsWhenSuccessful(): void
    {
        $response = Http::response(['status' => 'success'], 200);
        $this->payPalResponse->setResponse($response);
        $this->assertEmpty($this->payPalResponse->getErrorDetails());
    }

    public function testGetErrorDetailsWhenNotSuccessful(): void
    {
        $response = Http::response(['status' => 'error', 'details' => ['field' => 'value']], 400);
        $this->payPalResponse->setResponse($response);
        $this->assertEmpty($this->payPalResponse->getErrorDetails());
    }
}