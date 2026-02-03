<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Islas", description: "Operaciones relacionadas con islas y población")]
class ApiIslaController extends Controller
{
    #[OA\Get(
        path: "/api/isla/population",
        tags: ["Islas"],
        summary: "Mostrar población por isla",
        description: "Devuelve población total por isla con filtros combinables",
        parameters: [
            new OA\Parameter(name: "gender", in: "query", description: "Género (M, F, T)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "age", in: "query", description: "Edad exacta (numérica)", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "age_min", in: "query", description: "Edad mínima", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "age_max", in: "query", description: "Edad máxima", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "year", in: "query", description: "Año", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "order_by", in: "query", description: "Campo de orden (isla, total_population)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "order_dir", in: "query", description: "asc | desc", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Listado de población por isla")
        ]
    )]
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

        if ($request->filled('gender')) {
            $query->where('population.gender', $request->gender);
        }

        if ($request->filled('age')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(population.age, ' ', 1) AS UNSIGNED) = ?",
                [(int) $request->age]
            );
        }

        if ($request->filled('age_min')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(population.age, ' ', 1) AS UNSIGNED) >= ?",
                [$request->age_min]
            );
        }

        if ($request->filled('age_max')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(population.age, ' ', 1) AS UNSIGNED) <= ?",
                [$request->age_max]
            );
        }

        if ($request->filled('year')) {
            $query->where('population.year', $request->year);
        }

        $orderBy = $request->get('order_by', 'isla');
        $orderDir = $request->get('order_dir', 'asc');

        if (!in_array($orderBy, ['isla', 'total_population'])) {
            $orderBy = 'isla';
        }

        $query->orderBy($orderBy, $orderDir);

        return response()->json($query->get());
    }

    #[OA\Get(
        path: "/api/isla/search",
        tags: ["Islas"],
        summary: "Buscar islas",
        description: "Busca islas por nombre (filtrado con key 'name') con orden configurable",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                description: "Texto de búsqueda dentro del nombre de la isla",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "order_by",
                in: "query",
                description: "Campo de orden (id | gdc_isla | name)",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "order_dir",
                in: "query",
                description: "asc | desc",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Listado de islas filtradas y ordenadas")
        ]
    )]
    public function search(Request $request)
    {
        $name = $request->get('name', '');
        $orderBy  = $request->get('order_by', 'name');
        $orderDir = strtolower($request->get('order_dir', 'asc'));

        if (!in_array($orderBy, ['id', 'gdc_isla', 'name'])) {
            $orderBy = 'name';
        }

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        return DB::table('isla')
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%'])
            ->orderBy($orderBy, $orderDir)
            ->get();
    }
}