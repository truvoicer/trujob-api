<?php

namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\ListingMedia;
use App\Models\ListingReview;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingReviewService
{

    private User $user;
    private Request $request;

    private Listing $listing;
    private listingReview $listingReview;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createlistingReview(array $data) {
        $this->listingReview = new listingReview($data);
        $createListing = $this->listing->listingReview()->save($this->listingReview);
        if (!$createListing) {
            $this->addError('Error creating listing review for user', $data);
            return false;
        }
        return true;
    }

    public function updatelistingReview(array $data) {
        $this->listingReview->fill($data);
        $saveListingReview = $this->listingReview->save();
        if (!$saveListingReview) {
            $this->addError('Error saving listing review', $data);
            return false;
        }
        return true;
    }

    public function deletelistingReview() {
        if (!$this->listingReview->delete()) {
            $this->addError('Error deleting listing review');
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
     * @param listingReview $listingReview
     */
    public function setListingReview(listingReview $listingReview): void
    {
        $this->listingReview = $listingReview;
    }

    /**
     * @return listingReview
     */
    public function getListingReview(): listingReview
    {
        return $this->listingReview;
    }


}
