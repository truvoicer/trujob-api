<?php
namespace App\Exceptions\JWT;

use Illuminate\Http\JsonResponse;

class JWTException extends \Exception
{

    protected $message = 'An error occurred while processing the JWT token.';

    public function __construct(?string $message = null, int $code = 0)
    {
        if ($message) {
            $this->message = $message;
        }
        parent::__construct($this->message, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
            'code' => $this->getCode(),
        ], 400);
    }

}
