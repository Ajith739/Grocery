<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;

class ProductController extends Controller
{
    // ✅ GET /api/products
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            $products = $query->get();

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully',
                'data' => $products
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
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product fetched successfully',
                'data' => $product
            ], 200);

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
        'category' => 'required|string',
        'price' => 'required|numeric',
        'originalPrice' => 'nullable|numeric',
        'weight' => 'nullable|string',
        'rating' => 'nullable|numeric',
        'reviews' => 'nullable|integer',
        'brand' => 'nullable|string',
        'discount' => 'nullable|integer',
        'bgColor' => 'nullable|string',
    ]);

            $validated['product_code'] = 'p' . rand(100, 999);

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
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product->update($request->all());

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
}