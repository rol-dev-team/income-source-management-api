<?php

namespace App\Http\Controllers;

use App\Models\SourceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SourceCategoryController extends Controller
{
   public function getCategoriesBySourceId(string $id)
{
    // Get categories by source_id
    $categories = SourceCategory::where('source_id', $id)->get();

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


    public function dropdown()
    {
        $categories = SourceCategory::orderBy('id')->get();
        return response()->json($categories);
        }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('source_categories as sc')
            ->join('sources as s', 's.id', '=', 'sc.source_id')
            ->select('s.*', 'sc.*')
            ->paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = SourceCategory::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Category created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = SourceCategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = SourceCategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = SourceCategory::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
