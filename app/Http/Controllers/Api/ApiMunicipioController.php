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
        path: "/api/islas/population",
        summary: "Población total por isla",
        description: "Devuelve la población total por isla con filtros opcionales",
        tags: ["Islas"],
        parameters: [
            new OA\Parameter(name: "year", in: "query", description: "Año", required: false, schema: new OA\Schema(type: "integer", example: 2023)),
            new OA\Parameter(name: "gender", in: "query", description: "Género (T, M, F)", required: false, schema: new OA\Schema(type: "string", example: "T")),
        ],
        responses: [ new OA\Response(response: 200, description: "Listado de población por isla") ]
    )]
    public function population(Request $request, string $gdc)
    {
        $query = DB::table('population')->where('gdc_municipio', $gdc);

        foreach (['gender', 'age', 'year'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->$field);
            }
        }

        if ($request->filled('age_min')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(age," ",1) AS UNSIGNED) >= ?', [$request->age_min]);
        }
        if ($request->filled('age_max')) {
            $query->whereRaw('CAST(SUBSTRING_INDEX(age," ",1) AS UNSIGNED) <= ?', [$request->age_max]);
        }

        return response()->json(
            $query->orderBy($request->get('order_by', 'age'))->get()
        );
    }

    #[OA\Get(
        path: "/api/municipios/search",
        tags: ["Municipios"],
        summary: "Buscar municipios",
        parameters: [ new OA\Parameter(name: "q", in: "query", required: true, schema: new OA\Schema(type: "string")) ],
        responses: [ new OA\Response(response: 200, description: "Listado de municipios") ]
    )]
    public function search(Request $request)
    {
        return DB::table('municipio')
            ->where('name', 'like', '%' . $request->q . '%')
            ->orderBy('name')
            ->get();
    }
}
