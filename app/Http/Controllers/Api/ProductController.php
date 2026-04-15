<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    // ✅ GET /api/products
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $products = $query->with('category')->get();

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully',
                'data' => ProductResource::collection($products)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ GET /api/products/:id
    public function show($id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product fetched successfully',
                'data' => new ProductResource($product)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ POST /api/products
    public function store(Request $request)
    {
        try {
            // dd($request->all());

            $validated = $request->validate([
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
                'originalPrice' => 'nullable|numeric',
                'weight' => 'nullable|string',
                'rating' => 'nullable|numeric',
                'reviews' => 'nullable|integer',
                'brand' => 'nullable|string',
                'discount' => 'nullable|integer',
                'bgColor' => 'nullable|string',
                'quantity' => 'required|integer|min:0'
            ]);

            $validated['product_code'] = Product::generateProductCode();

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ PUT /api/products/:id
    public function update(Request $request, $id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'category_id' => 'sometimes|exists:categories,id',
                'price' => 'sometimes|numeric',
                'originalPrice' => 'nullable|numeric',
                'weight' => 'nullable|string',
                'rating' => 'nullable|numeric',
                'reviews' => 'nullable|integer',
                'brand' => 'nullable|string',
                'discount' => 'nullable|integer',
                'bgColor' => 'nullable|string',
                'quantity' => 'sometimes|integer|min:0'
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ DELETE /api/products/:id
    public function destroy($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNextproduct_code()
    {
        try {
            $nextcode = Product::generateProductCode();

            return response()->json([
                'success' => true,
                'message' => 'Next product code fetched',
                'code' => $nextcode
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ POST /api/products/:id/reduce-stock
    public function reduceStock(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($product->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock'
                ], 400);
            }

            $product->quantity -= $request->quantity;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => $product
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stock update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
