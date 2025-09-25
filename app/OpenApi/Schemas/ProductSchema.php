<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product model",
 *     required={"id", "title", "slug", "price", "status"},
 *
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Product ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Product title",
 *         example="iPhone 15 Pro"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Product slug",
 *         example="iphone-15-pro"
 *     ),
 *     @OA\Property(
 *         property="summary",
 *         type="string",
 *         description="Product summary",
 *         example="Latest iPhone with advanced features"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Product description",
 *         example="The iPhone 15 Pro features a titanium design, A17 Pro chip, and advanced camera system."
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Product price",
 *         example=999.99
 *     ),
 *     @OA\Property(
 *         property="special_price",
 *         type="number",
 *         format="float",
 *         description="Special/discounted price",
 *         example=899.99,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="special_price_start",
 *         type="string",
 *         format="date-time",
 *         description="Special price start date",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="special_price_end",
 *         type="string",
 *         format="date-time",
 *         description="Special price end date",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="stock",
 *         type="integer",
 *         description="Available stock quantity",
 *         example=50
 *     ),
 *     @OA\Property(
 *         property="sku",
 *         type="string",
 *         description="Product SKU",
 *         example="IPH15PRO128"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"active", "inactive"},
 *         description="Product status",
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="is_featured",
 *         type="boolean",
 *         description="Whether product is featured",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="d_deal",
 *         type="boolean",
 *         description="Whether product is a daily deal",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="brand_id",
 *         type="integer",
 *         description="Brand ID",
 *         example=1,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="brand",
 *         ref="#/components/schemas/Brand",
 *         description="Product brand"
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Category"),
 *         description="Product categories"
 *     ),
 *
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Tag"),
 *         description="Product tags"
 *     ),
 *
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Media"),
 *         description="Product images"
 *     ),
 *
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class ProductSchema
{
    // This class is only for documentation purposes
}
