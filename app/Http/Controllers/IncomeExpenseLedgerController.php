<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeExpenseLedgerController extends Controller
{

    // public function getLedgerData(array $filters)
    // {
    //     // Define a closure to apply common filters to any query builder instance.
    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['transaction_type']) && $filters['transaction_type'] !== 'all') {
    //             $query->where('transaction_type', $filters['transaction_type']);
    //         }

    //         if (isset($filters['head_id'])) {
    //             // Use a nested where clause for proper grouping
    //             $query->where(function ($nestedQuery) use ($filters) {
    //                 if ($filters['transaction_type'] === 'received') {
    //                     $nestedQuery->where('income_head_id', $filters['head_id']);
    //                 } elseif ($filters['transaction_type'] === 'payment') {
    //                     $nestedQuery->where('expense_head_id', $filters['head_id']);
    //                 }
    //             });
    //         }

    //         if (isset($filters['start_date']) && isset($filters['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['start_date'], $filters['end_date']]);
    //         }
    //         return $query;
    //     };

    //     // 1. Build and execute the summary query using the reusable filter logic.
    //     $summary = $applyFilters(DB::table('income_expense_postings'))
    //         ->selectRaw('
    //         SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) AS total_income,
    //         SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) AS total_expense,
    //         SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) - SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) AS net_balance
    //     ')
    //         ->first();

    //     // 2. Build and execute the detailed query, also using the reusable filter logic.
    //     $details = $applyFilters(
    //         DB::table('income_expense_postings as iep')
    //             ->leftJoin('income_heads as ih', 'iep.income_head_id', '=', 'ih.id')
    //             ->leftJoin('expense_heads as eh', 'iep.expense_head_id', '=', 'eh.id')
    //     )
    //         ->select(
    //             'iep.id',
    //             'iep.transaction_type',
    //             'ih.income_head',
    //             'eh.expense_head',
    //             'iep.amount_bdt',
    //             'iep.posting_date',
    //             'iep.note',
    //             'iep.status'
    //         )
    //         ->orderBy('iep.posting_date', 'DESC')
    //         ->orderBy('iep.id', 'DESC')
    //         ->get();

    //     return [
    //         'summary' => $summary,
    //         'details' => $details,
    //     ];
    // }

    public function getLedgerData(Request $request)
    {

        $filters = $request->query();

        $applyFilters = function ($query) use ($filters) {
            $query->where('status', 'approved');

            if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
                $query->where('transaction_type', $filters['filter']['transaction_type']);
            }

            if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
                $query->where(function ($nestedQuery) use ($filters) {
                    $nestedQuery->where('head_id', $filters['filter']['head_id']);
                });
            }

            if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
                $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
            }

            return $query;
        };
        $total = $applyFilters(DB::table('income_expense_postings'))->count();

        // 1. Build and execute the summary query.
        $summary = $applyFilters(DB::table('income_expense_postings'))
            ->selectRaw('
                SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) AS total_income,
                SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) AS total_expense,
                SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) - SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) AS net_balance
            ')
            ->first();

        // Get pagination and page size from query params
        $page = $filters['page'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;

        // 2. Build and execute the detailed query.
        $details = $applyFilters(
            DB::table('income_expense_postings as iep')
                ->leftJoin('income_expense_heads as ih', 'iep.head_id', '=', 'ih.id')
        )
            ->select(
                'iep.id',
                'iep.transaction_type',
                'ih.head_name',
                'iep.amount_bdt',
                'iep.posting_date',
                'iep.note',
                'iep.status'
            )
            ->orderBy('iep.posting_date', 'DESC')
            ->orderBy('iep.id', 'DESC')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return response()->json([
            'summary' => $summary,
            'details' => $details,
            'total' => $total,
        ]);
    }
}
