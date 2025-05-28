<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\Locale\AddressResource;
use App\Models\Address;
use App\Repositories\AddressRepository;
use App\Services\Locale\AddressService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{

    public function __construct(
        private AddressService $addressService,
        private AddressRepository $addressRepository
    )
    {
    }
    public function index(Request $request) {
        $this->addressService->setUser($request->user()->user);
        $this->addressService->setSite($request->user()->site);
        $this->addressRepository->setPagination(true);
        $this->addressRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->addressRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->addressRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->addressRepository->setPage(
            $request->get('page', 1)
        );
        $type = $request->get('type', null);
        if ($type) {
            $type = array_map(fn($val) => trim(strtolower($val)), explode(',', $type));
        }
        if (is_array($type)) {
            $this->addressRepository->addWhere(
                'type',
                $type,
                'in',
            );
        }

        $search = $request->get('query', null);
        if ($search) {
            $this->addressRepository->addWhere(
                'label',
                "%$search%",
                'like',
            );
        }

        return AddressResource::collection(
            $this->addressRepository->findMany()
        );
    }

    public function show(Address $address, Request $request) {
        $this->addressService->setUser($request->user()->user);
        $this->addressService->setSite($request->user()->site);
        return new AddressResource($address);
    }

    public function store(StoreAddressRequest $request) {
        $this->addressService->setUser($request->user()->user);
        $this->addressService->setSite($request->user()->site);
        $create = $this->addressService->createAddress($request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating address',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Address created',
        ], Response::HTTP_OK);
    }

    public function update(Address $address, UpdateAddressRequest $request) {
        $this->addressService->setUser($request->user()->user);
        $this->addressService->setSite($request->user()->site);
        $check = $request->user()->user->addresses()->where('id', $address->id)->first();
        if (!$check) {
            return response()->json([
                'message' => 'Address not found',
            ], Response::HTTP_NOT_FOUND);
        }
        $update = $this->addressService->updateAddress($address, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating address',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Address updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Address $address, Request $request) {
        $this->addressService->setUser($request->user()->user);
        $this->addressService->setSite($request->user()->site);
        $check = $request->user()->user->addresses()->where('id', $address->id)->first();
        if (!$check) {
            return response()->json([
                'message' => 'Address not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (!$this->addressService->deleteAddress($address)) {
            return response()->json([
                'message' => 'Error deleting address',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Address deleted',
        ], Response::HTTP_OK);
    }
}
