<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\AccountCurrentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankDepositController extends Controller

{
    /**
     * Display a listing of the resource.
     */



    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $status = $request->input('status');

        if (empty($status) && $status !== 'all') {
            $status = 'pending';
        }

        $query = DB::table('bank_deposits as iep')
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 'iep.payment_channel_id')
            ->leftJoin('account_numbers as ac', 'ac.id', '=', 'iep.account_id')
            ->select(
                'iep.*',
                'pcd.method_name',
                'ac.ac_no',
                'ac.ac_name'
            );


        if ($status !== 'all') {
            $query->where('iep.status', $status);
        }
        $query->orderBy('iep.id', 'desc');
        $source = $query->paginate($pageSize);

        return response()->json([
            'status' => true,
            'message' => 'Retrieved successfully.',
            'data' => $source->items(),
            'total' => $source->total(),
            'current_page' => $source->currentPage(),
            'last_page' => $source->lastPage(),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {


    //     $posting = BankDeposit::create($request->all());

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Created successfully.',
    //         'data' => $posting
    //     ], 201);
    // }

    public function store(Request $request)
    {
        try {
            $posting = null;

            DB::transaction(function () use ($request, &$posting) {
                // Create BankDeposit
                $posting = BankDeposit::create($request->all());

                // Update Account Current Balance
                $accountId = $request->input('account_id');
                $amount = (float) $request->input('amount_bdt');
                $transactionType = $request->input('transaction_type');

                $currentBalance = AccountCurrentBalance::where('account_id', $accountId)->first();

                if ($currentBalance) {
                    if ($transactionType === 'received') {
                        $currentBalance->balance += $amount;
                    } elseif ($transactionType === 'payment') {
                        $currentBalance->balance -= $amount;
                    }
                    $currentBalance->save();
                } else {
                    throw new \Exception("No current balance record found for account ID: $accountId");
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Created successfully.',
                'data' => $posting
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction failed. ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = BankDeposit::find($id);

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
    // public function update(Request $request, string $id)
    // {

    //     $source = BankDeposit::find($id);

    //     if (!$source) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Source not found.'
    //         ], 404);
    //     }


    //     $source->update($request->all());

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Updated successfully.',
    //         'data' => $source
    //     ], 200);
    // }


    public function update(Request $request, string $id)
    {
        try {
            $source = BankDeposit::find($id);

            if (!$source) {
                return response()->json([
                    'status' => false,
                    'message' => 'Source not found.'
                ], 404);
            }

            DB::transaction(function () use ($request, $source) {
                // Store old values for calculation
                $oldAmount = (float) $source->amount_bdt;
                $oldTransactionType = $source->transaction_type;
                $accountId = $source->account_id;

                // Update the BankDeposit
                $source->update($request->all());

                // Get new values
                $newAmount = (float) $request->input('amount_bdt', $source->amount_bdt);
                $newTransactionType = $request->input('transaction_type', $source->transaction_type);

                // Find the current balance
                $currentBalance = AccountCurrentBalance::where('account_id', $accountId)->first();

                if (!$currentBalance) {
                    throw new \Exception("No current balance record found for account ID: $accountId");
                }

                // Calculate the difference and update balance
                $amountDifference = $newAmount - $oldAmount;
                $transactionTypeChanged = ($oldTransactionType !== $newTransactionType);

                if ($transactionTypeChanged) {
                    // If transaction type changed, reverse old transaction and apply new one
                    if ($oldTransactionType === 'received') {
                        $currentBalance->balance -= $oldAmount; // Reverse old received
                    } elseif ($oldTransactionType === 'payment') {
                        $currentBalance->balance += $oldAmount; // Reverse old payment
                    }

                    // Apply new transaction
                    if ($newTransactionType === 'received') {
                        $currentBalance->balance += $newAmount;
                    } elseif ($newTransactionType === 'payment') {
                        $currentBalance->balance -= $newAmount;
                    }
                } else {
                    // If same transaction type, just adjust the difference
                    if ($newTransactionType === 'received') {
                        $currentBalance->balance += $amountDifference;
                    } elseif ($newTransactionType === 'payment') {
                        $currentBalance->balance -= $amountDifference;
                    }
                }

                $currentBalance->save();
            });

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.',
                'data' => $source
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Update failed. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $source = BankDeposit::find($id);

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

    public function statusUpdate(Request $request, string $id)
    {
        $posting = BankDeposit::find($id);

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
            $updateData['status'] = 'pending';
        }

        $posting->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Posting status updated successfully.',
            'data' => $posting
        ], 200);
    }
}
