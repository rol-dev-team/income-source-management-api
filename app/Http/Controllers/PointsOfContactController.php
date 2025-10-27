<?php

namespace App\Http\Controllers;

use App\Models\PointOfContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointsOfContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = PointOfContact::paginate($pageSize);


        return response()->json([
            'status' => true,
            'message' => 'Points of contact retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $source = PointOfContact::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Points of contact created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = PointOfContact::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Points of contact not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Points of contact retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the source by ID
        $source = PointOfContact::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Points of contact not found.'
            ], 404);
        }

        // Update the source with the request data
        $source->update($request->all());

        // Return success response with the updated source
        return response()->json([
            'status' => true,
            'message' => 'Points of contact updated successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = PointOfContact::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Points of contact not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Points of contact deleted successfully.'
        ], 200);
    }


    public function getBySubCategory($subCatId)
    {
        try {

            // return $subCatId;

            $pointsOfContact = DB::table('source_subcategory_details as ssd')
                ->join('point_of_contacts as poc', 'ssd.point_of_contact_id', '=', 'poc.id')
                ->join('source_subcategories as ss', 'ssd.source_subcat_id', '=', 'ss.id')
                ->where('ss.id', $subCatId)
                ->select('ssd.point_of_contact_id', 'poc.contact_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pointsOfContact
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch point of contacts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
