<?php

namespace App\Http\Controllers\Api\Shipping\Zone\Country;

use App\Http\Controllers\Controller;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Shipping\ShippingZoneResource;
use App\Models\Country;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingZoneCountryController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
        private ShippingZoneRepository $shippingZoneRepository,
    )
    {
    }
    public function index(ShippingZone $shippingZone, Request $request) {
        $this->shippingZoneRepository->setQuery(
            $shippingZone->countries()
        );
        $this->shippingZoneRepository->setPagination(true);
        $this->shippingZoneRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->shippingZoneRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->shippingZoneRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingZoneRepository->setPage(
            $request->get('page', 1)
        );

        return CountryResource::collection(
            $this->shippingZoneRepository->findMany()
        );
    }

    public function store(ShippingZone $shippingZone, Country $country, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);
        if ($shippingZone->countries()->where('country_id', $country->id)->exists()) {
            return response()->json([
                'message' => 'Country already exists in shipping zone',
            ], Response::HTTP_BAD_REQUEST);
        }
        $shippingZone->countries()->attach(
            $country->id
        );
        return response()->json([
            'message' => 'Shipping zone created',
        ], Response::HTTP_OK);
    }

    public function destroy(ShippingZone $shippingZone, Country $country, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);

        $shippingZone->countries()->detach(
            $country->id
        );

        return response()->json([
            'message' => 'Shipping zone deleted',
        ], Response::HTTP_OK);
    }
}
