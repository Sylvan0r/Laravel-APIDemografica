<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Isla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        //
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

    public function population(Request $request)
    {
        $query = DB::table('population')
            ->join('isla', 'population.gdc_isla', '=', 'isla.gdc_isla')
            ->select(
                'isla.name as isla',
                'population.gdc_isla',
                DB::raw('SUM(population.population) as total_population'),
                DB::raw('AVG(population.proportion) as avg_proportion')
            )
            ->groupBy('isla.name', 'population.gdc_isla');

        // Filtros combinables
        if ($request->filled('gender')) {
            $query->where('population.gender', $request->gender);
        }

        if ($request->filled('age')) {
            $query->where('population.age', $request->age);
        }

        if ($request->filled('age_min')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(population.age, " ", 1) AS UNSIGNED) >= ?', [$request->age_min]);
        }

        if ($request->filled('age_max')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(population.age, " ", 1) AS UNSIGNED) <= ?', [$request->age_max]);
        }

        if ($request->filled('year')) {
            $query->where('population.year', $request->year);
        }

        // Orden
        if ($request->filled('order_by')) {
            $query->orderBy($request->order_by, $request->get('order_dir', 'asc'));
        } else {
            $query->orderBy('isla');
        }

        return response()->json($query->get());
    }

    /**
     * Buscar islas por nombre
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $islas = DB::table('isla')
            ->where('name', 'like', "%$q%")
            ->orderBy('name')
            ->get();

        return response()->json($islas);
    }    
}
