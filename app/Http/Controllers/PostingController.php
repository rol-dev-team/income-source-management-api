<?php

namespace App\Http\Controllers;

use App\Models\ExpenseDetails;
use App\Models\Posting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class PostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//    public function index(Request $request)
//    {
//        $pageSize = $request->input('pageSize', 10);
//        $source = Posting::paginate($pageSize);
//
//
//        return response()->json([
//            'status' => true,
//            'message' => 'Posting retrieved successfully.',
//            'data' => $source->items(),
//            'total' => $source->total(),
//        ], 200);
//    }
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);

        $postings = DB::table('postings')
            ->join('sources', 'postings.source_id', '=', 'sources.id')
            ->join('transaction_types', 'postings.transaction_type_id', '=', 'transaction_types.id')
            ->join('source_categories', 'postings.source_cat_id', '=', 'source_categories.id')
            ->join('source_subcategories', 'postings.source_subcat_id', '=', 'source_subcategories.id')
            ->join('point_of_contacts', 'postings.point_of_contact_id', '=', 'point_of_contacts.id')
            ->join('payment_channel_details', 'postings.channel_detail_id', '=', 'payment_channel_details.id')
            ->join('payment_channels', 'payment_channel_details.channel_id', '=', 'payment_channels.id')
            ->leftJoin('expense_types', 'postings.expense_type_id', '=', 'expense_types.id')
            ->select(
                'postings.*',
                'sources.source_name',
                'transaction_types.transaction_type',
                'source_categories.cat_name',
                'source_subcategories.subcat_name',
                'point_of_contacts.contact_name',
                'payment_channels.channel_name',
                'payment_channel_details.method_name',
                'expense_types.expense_type'
            )
            ->orderBy('postings.posting_date', 'desc')
            ->paginate($pageSize);

        return response()->json([
            'status' => true,
            'message' => 'Postings retrieved successfully.',
            'data' => $postings->items(),
            'total' => $postings->total(),
            'current_page' => $postings->currentPage(),
            'last_page' => $postings->lastPage(),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    private function generatePostingId()
    {
        $year = date('y');
        $lastPosting = Posting::where('posting_id', 'like', $year . '%')
            ->orderBy('posting_id', 'desc')
            ->first();
        $sequence = 1;
        if ($lastPosting) {
            $lastSequence = (int) substr($lastPosting->posting_id, 2);
            $sequence = $lastSequence + 1;
        }
        // Pad sequence with zeros to 6 digits
        $sequencePadded = str_pad($sequence, 6, '0', STR_PAD_LEFT);

        return $year . $sequencePadded;
    }

    public function store(Request $request)
{
    // Validate the request data here if needed
    // $request->validate([...]);

    $payload = $request->all();
    $uniqeId = $this->generatePostingId();

    $dataToCreate = [
        'posting_id'=> $uniqeId,
        'source_id' => $payload['source_id'] ?? null,
        'transaction_type_id' => $payload['transaction_type_id'] ?? null,
        'source_cat_id' => $payload['source_cat_id'] ?? null,
        'source_subcat_id' => $payload['source_subcat_id'] ?? null,
        'expense_type_id' => $payload['expense_type'] ?? null,
        'point_of_contact_id' => $payload['point_of_contact_id'] ?? null,
        'channel_detail_id' => $payload['channel_detail_id'] ?? null,
        'recived_ac' => $payload['recived_ac'] ?? null,
        'from_ac' => $payload['from_ac'] ?? null,
        'foreign_currency' => $payload['foreign_currency'] ?? null,
        'exchange_rate' => $payload['exchange_rate'] ?? null,
        'total_amount' => $payload['total_amount'] ?? null,
        'posting_date' => $payload['posting_date'] ?? null,
        'note' => $payload['note'] ?? null,
    ];

    $createdPosting = Posting::create($dataToCreate);

    return response()->json([
        'status' => true,
        'message' => 'Posting created successfully.',
        'data' => $createdPosting
    ], 201);
}


//    public function store(Request $request)
//    {
//        $payload = $request->json()->all();
//        $isExpense = $payload['transaction_type_id'] == 2;
//
//        try {
//            DB::beginTransaction();
//
//            if ($isExpense) {
//                $totalAmount = collect($payload['expenseDetails'])->sum('amount');
//                $posting = Posting::create([
//                    'source_id' => $payload['source_id'],
//                    'transaction_type_id' => $payload['transaction_type_id'],
//                    'source_cat_id' => $payload['source_cat_id'],
//                    'source_subcat_id' => $payload['source_subcat_id'],
//                    'point_of_contact_id' => $payload['point_of_contact_id'],
//                    'total_amount' => $totalAmount,
//                    'posting_date' => $payload['posting_date'],
//                    'note' => $payload['note'],
//                ]);
//
//                foreach ($payload['expenseDetails'] as $detail) {
//                    ExpenseDetails::create([
//                        'posting_id' => $posting->id,
//                        'channel_detail_id' => $detail['paymentChannelDetailId'],
//                        'recived_ac' => $detail['recivedAccount'],
//                        'from_ac' => $detail['fromAccount'],
//                        'amount' => $detail['amount'],
//                        'exchange_rate' => $detail['exchangeRate'],
//                        'expense_date' => $detail['date'],
//                    ]);
//                }
//            } else {
//
//                Posting::create([
//                    'source_id' => $payload['source_id'],
//                    'transaction_type_id' => $payload['transaction_type_id'],
//                    'source_cat_id' => $payload['source_cat_id'],
//                    'source_subcat_id' => $payload['source_subcat_id'],
//                    'point_of_contact_id' => $payload['point_of_contact_id'],
//                    'channel_detail_id' => $payload['channel_detail_id'],
//                    'recived_ac' => $payload['recived_ac'],
//                    'from_ac' => $payload['from_ac'],
//                    'total_amount' => $payload['total_amount'],
//                    'unit_price' => $payload['isUnitPrice'] ? $payload['unit_price'] : null,
//                    'posting_date' => $payload['posting_date'],
//                    'note' => $payload['note'],
//                ]);
//            }
//
//            DB::commit();
//            return response()->json(['message' => 'Transaction saved successfully'], 201);
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['error' => 'Failed to save transaction: ' . $e->getMessage()], 500);
//        }
//    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = Posting::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'Posting not found.'
            ], 404);
        }

        // Return the found source as a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Posting retrieved successfully.',
            'data' => $source
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(Request $request, string $id)
//    {
//        // Find the source by ID
//        $source = Posting::find($id);
//
//        // If source not found, return error response
//        if (!$source) {
//            return response()->json([
//                'status' => false,
//                'message' => 'Posting not found.'
//            ], 404);
//        }
//
//        // Update the source with the request data
//        $source->update($request->all());
//
//        // Return success response with the updated source
//        return response()->json([
//            'status' => true,
//            'message' => 'Posting updated successfully.',
//            'data' => $source
//        ], 200);
//    }



    public function update(Request $request, string $id)
    {
        $posting = Posting::find($id);

        if (!$posting) {
            return response()->json([
                'status' => false,
                'message' => 'Posting not found.'
            ], 404);
        }

        $data = $request->all();

        // ✅ Convert ISO 8601 to MySQL compatible format
        if (isset($data['posting_date']) && !is_null($data['posting_date'])) {
            try {
                $data['posting_date'] = Carbon::parse($data['posting_date'])->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s');

            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid posting_date format.'
                ], 422);
            }
        }

        $posting->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Posting updated successfully.',
            'data' => $posting
        ], 200);
    }

    public function statusUpdate(Request $request, string $id)
{
    $posting = Posting::find($id);

    if (!$posting) {
        return response()->json([
            'status' => false,
            'message' => 'Posting not found.'
        ], 404);
    }

    // Get the data from the request body
    $newStatus = $request->input('status');
    $rejectionNote = $request->input('rejection_note');

    // Update the posting
    $updateData = ['status' => $newStatus];

    // Conditionally add the rejection note if provided
    if ($newStatus === 'rejected' && $rejectionNote) {
        $updateData['rejected_note'] = $rejectionNote;
    }

    $posting->update($updateData);

    return response()->json([
        'status' => true,
        'message' => 'Posting status updated successfully.',
        'data' => $posting
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the source by ID
        $source = Posting::find($id);

        // If source not found, return error response
        if (!$source) {
            return response()->json([
                'status' => false,
                'message' => 'posting not found.'
            ], 404);
        }

        // Delete the source
        $source->delete();

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Posting deleted successfully.'
        ], 200);
    }
}
