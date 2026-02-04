<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Municipios", description: "Datos demográficos por municipio")]
class ApiMunicipioController extends Controller
{
    #[OA\Get(
        path: "/api/municipios/{gdc}/population",
        tags: ["Municipios"],
        summary: "Población por municipio",
        description: "Devuelve la población de un municipio con filtros combinables",
        parameters: [
            new OA\Parameter(
                name: "gdc",
                in: "path",
                required: true,
                description: "Código GDC del municipio",
                schema: new OA\Schema(type: "string", example: "38024")
            ),
            new OA\Parameter(name: "year", in: "query", description: "Año", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "gender", in: "query", description: "Género (T, M, F)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "age", in: "query", description: "Edad exacta", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "age_min", in: "query", description: "Edad mínima", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "age_max", in: "query", description: "Edad máxima", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "order_by", in: "query", description: "Campo de orden (age, population)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "order_dir", in: "query", description: "asc | desc", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Datos de población del municipio"),
            new OA\Response(response: 404, description: "Municipio sin datos")
        ]
    )]
    public function population(Request $request, string $gdc)
    {
        $year = $request->filled('year')
            ? (int) $request->year
            : DB::table('population')
                ->where('gdc_municipio', $gdc)
                ->max('year');

        if (!$year) {
            return response()->json([
                'message' => 'No hay datos para este municipio',
                'gdc_municipio' => $gdc
            ], 404);
        }

        $query = DB::table('population')
            ->select(
                'year',
                'gdc_municipio',
                DB::raw('SUM(population) as total_population'),
                DB::raw('AVG(proportion) as avg_proportion')
            )
            ->where('gdc_municipio', $gdc)
            ->where('year', $year);

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }else{
            $query->where('gender', 'T');
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

        $query->groupBy('year', 'gdc_municipio');

        $orderBy = $request->get('order_by', 'year');
        $orderDir = strtolower($request->get('order_dir', 'asc'));

        $allowedOrderColumns = ['year', 'total_population', 'avg_proportion'];

        if (!in_array($orderBy, $allowedOrderColumns)) {
            $orderBy = 'year';
        }

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        $data = $query->orderBy($orderBy, $orderDir)->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'No hay datos para este municipio',
                'gdc_municipio' => $gdc,
                'year' => $year
            ], 404);
        }

        return response()->json([
            'gdc_municipio' => $gdc,
            'year' => $year,
            'data' => $data
        ]);
    }

    #[OA\Get(
        path: "/api/municipios/search",
        tags: ["Municipios"],
        summary: "Buscar municipios",
        description: "Busca municipios por nombre (filtrado con key 'name') con orden configurable",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                description: "Texto de búsqueda dentro del nombre del municipio",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "order_by",
                in: "query",
                description: "Campo de orden (id | isla_name | name | isla_id | gdc_municipio | gdc_isla)",
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
            new OA\Response(response: 200, description: "Listado de municipios filtrados y ordenados")
        ]
    )]
    public function search(Request $request)
    {
        $name = $request->get('name', '');
        $orderBy  = $request->get('order_by', 'name');
        $orderDir = strtolower($request->get('order_dir', 'asc'));

        if (!in_array($orderBy, ['id', 'isla_name', 'name', 'isla_id', 'gdc_municipio', 'gdc_isla'])) {
            $orderBy = 'name';
        }

        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        return DB::table('municipio')
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%'])
            ->orderBy($orderBy, $orderDir)
            ->get();
    }
}