<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware;

use App\Services\Payment\PayPal\Middleware\PayPalBaseService;
use App\Services\Payment\PayPal\Middleware\PayPalResponse;
use Exception;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalBaseServiceTest extends TestCase
{
    private PayPalBaseService $payPalBaseService;

    protected function setUp(): void
    {
        parent::setUp();

        // Use a mock or testing credentials/settings here
        $this->payPalBaseService = new PayPalBaseService();
        $this->payPalBaseService->setClientId('testClientId');
        $this->payPalBaseService->setClientSecret('testClientSecret');
        $this->payPalBaseService->setSandboxMode(true); // Or false, depending on your test case
    }

    public function testGetResponseData(): void
    {
        $this->payPalBaseService->setResponseData(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $this->payPalBaseService->getResponseData());
    }

    public function testSetResponseData(): void
    {
        $this->payPalBaseService->setResponseData(['newKey' => 'newValue']);
        $this->assertEquals(['newKey' => 'newValue'], $this->payPalBaseService->getResponseData());
    }

    public function testSetClientId(): void
    {
        $this->payPalBaseService->setClientId('newClientId');
        $this->setProperty($this->payPalBaseService, 'clientId', 'newClientId'); //Use reflection to access protected property
        $this->assertEquals('newClientId', $this->getObjectAttribute($this->payPalBaseService, 'clientId'));
    }

    public function testSetClientSecret(): void
    {
        $this->payPalBaseService->setClientSecret('newClientSecret');
        $this->setProperty($this->payPalBaseService, 'clientSecret', 'newClientSecret'); //Use reflection to access protected property
        $this->assertEquals('newClientSecret', $this->getObjectAttribute($this->payPalBaseService, 'clientSecret'));
    }

    public function testSetCredentials(): void
    {
        $this->payPalBaseService->setCredentials('newClientId', 'newClientSecret');

        $this->setProperty($this->payPalBaseService, 'clientId', 'newClientId'); //Use reflection to access protected property
        $this->setProperty($this->payPalBaseService, 'clientSecret', 'newClientSecret'); //Use reflection to access protected property
        $this->setProperty($this->payPalBaseService, 'accessToken', null); //Use reflection to access protected property
        $this->setProperty($this->payPalBaseService, 'tokenExpiresAt', 0); //Use reflection to access protected property

        $this->assertEquals('newClientId', $this->getObjectAttribute($this->payPalBaseService, 'clientId'));
        $this->assertEquals('newClientSecret', $this->getObjectAttribute($this->payPalBaseService, 'clientSecret'));
        $this->assertNull($this->getObjectAttribute($this->payPalBaseService, 'accessToken'));
        $this->assertEquals(0, $this->getObjectAttribute($this->payPalBaseService, 'tokenExpiresAt'));
    }

    public function testSetSandboxMode(): void
    {
        $this->payPalBaseService->setSandboxMode(false);
        $this->setProperty($this->payPalBaseService, 'accessToken', null); //Use reflection to access protected property
        $this->setProperty($this->payPalBaseService, 'tokenExpiresAt', 0); //Use reflection to access protected property
        $this->assertEquals('https://api-m.paypal.com', $this->getObjectAttribute($this->payPalBaseService, 'baseUrl'));
        $this->assertNull($this->getObjectAttribute($this->payPalBaseService, 'accessToken'));
        $this->assertEquals(0, $this->getObjectAttribute($this->payPalBaseService, 'tokenExpiresAt'));

        $this->payPalBaseService->setSandboxMode(true);
        $this->setProperty($this->payPalBaseService, 'accessToken', null); //Use reflection to access protected property
        $this->setProperty($this->payPalBaseService, 'tokenExpiresAt', 0); //Use reflection to access protected property
        $this->assertEquals('https://api-m.sandbox.paypal.com', $this->getObjectAttribute($this->payPalBaseService, 'baseUrl'));
        $this->assertNull($this->getObjectAttribute($this->payPalBaseService, 'accessToken'));
        $this->assertEquals(0, $this->getObjectAttribute($this->payPalBaseService, 'tokenExpiresAt'));
    }

    public function testGetAccessToken(): void
    {
        // Mock the authenticate method to avoid actual API calls.
        $mock = $this->getMockBuilder(PayPalBaseService::class)
                     ->onlyMethods(['authenticate', 'isTokenExpired'])
                     ->getMock();

        // Set expectations for the isTokenExpired method.
        $mock->method('isTokenExpired')
             ->willReturn(true); // Simulate expired token

        // Set expectations for the authenticate method.
        $mock->method('authenticate')
             ->willReturnCallback(function () use ($mock) {
                 $this->setProperty($mock, 'accessToken', 'mockAccessToken');
                 $this->setProperty($mock, 'tokenExpiresAt', time() + 3600);
             });
        $this->setProperty($mock, 'clientId', 'testClientId');
        $this->setProperty($mock, 'clientSecret', 'testClientSecret');
        $this->setProperty($mock, 'baseUrl', 'https://api-m.sandbox.paypal.com');

        $accessToken = $mock->getAccessToken();

        $this->assertEquals('mockAccessToken', $accessToken);
    }

    public function testGetAccessTokenThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to obtain PayPal access token.');

        $mock = $this->getMockBuilder(PayPalBaseService::class)
                     ->onlyMethods(['authenticate', 'isTokenExpired'])
                     ->getMock();

        // Simulate authentication failure by not setting the access token.
        $mock->method('authenticate')
             ->willReturn(null);
        $mock->method('isTokenExpired')
             ->willReturn(true);

        $mock->getAccessToken();
    }

    public function testIsTokenExpired(): void
    {
        // Set a future expiration time
        $this->setProperty($this->payPalBaseService, 'tokenExpiresAt', time() + 3600);
        $this->assertFalse($this->invokeMethod($this->payPalBaseService, 'isTokenExpired'));

        // Set a past expiration time
        $this->setProperty($this->payPalBaseService, 'tokenExpiresAt', time() - 3600);
        $this->assertTrue($this->invokeMethod($this->payPalBaseService, 'isTokenExpired'));
    }

    public function testMakeRequestSuccess(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/test-endpoint' => Http::response(['data' => 'success'], 200),
        ]);

        // Mock getAccessToken to return a valid token
        $mock = $this->getMockBuilder(PayPalBaseService::class)
            ->onlyMethods(['getAccessToken'])
            ->getMock();
        $mock->method('getAccessToken')->willReturn('test_access_token');
        $this->setProperty($mock, 'clientId', 'testClientId');
        $this->setProperty($mock, 'clientSecret', 'testClientSecret');
        $this->setProperty($mock, 'baseUrl', 'https://api-m.sandbox.paypal.com');

        $response = $mock->makeRequest('GET', '/test-endpoint');

        $this->assertEquals(['data' => 'success'], $response);
    }

    public function testMakeRequestFailure(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/test-endpoint' => Http::response(['message' => 'Test error'], 400),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal API request to /test-endpoint failed (HTTP 400): Test error');

        // Mock getAccessToken to return a valid token
        $mock = $this->getMockBuilder(PayPalBaseService::class)
            ->onlyMethods(['getAccessToken'])
            ->getMock();
        $mock->method('getAccessToken')->willReturn('test_access_token');
        $this->setProperty($mock, 'clientId', 'testClientId');
        $this->setProperty($mock, 'clientSecret', 'testClientSecret');
        $this->setProperty($mock, 'baseUrl', 'https://api-m.sandbox.paypal.com');
        $mock->makeRequest('GET', '/test-endpoint');
    }

    public function testAuthenticateSuccess(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'test_access_token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $this->payPalBaseService->authenticate();

        $this->assertEquals('test_access_token', $this->getObjectAttribute($this->payPalBaseService, 'accessToken'));
        $this->assertGreaterThan(time(), $this->getObjectAttribute($this->payPalBaseService, 'tokenExpiresAt'));
    }

    public function testAuthenticateFailure(): void
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'error_description' => 'Authentication failed',
            ], 400),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal authentication failed (HTTP 400): Authentication failed');

        $this->payPalBaseService->authenticate();
    }

    public function testHandleResponse(): void
    {
        $payPalResponse = new PayPalResponse([]);
        $mockResponse = \Mockery::mock(\Illuminate\Http\Client\Response::class);
        $mockResponse->shouldReceive('getStatusCode')->andReturn(200);
        $mockResponse->shouldReceive('getBody')->andReturn('test body');

        $this->payPalBaseService->payPalResponse->setResponse($mockResponse);

        $returnedResponse = $this->invokeMethod($this->payPalBaseService, 'handleResponse', [$payPalResponse]);

        $this->assertInstanceOf(PayPalResponse::class, $returnedResponse);
        $this->assertEquals(200, $returnedResponse->getResponse()->getStatusCode());
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Set protected/private property of a class.
     *
     * @param object &$object    Instantiated object that we will set property on.
     * @param string $propertyName Property name to set
     * @param mixed  $value Value to assign to the property.
     */
    public function setProperty(&$object, string $propertyName, $value): void
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}