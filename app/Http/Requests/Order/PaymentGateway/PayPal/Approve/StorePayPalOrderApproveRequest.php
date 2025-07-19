<?php

namespace App\Http\Requests\Order\PaymentGateway\PayPal\Approve;

use Illuminate\Foundation\Http\FormRequest;

class StorePayPalOrderApproveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            'orderID' => ['required', 'string'],
            'subscriptionID' => ['required', 'string'],
            'facilitatorAccessToken' => ['required', 'string'],
            'paymentSource' => ['required', 'string'],
        ];
    }
}
