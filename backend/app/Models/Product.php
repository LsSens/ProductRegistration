<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Product",
 *   type="object",
 *   required={"name", "price", "quantity"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Martelo"),
 *   @OA\Property(property="description", type="string", example="Martelo de aço com cabo de madeira"),
 *   @OA\Property(property="price", type="number", format="float", example=29.90),
 *   @OA\Property(property="quantity", type="integer", example=10),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity'
    ];
}
