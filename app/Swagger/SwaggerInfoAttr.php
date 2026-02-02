<?php

declare(strict_types=1);

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API Demográfica Canarias",
    version: "1.0.0",
    description: "API para consulta de datos demográficos por islas y municipios",
    contact: new OA\Contact(email: "admin@demo.local")
)]
final class SwaggerInfoAttr
{
}
