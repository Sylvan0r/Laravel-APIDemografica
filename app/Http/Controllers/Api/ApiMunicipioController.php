<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiMunicipioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $gdc)
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
    public function show(string $gdc)
    {
        $data = DB::table('population')
            ->where('gdc_municipio', $gdc)
            ->whereNotNull('population')
            ->orderBy('year')
            ->orderBy('age')
            ->get([
                'year',
                'age',
                'gender',
                'population',
                'proportion',
            ]);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No hay datos para el municipio indicado',
                'gdc_municipio' => $gdc,
            ], 404);
        }

        return response()->json([
            'gdc_municipio' => $gdc,
            'total_records' => $data->count(),
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Municipio $municipio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Municipio $municipio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Municipio $municipio)
    {
        //
    }
}
