<?php

namespace App\Http\Controllers;

use App\Models\PaymentChannelDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentChannelDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getPaymentChannels(Request $request)
    {
        $source = DB::table('payment_channel_details as pcd')
            ->join('payment_channels as pc', 'pc.id', '=', 'pcd.channel_id')
            ->select(
                'pcd.id',
                'pcd.channel_id',
                'pcd.method_name',
                'pcd.created_at',
                'pcd.updated_at',
                'pc.channel_name'
            )
            ->get();


        return response()->json([
            'status' => true,
            'message' => 'Payment channel details retrieved successfully.',
            'data' => $source
        ], 200);
    }
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('payment_channel_details as pcd')
            ->join('payment_channels as pc', 'pc.id', '=', 'pcd.channel_id')
            ->select(
                'pcd.id',
                'pcd.channel_id',
                'pcd.method_name',
                'pcd.created_at',
                'pcd.updated_at',
                'pc.channel_name'
            )
            ->paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Payment channel details retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = PaymentChannelDetails::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Payment channel details created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = PaymentChannelDetails::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Payment channel details not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Payment channel details retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = PaymentChannelDetails::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Payment channel details not found.'
            ], 404);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Payment channel details updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = PaymentChannelDetails::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Payment channel details not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Payment channel details deleted successfully.'
        ], 200);
    }
}
