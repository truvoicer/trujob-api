<?php

namespace App\Services\Locale;

use App\Models\Address;
use App\Services\BaseService;

class AddressService extends BaseService
{

    public function updateDefaults(Address $address, array $data)
    {
        if (!empty($data['is_default'])) {
            $this->user->addresses()
            ->where('addresses.id', '!=', $address->id)
            ->where('addresses.type', $address->type)
            ->update(['is_default' => false]);
        }
    }

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
        $this->updateDefaults($address, $data);
        return true;
    }

    public function updateAddress(Address $address, array $data) {
        if (!$address->update($data)) {
            throw new \Exception('Error updating address');
        }
        $this->updateDefaults($address, $data);
        return true;
    }

    public function deleteAddress(Address $address) {
        if (!$address->delete()) {
            throw new \Exception('Error deleting address');
        }
        return true;
    }


}
