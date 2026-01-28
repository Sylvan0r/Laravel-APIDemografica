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
        //
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

    public function population(Request $request, string $gdc)
    {
        $query = DB::table('population')
            ->where('gdc_municipio', $gdc);

        // Filtros
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('age')) {
            $query->where('age', $request->age);
        }
        if ($request->filled('age_min')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(age, " ", 1) AS UNSIGNED) >= ?', [$request->age_min]);
        }
        if ($request->filled('age_max')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(age, " ", 1) AS UNSIGNED) <= ?', [$request->age_max]);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Orden
        if ($request->filled('order_by')) {
            $query->orderBy($request->order_by, $request->get('order_dir', 'asc'));
        } else {
            $query->orderBy('age');
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No hay datos para este municipio',
                'gdc_municipio' => $gdc
            ], 404);
        }

        return response()->json($data);
    }

    /**
     * Buscar municipios por nombre
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $municipios = DB::table('municipio')
            ->where('name', 'like', "%$q%")
            ->orderBy('name')
            ->get();

        return response()->json($municipios);
    }    
}
