<?php

namespace App\Services\Locale;

use App\Models\Address;
use App\Services\BaseService;

class AddressService extends BaseService
{

    public function createAddressBatch(array $data) {
        $addresses = $this->user->addresses()->saveMany($data);
        if (!$addresses->count()) {
            throw new \Exception('Error creating address batch');
        }
        return true;
    }
    public function createAddress(array $data) {
        $address = $this->user->addresses()->create($data);
        if (!$address->exists()) {
            throw new \Exception('Error creating address');
        }
        return true;
    }

    public function updateAddress(Address $address, array $data) {
        if (!$address->update($data)) {
            throw new \Exception('Error updating address');
        }
        return true;
    }

    public function deleteAddress(Address $address) {
        if (!$address->delete()) {
            throw new \Exception('Error deleting address');
        }
        return true;
    }


}
