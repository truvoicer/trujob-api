<?php

namespace App\Http\Resources\Transaction;

use App\Enums\JWT\EncryptedResponse;
use App\Helpers\Response\ResponseHelpers;
use App\Http\Resources\Currency\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\TransactionAmount
 */
class TransactionAmountResource extends JsonResource
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
            'currency' => $this->whenLoaded('currency', function () {
                return CurrencyResource::make($this->currency);
            }),
            'type' => $this->type,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ],
            (
                !empty($this->additional[EncryptedResponse::ENCRYPTED_RESPONSE->value])
            )
        );
    }
}
