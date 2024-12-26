<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingTransactionRequest;
use App\Http\Requests\Listing\UpdateListingTransactionRequest;
use App\Models\ListingTransaction;

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
     * @param  \App\Models\ListingTransaction  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(ListingTransaction $listingTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ListingTransaction  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(ListingTransaction $listingTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Listing\UpdateListingTransactionRequest  $request
     * @param  \App\Models\ListingTransaction  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateListingTransactionRequest $request, ListingTransaction $listingTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ListingTransaction  $listingTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListingTransaction $listingTransaction)
    {
        //
    }
}
