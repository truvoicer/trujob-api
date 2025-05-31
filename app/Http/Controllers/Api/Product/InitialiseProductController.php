<?php

namespace App\Http\Controllers\Api\Product;

use Illuminate\Http\Request;

class InitialiseProductController extends ProductBaseController
{

    public function __invoke(Request $request)
    {
        $this->productsAdminService->setUser($request->user());
        return $this->sendSuccessResponse(
            'Product created',
            [
                'code' => 'user_can_create_product'
            ],
            $this->productsAdminService->getErrors()
        );
    }
}
