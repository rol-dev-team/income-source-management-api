<?php

namespace App\Http\Controllers;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
      public function getAllSources(Request $request)
    {
        $source = Source::all();


        return response()->json([
            'status' => true,
            'message' => 'Sources retrieved successfully.',
            'data' => $source,

        ], 200);
        
    }
    public function index(Request $request)
    {
        $source = Source::paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Sources retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = Source::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Source created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = Source::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Source not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Source retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = Source::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Source not found.'
            ], 404);
        }

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'source_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|integer|min:0|max:1',
            'remarks' => 'nullable|string',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Source updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = Source::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Source not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Source deleted successfully.'
        ], 200);
    }
}
