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
        tags: ["Population"],
        summary: "Evolución de población",
        parameters: [
            new OA\Parameter(name: "gdc_isla", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "gdc_municipio", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "gender", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "age", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "age_min", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "age_max", in: "query", schema: new OA\Schema(type: "integer")),
        ],
        responses: [ new OA\Response(response: 200, description: "Evolución demográfica") ]
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
            )
            ->groupBy('year', 'gdc_isla', 'gdc_municipio');

        foreach (['gdc_isla', 'gdc_municipio', 'gender', 'age'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        return response()->json(
            $query->orderBy('year')->get()
        );
    }
}
