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
            new OA\Parameter(name: "order_by", in: "query", description: "Campo de orden", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "order_dir", in: "query", description: "asc | desc", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Datos de evolución demográfica")
        ]
    )]
    public function evolution(Request $request)
    {
        $query = DB::table('population')
            ->select(
                'year',
                'gdc_isla',
                'gdc_municipio',
                DB::raw('SUM(population) as total_population'),
                DB::raw('AVG(proportion) as avg_proportion')
            );

        $level = $request->get('level', 'municipio');

        switch ($level) {
            case 'municipio':
                $query->whereNotNull('gdc_municipio');
                break;

            case 'isla':
                $query->whereNull('gdc_municipio')
                      ->where('gdc_isla', '!=', 'ES70');
                break;

            case 'canarias':
                $query->where('gdc_isla', 'ES70');
                break;
        }

        if ($request->filled('gdc_isla')) {
            $query->where('gdc_isla', $request->gdc_isla);
        }

        if ($request->filled('gdc_municipio')) {
            $query->where('gdc_municipio', $request->gdc_municipio);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('year')) {
            $query->where('year', (int) $request->year);
        }

        if ($request->filled('age')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(age, ' ', 1) AS UNSIGNED) = ?",
                [(int) $request->age]
            );
        }

        if ($request->filled('age_min')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(age, ' ', 1) AS UNSIGNED) >= ?",
                [(int) $request->age_min]
            );
        }

        if ($request->filled('age_max')) {
            $query->whereRaw(
                "CAST(SUBSTRING_INDEX(age, ' ', 1) AS UNSIGNED) <= ?",
                [(int) $request->age_max]
            );
        }

        $query->groupBy('year', 'gdc_isla', 'gdc_municipio');

        $orderBy = $request->get('order_by', 'year');
        $orderDir = strtolower($request->get('order_dir', 'asc'));

        $allowedOrderColumns = [
            'year',
            'total_population',
            'avg_proportion',
            'gdc_isla',
            'gdc_municipio'
        ];

        if (!in_array($orderBy, $allowedOrderColumns)) {
            $orderBy = 'year';
        }

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        $query->orderBy($orderBy, $orderDir);

        return response()->json($query->get());
    }
}