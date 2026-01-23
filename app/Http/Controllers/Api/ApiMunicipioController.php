<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class ApiMunicipioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Municipio $municipio)
    {
        $query = $municipio->population()
            ->where('type', 'POBLACION');

        // 🔹 Filtros
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('age')) {
            $query->where('age', $request->age);
        }

        // 🔹 Ordenación
        $orderBy = $request->get('order_by', 'age');
        $orderDir = $request->get('order_dir', 'asc');

        $query->orderBy($orderBy, $orderDir);

        return response()->json([
            'municipio' => [
                'name' => $municipio->name,
                'gdc' => $municipio->gdc_municipio,
            ],
            'filters' => $request->only(['year', 'gender', 'age']),
            'data' => $query->get(),
        ]);
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
    public function show(Municipio $municipio)
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
}
