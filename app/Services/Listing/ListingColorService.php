<?php

namespace App\Services\Listing;

use App\Models\Color;
use App\Models\Listing;
use App\Models\ListingColor;
use App\Models\ListingMedia;
use App\Models\User;
use App\Services\FetchService;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingColorService
{
    use FetchService;

    private User $user;
    private Request $request;

    private Listing $listing;
    private ListingColor $listingColor;
    private Color $color;
    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function colorFetch()
    {
        $color = Color::query();
        if ($this->getPagination()) {
            return $color->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getOffset() ?? null
            );
        }
        return $color->get();
    }
    public function addColorToListing() {
        $this->listingColor = new ListingColor();
        $this->listingColor->color_id = $this->color->id;
        $this->listingColor->listing_id = $this->listing->id;
        $create = $this->listing->listingColor()->save($this->listingColor);
        if (!$create) {
            $this->addError('Error creating listing color for user');
            return false;
        }
        return true;
    }
    public function removeColorFromListing() {
        $this->listingColor = new ListingColor();
        $this->listingColor->color_id = $this->color->id;
        $this->listingColor->listing_id = $this->listing->id;
        $delete = $this->listing->listingColor()->delete($this->listingColor);
        if (!$delete) {
            $this->addError('Error deleting listing color for user');
            return false;
        }
        return true;
    }

    public function createColor(array $data) {
        $this->listingColor = new ListingColor($data);
        $saveListingColor = $this->listingColor->save();
        if (!$saveListingColor) {
            $this->addError('Error saving listing color', $data);
            return false;
        }
        return true;
    }
    public function updateColor(array $data) {
        $this->listingColor->fill($data);
        $saveListingColor = $this->listingColor->save();
        if (!$saveListingColor) {
            $this->addError('Error saving listing color', $data);
            return false;
        }
        return true;
    }

    public function deleteColor() {
        if (!$this->listingColor->delete()) {
            $this->addError('Error deleting listing color');
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
     * @param ListingColor $listingColor
     */
    public function setListingColor(ListingColor $listingColor): void
    {
        $this->listingColor = $listingColor;
    }

    /**
     * @return ListingColor
     */
    public function getListingColor(): ListingColor
    {
        return $this->listingColor;
    }

    /**
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * @param Color $color
     */
    public function setColor(Color $color): void
    {
        $this->color = $color;
    }


}
