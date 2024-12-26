<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingFollow;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingFollowService
{

    private User $user;
    private Request $request;

    private Listing $listing;
    private ListingFollow $listingFollow;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createListingFollow(array $data) {
        $this->listingFollow = new ListingFollow($data);
        $createListing = $this->listing->listingFollow()->save($this->listingFollow);
        if (!$createListing) {
            $this->addError('Error creating listing follow for user', $data);
            return false;
        }
        return true;
    }

    public function updateListingFollow(array $data) {
        $this->listingFollow->fill($data);
        $saveListingFollow = $this->listingFollow->save();
        if (!$saveListingFollow) {
            $this->addError('Error saving listing follow', $data);
            return false;
        }
        return true;
    }

    public function deleteListingFollow() {
        if (!$this->listingFollow->delete()) {
            $this->addError('Error deleting listing follow');
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
     * @param ListingFollow $listingFollow
     */
    public function setListingFollow(ListingFollow $listingFollow): void
    {
        $this->listingFollow = $listingFollow;
    }

    /**
     * @return ListingFollow
     */
    public function getListingFollow(): ListingFollow
    {
        return $this->listingFollow;
    }


}
