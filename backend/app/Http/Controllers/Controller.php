<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "Cinema Booking API")]
#[OA\Server(url: "http://localhost", description: "API Server")]
abstract class Controller
{
    //
}
