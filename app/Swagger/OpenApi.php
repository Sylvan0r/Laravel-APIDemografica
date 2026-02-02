<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Demográfica Canarias",
 *     description="API para consulta de datos demográficos por islas y municipios",
 *     @OA\Contact(
 *         email="admin@demo.local"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Servidor local"
 * )
 *
 * @OA\PathItem(
 *     path="/api/ping",
 *     @OA\Get(
 *         summary="Ping",
 *         description="Endpoint de verificación",
 *         tags={"Misc"},
 *         @OA\Response(response=200, description="OK")
 *     )
 * )
 */

