<?php

namespace App\Http\Controllers;

use App\Models\RentalHousePartyMap;
use App\Models\RentalParty;
use App\Models\RentalPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalHousePartyMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function getAllMappings(Request $request)
    {
        $source = DB::table('rental_house_party_maps as rhpm')
            ->join('rental_houses as rh', 'rh.id', '=', 'rhpm.rental_house_id')
            ->select('rhpm.*', 'rh.house_name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source,

        ], 200);
    }

    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);

        $source = DB::table('rental_parties as rp')
            ->join('rental_house_party_maps as rhpm', 'rhpm.rental_party_id', '=', 'rp.id')
            ->join('rental_houses as rh', 'rh.id', '=', 'rhpm.rental_house_id')
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 'rhpm.payment_channel_id')
            ->join('account_numbers as ac', 'pcd.id', '=', 'ac.channel_detail_id')
            ->select(
                'rp.id as party_id',
                'rp.party_name',
                'rp.cell_number',
                'rp.nid',
                'rp.party_ac_no',
                'rhpm.security_money',
                'rhpm.remaining_security_money',
                'rhpm.id',
                'rhpm.rental_house_id',
                'rhpm.auto_adjustment',
                'rhpm.monthly_rent',
                'rhpm.refund_security_money',
                'rhpm.remaining_security_money',
                'rhpm.rent_start_date',
                'rhpm.status',
                'rp.party_name',
                'rp.cell_number',
                'rh.id as house_id',
                'rh.house_name',
                'rh.address',
                'pcd.method_name',
            'rhpm.payment_channel_id',
                'rhpm.account_id',
                'ac.ac_no',
                'ac.ac_name'
            )
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
            'current_page' => $source->currentPage(),
            'last_page' => $source->lastPage(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {


        try {
            DB::beginTransaction();
            $existingMapping = RentalHousePartyMap::where('rental_house_id', $request->input('house'))
                ->where('status', 'active')->first();

            if ($existingMapping) {
                if ($existingMapping->rental_party_id == $request->input('party_name')) {
                    // Exact party-house mapping already exists
                    return response()->json([
                        'status' => false,
                        'message' => 'A mapping already exists for this party and this house.',
                        'data' => null
                    ], 409);
                } else {
                    // House is already mapped with another party
                    return response()->json([
                        'status' => false,
                        'message' => 'This house is already mapped with another party.',
                        'data' => null
                    ], 409);
                }
            }



            $data = [
                'rental_party_id'   => $request->input('party_name'),
                'rental_house_id'   => $request->input('house'),
                'security_money'    => !empty($request->input('security_money'))
                    ? $request->input('security_money')
                    : 0.00,
                'remaining_security_money' => !empty($request->input('security_money'))
                    ? $request->input('security_money')
                    : 0.00,
                'monthly_rent'      => $request->input('monthly_rent'),
                'auto_adjustment'   => !empty($request->input('auto_adjustment'))
                    ? $request->input('auto_adjustment')
                    : 0.00,
                'payment_channel_id' => $request->input('payment_channel_id'),
                'account_id'        => $request->input('account_id'),
                'rent_start_date'   => $request->input('rent_start_date'),
                'status'            => $request->input('status'),
            ];
            $source = RentalHousePartyMap::create($data);

            if (!empty($request->input('security_money')) && $request->input('security_money') > 0) {
                $exist = RentalPosting::where('head_id', $request->input('party_name'))
                    ->where('house_id', $request->input('house'))
                    ->where('entry_type', 'security_money_amount')->exists();
                if (!$exist) {
                    RentalPosting::create([
                        'transaction_type'   => 'received',
                        'head_id'            => $request->input('party_name'),
                        'house_id'           => $request->input('house'),
                        'payment_channel_id' => $request->input('payment_channel_id'),
                        'account_id'         => $request->input('account_id'),
                        'receipt_number'     => $request->input('receipt_number', null),
                        'amount_bdt'         => $request->input('security_money'),
                        'posting_date'       => Carbon::now()->toDateString(),
                        'note'               => $request->input('note', null),
                        'entry_type'         => 'security_money_amount',
                        'status'             => 'approved',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Rental Party and associated houses created successfully.',
                'data' => $source
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = RentalHousePartyMap::find($id);

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

    // public function show(string $id)
    // {
    //     // Find all house mappings for this party ID
    //     $houseMappings = RentalHousePartyMap::with('rentalHouse')
    //         ->where('rental_party_id', $id)
    //         ->where('status', 'active')
    //         ->get();

    //     // If no mappings found, return error response
    //     if ($houseMappings->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No house mappings found for this party.'
    //         ], 404);
    //     }

    //     // Return the house mappings as a JSON response
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'House mappings retrieved successfully.',
    //         'data' => $houseMappings
    //     ], 200);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $party = RentalHousePartyMap::find($id);

        if (!$party) {
            return response()->json([
                'status' => false,
                'message' => 'Rental Party not found.',
                'data' => null
            ], 404);
        }


        try {
            DB::beginTransaction();
            $data = [
                'rental_party_id'   => $request->input('party_name'),
                'rental_house_id'   => $request->input('house'),
                'security_money'    => !empty($request->input('security_money'))
                    ? $request->input('security_money')
                    : 0.00,
                'monthly_rent'      => $request->input('monthly_rent'),
                'auto_adjustment'   => !empty($request->input('auto_adjustment'))
                    ? $request->input('auto_adjustment')
                    : 0.00,
                'payment_channel_id' => $request->input('payment_channel_id'),
                'account_id'        => $request->input('account_id'),
                'rent_start_date'   => $request->input('rent_start_date'),
                'status'            => $request->input('status'),
            ];


            $party->update($data);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Rental Party and associated houses updated successfully.',
                'data' => $party
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to update the rental party due to a database error. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $source = RentalHousePartyMap::find($id);

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


    // public function getHouseMappingsByParty($partyId)
    // {
    //     try {
    //         // Validate if party exists
    //         $party = RentalParty::find($partyId);

    //         if (!$party) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Party not found.'
    //             ], 404);
    //         }

    //         // Raw SQL query to get house mappings
    //         $houseMappings = DB::select("
    //         SELECT rh.id, rh.house_name 
    //         FROM rental_houses rh
    //         JOIN rental_house_party_maps rhpm ON rh.id = rhpm.rental_house_id
    //         WHERE rhpm.rental_party_id = ? 
    //         AND rhpm.status = 'active'
    //     ", [$partyId]);


    //         return response()->json([$houseMappings], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to retrieve house mappings.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }

    //     // If no mappings found, return empty array
    //     //     if (empty($houseMappings)) {
    //     //         return response()->json([
    //     //             'status' => true,
    //     //             'message' => 'No house mappings found for this party.',
    //     //             'data' => []
    //     //         ], 200);
    //     //     }

    //     //     return response()->json([
    //     //         'status' => true,
    //     //         'message' => 'House mappings retrieved successfully.',
    //     //         'data' => $houseMappings
    //     //     ], 200);
    //     // } catch (\Exception $e) {
    //     //     return response()->json([
    //     //         'status' => false,
    //     //         'message' => 'Failed to retrieve house mappings.',
    //     //         'error' => $e->getMessage()
    //     //     ], 500);
    //     // }
    // }
}
