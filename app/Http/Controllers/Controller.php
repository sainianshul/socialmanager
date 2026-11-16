<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API endpoints for SocialManager Platform',
    title: 'SocialManager API Documentation',
    contact: new OA\Contact(email: 'admin@socialmanager.com')
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'Login with phone and OTP to get Sanctum token',
    name: 'Bearer Token',
    in: 'header',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    //
}
