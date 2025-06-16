<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="API de Produtos ConstrUP",
 *     version="1.0.0",
 *     description="Documentação da API de cadastro de produtos"
 * )
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

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar produtos paginados",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de produtos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $products = Product::orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($products);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Detalhes de um produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do produto",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        return response()->json(['data' => $product]);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Editar um produto",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "quantity"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Martelo"),
     *             @OA\Property(property="description", type="string", example="Martelo de aço"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=29.90),
     *             @OA\Property(property="quantity", type="integer", minimum=0, example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Produto atualizado com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);
        $product->update($validated);
        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data' => $product
        ]);
    }
}
