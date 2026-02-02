<?php

declare(strict_types=1);

namespace App\Swagger;

use OpenApi\Attributes as OA;

/**
 * Class to declare a simple attribute-based path so OpenAPI detects at least one PathItem.
 */
#[OA\PathItem(
    path: "/api/ping",
    get: new OA\Get(
        summary: "Ping",
        description: "Endpoint de verificación",
        tags: ["Misc"],
        responses: [new OA\Response(response: 200, description: "OK")]
    )
)]
final class PingPath
{
}
