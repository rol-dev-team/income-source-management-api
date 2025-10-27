<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\LoanPosting;
use App\Models\AccountCurrentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\LoanInterestRate;
use App\Models\LoanSchedule;

class LoanPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // public function getLoanLedgerData(Request $request)
    // {
    //     $filters = $request->query();

    //     // Use a closure to apply filters to different queries
    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('lp.status', 'approved');

    //         if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
    //             $query->where('entry_type', $filters['filter']['transaction_type']);
    //         }

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('lp.posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Get total count of filtered records
    //     $total = $applyFilters(DB::table('loan_postings as lp'))->count();

    //     // 1. Build and execute the summary query.
    //     $summary = $applyFilters(DB::table('loan_postings as lp'))
    //         ->selectRaw('
    //             SUM(CASE WHEN lp.transaction_type = "received" THEN lp.amount_bdt ELSE 0 END) AS total_received,
    //             SUM(CASE WHEN lp.transaction_type = "payment" THEN lp.amount_bdt ELSE 0 END) AS total_payment,
    //             SUM(CASE WHEN lp.transaction_type = "received" THEN lp.amount_bdt ELSE 0 END) - SUM(CASE WHEN lp.transaction_type = "payment" THEN lp.amount_bdt ELSE 0 END) AS balance
    //         ')
    //         ->first();

    //     // Get pagination and page size from query params
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     // 2. Build and execute the detailed query.
    //     $details = $applyFilters(
    //         DB::table('loan_postings as lp')
    //             ->leftJoin('loan_bank_parties as lbp', 'lbp.id', '=', 'lp.head_id')
    //             ->leftJoin('loans as l', 'l.id', '=', 'lp.loan_id')
    //             ->leftJoin('loan_interest_rates as lir', 'lir.id', '=', 'lp.interest_rate_id')
    //     )
    //         ->select(
    //             'lp.id',
    //             'lp.transaction_type',
    //             'l.principal_amount',
    //             'l.term_in_month',
    //             'lir.interest_rate',
    //             'lp.entry_type',
    //             'lbp.party_name',
    //             'lp.amount_bdt',
    //             'lp.posting_date',
    //             'lp.note',
    //             'lp.status'
    //         )
    //         ->orderBy('lp.posting_date', 'DESC')
    //         ->orderBy('lp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }


    // public function getLoanLedgerData(Request $request)
    // {
    //     $filters = $request->query();

    //     // Closure to apply filters
    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('lp.status', 'approved');

    //         if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
    //             $query->where('entry_type', $filters['filter']['transaction_type']);
    //         }

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('lp.posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Total count
    //     $total = $applyFilters(DB::table('loan_postings as lp'))->count();

    //     // Summary
    //     // $summary = $applyFilters(DB::table('loan_postings as lp'))
    //     //     ->selectRaw('
    //     //     SUM(CASE WHEN lp.transaction_type = "received" THEN lp.amount_bdt ELSE 0 END) AS total_received,
    //     //     SUM(CASE WHEN lp.transaction_type = "payment" THEN lp.amount_bdt ELSE 0 END) AS total_payment,
    //     //     SUM(CASE WHEN lp.transaction_type = "received" THEN lp.amount_bdt ELSE 0 END) - SUM(CASE WHEN lp.transaction_type = "payment" THEN lp.amount_bdt ELSE 0 END) AS balance
    //     // ')
    //     //     ->first();


    //     // Total count (still uses the filterable query builder)
    //     $total = $applyFilters(DB::table('loan_postings as lp'))->count();

    //     // Custom summary query
    //     $summaryQuery = "
    //     SELECT
    //         SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_given,
    //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_taken,
    //         SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
    //         SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment,

    //         -- Receivable = loan_given - loan_received
    //         SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
    //         - SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS receivable,

    //         -- Payable = loan_taken - loan_payment
    //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
    //         - SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS payable

    //     FROM loans l
    //     JOIN loan_postings lp ON l.id = lp.loan_id
    //     JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
    //     WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
    //     AND lp.status = 'approved'
    //     ";

    //     // Run the raw query
    //     $summary = DB::selectOne($summaryQuery);


    //     // Pagination
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     // Aggregated subquery for last received/payment dates and counts
    //     // $aggSubquery = DB::table('loan_postings')
    //     //     ->select(
    //     //         'loan_id',
    //     //         DB::raw("SUM(CASE WHEN entry_type = 'loan_given' THEN amount_bdt ELSE 0 END) AS sum_received"),
    //     //         DB::raw("SUM(CASE WHEN entry_type = 'loan_taken' THEN amount_bdt ELSE 0 END) AS sum_payment"),
    //     //         DB::raw("COUNT(CASE WHEN entry_type = 'loan_given' THEN id END) AS cnt_received"),
    //     //         DB::raw("COUNT(CASE WHEN entry_type = 'loan_taken' THEN id END) AS cnt_payment"),
    //     //         DB::raw("MAX(CASE WHEN entry_type = 'loan_given' THEN posting_date END) AS last_received_date"),
    //     //         DB::raw("MAX(CASE WHEN entry_type = 'loan_taken' THEN posting_date END) AS last_payment_date")
    //     //     )
    //     //     ->groupBy('loan_id');

    //     $aggSubquery = DB::table('loan_postings')
    //         ->select(
    //             'loan_id',
    //             DB::raw("SUM(CASE WHEN entry_type = 'loan_received' THEN amount_bdt ELSE 0 END) AS sum_received"),
    //             DB::raw("SUM(CASE WHEN entry_type = 'loan_payment' THEN amount_bdt ELSE 0 END) AS sum_payment"),
    //             DB::raw("COUNT(CASE WHEN entry_type = 'loan_received' THEN id END) AS cnt_received"),
    //             DB::raw("COUNT(CASE WHEN entry_type = 'loan_payment' THEN id END) AS cnt_payment"),
    //             DB::raw("MAX(CASE WHEN entry_type = 'loan_received' THEN posting_date END) AS last_received_date"),
    //             DB::raw("MAX(CASE WHEN entry_type = 'loan_payment' THEN posting_date END) AS last_payment_date")
    //         )
    //         ->groupBy('loan_id');


    //     // Detailed query with all calculated fields
    //     $details = $applyFilters(
    //         DB::table('loan_postings as lp')
    //             ->leftJoin('loan_bank_parties as lbp', 'lbp.id', '=', 'lp.head_id')
    //             ->leftJoin('loans as l', 'l.id', '=', 'lp.loan_id')
    //             ->leftJoin('loan_interest_rates as lir', 'lir.id', '=', 'lp.interest_rate_id')
    //             ->leftJoinSub($aggSubquery, 'agg', function ($join) {
    //                 $join->on('agg.loan_id', '=', 'l.id');
    //             })
    //     )
    //         ->select(
    //             'lp.id',
    //             'lp.transaction_type',
    //             'l.principal_amount',
    //             'l.term_in_month',
    //             'lir.interest_rate',
    //             'lp.entry_type',
    //             'lbp.party_name',
    //             'lp.amount_bdt',
    //             'lp.posting_date',
    //             'lp.note',
    //             'lp.status',
    //             //     DB::raw("
    //             //     CASE
    //             //         WHEN l.term_in_month > 0 AND lir.interest_rate > 0 THEN
    //             //             FORMAT(
    //             //                 ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
    //             //                 / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 )
    //             //             , 2)
    //             //         ELSE NULL
    //             //     END AS emi

    //             // "),

    //             DB::raw("
    //                 CASE
    //                     WHEN (lp.entry_type = 'loan_given' OR lp.entry_type = 'loan_taken')
    //                         AND l.term_in_month > 0
    //                         AND lir.interest_rate > 0 THEN
    //                         FORMAT(
    //                             ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
    //                             / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 )
    //                         , 2)
    //                     ELSE NULL
    //                 END AS emi
    //             "),

    //             DB::raw("
    //             CASE
    //                 WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
    //                 WHEN lp.entry_type = 'loan_taken' THEN agg.last_payment_date
    //                 ELSE NULL
    //             END AS last_payment_date
    //         "),

    //             DB::raw("
    //             CASE
    //                 WHEN lp.entry_type = 'loan_given' OR lp.entry_type = 'loan_taken' THEN
    //                     DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
    //                 ELSE NULL
    //             END AS installment_date
    //         "),

    //             // DB::raw("
    //             //     CASE
    //             //         WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
    //             //             DATE(CONCAT(
    //             //                 DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //             //                 LPAD(l.installment_date, 2, '0')
    //             //             ))
    //             //         WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
    //             //             DATE(CONCAT(
    //             //                 DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //             //                 LPAD(l.installment_date, 2, '0')
    //             //             ))
    //             //         WHEN lp.entry_type = 'loan_given' OR lp.entry_type = 'loan_taken' THEN
    //             //             DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
    //             //         ELSE
    //             //             NULL
    //             //     END AS next_due_date
    //             // "),


    //             DB::raw("
    //                 CASE
    //         WHEN (
    //             CASE
    //                 WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
    //                     DATE(CONCAT(
    //                         DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //                         LPAD(l.installment_date, 2, '0')
    //                     ))
    //                 WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
    //                     DATE(CONCAT(
    //                         DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //                         LPAD(l.installment_date, 2, '0')
    //                     ))
    //                 ELSE
    //                     DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
    //             END
    //         ) > l.loan_start_date
    //         THEN (
    //             CASE
    //                 WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
    //                     DATE(CONCAT(
    //                         DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //                         LPAD(l.installment_date, 2, '0')
    //                     ))
    //                 WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
    //                     DATE(CONCAT(
    //                         DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //                         LPAD(l.installment_date, 2, '0')
    //                     ))
    //                 ELSE
    //                     DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
    //             END
    //         )
    //         ELSE '--'
    //     END AS next_due_date
    //                     "),




    //             DB::raw("
    //                     CASE
    //                         WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_given'
    //                         THEN l.term_in_month - COALESCE(agg.cnt_received, 0)
    //                         WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_taken'
    //                         THEN l.term_in_month - COALESCE(agg.cnt_payment, 0)
    //                         ELSE NULL
    //                     END AS remaining_term
    //                 "),


    //             DB::raw("
    //                     CASE
    //         WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
    //             FORMAT(
    //                 (
    //                     CASE
    //                         WHEN lir.interest_rate > 0 THEN
    //                             ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
    //                             / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 )
    //                         ELSE 0
    //                     END
    //                 ) *
    //                 (
    //                     GREATEST(
    //                         TIMESTAMPDIFF(
    //                             MONTH,
    //                             l.loan_start_date,
    //                             CURDATE()
    //                         ) + 1
    //                         -
    //                         (
    //                             CASE
    //                                 WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.cnt_received, 0)
    //                                 WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.cnt_payment, 0)
    //                                 ELSE 0
    //                             END
    //                         ),
    //                     0)
    //                 )
    //             , 2)
    //         ELSE NULL
    //     END AS total_due_amount
    //                 "),





    //             DB::raw("
    //         CASE
    //         WHEN l.term_in_month > 0 THEN
    //             FORMAT(
    //                 (
    //                     CASE
    //                         WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
    //                         WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
    //                         ELSE 0
    //                     END
    //                 ) -
    //                 (
    //                     CASE
    //                         WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
    //                             GREATEST(
    //                                 TIMESTAMPDIFF(
    //                                     MONTH,
    //                                     l.loan_start_date,
    //                                     CURDATE()
    //                                 ) + 1,
    //                                 0
    //                             )
    //                         ELSE 0
    //                     END
    //                     * (
    //                         CASE
    //                             WHEN l.term_in_month > 0 AND lir.interest_rate > 0
    //                                 THEN (
    //                                     ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
    //                                     / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 )
    //                                 )
    //                             ELSE 0
    //                         END
    //                     )
    //                 )
    //             , 2)
    //         ELSE 0
    //     END AS emi_adjustment_amount

    //     "),

    //             DB::raw("
    //             FORMAT(
    //                 l.principal_amount - (
    //                     CASE
    //                         WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
    //                         WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
    //                     END
    //                 ), 2
    //             ) AS `Payable / Receivable`
    //         ")

    //         )
    //         ->orderBy('lp.posting_date', 'DESC')
    //         ->orderBy('lp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }


    public function getLoanLedgerData(Request $request)
    {
        $filters = $request->query();

        // Closure to apply filters
        $applyFilters = function ($query) use ($filters) {
            $query->where('lp.status', 'approved');

            if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
                $query->where('entry_type', $filters['filter']['transaction_type']);
            }

            if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
                $query->where('head_id', $filters['filter']['head_id']);
            }

            if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
                $query->whereBetween('lp.posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
            }

            return $query;
        };

        // Total count
        $total = $applyFilters(DB::table('loan_postings as lp'))->count();

        // FIXED: Summary query that applies the same filters as details
        $summaryQuery = "
        SELECT
            SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_given,
            SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_taken,
            SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
            SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment,

            -- Receivable = loan_given - loan_received
            SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
            - SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS receivable,

            -- Payable = loan_taken - loan_payment
            SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
            - SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS payable

        FROM loans l
        JOIN loan_postings lp ON l.id = lp.loan_id
        JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
        WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
        AND lp.status = 'approved'
        ";

        // Apply the same filters to summary query
        $whereConditions = [];

        // Transaction type filter
        if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
            $transactionType = $filters['filter']['transaction_type'];
            $whereConditions[] = "lp.entry_type = '$transactionType'";
        }

        // Head ID filter
        if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
            $headId = $filters['filter']['head_id'];
            $whereConditions[] = "lp.head_id = $headId";
        }

        // Date range filter
        if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
            $startDate = $filters['filter']['start_date'];
            $endDate = $filters['filter']['end_date'];
            $whereConditions[] = "lp.posting_date BETWEEN '$startDate' AND '$endDate'";
        }

        // Add WHERE conditions if any exist
        if (!empty($whereConditions)) {
            $summaryQuery .= " AND " . implode(" AND ", $whereConditions);
        }

        // Run the filtered summary query
        $summary = DB::selectOne($summaryQuery);

        // Pagination
        $page = $filters['page'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;

        $aggSubquery = DB::table('loan_postings')
            ->select(
                'loan_id',
                DB::raw("SUM(CASE WHEN entry_type = 'loan_received' THEN amount_bdt ELSE 0 END) AS sum_received"),
                DB::raw("SUM(CASE WHEN entry_type = 'loan_payment' THEN amount_bdt ELSE 0 END) AS sum_payment"),
                DB::raw("COUNT(CASE WHEN entry_type = 'loan_received' THEN id END) AS cnt_received"),
                DB::raw("COUNT(CASE WHEN entry_type = 'loan_payment' THEN id END) AS cnt_payment"),
                DB::raw("MAX(CASE WHEN entry_type = 'loan_received' THEN posting_date END) AS last_received_date"),
                DB::raw("MAX(CASE WHEN entry_type = 'loan_payment' THEN posting_date END) AS last_payment_date")
            )
            ->groupBy('loan_id');

        // Detailed query with all calculated fields (your existing code remains the same)
        $details = $applyFilters(
            DB::table('loan_postings as lp')
                ->leftJoin('loan_bank_parties as lbp', 'lbp.id', '=', 'lp.head_id')
                ->leftJoin('loans as l', 'l.id', '=', 'lp.loan_id')
                ->leftJoin('loan_interest_rates as lir', 'lir.id', '=', 'lp.interest_rate_id')
                ->leftJoinSub($aggSubquery, 'agg', function ($join) {
                    $join->on('agg.loan_id', '=', 'l.id');
                })
        )
            ->select(
                'lp.id',
                'lp.transaction_type',
                'l.principal_amount',
                'l.term_in_month',
                'lir.interest_rate',
                'lp.entry_type',
                'lbp.party_name',
                'lp.amount_bdt',
                'lp.posting_date',
                'lp.note',
                'lp.status',
                DB::raw("
                CASE
                    WHEN (lp.entry_type = 'loan_given' OR lp.entry_type = 'loan_taken')
                        AND l.term_in_month > 0
                        AND lir.interest_rate > 0 THEN
                        FORMAT(
                            ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
                            / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 )+ l.extra_charge
                        , 2)
                    ELSE NULL
                END AS emi
            "),
                DB::raw("
            CASE
                WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
                WHEN lp.entry_type = 'loan_taken' THEN agg.last_payment_date
                ELSE NULL
            END AS last_payment_date
        "),
                DB::raw("
            CASE
                WHEN lp.entry_type = 'loan_given' OR lp.entry_type = 'loan_taken' THEN
                    DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
                ELSE NULL
            END AS installment_date
        "),
                DB::raw("
                CASE
        WHEN (
            CASE
                WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                ELSE
                    DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
            END
        ) > l.loan_start_date
        THEN (
            CASE
                WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                ELSE
                    DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
            END
        )
        ELSE '--'
        END AS next_due_date
            "),
                DB::raw("
                    CASE
                        WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_given'
                        THEN l.term_in_month - COALESCE(agg.cnt_received, 0)
                        WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_taken'
                        THEN l.term_in_month - COALESCE(agg.cnt_payment, 0)
                        ELSE NULL
                    END AS remaining_term
                "),
                DB::raw("
                    CASE
            WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
                FORMAT(
                    (
                        CASE
                            WHEN lir.interest_rate > 0 THEN
                                ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
                                / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 ) + l.extra_charge
                            ELSE 0
                        END
                    ) *
                    (
                        GREATEST(
                            TIMESTAMPDIFF(
                                MONTH,
                                l.loan_start_date,
                                CURDATE()
                            ) + 1
                            -
                            (
                                CASE
                                    WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.cnt_received, 0)
                                    WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.cnt_payment, 0)
                                    ELSE 0
                                END
                            ),
                        0)
                    )
                , 2)
            ELSE NULL
        END AS total_due_amount
                "),
                DB::raw("
        CASE
        WHEN l.term_in_month > 0 THEN
            FORMAT(
                (
                    CASE
                        WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
                        WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
                        ELSE 0
                    END
                ) -
                (
                    CASE
                        WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
                            GREATEST(
                                TIMESTAMPDIFF(
                                    MONTH,
                                    l.loan_start_date,
                                    CURDATE()
                                ) + 1,
                                0
                            )
                        ELSE 0
                    END
                    * (
                        CASE
                            WHEN l.term_in_month > 0 AND lir.interest_rate > 0
                                THEN (
                                    ( l.principal_amount * (lir.interest_rate/12/100) * POW(1 + (lir.interest_rate/12/100), l.term_in_month) )
                                    / ( POW(1 + (lir.interest_rate/12/100), l.term_in_month) - 1 ) + l.extra_charge
                                )
                            ELSE 0
                        END
                    )
                )
            , 2)
        ELSE 0
        END AS emi_adjustment_amount
        "),
                DB::raw("
            FORMAT(
                l.principal_amount - (
                    CASE
                        WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
                        WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
                    END
                ), 2
            ) AS `Payable / Receivable`
        ")
            )
            ->orderBy('lp.posting_date', 'DESC')
            ->orderBy('lp.id', 'DESC')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        return response()->json([
            'summary' => $summary,
            'details' => $details,
            'total' => $total,
        ]);
    }








    // public function getLoanSummary(Request $request)
    // {
    //     // Get the transaction type filter from request, prefer summary_transaction_type first
    //     $transactionType = $request->input(
    //         'filter.summary_transaction_type',
    //         $request->input('filter.transaction_type', 'all')
    //     );

    //     // Get pagination parameters
    //     $page = $request->input('page', 1);
    //     $pageSize = $request->input('pageSize', 10);
    //     $offset = ($page - 1) * $pageSize;

    //     // Base query for loans
    //     $loanQuery = "
    //     SELECT
    //      -- l.id AS loan_id,
    //       CASE
    //             WHEN lp.entry_type = 'loan_given' THEN 'Loan Given'
    //             WHEN lp.entry_type = 'loan_taken' THEN 'Loan Taken'
    //             ELSE lp.entry_type
    //       END AS Trx_Category,
    //       CASE
    //         WHEN lp.entry_type = 'loan_given' THEN 'payment'
    //         WHEN lp.entry_type = 'loan_taken' THEN 'received'
    //       END AS trx_type,
    //       lb.party_name,
    //       FORMAT(l.principal_amount, 2) AS principal_amount,
    //       l.term_in_month,
    //       CASE
    //         WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
    //         WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
    //       END AS amount_bdt,
    //       lr.interest_rate AS 'Interest (%)',
    //       CASE
    //         WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_given'
    //           THEN l.term_in_month - COALESCE(agg.cnt_received, 0)
    //         WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_taken'
    //           THEN l.term_in_month - COALESCE(agg.cnt_payment, 0)
    //         ELSE NULL
    //       END AS remaining_term,
    //       CASE
    //         WHEN l.term_in_month > 0 AND lr.interest_rate > 0 THEN
    //             FORMAT(
    //                 (
    //                     ( l.principal_amount * (lr.interest_rate/12/100) * POW(1 + (lr.interest_rate/12/100), l.term_in_month) )
    //                     / ( POW(1 + (lr.interest_rate/12/100), l.term_in_month) - 1 )
    //                 ), 2
    //             )
    //         ELSE NULL
    //         END AS emi,
    //      -- DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0'))) AS installment_date,
    //       CASE
    //         WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
    //         WHEN lp.entry_type = 'loan_taken'  THEN agg.last_payment_date
    //       END AS last_payment_date,
    //       CASE
    //     WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
    //         DATE(CONCAT(
    //             DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //             LPAD(l.installment_date, 2, '0')
    //         ))
    //     WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
    //         DATE(CONCAT(
    //             DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
    //             LPAD(l.installment_date, 2, '0')
    //         ))
    //     ELSE
    //         DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
    //     END AS next_due_date,
    //       CASE
    //         WHEN l.term_in_month > 0 THEN
    //           GREATEST(
    //             TIMESTAMPDIFF(
    //               MONTH,
    //               CASE
    //                 WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
    //                 WHEN lp.entry_type = 'loan_taken' THEN agg.last_payment_date
    //               END,
    //               CURDATE()
    //             ), 0
    //           )
    //         ELSE 0
    //       END AS emi_due_month,
    //       CASE
    //     WHEN l.term_in_month > 0 THEN
    //     FORMAT(
    //         GREATEST(
    //             TIMESTAMPDIFF(
    //                 MONTH,
    //                 CASE
    //                     WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
    //                     WHEN lp.entry_type = 'loan_taken' THEN agg.last_payment_date
    //                 END,
    //                 CURDATE()
    //             ), 0
    //         ) *
    //         CASE
    //             WHEN lr.interest_rate > 0 THEN
    //                 ( l.principal_amount * (lr.interest_rate/12/100) * POW(1 + (lr.interest_rate/12/100), l.term_in_month) )
    //                 / ( POW(1 + (lr.interest_rate/12/100), l.term_in_month) - 1 )
    //             ELSE 0
    //         END
    //     , 2)
    //     ELSE NULL
    //     END AS total_due_amount

    //     FROM loans l
    //     JOIN loan_postings lp
    //       ON l.id = lp.loan_id
    //       AND lp.entry_type IN ('loan_given', 'loan_taken')
    //     JOIN loan_interest_rates lr
    //       ON lp.interest_rate_id = lr.id
    //     JOIN loan_bank_parties lb
    //       ON lp.head_id = lb.id
    //     LEFT JOIN (
    //       SELECT
    //         loan_id,
    //         SUM(CASE WHEN entry_type = 'loan_received' THEN amount_bdt ELSE 0 END) AS sum_received,
    //         SUM(CASE WHEN entry_type = 'loan_payment'  THEN amount_bdt ELSE 0 END) AS sum_payment,
    //         COUNT(CASE WHEN entry_type = 'loan_received' THEN 1 END) AS cnt_received,
    //         COUNT(CASE WHEN entry_type = 'loan_payment'  THEN 1 END) AS cnt_payment,
    //         MAX(CASE WHEN entry_type = 'loan_received' THEN posting_date END) AS last_received_date,
    //         MAX(CASE WHEN entry_type = 'loan_payment'  THEN posting_date END) AS last_payment_date
    //       FROM loan_postings
    //       GROUP BY loan_id
    //     ) agg
    //       ON agg.loan_id = l.id
    //     WHERE lp.status = 'approved'
    //     ";

    //     // Apply transaction type filter if not 'all'
    //     if ($transactionType !== 'all') {
    //         $loanQuery .= " AND lp.entry_type = '" . $transactionType . "'";
    //     }

    //     // Count total records for pagination
    //     $countQuery = "SELECT COUNT(*) as total FROM (" . $loanQuery . ") as counted";
    //     $totalResult = DB::select($countQuery);
    //     $totalRecords = $totalResult[0]->total;

    //     // Add pagination to main query
    //     $loanQuery .= " ORDER BY l.id LIMIT " . $pageSize . " OFFSET " . $offset;

    //     $loans = DB::select($loanQuery);

    //     // Summary query with transaction type filter
    //     //     $summaryQuery = "
    //     //     SELECT
    //     //   SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount+(l.principal_amount*lr.interest_rate/100)) ELSE 0 END) AS loan_given,
    //     //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount+(l.principal_amount*lr.interest_rate/100)) ELSE 0 END) AS loan_taken,
    //     //         SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
    //     //         SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment
    //     //     FROM loans l
    //     //     JOIN loan_postings lp ON l.id = lp.loan_id
    //     // 			JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
    //     //     WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
    //     //     AND lp.status = 'approved'
    //     //     ";

    //     $summaryQuery = "
    //         SELECT
    //         SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_given,
    //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_taken,
    //         SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
    //         SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment,

    //         -- Receivable = loan_given - loan_received
    //         SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
    //         - SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS receivable,

    //         -- Payable = loan_taken - loan_payment
    //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
    //         - SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS payable

    //         FROM loans l
    //         JOIN loan_postings lp ON l.id = lp.loan_id
    //         JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
    //         WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
    //         AND lp.status = 'approved'
    //         ";

    //     // Apply transaction type filter to summary if not 'all'
    //     if ($transactionType !== 'all') {
    //         if ($transactionType === 'loan_given') {
    //             $summaryQuery .= " AND lp.entry_type IN ('loan_given', 'loan_received')";
    //         } elseif ($transactionType === 'loan_taken') {
    //             $summaryQuery .= " AND lp.entry_type IN ('loan_taken', 'loan_payment')";
    //         }
    //     }

    //     $summary = DB::select($summaryQuery);

    //     return response()->json([
    //         'status' => true,
    //         'data' => $loans,
    //         'total' => $totalRecords,
    //         'summary' => $summary[0] ?? [
    //             'loan_given' => 0,
    //             'loan_taken' => 0,
    //             'loan_received' => 0,
    //             'loan_payment' => 0,
    //             'receivable' => 0,
    //             'payable' => 0,
    //         ],
    //     ]);
    // }



    public function getLoanSummary(Request $request)
    {
        // Get the transaction type filter from request, prefer summary_transaction_type first
        $transactionType = $request->input(
            'filter.summary_transaction_type',
            $request->input('filter.transaction_type', 'all')
        );

        // Get pagination parameters
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $offset = ($page - 1) * $pageSize;

        // Base query for loans
        $loanQuery = "
        SELECT
         -- l.id AS loan_id,
          CASE
                WHEN lp.entry_type = 'loan_given' THEN 'Loan Given'
                WHEN lp.entry_type = 'loan_taken' THEN 'Loan Taken'
                ELSE lp.entry_type
          END AS Trx_Category,

          lb.party_name,
          FORMAT(l.principal_amount, 2) AS principal_amount,
          l.term_in_month,
          FORMAT(
                CASE
                    WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
                    WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
                END, 2
            ) AS paid_amount,
          lr.interest_rate AS 'Interest (%)',
          CASE
            WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_given'
              THEN l.term_in_month - COALESCE(agg.cnt_received, 0)
            WHEN l.term_in_month > 0 AND lp.entry_type = 'loan_taken'
              THEN l.term_in_month - COALESCE(agg.cnt_payment, 0)
            ELSE NULL
          END AS remaining_term,
          CASE
            WHEN l.term_in_month > 0 AND lr.interest_rate > 0 THEN
                FORMAT(
                    (
                        ( l.principal_amount * (lr.interest_rate/12/100) * POW(1 + (lr.interest_rate/12/100), l.term_in_month) )
                        / ( POW(1 + (lr.interest_rate/12/100), l.term_in_month) - 1 ) + l.extra_charge
                    ), 2
                )
            ELSE NULL
            END AS emi,
         -- DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0'))) AS installment_date,
          CASE
            WHEN lp.entry_type = 'loan_given' THEN agg.last_received_date
            WHEN lp.entry_type = 'loan_taken'  THEN agg.last_payment_date
          END AS last_payment_date,
          CASE
        WHEN (
            CASE
                WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                ELSE
                    DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
            END
        ) > l.loan_start_date
        THEN (
            CASE
                WHEN lp.entry_type = 'loan_given' AND agg.last_received_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_received_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                WHEN lp.entry_type = 'loan_taken' AND agg.last_payment_date IS NOT NULL THEN
                    DATE(CONCAT(
                        DATE_FORMAT(DATE_ADD(agg.last_payment_date, INTERVAL 1 MONTH), '%Y-%m-'),
                        LPAD(l.installment_date, 2, '0')
                    ))
                ELSE
                    DATE(CONCAT(DATE_FORMAT(CURDATE(), '%Y-%m-'), LPAD(l.installment_date, 2, '0')))
            END
        )
        ELSE '--'
        END AS next_due_date,


                CASE
            WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
                GREATEST(
                    TIMESTAMPDIFF(
                        MONTH,
                        l.loan_start_date,
                        CURDATE()
                    ) + 1
                    -
                    (
                        CASE
                            WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.cnt_received, 0)
                            WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.cnt_payment, 0)
                            ELSE 0
                        END
                    ),
                0)
            ELSE 0
        END AS emi_due_month,

                CASE
            WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
                FORMAT(
                    (
                        CASE
                            WHEN lr.interest_rate > 0 THEN
                                ( l.principal_amount * (lr.interest_rate/12/100) * POW(1 + (lr.interest_rate/12/100), l.term_in_month) )
                                / ( POW(1 + (lr.interest_rate/12/100), l.term_in_month) - 1 ) + l.extra_charge
                            ELSE 0
                        END
                    ) *
                    (
                        GREATEST(
                            TIMESTAMPDIFF(
                                MONTH,
                                l.loan_start_date,
                                CURDATE()
                            ) + 1
                            -
                            (
                                CASE
                                    WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.cnt_received, 0)
                                    WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.cnt_payment, 0)
                                    ELSE 0
                                END
                            ),
                        0)
                    )
                , 2)
            ELSE NULL
        END AS emi_due_amount,
                CASE
            WHEN l.term_in_month > 0 THEN
                FORMAT(
                    (
                        CASE
                            WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
                            WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
                            ELSE 0
                        END
                    ) -
                    (
                        CASE
                            WHEN l.term_in_month > 0 AND CURDATE() > l.loan_start_date THEN
                                GREATEST(
                                    TIMESTAMPDIFF(
                                        MONTH,
                                        l.loan_start_date,
                                        CURDATE()
                                    ) + 1,
                                    0
                                )
                            ELSE 0
                        END
                        * (
                            CASE
                                WHEN l.term_in_month > 0 AND lr.interest_rate > 0
                                    THEN (
                                        ( l.principal_amount * (lr.interest_rate/12/100) * POW(1 + (lr.interest_rate/12/100), l.term_in_month) )
                                        / ( POW(1 + (lr.interest_rate/12/100), l.term_in_month) - 1 ) + l.extra_charge
                                    )
                                ELSE 0
                            END
                        )
                    )
                , 2)
            ELSE 0
        END AS emi_adjusted_amount
        ,
                FORMAT(
                l.principal_amount - (
                    CASE
                        WHEN lp.entry_type = 'loan_given' THEN COALESCE(agg.sum_received, 0)
                        WHEN lp.entry_type = 'loan_taken' THEN COALESCE(agg.sum_payment, 0)
                    END
                ), 2
            ) AS `Payable / Receivable`









            FROM loans l
            JOIN loan_postings lp
            ON l.id = lp.loan_id
            AND lp.entry_type IN ('loan_given', 'loan_taken')
            JOIN loan_interest_rates lr
            ON lp.interest_rate_id = lr.id
            JOIN loan_bank_parties lb
            ON lp.head_id = lb.id
            LEFT JOIN (
            SELECT
                loan_id,
                SUM(CASE WHEN entry_type = 'loan_received' THEN amount_bdt ELSE 0 END) AS sum_received,
                SUM(CASE WHEN entry_type = 'loan_payment'  THEN amount_bdt ELSE 0 END) AS sum_payment,
                COUNT(CASE WHEN entry_type = 'loan_received' THEN 1 END) AS cnt_received,
                COUNT(CASE WHEN entry_type = 'loan_payment'  THEN 1 END) AS cnt_payment,
                MAX(CASE WHEN entry_type = 'loan_received' THEN posting_date END) AS last_received_date,
                MAX(CASE WHEN entry_type = 'loan_payment'  THEN posting_date END) AS last_payment_date
            FROM loan_postings
            GROUP BY loan_id
            ) agg
            ON agg.loan_id = l.id
            WHERE lp.status = 'approved'
            ";





        // Apply transaction type filter if not 'all'
        if ($transactionType !== 'all') {
            $loanQuery .= " AND lp.entry_type = '" . $transactionType . "'";
        }

        // Count total records for pagination
        $countQuery = "SELECT COUNT(*) as total FROM (" . $loanQuery . ") as counted";
        $totalResult = DB::select($countQuery);
        $totalRecords = $totalResult[0]->total;

        // Add pagination to main query
        $loanQuery .= " ORDER BY l.id LIMIT " . $pageSize . " OFFSET " . $offset;

        $loans = DB::select($loanQuery);

        // Summary query with transaction type filter
        //     $summaryQuery = "
        //     SELECT
        //   SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount+(l.principal_amount*lr.interest_rate/100)) ELSE 0 END) AS loan_given,
        //         SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount+(l.principal_amount*lr.interest_rate/100)) ELSE 0 END) AS loan_taken,
        //         SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
        //         SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment
        //     FROM loans l
        //     JOIN loan_postings lp ON l.id = lp.loan_id
        // 			JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
        //     WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
        //     AND lp.status = 'approved'
        //     ";

        $summaryQuery = "
            SELECT
            SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_given,
            SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END) AS loan_taken,
            SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS loan_received,
            SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS loan_payment,

            -- Receivable = loan_given - loan_received
            SUM(CASE WHEN lp.entry_type = 'loan_given' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
            - SUM(CASE WHEN lp.entry_type = 'loan_received' THEN lp.amount_bdt ELSE 0 END) AS receivable,

            -- Payable = loan_taken - loan_payment
            SUM(CASE WHEN lp.entry_type = 'loan_taken' THEN (l.principal_amount + (l.principal_amount * lr.interest_rate / 100)) ELSE 0 END)
            - SUM(CASE WHEN lp.entry_type = 'loan_payment' THEN lp.amount_bdt ELSE 0 END) AS payable

            FROM loans l
            JOIN loan_postings lp ON l.id = lp.loan_id
            JOIN loan_interest_rates lr ON lp.interest_rate_id = lr.id
            WHERE lp.entry_type IN ('loan_given', 'loan_taken', 'loan_received', 'loan_payment')
            AND lp.status = 'approved'
            ";

        // Apply transaction type filter to summary if not 'all'
        if ($transactionType !== 'all') {
            if ($transactionType === 'loan_given') {
                $summaryQuery .= " AND lp.entry_type IN ('loan_given', 'loan_received')";
            } elseif ($transactionType === 'loan_taken') {
                $summaryQuery .= " AND lp.entry_type IN ('loan_taken', 'loan_payment')";
            }
        }

        $summary = DB::select($summaryQuery);

        return response()->json([
            'status' => true,
            'data' => $loans,
            'total' => $totalRecords,
            'summary' => $summary[0] ?? [
                    'loan_given' => 0,
                    'loan_taken' => 0,
                    'loan_received' => 0,
                    'loan_payment' => 0,
                    'receivable' => 0,
                    'payable' => 0,
                ],
        ]);
    }


    // public function getLoanCalculation($loan_party_id)
    // {
    //     $loans = LoanPosting::where('head_id', $loan_party_id)
    //         ->whereIn('entry_type', ['loan_taken', 'loan_given'])
    //         ->with(['loan.interestRates'])
    //         ->get();

    //     $totalLoanAmount = 0;
    //     $totalRemainingBalance = 0;
    //     $interestRate = 0;
    //     $totalTermMonths = 0;

    //     foreach ($loans as $posting) {
    //         $loan = $posting->loan;

    //         if ($loan) {
    //             // Get the principal amount from the loan
    //             $totalLoanAmount += $loan->principal_amount;
    //             $totalTermMonths += $loan->term_in_month;

    //             // Calculate payments made for this specific loan
    //             $payments = LoanPosting::where('loan_id', $loan->id)
    //                 ->where('entry_type', 'loan_payment')
    //                 ->sum('amount_bdt');

    //             // Calculate the remaining balance
    //             $totalRemainingBalance += $loan->principal_amount - $payments;

    //             // Get the current interest rate for the loan
    //             $latestInterestRate = $loan->interestRates()
    //                 ->where('effective_date', '<=', now())
    //                 ->orderByDesc('effective_date')
    //                 ->first();

    //             if ($latestInterestRate) {
    //                 $interestRate = $latestInterestRate->interest_rate;
    //             }
    //         }
    //     }

    //     $principal    = (float) $totalLoanAmount;
    //     $interestRate = (float) $interestRate;


    //     // Calculate interest
    //     $totalInterest         = ($principal * $interestRate) / 100;
    //     $principalWithInterest = $principal + $totalInterest;
    //     $perMonth              = $principalWithInterest / $totalTermMonths;
    //     return response()->json([
    //         'loan_principal_amount' => $totalLoanAmount,
    //         'loan_principal_amount_with_interest' => $principalWithInterest,
    //         'remaining_balance' => $totalRemainingBalance,
    //         'per_month' => round($perMonth, 2),
    //         'total_term' => $totalTermMonths,
    //         'remaining_term' => $totalTermMonths,
    //         'interest_rate' => $interestRate,
    //     ]);
    // }
    // public function getLoanCalculation($loan_party_id)
    // {
    //     $loans = LoanPosting::where('head_id', $loan_party_id)
    //         ->whereIn('entry_type', ['loan_taken', 'loan_given'])
    //         ->with(['loan.interestRates'])
    //         ->get();

    //     $totalLoanAmount = 0;
    //     $totalRemainingBalance = 0;
    //     $interestRate = 0;
    //     $totalTermMonths = 0;
    //     $totalRemainingTerm = 0;

    //     foreach ($loans as $posting) {
    //         $loan = $posting->loan;

    //         if ($loan) {
    //             // Get the principal amount from the loan
    //             $totalLoanAmount += $loan->principal_amount;
    //             $totalTermMonths += $loan->term_in_month;

    //             // Calculate payments made for this specific loan
    //             $payments = LoanPosting::where('loan_id', $loan->id)
    //                 ->where('entry_type', 'loan_payment')
    //                 ->sum('amount_bdt');

    //             // Calculate the remaining balance
    //             $totalRemainingBalance += $loan->principal_amount - $payments;

    //             // Calculate remaining term in months
    //             $paidTerm = LoanPosting::where('loan_id', $loan->id)
    //                 ->where('entry_type', 'loan_payment')
    //                 ->where('status', 'approved') // Only count approved payments
    //                 ->count();
    //             $totalRemainingTerm += $loan->term_in_month - $paidTerm;

    //             // Get the current interest rate for the loan
    //             $latestInterestRate = $loan->interestRates()
    //                 ->where('effective_date', '<=', now())
    //                 ->orderByDesc('effective_date')
    //                 ->first();

    //             if ($latestInterestRate) {
    //                 $interestRate = $latestInterestRate->interest_rate;
    //             }
    //         }
    //     }

    //     $principal = (float) $totalLoanAmount;
    //     $interestRate = (float) $interestRate;

    //     // Calculate interest
    //     $totalInterest = ($principal * $interestRate) / 100;
    //     $principalWithInterest = $principal + $totalInterest;

    //     // Check for division by zero before calculating per month payment
    //     if ($totalTermMonths > 0) {
    //         $perMonth = $principalWithInterest / $totalTermMonths;
    //     } else {
    //         $perMonth = 0; // Default to 0 if term is 0 to prevent error
    //     }

    //     return response()->json([
    //         'loan_principal_amount' => $totalLoanAmount,
    //         'loan_principal_amount_with_interest' => $principalWithInterest,
    //         'remaining_balance' => $totalRemainingBalance,
    //         'per_month' => round($perMonth, 2),
    //         'total_term' => $totalTermMonths,
    //         'remaining_term' => $totalRemainingTerm,
    //         'interest_rate' => $interestRate,
    //         'interest_rate_id' => $loans->interest_rate_id,
    //         'loan_id' => $loans->loan_id,
    //     ]);
    // }

    // public function getLoanCalculation($loan_party_id, $interest_rate_date)
    // {

    //     $loanPosting = LoanPosting::where('head_id', $loan_party_id)
    //         ->whereIn('entry_type', ['loan_taken', 'loan_given'])
    //         ->where('status', 'approved')
    //         ->whereHas('loan', function ($query) {
    //             $query->where('status', 'active');
    //         })
    //         ->with(['loan.interestRates', 'loan.loanPayments'])
    //         ->first();

    //     if (!$loanPosting) {
    //         return response()->json([
    //             'loan_principal_amount' => 0,
    //             'loan_principal_amount_with_interest' => 0,
    //             'remaining_balance' => 0,
    //             'per_month' => 0,
    //             'total_term' => 0,
    //             'remaining_term' => 0,
    //             'interest_rate' => 0,
    //             'interest_rate_id' => null,
    //             'loan_id' => null,
    //         ]);
    //     }

    //     $loan = $loanPosting->loan;

    //     // Get principal and term from the single loan
    //     $principal = $loan->principal_amount;
    //     $totalTermMonths = $loan->term_in_month;
    //     return $loan;
    //     // Calculate total payments using the eager-loaded relationship
    //     $paymentsMade = $loan->loanPayments->where('status', 'approved')->sum('amount_bdt');


    //     // Assuming each payment reduces the term by one month
    //     $paidTerm = $loan->loanPayments->where('status', 'approved')->count();

    //     $remainingTerm = $totalTermMonths - $paidTerm;

    //     // Get the latest interest rate
    //     $latestInterestRate = $loan->interestRates()
    //         ->where('effective_date', '<=', now())
    //         ->orderByDesc('effective_date')
    //         ->first();
    //     // return $latestInterestRate;
    //     // $latestInterestRate = $loan->interestRates()
    //     //     ->where('effective_date', '<=', $interest_rate_date)
    //     //     ->where(function ($query) use ($interest_rate_date) {
    //     //         $query->whereNull('end_date')
    //     //             ->orWhere('end_date', '>=', $interest_rate_date);
    //     //     })
    //     //     ->orderByDesc('effective_date')
    //     //     ->first();

    //     $interestRate = $latestInterestRate ? (float) $latestInterestRate->interest_rate : 0;
    //     $interestRateId = $latestInterestRate ? $latestInterestRate->id : null;

    //     // Calculate total interest and total amount
    //     $totalInterest = ($principal * $interestRate) / 100;
    //     $principalWithInterest = $principal + $totalInterest;
    //     // Calculate remaining balance and term
    //     $remainingBalance = $principalWithInterest - $paymentsMade;

    //     // Calculate per-month payment
    //     $perMonth = $totalTermMonths > 0 ? round($principalWithInterest / $totalTermMonths, 2) : 0;

    //     return response()->json([
    //         'loan_principal_amount' => $principal,
    //         'loan_principal_amount_with_interest' => $principalWithInterest,
    //         'total_payments' => $paymentsMade,
    //         'remaining_balance' => $remainingBalance,
    //         'per_month' => $perMonth,
    //         'total_term' => $totalTermMonths,
    //         'remaining_term' => $remainingTerm,
    //         'interest_rate' => $interestRate,
    //         'interest_rate_id' => $interestRateId,
    //         'loan_id' => $loan->id,
    //     ]);
    // }

    public function getLoanCalculation($loan_party_id, $interest_rate_date)
    {
        $loanPosting = LoanPosting::where('head_id', $loan_party_id)
            ->whereIn('entry_type', ['loan_taken', 'loan_given'])
            ->where('status', 'approved')
            ->whereHas('loan', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['loan.interestRates', 'loan.loanPayments'])
            ->first();

        if (!$loanPosting) {
            return response()->json([
                'loan_principal_amount' => 0,
                'loan_principal_amount_with_interest' => 0,
                'remaining_balance' => 0,
                'per_month' => 0,
                'total_term' => 0,
                'remaining_term' => 0,
                'interest_rate' => 0,
                'interest_rate_id' => null,
                'loan_id' => null,
            ]);
        }

        $loan = $loanPosting->loan;

        $principal = $loan->principal_amount;
        $extra_charge = $loan->extra_charge;
        $totalTermMonths = $loan->term_in_month;

        // Payments made
        $paymentsMade = $loan->loanPayments->where('status', 'approved')->sum('amount_bdt');
        $paidTerm = $loan->loanPayments->where('status', 'approved')->count();
        $remainingTerm = $totalTermMonths > 0 ? $totalTermMonths - $paidTerm : $totalTermMonths;

        // Latest interest rate
        $latestInterestRate = $loan->interestRates()
            ->where('effective_date', '<=', now())
            ->orderByDesc('effective_date')
            ->first();

        $annualInterestRate = $latestInterestRate ? (float) $latestInterestRate->interest_rate : 0;
        $interestRateId = $latestInterestRate ? $latestInterestRate->id : null;

        // Monthly interest rate
        $monthlyRate = $annualInterestRate / (12 * 100);

        // EMI calculation
        if ($totalTermMonths > 0) {
            if ($monthlyRate > 0) {
                $emi = ($principal * $monthlyRate *  (pow(1 + $monthlyRate, $totalTermMonths)))
                    / (((pow(1 + $monthlyRate, $totalTermMonths)) - 1));
                $totalPayable = $emi * $totalTermMonths;
                $remainingBalance = $totalPayable - $paymentsMade;
            } else {
                // No interest case
                $emi = $principal / $totalTermMonths;
                $totalPayable = $emi * $totalTermMonths;
                $remainingBalance = $totalPayable - $paymentsMade;
            }
        } else {
            // No term defined
            $emi = 0;
            $totalPayable = $principal;
            $remainingBalance = $totalPayable - $paymentsMade;
        }

        // return $emi = $emi;
        // $totalPayable = $emi * $totalTermMonths;
        // $remainingBalance = $totalPayable - $paymentsMade;

        return response()->json([
            'loan_principal_amount' => $principal,
            'loan_principal_amount_with_interest' => round($totalPayable, 2),
            'total_payments' => $paymentsMade,
            'remaining_balance' => round($remainingBalance, 2),
            'per_month' => round($emi, 2) + ($extra_charge ?? 0),
            'total_term' => $totalTermMonths,
            'remaining_term' => $remainingTerm,
            'interest_rate' => $annualInterestRate,
            'interest_rate_id' => $interestRateId,
            'loan_id' => $loan->id,
        ]);
    }


    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $status = $request->input('status');

        if (empty($status) && $status !== 'all') {
            $status = 'pending';
        }

        $query = DB::table('loan_postings as iep')
            ->leftJoin('loan_bank_parties as ih', 'ih.id', '=', 'iep.head_id')
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 'iep.payment_channel_id')
            ->leftJoin('account_numbers as ac', 'ac.id', '=', 'iep.account_id')
            ->leftJoin('loans as l', 'l.id', '=', 'iep.loan_id')
            // ->leftJoin('loan_interest_rates as lir', 'lir.id', '=', 'iep.interest_rate_id')
            ->leftJoin('loan_interest_rates as lir', function ($join) {
                $join->on('lir.loan_id', '=', 'iep.loan_id')
                    ->whereRaw('lir.id = (SELECT MAX(id) FROM loan_interest_rates WHERE loan_id = iep.loan_id)');
            })
            ->select(
                'iep.*',
                'ih.party_name',
                'pcd.method_name',
                'ac.ac_no',
                'ac.ac_name',
                'l.principal_amount',
                'l.extra_charge',
                'l.term_in_month',
                'l.loan_start_date',
                'l.status as loan_status',
                'lir.interest_rate',
                'lir.effective_date',
                'lir.end_date'
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
    //     $mapping = [
    //         'loan_taken'   => 'received',
    //         'loan_given'   => 'payment',
    //         'loan_payment' => 'payment',
    //     ];

    //     try {
    //         $posting = null;

    //         DB::transaction(function () use ($request, $mapping, &$posting) {
    //             $principal    = (float) $request->input('amount_bdt');
    //             $interestRate = (float) $request->input('interest_rate');
    //             $termMonths   = (int) ($request->input('term_months') * 12);
    //             $startDate    = Carbon::parse($request->input('posting_date'));

    //             // Calculate interest
    //             $totalInterest         = ($principal * $interestRate) / 100;
    //             $principalWithInterest = $principal + $totalInterest;
    //             $perMonth              = $principalWithInterest / $termMonths;

    //             // 1. Create Loan
    //             $loan = Loan::create([
    //                 'principal_amount' => $request->input('amount_bdt'),
    //                 'term_in_month'    => $termMonths,
    //                 'loan_start_date'  => $startDate,
    //                 'status'           => 'active',
    //             ]);

    //             // 2. Create Interest Rate
    //             $rate = LoanInterestRate::create([
    //                 'loan_id'        => $loan->id,
    //                 'interest_rate'  => $interestRate,
    //                 'effective_date' => $startDate,
    //                 'end_date'       => null,
    //             ]);

    //             // 3. Create Posting
    //             $posting = LoanPosting::create([
    //                 'transaction_type'   => $mapping[$request->input('transaction_type')],
    //                 'head_type'          => $request->input('head_type'),
    //                 'head_id'            => $request->input('head_id'),
    //                 'payment_channel_id' => $request->input('payment_channel_id'),
    //                 'account_id'         => $request->input('account_id'),
    //                 'receipt_number'     => $request->input('receipt_number'),
    //                 'amount_bdt'         => $request->input('amount_bdt'),
    //                 'posting_date'       => $request->input('posting_date'),
    //                 'note'               => $request->input('note'),
    //                 'entry_type'         => $request->input('transaction_type'),
    //                 'loan_id'            => $loan->id,
    //                 'interest_rate_id'   => $rate->id,
    //             ]);
    //         });

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Created successfully.',
    //             'data'    => $posting,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Transaction failed. ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
    // public function store(Request $request)
    // {

    //     $mapping = [
    //         'loan_taken'   => 'received',
    //         'loan_given'   => 'payment',
    //         'loan_payment' => 'payment',
    //         'loan_received' => 'received',
    //     ];

    //     try {
    //         $posting = null;

    //         DB::transaction(function () use ($request, $mapping, &$posting) {
    //             $loanId = $request->input('loan_id');
    //             $interestRateId = $request->input('interest_rate_id');

    //             if ($request->input('transaction_type') === 'loan_taken' || $request->input('transaction_type') === 'loan_given') {
    //                 $principal    = (float) $request->input('amount_bdt');
    //                 $interestRate = (float) $request->input('interest_rate');
    //                 $termMonths   = (int) $request->input('term_months');
    //                 $installmentDate = (int) $request->input('installment_date');
    //                 $startDate    = Carbon::parse($request->input('posting_date'));
    //                 $interestRateEffectiveDate    = Carbon::parse($request->input('interest_rate_effective_date'));

    //                 // 1. Create Loan
    //                 $loan = Loan::create([
    //                     'principal_amount' => $request->input('amount_bdt'),
    //                     'term_in_month'    => $termMonths,
    //                     'loan_start_date'  => $startDate,
    //                     'installment_date' => $installmentDate,
    //                     'status'           => 'active',
    //                 ]);

    //                 $loanId = $loan->id;

    //                 // 2. Create Interest Rate
    //                 $rate = LoanInterestRate::create([
    //                     'loan_id'        => $loan->id,
    //                     'interest_rate'  => $interestRate,
    //                     'effective_date' => $interestRateEffectiveDate,
    //                     'end_date'       => null,
    //                 ]);

    //                 $interestRateId = $rate->id;
    //             }

    //             // 3. Create Posting with the correct loan_id and interest_rate_id
    //             $posting = LoanPosting::create([
    //                 'transaction_type'   => $mapping[$request->input('transaction_type')],
    //                 'head_type'          => $request->input('head_type'),
    //                 'head_id'            => $request->input('head_id'),
    //                 'payment_channel_id' => $request->input('payment_channel_id'),
    //                 'account_id'         => $request->input('account_id'),
    //                 'receipt_number'     => $request->input('receipt_number'),
    //                 'amount_bdt'         => $request->input('amount_bdt'),
    //                 'posting_date'       => $request->input('posting_date'),
    //                 'note'               => $request->input('note'),
    //                 'entry_type'         => $request->input('transaction_type'),
    //                 'loan_id'            => $loanId,
    //                 'interest_rate_id'   => $interestRateId,
    //             ]);
    //         });

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Created successfully.',
    //             'data'    => $posting,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Transaction failed. ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $mapping = [
            'loan_taken'   => 'received',
            'loan_given'   => 'payment',
            'loan_payment' => 'payment',
            'loan_received' => 'received',
        ];

        try {
            $posting = null;

            DB::transaction(function () use ($request, $mapping, &$posting) {
                $loanId = $request->input('loan_id');
                $interestRateId = $request->input('interest_rate_id');

                if ($request->input('transaction_type') === 'loan_taken' || $request->input('transaction_type') === 'loan_given') {
                    $principal    = (float) $request->input('amount_bdt');
                    $interestRate = (float) $request->input('interest_rate');
                    $termMonths   = (int) $request->input('term_months');
                    $installmentDate = (int) $request->input('installment_date');
                    $startDate    = Carbon::parse($request->input('posting_date'));
                    $interestRateEffectiveDate    = Carbon::parse($request->input('interest_rate_effective_date'));


                    $loan = Loan::create([
                        'principal_amount' => $request->input('amount_bdt'),
                        'extra_charge'     => $request->input('extra_charge'),
                        'term_in_month'    => $termMonths,
                        'loan_start_date'  => $startDate,
                        'installment_date' => $installmentDate,
                        'status'           => 'active',
                    ]);

                    $loanId = $loan->id;


                    $rate = LoanInterestRate::create([
                        'loan_id'        => $loan->id,
                        'interest_rate'  => $interestRate,
                        'effective_date' => $interestRateEffectiveDate,
                        'end_date'       => null,
                    ]);

                    $interestRateId = $rate->id;
                }


                $posting = LoanPosting::create([
                    'transaction_type'   => $mapping[$request->input('transaction_type')],
                    'head_type'          => $request->input('head_type'),
                    'head_id'            => $request->input('head_id'),
                    'payment_channel_id' => $request->input('payment_channel_id'),
                    'account_id'         => $request->input('account_id'),
                    'receipt_number'     => $request->input('receipt_number'),
                    'amount_bdt'         => $request->input('amount_bdt'),
                    'posting_date'       => $request->input('posting_date'),
                    'note'               => $request->input('note'),
                    'entry_type'         => $request->input('transaction_type'),
                    'loan_id'            => $loanId,
                    'interest_rate_id'   => $interestRateId,
                ]);


                $accountId = $request->input('account_id');
                $amount = (float) $request->input('amount_bdt');
                $transactionType = $mapping[$request->input('transaction_type')];

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
                'status'  => true,
                'message' => 'Created successfully.',
                'data'    => $posting,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
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
        $source = LoanPosting::find($id);

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

    //     $loanPosting = LoanPosting::find($id);


    //     if ($request->has('interest_rate') && $request->has('interest_rate_effective_date')) {
    //         $loanId = $loanPosting->loan_id;
    //         $newInterestRate = $request->input('interest_rate');
    //         $effectiveDate = $request->input('interest_rate_effective_date');


    //         $previousRate = LoanInterestRate::where('loan_id', $loanId)
    //             ->whereNull('end_date')
    //             ->orderByDesc('effective_date')
    //             ->first();

    //         // If a previous active rate exists, update its end_date
    //         if ($previousRate) {
    //             $endDate = Carbon::parse($effectiveDate)->subDay()->toDateString();
    //             $previousRate->update(['end_date' => $endDate]);
    //         }

    //         // Step 2: Insert the new interest rate record
    //         $newRate = LoanInterestRate::create([
    //             'loan_id' => $loanId,
    //             'interest_rate' => $newInterestRate,
    //             'effective_date' => $effectiveDate,
    //             'end_date' => null,
    //         ]);
    //     }

    //     // $source->update($request->all());

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Updated successfully.',
    //         'data' => $$newRate
    //     ], 200);
    // }

    // public function update(Request $request, string $id)
    // {
    //     // Find the loan posting record
    //     $loanPosting = LoanPosting::find($id);

    //     // Handle case where loan posting is not found
    //     if (!$loanPosting) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Loan posting not found.'
    //         ], 404);
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $newRate = null;

    //         // Check if the request contains new interest rate data
    //         if ($request->has('interest_rate') && $request->has('interest_rate_effective_date')) {
    //             $loanId = $loanPosting->loan_id;
    //             $newInterestRate = $request->input('interest_rate');
    //             $effectiveDate = $request->input('interest_rate_effective_date');

    //             // Find the active interest rate record for this loan
    //             $previousRate = LoanInterestRate::where('loan_id', $loanId)
    //                 ->whereNull('end_date')
    //                 ->first();

    //             // If a previous active rate exists, update its end_date
    //             if ($previousRate) {
    //                 $endDate = Carbon::parse($effectiveDate)->subDay()->toDateString();
    //                 $previousRate->update(['end_date' => $endDate]);
    //             }

    //             // Insert the new interest rate record
    //             $newRate = LoanInterestRate::create([
    //                 'loan_id' => $loanId,
    //                 'interest_rate' => $newInterestRate,
    //                 'effective_date' => $effectiveDate,
    //                 'end_date' => null,
    //             ]);
    //         }
    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Updated successfully.',
    //             'data' => $loanPosting,
    //             'new_interest_rate_data' => $newRate,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred during the update.',
    //             'error_details' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function update(Request $request, string $id)
    {
        // Find the loan posting record
        $loanPosting = LoanPosting::find($id);

        // Handle case where loan posting is not found
        if (!$loanPosting) {
            return response()->json([
                'status' => false,
                'message' => 'Loan posting not found.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Store old values for calculation
            $oldAmount = (float) $loanPosting->amount_bdt;
            $oldTransactionType = $loanPosting->transaction_type;
            $accountId = $loanPosting->account_id;

            $newRate = null;

            // Check if the request contains new interest rate data
            if ($request->has('interest_rate') && $request->has('interest_rate_effective_date')) {
                $loanId = $loanPosting->loan_id;
                $newInterestRate = $request->input('interest_rate');
                $effectiveDate = $request->input('interest_rate_effective_date');

                // Find the active interest rate record for this loan
                $previousRate = LoanInterestRate::where('loan_id', $loanId)
                    ->whereNull('end_date')
                    ->first();

                // If a previous active rate exists, update its end_date
                if ($previousRate) {
                    $endDate = Carbon::parse($effectiveDate)->subDay()->toDateString();
                    $previousRate->update(['end_date' => $endDate]);
                }

                // Insert the new interest rate record
                $newRate = LoanInterestRate::create([
                    'loan_id' => $loanId,
                    'interest_rate' => $newInterestRate,
                    'effective_date' => $effectiveDate,
                    'end_date' => null,
                ]);
            }

            // Update the LoanPosting
            $loanPosting->update($request->all());

            // Get new values
            $newAmount = (float) $request->input('amount_bdt', $loanPosting->amount_bdt);
            $newTransactionType = $request->input('transaction_type', $loanPosting->transaction_type);

            // Update Account Current Balance
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

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.',
                'data' => $loanPosting,
                'new_interest_rate_data' => $newRate,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred during the update.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        $source = LoanPosting::find($id);

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
        $posting = LoanPosting::find($id);

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
