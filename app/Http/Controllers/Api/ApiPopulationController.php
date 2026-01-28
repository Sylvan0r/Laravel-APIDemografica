<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Population;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiPopulationController extends Controller
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
    public function show(Population $population)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Population $population)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Population $population)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Population $population)
    {
        //
    }

    public function evolution(Request $request)
    {
        $query = DB::table('population')
            ->select(
                'year',
                'gdc_isla',
                'gdc_municipio',
                DB::raw('SUM(population) as total_population'),
                DB::raw('AVG(proportion) as avg_proportion')
            )
            ->groupBy('year', 'gdc_isla', 'gdc_municipio');

        // Filtros combinables
        if ($request->filled('gdc_isla')) {
            $query->where('gdc_isla', $request->gdc_isla);
        }

        if ($request->filled('gdc_municipio')) {
            $query->where('gdc_municipio', $request->gdc_municipio);
        }

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

        // Orden
        if ($request->filled('order_by')) {
            $query->orderBy($request->order_by, $request->get('order_dir', 'asc'));
        } else {
            $query->orderBy('year');
        }

        return response()->json($query->get());
    }    
}
