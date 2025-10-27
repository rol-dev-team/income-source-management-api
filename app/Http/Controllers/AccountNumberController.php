<?php

namespace App\Http\Controllers;

use App\Models\AccountNumber;
use App\Models\AccountCurrentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AccountNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('account_numbers as ac')
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 'ac.channel_detail_id')
            ->join('payment_channels as pc', 'pc.id', '=', 'pcd.channel_id')
            ->select('ac.*', 'pcd.*', 'pc.*')
            ->paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Account retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = AccountNumber::create($request->all());

        AccountCurrentBalance::create([
            'account_id' => $source->id,
            'balance' => 0,
        ]);

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => ' Created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = AccountNumber::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source
        ], 200);
    }
    public function accountNumbersByChannel(string $id)
    {

        $accounts = AccountNumber::where('channel_detail_id', $id)->get();



        // Return success
        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $accounts
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = AccountNumber::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Not found.'
            ], 404);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = AccountNumber::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully.'
        ], 200);
    }


    public function showAllAcNo()
    {
        // Retrieve all sources
        $sources = AccountNumber::all();

        // Return all sources as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $sources
        ], 200);
    }


    public function balanceCheck(string $accountId)
    {
        $balanceRecord = AccountCurrentBalance::where('account_id', $accountId)->first();

        return response()->json([
            'status' => true,
            'message' => 'Balance retrieved successfully.',
            'data' => $balanceRecord->balance,
        ], 200);
    }
}
