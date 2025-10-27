<?php

namespace App\Http\Controllers;

use App\Models\LoanBankParty;
use Illuminate\Http\Request;

class LoanBankPartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getAllParties(Request $request)
    {
        $source = LoanBankParty::all();

        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source,

        ], 200);
    }
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = LoanBankParty::paginate($pageSize);

        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (LoanBankParty::where('party_name', $request->input('party_name'))->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'The  head already exists.',
                'data' => null
            ], 409);
        }
        $source = LoanBankParty::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = LoanBankParty::find($id);

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
            'message' => 'Retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $source = LoanBankParty::find($id);

        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Source not found.'
            ], 404);
        }


        $source->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully.',
            'data' => $source
        ], 200);
    }

    public function destroy(string $id)
    {
        $source = LoanBankParty::find($id);

        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Source not found.'
            ], 404);
        }

        $source->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully.'
        ], 200);
    }
}
