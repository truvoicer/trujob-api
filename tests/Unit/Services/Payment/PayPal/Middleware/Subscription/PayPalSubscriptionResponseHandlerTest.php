<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Subscription;

use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionResponseHandler;
use Tests\TestCase;

class PayPalSubscriptionResponseHandlerTest extends TestCase
{
    /**
     * Test case for getSubscriptionId method when the response is successful and contains an ID.
     *
     * @return void
     */
    public function testGetSubscriptionIdSuccess(): void
    {
        $responseData = ['id' => 'SUB-1234567890ABCDEFGHI'];
        $response = new PayPalSubscriptionResponseHandler(true, $responseData);

        $subscriptionId = $response->getSubscriptionId();

        $this->assertEquals('SUB-1234567890ABCDEFGHI', $subscriptionId);
    }

    /**
     * Test case for getSubscriptionId method when the response is successful but does not contain an ID.
     *
     * @return void
     */
    public function testGetSubscriptionIdSuccessNoId(): void
    {
        $responseData = [];
        $response = new PayPalSubscriptionResponseHandler(true, $responseData);

        $subscriptionId = $response->getSubscriptionId();

        $this->assertNull($subscriptionId);
    }

    /**
     * Test case for getSubscriptionId method when the response is not successful.
     *
     * @return void
     */
    public function testGetSubscriptionIdFailure(): void
    {
        $responseData = ['id' => 'SUB-1234567890ABCDEFGHI'];
        $response = new PayPalSubscriptionResponseHandler(false, $responseData);

        $subscriptionId = $response->getSubscriptionId();

        $this->assertNull($subscriptionId);
    }

    /**
     * Test case for getSubscriptionId method when the response is successful and the 'id' value is null.
     *
     * @return void
     */
    public function testGetSubscriptionIdSuccessIdIsNull(): void
    {
        $responseData = ['id' => null];
        $response = new PayPalSubscriptionResponseHandler(true, $responseData);

        $subscriptionId = $response->getSubscriptionId();

        $this->assertNull($subscriptionId);
    }
}