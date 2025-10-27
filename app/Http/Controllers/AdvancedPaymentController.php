<?php

namespace App\Http\Controllers;

use App\Models\AdvancedPayment;
use App\Models\Posting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvancedPaymentController extends Controller
{
    // public function index()
    // {
    //     return response()->json(AdvancedPayment::all());
    // }

    public function index()
    {
        $data = DB::table('advanced_payments as ap')
            ->join('point_of_contacts as poc', 'ap.point_of_contact_id', '=', 'poc.id')
            ->join('source_subcategories as ss', 'ap.sub_cat_id', '=', 'ss.id')
            ->select('ap.id','ap.advanced_payment_type','ap.sub_cat_id','ss.subcat_name','ap.point_of_contact_id','poc.contact_name',
                'ap.amount','ap.auto_adjustment_amount','ap.remaining_amount', 'ap.created_at','ap.updated_at'
            )
            ->get();

        return response()->json($data);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'advanced_payment_type' => 'required|string',
    //         'sub_cat_id' => 'required|integer',
    //         'point_of_contact_id' => 'required|integer',
    //         'amount' => 'required|numeric',
    //         'auto_adjustment_amount' => 'required|numeric',
    //         'remaining_amount' => 'required|numeric',
    //     ]);

    //     $payment = AdvancedPayment::create($validated);

    //     return response()->json(['message' => 'Created successfully', 'data' => $payment], 201);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'advanced_payment_type' => 'required|string|in:Advance,Refund',
            'sub_cat_id' => 'required|integer',
            'point_of_contact_id' => 'required|integer',
            'amount' => 'required|numeric',
            'auto_adjustment_amount' => 'required|numeric',
            'remaining_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $payment = AdvancedPayment::create($validated);

            $transactionTypeId = ($validated['advanced_payment_type'] === 'Advance') ? 1 : 2;
            $expenseTypeId = ($validated['advanced_payment_type'] === 'Advance') ? 5 : 6;

            $lastPosting = Posting::orderBy('id', 'desc')->first();
            $nextPostingId = $lastPosting ? $lastPosting->posting_id + 1 : 1;

            $posting = Posting::create([
                'posting_id' => $nextPostingId,
                'source_id' => 1,
                'transaction_type_id' => $transactionTypeId,
                'source_cat_id' => 1,
                'channel_detail_id' => 1,
                'expense_type_id' => $expenseTypeId,
                'source_subcat_id' => $validated['sub_cat_id'],
                'point_of_contact_id' => $validated['point_of_contact_id'],
                'total_amount' => $validated['amount'],
                'posting_date' => now(),
                'note' => $validated['advanced_payment_type'] . ' Payment',
                'status' => 'pending',
                'recived_ac' => null,
                'from_ac' => null,
                'foreign_currency' => null,
                'exchange_rate' => null,
                'rejected_note' => null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Created successfully', 
                'data' => [
                    'advanced_payment' => $payment,
                    'posting' => $posting
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating records: ' . $e->getMessage()], 500);
        }
    }

    // public function show($id)
    // {
    //     $payment = AdvancedPayment::find($id);
    //     if (!$payment) {
    //         return response()->json(['message' => 'Not Found'], 404);
    //     }
    //     return response()->json($payment);
    // }

    public function advancedPaymentByPointOfContactId(Request $request)
    {
        $subcatId = $request->sub_cat_id;
        $pointOfContactId = $request->point_of_contact_id;

        $payment = AdvancedPayment::where('sub_cat_id',  $subcatId)
        ->where('point_of_contact_id', $pointOfContactId)
        ->where('advanced_payment_type', 'Advance')
            ->select('id', 'advanced_payment_type', 'sub_cat_id', 'point_of_contact_id', 'amount', 'auto_adjustment_amount', 'remaining_amount')
            ->get();

        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json(
            ['status' => true, 'message' => 'Payment retrieved successfully.', 'data' => $payment],200);
  
    }

    public function show($id)
    {
        $payment = DB::table('advanced_payments as ap')
            ->join('source_subcategories as ss', 'ap.sub_cat_id', '=', 'ss.id')
            ->join('point_of_contacts as poc', 'ap.point_of_contact_id', '=', 'poc.id')
            ->select(
                'ap.id',
                'ap.advanced_payment_type',
                'ap.sub_cat_id',
                'ss.subcat_name',
                'ap.point_of_contact_id',
                'poc.contact_name',
                'ap.amount',
                'ap.auto_adjustment_amount',
                'ap.remaining_amount'
            )
            ->where('ap.id', $id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($payment);
    }


    public function update(Request $request, $id)
    {
        $payment = AdvancedPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $validated = $request->validate([
            'advanced_payment_type' => 'sometimes|string',
            'sub_cat_id' => 'sometimes|integer',
            'point_of_contact_id' => 'sometimes|integer',
            'amount' => 'sometimes|numeric',
            'auto_adjustment_amount' => 'sometimes|numeric',
            'remaining_amount' => 'sometimes|numeric',
        ]);

        $payment->update($validated);

        return response()->json(['message' => 'Updated successfully', 'data' => $payment]);
    }

    public function destroy($id)
    {
        $payment = AdvancedPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

