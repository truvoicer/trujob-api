<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Order;

use App\Services\Payment\PayPal\Middleware\Order\PayPalOrderResponseHandler;
use PaypalServerSdkLib\Http\ApiResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class PayPalOrderResponseHandlerTest extends TestCase
{
    private MockObject $apiResponse;
    private PayPalOrderResponseHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiResponse = $this->createMock(ApiResponse::class);
        $this->handler = new PayPalOrderResponseHandler($this->apiResponse);
    }

    public function testGetResult(): void
    {
        $expectedResult = (object)['id' => 'ORDER-ID'];
        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResult);

        $result = $this->handler->getResult();

        $this->assertEquals($expectedResult, $result);
    }

    public function testIsSuccess_success(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->apiResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(201);

        $this->assertTrue($this->handler->isSuccess());
    }

    public function testIsSuccess_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->assertFalse($this->handler->isSuccess());
    }

    public function testGetOrderId_success(): void
    {
        $resultObject = (object)['id' => 'ORDER-ID'];

        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->apiResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(201);

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($resultObject);

        $this->assertEquals('ORDER-ID', $this->handler->getOrderId());
    }

    public function testGetOrderId_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->assertNull($this->handler->getOrderId());
    }

    public function testGetApprovalLink_success(): void
    {
        $linkObject = (object)[
            'rel' => 'approve',
            'href' => 'https://example.com/approval',
            'method' => 'GET',
        ];
        $resultObject = (object)[
            'links' => [
                $linkObject,
            ],
        ];

        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->apiResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(201);

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($resultObject);

        $this->assertEquals('https://example.com/approval', $this->handler->getApprovalLink());
    }

    public function testGetApprovalLink_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->assertNull($this->handler->getApprovalLink());
    }

    public function testGetLinks_success(): void
    {
        $linkObject = (object)[
            'rel' => 'approve',
            'href' => 'https://example.com/approval',
            'method' => 'GET',
        ];
        $resultObject = (object)[
            'links' => [
                $linkObject,
            ],
        ];

        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->apiResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(201);

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($resultObject);

        $expected = [
            [
                'rel' => 'approve',
                'href' => 'https://example.com/approval',
                'method' => 'GET',
            ]
        ];
        $this->assertEquals($expected, $this->handler->getLinks());
    }

    public function testGetLinks_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->assertEquals([], $this->handler->getLinks());
    }

    public function testGetErrorMessage_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $resultObject = (object)['message' => 'Error message'];

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($resultObject);

        $this->assertEquals('Error message', $this->handler->getErrorMessage());
    }

    public function testGetErrorMessage_failure_no_result(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn(null);

        $this->apiResponse->expects($this->once())
            ->method('getRawBody')
            ->willReturn('Raw body error');

        $this->assertEquals('Raw body error', $this->handler->getErrorMessage());
    }

    public function testGetErrorMessage_success(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->assertNull($this->handler->getErrorMessage());
    }

    public function testGetErrorDetails_failure(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $errorDetails = ['field' => 'value'];
        $resultObject = (object)['details' => $errorDetails];

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn($resultObject);


        $this->assertEquals($errorDetails, $this->handler->getErrorDetails());
    }

    public function testGetErrorDetails_failure_no_details(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->apiResponse->expects($this->once())
            ->method('getResult')
            ->willReturn((object)[]);

        $this->assertEquals([], $this->handler->getErrorDetails());
    }

    public function testGetErrorDetails_success(): void
    {
        $this->apiResponse->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->assertEquals([], $this->handler->getErrorDetails());
    }

    public function testGetRawResponse(): void
    {
        $this->assertEquals($this->apiResponse, $this->handler->getRawResponse());
    }
}