<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Brigade API"
)]
#[OA\Server(
    url: "http://localhost/brigade-api/public",
    description: "Local server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]

abstract class Controller
{
    //
}
