<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Isla;
use Illuminate\Http\Request;

class ApiIslaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Isla $isla)
    {
        return response()->json([
            'id' => $isla->id,
            'name' => $isla->name,
            'gdc_isla' => $isla->gdc_isla,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Isla $isla)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Isla $isla)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Isla $isla)
    {
        //
    }
}
