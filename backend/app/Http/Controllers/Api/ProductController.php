<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints para gerenciamento de produtos"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Criar um novo produto",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "quantity"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Martelo"),
     *             @OA\Property(property="description", type="string", example="Martelo de aço com cabo de madeira"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=29.90),
     *             @OA\Property(property="quantity", type="integer", minimum=0, example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Produto criado com sucesso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Martelo"),
     *                 @OA\Property(property="description", type="string", example="Martelo de aço com cabo de madeira"),
     *                 @OA\Property(property="price", type="number", format="float", example=29.90),
     *                 @OA\Property(property="quantity", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="O campo nome é obrigatório")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data' => $product
        ], 201);
    }
}
