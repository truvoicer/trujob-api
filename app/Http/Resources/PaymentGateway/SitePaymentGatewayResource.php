<?php

namespace App\Http\Resources\PaymentGateway;

use App\Enums\JWT\EncryptedResponse;
use App\Helpers\Response\ResponseHelpers;
use App\Services\JWT\JWTService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PaymentGateway
 */
class SitePaymentGatewayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ResponseHelpers::resourseResponse(
            [
                'id' => $this->id,
                'name' => $this->name,
                'label' => $this->label,
                'description' => $this->description,
                'icon' => $this->icon,
                'is_default' => $this->is_default,
                'is_active' => $this->is_active,
                'is_integrated' => $this->whenLoaded(
                    'sites',
                    $this->sites->where(
                        'id',
                        request()->user()->site?->id
                    )->isNotEmpty()
                ),
                'site' => $this->whenLoaded('sites', function () {
                    $firstSite = $this->sites->first();
                    if (!$firstSite) {
                        return null;
                    }
                    return [
                        'id' => $firstSite->id,
                        'name' => $firstSite->name,
                        'settings' => $firstSite->pivot->settings ?? [],
                        'is_active' => $firstSite->pivot->is_active ?? false,
                        'is_default' => $firstSite->pivot->is_default ?? false,
                        'environment' => $firstSite->pivot->environment
                    ];
                }),
                'settings' => $this->settings,
                'required_fields' => $this->required_fields,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            (
                !empty($this->additional[EncryptedResponse::ENCRYPTED_RESPONSE->value])
            )
        );
    }
}
