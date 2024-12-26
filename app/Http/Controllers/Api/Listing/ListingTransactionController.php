<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingTransactionRequest;
use App\Http\Requests\Listing\UpdateListingTransactionRequest;
use App\Models\ListingPrice;

class ListingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Listing\StoreListingTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreListingTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ListingPrice  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(ListingPrice $listingTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ListingPrice  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(ListingPrice $listingTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Listing\UpdateListingTransactionRequest  $request
     * @param  \App\Models\ListingPrice  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateListingTransactionRequest $request, ListingPrice $listingTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ListingPrice  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListingPrice $listingTransaction)
    {
        //
    }
}
