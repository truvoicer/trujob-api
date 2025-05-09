<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFeature;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingFeatureService
{

    private User $user;
    private Request $request;

    private Listing $listing;
    private ListingFeature $listingFeature;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createListingFeature(array $data) {
        $this->listingFeature = new ListingFeature($data);
        $createListing = $this->listing->features()->attach($this->listingFeature->id);
        if (!$createListing) {
            $this->addError('Error creating listing feature for user', $data);
            return false;
        }
        return true;
    }

    public function updateListingFeature(array $data) {
        $this->listingFeature->fill($data);
        $saveListingFeature = $this->listingFeature->save();
        if (!$saveListingFeature) {
            $this->addError('Error saving listing feature', $data);
            return false;
        }
        return true;
    }

    public function deleteListingFeature() {
        if (!$this->listingFeature->delete()) {
            $this->addError('Error deleting listing feature');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Listing
     */
    public function getListing(): Listing
    {
        return $this->listing;
    }

    /**
     * @param Listing $listing
     */
    public function setListing(Listing $listing): void
    {
        $this->listing = $listing;
    }

    /**
     * @param ListingFeature $listingFeature
     */
    public function setListingFeature(ListingFeature $listingFeature): void
    {
        $this->listingFeature = $listingFeature;
    }

    /**
     * @return ListingFeature
     */
    public function getListingFeature(): ListingFeature
    {
        return $this->listingFeature;
    }


}
