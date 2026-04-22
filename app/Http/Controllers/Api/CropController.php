<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CropController extends Controller
{
    /**
     * Get all available crops (public)
     */
    public function index(Request $request)
    {
        $query = Crop::with(['farmer'])
            ->where('is_available', true);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price_per_kg', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price_per_kg', '<=', $request->max_price);
        }

        // Search by name or description
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $crops = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'crops' => $crops->items(),
                'pagination' => [
                    'current_page' => $crops->currentPage(),
                    'per_page' => $crops->perPage(),
                    'total' => $crops->total(),
                    'last_page' => $crops->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get specific crop details
     */
    public function show($id)
    {
        $crop = Crop::with(['farmer', 'category', 'images'])
            ->where('is_available', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'crop' => $crop
            ]
        ]);
    }

    /**
     * Get crops by category
     */
    public function byCategory($categoryId, Request $request)
    {
        $crops = Crop::with(['farmer', 'category'])
            ->where('category_id', $categoryId)
            ->where('is_available', true)
            ->whereHas('farmer', function ($query) {
                $query->where('is_verified', true);
            })
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'crops' => $crops->items(),
                'pagination' => [
                    'current_page' => $crops->currentPage(),
                    'per_page' => $crops->perPage(),
                    'total' => $crops->total(),
                    'last_page' => $crops->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        // Return hardcoded categories since categories table doesn't exist
        $categories = [
            ['id' => 1, 'name' => 'Vegetables', 'crops_count' => 0],
            ['id' => 2, 'name' => 'Fruits', 'crops_count' => 0],
            ['id' => 3, 'name' => 'Grains', 'crops_count' => 0],
            ['id' => 4, 'name' => 'Legumes', 'crops_count' => 0],
            ['id' => 5, 'name' => 'Root Crops', 'crops_count' => 0],
            ['id' => 6, 'name' => 'Herbs', 'crops_count' => 0],
            ['id' => 7, 'name' => 'Spices', 'crops_count' => 0],
            ['id' => 8, 'name' => 'Other', 'crops_count' => 0],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories
            ]
        ]);
    }

    /**
     * Get farmer's crops (authenticated)
     */
    public function myCrops(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isFarmer()) {
            return response()->json([
                'success' => false,
                'message' => 'Only farmers can access this endpoint'
            ], 403);
        }

        $query = $user->crops()->with(['category', 'images']);

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $crops = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'crops' => $crops->items(),
                'pagination' => [
                    'current_page' => $crops->currentPage(),
                    'per_page' => $crops->perPage(),
                    'total' => $crops->total(),
                    'last_page' => $crops->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Create new crop (farmers only)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isFarmer()) {
            return response()->json([
                'success' => false,
                'message' => 'Only farmers can create crops'
            ], 403);
        }

        // Check verification
        if (!$user->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be verified to create crops'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price_per_unit' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'location' => 'required|string|max:255',
            'is_available' => 'boolean',
            'images' => 'array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $crop = $user->crops()->create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price_per_unit' => $request->price_per_unit,
            'quantity_available' => $request->quantity_available,
            'unit' => $request->unit,
            'location' => $request->location,
            'is_available' => $request->boolean('is_available', true),
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('crops', 'public');
                $crop->images()->create(['image_path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Crop created successfully',
            'data' => [
                'crop' => $crop->load(['category', 'images'])
            ]
        ], 201);
    }

    /**
     * Update crop (owner only)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $crop = $user->crops()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|string',
            'price_per_unit' => 'sometimes|numeric|min:0',
            'quantity_available' => 'sometimes|integer|min:0',
            'unit' => 'sometimes|string|max:50',
            'location' => 'sometimes|string|max:255',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $crop->update($request->only([
            'name', 'category_id', 'description', 
            'price_per_unit', 'quantity_available', 
            'unit', 'location', 'is_available'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Crop updated successfully',
            'data' => [
                'crop' => $crop->load(['category', 'images'])
            ]
        ]);
    }

    /**
     * Delete crop (owner only)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $crop = $user->crops()->findOrFail($id);

        // Delete associated images
        foreach ($crop->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $crop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Crop deleted successfully'
        ]);
    }
}
