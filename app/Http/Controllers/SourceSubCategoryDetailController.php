<?php

namespace App\Http\Controllers;

use App\Models\SourceSubcategoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SourceSubCategoryDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $source = DB::table('source_subcategory_details as ssd')
            ->join('sources as s', 's.id', '=', 'ssd.source_id')
            ->join('source_categories as sc', 'sc.id', '=', 'ssd.source_cat_id')
            ->join('source_subcategories as ss', 'ss.id', '=', 'ssd.source_subcat_id')
            ->join('point_of_contacts as poc', 'poc.id', '=', 'ssd.point_of_contact_id')
            ->select('ssd.*', 'ss.subcat_name','s.source_name','sc.cat_name','poc.contact_name')
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'message' => 'Sub Category detail retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $payloads = $request->all();

    if (isset($payloads['source_id']) && $payloads['source_id'] == 1) {
            $existPointOfContact = SourceSubcategoryDetail::where('source_id', $payloads['source_id'])
                ->where('source_subcat_id', $payloads['source_subcat_id']) 
                ->where('point_of_contact_id', $payloads['point_of_contact_id'])
                ->where('status', 1)
                ->exists();

            if ($existPointOfContact) {
                return response()->json([
                    'status' => false,
                    'message' => 'This point of contact already exists for this source and subcategory.'
                ], 400);
            }
        }

        $source = SourceSubcategoryDetail::create($request->all());

        // Return success response with the created source
        return response()->json([
            'status' => true,
            'message' => 'Sub Category detail created successfully.',
            'data' => $source
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = SourceSubcategoryDetail::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Sub Category detail not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Sub Category detail retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
{
    // 1. Find the record, or fail if it doesn't exist.
    // This is more concise than using 'if (!$source)'
    $source = SourceSubcategoryDetail::find($id);

    // If source not found, return error response
    if (!$source) {
        return response()->json([
            'status' => false,
            'message' => 'Sub Category detail not found.'
        ], 404);
    }

    // 2. Validate the incoming data before updating.
    $validatedData = $request->validate([
        // 'point_of_contact_id' => 'required|exists:point_of_contacts,id',
        'source_subcat_id'    => 'required|exists:source_subcategories,id',
        'source_cat_id'       => 'required|exists:source_categories,id',
        'source_id'           => 'required|exists:sources,id',
        'status'              => 'sometimes|integer|in:0,1',
    ]);

    // 3. Update the source with the validated data.
    $source->update($validatedData);

    // 4. Return a success response.
    return response()->json([
        'status' => true,
        'message' => 'Sub Category detail updated successfully.',
        'data' => $source
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = SourceSubcategoryDetail::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Sub Category detail not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Sub Category detail deleted successfully.'
        ], 200);
    }
}
