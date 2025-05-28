<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserMediaRequest;
use App\Http\Requests\User\UpdateUserMediaRequest;
use App\Models\UserMedia;

class UserMediaController extends Controller
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
    public function store()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\StoreUserMediaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserMediaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserMedia  $userMedia
     * @return \Illuminate\Http\Response
     */
    public function show(UserMedia $userMedia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserMedia  $userMedia
     * @return \Illuminate\Http\Response
     */
    public function edit(UserMedia $userMedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\UpdateUserMediaRequest  $request
     * @param  \App\Models\UserMedia  $userMedia
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserMediaRequest $request, UserMedia $userMedia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserMedia  $userMedia
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserMedia $userMedia)
    {
        //
    }
}
