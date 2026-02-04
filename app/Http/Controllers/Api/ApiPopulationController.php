<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Population", description: "Evolución y análisis de población")]
class ApiPopulationController extends Controller
{
    #[OA\Get(
        path: "/api/population/evolution",
        summary: "Evolución de población por municipio, isla o Canarias",
        tags: ["Population"],
        parameters: [
            new OA\Parameter(
                name: "level",
                in: "query",
                required: false,
                description: "Nivel de agregación: municipio | isla | canarias",
                schema: new OA\Schema(type: "string", enum: ["municipio", "isla", "canarias"])
            ),
            new OA\Parameter(name: "gdc_isla", in: "query", schema: new OA\Schema(type: "string"), description: "Código GDC de la isla"),
            new OA\Parameter(name: "gdc_municipio", in: "query", schema: new OA\Schema(type: "string"), description: "Código GDC del municipio"),
            new OA\Parameter(name: "gender", in: "query", schema: new OA\Schema(type: "string", enum: ["H", "M", "T"]), description: "Género"),
            new OA\Parameter(name: "age", in: "query", schema: new OA\Schema(type: "integer"), description: "Edad exacta"),
            new OA\Parameter(name: "age_min", in: "query", schema: new OA\Schema(type: "integer"), description: "Edad mínima"),
            new OA\Parameter(name: "age_max", in: "query", schema: new OA\Schema(type: "integer"), description: "Edad máxima"),
            new OA\Parameter(name: "year", in: "query", schema: new OA\Schema(type: "integer"), description: "Año"),
            new OA\Parameter(name: "municipio_name", in: "query", schema: new OA\Schema(type: "string"), description: "Nombre del municipio"),
            new OA\Parameter(name: "order_by", in: "query", description: "Campo de orden", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "order_dir", in: "query", description: "asc | desc", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Datos de evolución demográfica")
        ]
    )]
    public function evolution(Request $request)
    {
        $level = $request->get('level', 'municipio');

        $query = DB::table('population');

        if ($level === 'municipio') {
            $query->select(
                'population.year',
                'population.gdc_isla',
                'population.gdc_municipio',
                'municipio.name as municipio_name',
                DB::raw('SUM(population.population) as total_population'),
                DB::raw('AVG(population.proportion) as avg_proportion')
            )
            ->join('municipio', 'population.gdc_municipio', '=', 'municipio.gdc_municipio');
        } else {
            $query->select(
                'year',
                'gdc_isla',
                DB::raw('SUM(population) as total_population'),
                DB::raw('AVG(proportion) as avg_proportion')
            );
        }

        switch ($level) {
            case 'municipio':
                $query->whereNotNull('population.gdc_municipio');
                break;

            case 'isla':
                $query->whereNull('population.gdc_municipio')
                    ->where('population.gdc_isla', '!=', 'ES70');
                break;

            case 'canarias':
                $query->where('population.gdc_isla', 'ES70')
                    ->whereNull('population.gdc_municipio');
                break;
        }

        if ($request->filled('gdc_isla')) {
            $query->where('population.gdc_isla', $request->gdc_isla);
        }

        if ($request->filled('gdc_municipio') && $level === 'municipio') {
            $query->where('population.gdc_municipio', $request->gdc_municipio);
        }

        if ($request->filled('municipio_name') && $level === 'municipio') {
            $name = strtolower($request->municipio_name);
            $query->whereRaw('LOWER(municipio.name) LIKE ?', ["%{$name}%"]);
        }

        if ($request->filled('gender')) {
            $query->where('population.gender', $request->gender);
        }else{
            $query->where('population.gender', 'T');
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
                [(int) $request->age_min]
            );
        }

        if ($request->filled('age_max')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(population.age, ' ', 1) AS UNSIGNED) <= ?",
                [(int) $request->age_max]
            );
        }

        if ($request->filled('year')) {
            $query->where('population.year', (int) $request->year);
        } else {
            $maxYear = DB::table('population')->max('year');
            $query->where('population.year', $maxYear);
        }

        if ($level === 'municipio') {
            $query->groupBy('population.year', 'population.gdc_isla', 'population.gdc_municipio', 'municipio.name');
        } else {
            $query->groupBy('year', 'gdc_isla');
        }

        $orderBy = $request->get('order_by', 'year');
        $orderDir = strtolower($request->get('order_dir', 'asc'));

        $allowedOrderColumns = [
            'year',
            'total_population',
            'avg_proportion',
            'gdc_isla',
            'gdc_municipio',
            'municipio_name'
        ];

        if (!in_array($orderBy, $allowedOrderColumns)) {
            $orderBy = 'year';
        }

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        $query->orderBy($orderBy, $orderDir);

        return response()->json([
            'level' => $level,
            'data' => $query->get()
        ]);
    }
}