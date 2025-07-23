<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateException extends Exception
{
    public function __construct(
        $message = "Error updating resource",
        $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
