<?php

namespace App\Http\Resources\Transaction;

use App\Enums\JWT\EncryptedResponse;
use App\Helpers\Response\ResponseHelpers;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Transaction
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ResponseHelpers::response(
            [
                'id' => $this->id,
                'payment_gateway' => $this->whenLoaded('paymentGateway', PaymentGatewayResource::make($this->paymentGateway)),
                'transaction_amounts' => $this->whenLoaded('transactionAmounts', function () {
                    return TransactionAmountResource::collection($this->transactionAmounts);
                }),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            (
                !empty($this->additional[EncryptedResponse::ENCRYPTED_RESPONSE->value])
            )
        );
    }
}
