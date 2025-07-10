<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="SaaS API Documentation",
 *     description="API documentation for the SaaS platform with authentication, user management, company management, and plan management.",
 *     @OA\Contact(
 *         email="admin@saas.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter the token without the 'Bearer ' prefix"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for user management"
 * )
 * 
 * @OA\Tag(
 *     name="Companies",
 *     description="API Endpoints for company management"
 * )
 * 
 * @OA\Tag(
 *     name="Plans",
 *     description="API Endpoints for plan management"
 * )
 */
class SwaggerController extends Controller
{
    //
} 