<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\AccountCurrentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);

        $query = DB::table('transfers as t')
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 't.payment_channel_id')
            ->leftJoin('account_numbers as ac', 'ac.id', '=', 't.account_id')
            ->select(
                't.*',
                'pcd.method_name',
                'ac.ac_no',
                'ac.ac_name'
            );

        $query->orderBy('t.id', 'desc');
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
    //     $payload = $request->json()->all();

    //     $currentYear = date('y');
    //     $lastTransfer = Transfer::where('transfer_id', 'like', $currentYear . '%')
    //         ->orderBy('transfer_id', 'desc')
    //         ->first();

    //     $incrementalPart = 1;

    //     if ($lastTransfer) {
    //         $lastId = $lastTransfer->transfer_id;
    //         $lastIncrementalPart = substr($lastId, 4);
    //         $incrementalPart = intval($lastIncrementalPart) + 1;
    //     }

    //     $paddedIncrementalPart = str_pad($incrementalPart, 6, '0', STR_PAD_LEFT);
    //     $transferId = "{$currentYear}{$paddedIncrementalPart}";


    //     $paymentData = [
    //         'transfer_id' => $transferId,
    //         'transaction_type' => 'payment',
    //         'payment_channel_id' => $payload['from_payment_channel_id'],
    //         'account_id' => $payload['from_account_id'],
    //         'amount_bdt' => $payload['amount_bdt'],
    //         'transfer_date' => $payload['transfer_date'],
    //         'note' => $payload['note'],
    //     ];

    //     $receivedData = [
    //         'transfer_id' => $transferId,
    //         'transaction_type' => 'received',
    //         'payment_channel_id' => $payload['to_payment_channel_id'],
    //         'account_id' => $payload['to_account_id'],
    //         'amount_bdt' => $payload['amount_bdt'],
    //         'transfer_date' => $payload['transfer_date'],
    //         'note' => $payload['note'],
    //     ];

    //     try {
    //         DB::beginTransaction();

    //         Transfer::create($paymentData);
    //         Transfer::create($receivedData);

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Transfer created successfully.',
    //             'transfer_id' => $transferId,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Failed to create transfer.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $payload = $request->json()->all();

        $currentYear = date('y');
        $lastTransfer = Transfer::where('transfer_id', 'like', $currentYear . '%')
            ->orderBy('transfer_id', 'desc')
            ->first();

        $incrementalPart = 1;

        if ($lastTransfer) {
            $lastId = $lastTransfer->transfer_id;
            $lastIncrementalPart = substr($lastId, 4);
            $incrementalPart = intval($lastIncrementalPart) + 1;
        }

        $paddedIncrementalPart = str_pad($incrementalPart, 6, '0', STR_PAD_LEFT);
        $transferId = "{$currentYear}{$paddedIncrementalPart}";

        $paymentData = [
            'transfer_id' => $transferId,
            'transaction_type' => 'payment',
            'payment_channel_id' => $payload['from_payment_channel_id'],
            'account_id' => $payload['from_account_id'],
            'amount_bdt' => $payload['amount_bdt'],
            'transfer_date' => $payload['transfer_date'],
            'note' => $payload['note'],
        ];

        $receivedData = [
            'transfer_id' => $transferId,
            'transaction_type' => 'received',
            'payment_channel_id' => $payload['to_payment_channel_id'],
            'account_id' => $payload['to_account_id'],
            'amount_bdt' => $payload['amount_bdt'],
            'transfer_date' => $payload['transfer_date'],
            'note' => $payload['note'],
        ];

        try {
            DB::beginTransaction();

            Transfer::create($paymentData);
            Transfer::create($receivedData);

            // Update Account Current Balances for both accounts
            $amount = (float) $payload['amount_bdt'];
            $fromAccountId = $payload['from_account_id'];
            $toAccountId = $payload['to_account_id'];

            // Update FROM account (payment) - subtract amount
            $fromAccountBalance = AccountCurrentBalance::where('account_id', $fromAccountId)->first();
            if ($fromAccountBalance) {
                $fromAccountBalance->balance -= $amount;
                $fromAccountBalance->save();
            } else {
                throw new \Exception("No current balance record found for FROM account ID: $fromAccountId");
            }

            // Update TO account (received) - add amount
            $toAccountBalance = AccountCurrentBalance::where('account_id', $toAccountId)->first();
            if ($toAccountBalance) {
                $toAccountBalance->balance += $amount;
                $toAccountBalance->save();
            } else {
                throw new \Exception("No current balance record found for TO account ID: $toAccountId");
            }

            DB::commit();

            return response()->json([
                'message' => 'Transfer created successfully.',
                'transfer_id' => $transferId,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create transfer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the source by ID
        $source = Transfer::find($id);

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

    //     $source = Transfer::find($id);

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
            $source = Transfer::find($id);

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
                $oldAccountId = $source->account_id;

                // Update the Transfer
                $source->update($request->all());

                // Get new values
                $newAmount = (float) $request->input('amount_bdt', $source->amount_bdt);
                $newTransactionType = $request->input('transaction_type', $source->transaction_type);
                $newAccountId = $request->input('account_id', $source->account_id);

                // Calculate the difference
                $amountDifference = $newAmount - $oldAmount;

                // Handle account change scenario
                $accountChanged = ($oldAccountId !== $newAccountId);

                if ($accountChanged) {
                    // If account changed, reverse old transaction on old account and apply new transaction on new account

                    // Reverse old transaction on old account
                    $oldAccountBalance = AccountCurrentBalance::where('account_id', $oldAccountId)->first();
                    if ($oldAccountBalance) {
                        if ($oldTransactionType === 'received') {
                            $oldAccountBalance->balance -= $oldAmount;
                        } elseif ($oldTransactionType === 'payment') {
                            $oldAccountBalance->balance += $oldAmount;
                        }
                        $oldAccountBalance->save();
                    } else {
                        throw new \Exception("No current balance record found for old account ID: $oldAccountId");
                    }

                    // Apply new transaction on new account
                    $newAccountBalance = AccountCurrentBalance::where('account_id', $newAccountId)->first();
                    if ($newAccountBalance) {
                        if ($newTransactionType === 'received') {
                            $newAccountBalance->balance += $newAmount;
                        } elseif ($newTransactionType === 'payment') {
                            $newAccountBalance->balance -= $newAmount;
                        }
                        $newAccountBalance->save();
                    } else {
                        throw new \Exception("No current balance record found for new account ID: $newAccountId");
                    }
                } else {
                    // Same account, adjust balance based on transaction type and amount difference
                    $currentBalance = AccountCurrentBalance::where('account_id', $oldAccountId)->first();

                    if (!$currentBalance) {
                        throw new \Exception("No current balance record found for account ID: $oldAccountId");
                    }

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
                }
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
        $source = Transfer::find($id);

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
