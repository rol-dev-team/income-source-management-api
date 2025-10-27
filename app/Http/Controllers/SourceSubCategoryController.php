<?php

namespace App\Http\Controllers;

use App\Models\SourceSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SourceSubCategoryController extends Controller
{

    public function getSubCategoriesBySourceId(string $id)
{
    // Get categories by source_id
    $categories = SourceSubcategory::where('source_id', $id)->get();

    // If no categories found, return error response
    if ($categories->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'Category not found.'
        ], 404);
    }

    // Return categories
    return response()->json([
        'status' => true,
        'message' => 'Category retrieved successfully.',
        'data' => $categories
    ], 200);
}
    public function dropdownSubCat()
    {
        $subCategories = SourceSubcategory::orderBy('id')->get();
        return response()->json($subCategories);
        }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('source_subcategories as ss')
            ->join('sources as s', 's.id', '=', 'ss.source_id')
            ->select('s.*', 'ss.*')
            ->paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Sub Category retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = SourceSubcategory::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Sub Category created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = SourceSubcategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Sub Category retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = SourceSubcategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found.'
            ], 404);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Sub Category updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = SourceSubcategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully.'
        ], 200);
    }
}
