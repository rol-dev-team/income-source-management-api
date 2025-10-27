<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getExpenseTypesBySourceId(string $id)
{
    // Get categories by source_id
    $categories = ExpenseType::where('source_id', $id)->get();

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
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('expense_types as et')
            ->join('sources as s', 's.id', '=', 'et.source_id')
            ->select('s.*', 'et.*')
            ->paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Expense type retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = ExpenseType::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Expense type created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = ExpenseType::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Expense type not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Expense type retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = ExpenseType::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Expense type not found.'
            ], 404);
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
        $source = ExpenseType::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Expense type not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Expense type deleted successfully.'
        ], 200);
    }
}
