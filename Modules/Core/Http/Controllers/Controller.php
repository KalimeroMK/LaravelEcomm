<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Laravel E-commerce API",
 *     version="1.0.0",
 *     description="A comprehensive e-commerce API built with Laravel",
 *
 *     @OA\Contact(
 *         email="admin@laravel-ecomm.com",
 *         name="API Support"
 *     ),
 *
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
 *     description="Enter token in format: Bearer <token>"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Enter token in format: Bearer <token>"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and authorization endpoints"
 * )
 * @OA\Tag(
 *     name="Products",
 *     description="Product management and browsing endpoints"
 * )
 * @OA\Tag(
 *     name="Categories",
 *     description="Product category management endpoints"
 * )
 * @OA\Tag(
 *     name="Brands",
 *     description="Brand management endpoints"
 * )
 * @OA\Tag(
 *     name="Cart",
 *     description="Shopping cart management endpoints"
 * )
 * @OA\Tag(
 *     name="Orders",
 *     description="Order management and processing endpoints"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User profile and account management endpoints"
 * )
 * @OA\Tag(
 *     name="Coupons",
 *     description="Coupon and discount management endpoints"
 * )
 * @OA\Tag(
 *     name="Bundles",
 *     description="Product bundle management endpoints"
 * )
 * @OA\Tag(
 *     name="Wishlists",
 *     description="User wishlist management endpoints"
 * )
 * @OA\Tag(
 *     name="Newsletter",
 *     description="Newsletter subscription management endpoints"
 * )
 * @OA\Tag(
 *     name="Analytics",
 *     description="Analytics and reporting endpoints"
 * )
 * @OA\Tag(
 *     name="Messages",
 *     description="Contact message management endpoints"
 * )
 * @OA\Tag(
 *     name="Posts",
 *     description="Blog post management endpoints"
 * )
 * @OA\Tag(
 *     name="Banners",
 *     description="Banner and promotional content management endpoints"
 * )
 * @OA\Tag(
 *     name="Settings",
 *     description="Application settings management endpoints"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
