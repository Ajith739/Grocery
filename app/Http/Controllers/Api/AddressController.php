<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    // 🏠 GET /api/address
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)->get();

        return response()->json($addresses);
    }

    // 🏠 POST /api/address
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'pincode' => 'required|string',
            'address_line' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'type' => 'nullable|string',
            'is_default' => 'nullable|boolean'
        ]);

        // ✅ only one default address
        if (!empty($validated['is_default']) && $validated['is_default']) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $validated['user_id'] = $request->user()->id;

        $address = Address::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address added',
            'data' => $address
        ], 201);
    }

    // 🏠 PUT /api/address/:id
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'pincode' => 'sometimes|string',
            'address_line' => 'sometimes|string',
            'city' => 'sometimes|string',
            'state' => 'sometimes|string',
            'type' => 'nullable|string',
            'is_default' => 'nullable|boolean'
        ]);

        if (!empty($validated['is_default']) && $validated['is_default']) {
            Address::where('user_id', $request->user()->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated',
            'data' => $address
        ]);
    }

    // 🏠 DELETE /api/address/:id
    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted'
        ]);
    }
}
