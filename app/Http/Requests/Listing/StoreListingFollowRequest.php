<?php

namespace App\Http\Requests\Listing;

use App\Models\User;
use App\Rules\ExistsInSite;
use Illuminate\Foundation\Http\FormRequest;

class StoreListingFollowRequest extends FormRequest
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
            'user_ids' => [
                'required',
                'array'
            ],
            'user_ids.*' => [
                'required',
                'integer',
                new ExistsInSite(
                    new User(),
                    'sites',
                    request()->user()?->site?->id,
                    'The user with id %s does not exist.',
                    'sites.id'
                ),
            ],
        ];
    }
}
