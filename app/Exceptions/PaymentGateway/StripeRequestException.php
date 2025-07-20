<?php

namespace App\Exceptions\PaymentGateway;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class StripeRequestException extends Exception
{
    protected array $data;

    public function __construct(
        array $data,
        ?string $message = 'Stripe request failed.',
        ?int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
    ) {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    public function getErrorDetails(): array
    {
        return $this->data;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'data' => $this->getErrorDetails(),
        ], $this->getCode());
    }
}
