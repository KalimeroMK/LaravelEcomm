<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     title="API Response",
 *     description="Standard API response format",
 *
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if the request was successful",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Response message",
 *         example="Operation completed successfully"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         description="Response data",
 *         oneOf={
 *
 *             @OA\Schema(type="object"),
 *             @OA\Schema(type="array", @OA\Items()),
 *             @OA\Schema(type="string"),
 *             @OA\Schema(type="null")
 *         }
 *     )
 * )
 * @OA\Schema(
 *     schema="ApiError",
 *     type="object",
 *     title="API Error Response",
 *     description="Standard API error response format",
 *
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Always false for error responses",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         @OA\Property(
 *             property="code",
 *             type="string",
 *             description="Error code",
 *             example="VALIDATION_ERROR"
 *         ),
 *         @OA\Property(
 *             property="message",
 *             type="string",
 *             description="Error message",
 *             example="The given data was invalid."
 *         ),
 *         @OA\Property(
 *             property="status_code",
 *             type="integer",
 *             description="HTTP status code",
 *             example=422
 *         ),
 *         @OA\Property(
 *             property="timestamp",
 *             type="string",
 *             format="date-time",
 *             description="Error timestamp"
 *         ),
 *         @OA\Property(
 *             property="path",
 *             type="string",
 *             description="Request path",
 *             example="api/v1/products"
 *         ),
 *         @OA\Property(
 *             property="method",
 *             type="string",
 *             description="HTTP method",
 *             example="POST"
 *         ),
 *         @OA\Property(
 *             property="validation_errors",
 *             type="object",
 *             description="Validation errors (for validation failures)",
 *             example={"title": {"The title field is required."}}
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     title="Paginated Response",
 *     description="Paginated response format",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/ApiResponse"),
 *         @OA\Schema(
 *
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="data",
 *                     type="array",
 *
 *                     @OA\Items(),
 *                     description="Array of items"
 *                 ),
 *
 *                 @OA\Property(
 *                     property="current_page",
 *                     type="integer",
 *                     description="Current page number",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="first_page_url",
 *                     type="string",
 *                     description="First page URL",
 *                     example="http://localhost/api/v1/products?page=1"
 *                 ),
 *                 @OA\Property(
 *                     property="from",
 *                     type="integer",
 *                     description="First item number",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="last_page",
 *                     type="integer",
 *                     description="Last page number",
 *                     example=10
 *                 ),
 *                 @OA\Property(
 *                     property="last_page_url",
 *                     type="string",
 *                     description="Last page URL",
 *                     example="http://localhost/api/v1/products?page=10"
 *                 ),
 *                 @OA\Property(
 *                     property="links",
 *                     type="array",
 *
 *                     @OA\Items(
 *
 *                         @OA\Property(property="url", type="string", nullable=true),
 *                         @OA\Property(property="label", type="string"),
 *                         @OA\Property(property="active", type="boolean")
 *                     ),
 *                     description="Pagination links"
 *                 ),
 *                 @OA\Property(
 *                     property="next_page_url",
 *                     type="string",
 *                     description="Next page URL",
 *                     nullable=true,
 *                     example="http://localhost/api/v1/products?page=2"
 *                 ),
 *                 @OA\Property(
 *                     property="path",
 *                     type="string",
 *                     description="Base path",
 *                     example="http://localhost/api/v1/products"
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="integer",
 *                     description="Items per page",
 *                     example=15
 *                 ),
 *                 @OA\Property(
 *                     property="prev_page_url",
 *                     type="string",
 *                     description="Previous page URL",
 *                     nullable=true,
 *                     example=null
 *                 ),
 *                 @OA\Property(
 *                     property="to",
 *                     type="integer",
 *                     description="Last item number",
 *                     example=15
 *                 ),
 *                 @OA\Property(
 *                     property="total",
 *                     type="integer",
 *                     description="Total items count",
 *                     example=150
 *                 )
 *             )
 *         )
 *     }
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Electronics"),
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Brand",
 *     type="object",
 *     title="Brand",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Apple"),
 *     @OA\Property(property="slug", type="string", example="apple"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     title="Tag",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="smartphone"),
 *     @OA\Property(property="slug", type="string", example="smartphone"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Media",
 *     type="object",
 *     title="Media",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="model_type", type="string", example="Modules\\Product\\Models\\Product"),
 *     @OA\Property(property="model_id", type="integer", example=1),
 *     @OA\Property(property="collection_name", type="string", example="default"),
 *     @OA\Property(property="name", type="string", example="product-image"),
 *     @OA\Property(property="file_name", type="string", example="product-image.jpg"),
 *     @OA\Property(property="mime_type", type="string", example="image/jpeg"),
 *     @OA\Property(property="disk", type="string", example="public"),
 *     @OA\Property(property="size", type="integer", example=1024000),
 *     @OA\Property(property="url", type="string", example="http://localhost/storage/1/product-image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CommonSchemas
{
    // This class is only for documentation purposes
}
