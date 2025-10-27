<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RentalPosting;
use App\Models\RentalParty;
use App\Models\RentalHousePartyMap;
use App\Models\AccountCurrentBalance;
use App\Models\RentalHouse;

class RentalPostingController extends Controller
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

    // public function getRentalLedger(Request $request)
    // {
    //     $filters = $request->query();

    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '' && $filters['filter']['head_id'] !== 'all') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Get total count
    //     $total = $applyFilters(DB::table('rental_postings'))->count();

    //     // Summary data
    //     $summary = $applyFilters(DB::table('rental_postings as rp')
    //         ->join('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id'))
    //         ->selectRaw('
    //         rpy.id as head_id,
    //         rpy.party_name,
    //         rpy.monthly_rent,
    //         rpy.security_money,
    //         rpy.auto_adjustment,
    //         SUM(CASE WHEN rp.entry_type = "auto_adjustment" THEN rp.amount_bdt ELSE 0 END) AS total_auto_adjustment,
    //         rpy.remaining_security_money,
    //         SUM(CASE WHEN rp.entry_type = "rent_received" THEN rp.amount_bdt ELSE 0 END) AS total_rent_received,
    //         SUM(CASE WHEN rp.entry_type = "security_money_refund" THEN rp.amount_bdt ELSE 0 END) AS total_security_refund
    //     ')
    //         ->groupBy('rpy.id', 'rpy.party_name', 'rpy.monthly_rent', 'rpy.security_money', 'rpy.auto_adjustment', 'rpy.remaining_security_money')
    //         ->first();

    //     // Pagination
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     $details = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->leftJoin('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //     )
    //         ->select('rp.id', 'rp.transaction_type', 'rpy.party_name', 'rp.amount_bdt', 'rp.posting_date', 'rp.note', 'rp.status', 'rp.entry_type')
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }

    // public function getRentalLedger(Request $request)
    // {
    //     $filters = $request->query();

    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '' && $filters['filter']['head_id'] !== 'all') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Get total count
    //     $total = $applyFilters(DB::table('rental_postings'))->count();

    //     $summary = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->join('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //             ->join(
    //                 DB::raw('(SELECT rental_party_id,
    //                            SUM(security_money) AS total_security_money,
    //                            SUM(remaining_security_money) AS total_remaining_security_money
    //                     FROM rental_house_party_maps
    //                     GROUP BY rental_party_id) rhpm_tot'),
    //                 'rhpm_tot.rental_party_id',
    //                 '=',
    //                 'rpy.id'
    //             )
    //     )
    //         ->selectRaw('
    //         rhpm_tot.total_security_money,
    //         rhpm_tot.total_remaining_security_money,
    //         SUM(CASE WHEN rp.entry_type = "rent_received" THEN rp.amount_bdt ELSE 0 END) AS total_rent_received,
    //         SUM(CASE WHEN rp.entry_type = "auto_adjustment" THEN rp.amount_bdt ELSE 0 END) AS total_auto_adjustment,
    //         SUM(CASE WHEN rp.entry_type = "security_money_refund" THEN rp.amount_bdt ELSE 0 END) AS total_security_refund
    //     ')
    //         ->first();



    //     // Pagination for details
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     $details = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->leftJoin('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //     )
    //         ->select('rp.id', 'rp.transaction_type', 'rpy.party_name', 'rp.amount_bdt', 'rp.posting_date', 'rp.note', 'rp.status', 'rp.entry_type')
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }

    // public function getRentalLedger(Request $request)
    // {
    //     $filters = $request->query();

    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '' && $filters['filter']['head_id'] !== 'all') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Get total count
    //     $total = $applyFilters(DB::table('rental_postings'))->count();

    //     // Party-wise summary using your current subquery
    //     $summary = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->join('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //             ->join(
    //                 DB::raw('(SELECT rental_party_id,
    //                            SUM(security_money) AS total_security_money,
    //                            SUM(remaining_security_money) AS total_remaining_security_money,
    //                            SUM(monthly_rent) AS total_monthly_rent
    //                     FROM rental_house_party_maps
    //                     GROUP BY rental_party_id) rhpm_tot'),
    //                 'rhpm_tot.rental_party_id',
    //                 '=',
    //                 'rpy.id'
    //             )
    //     )
    //         ->selectRaw('
    //         rhpm_tot.total_security_money,
    //         rhpm_tot.total_remaining_security_money,
    //         SUM(CASE WHEN rp.transaction_type = "received" THEN rp.amount_bdt ELSE 0 END) AS total_rent_received,
    //         SUM(CASE WHEN rp.entry_type = "auto_adjustment" THEN rp.amount_bdt ELSE 0 END) AS total_auto_adjustment,
    //         SUM(CASE WHEN rp.entry_type = "security_money_refund" THEN rp.amount_bdt ELSE 0 END) AS total_security_refund,
    //         rhpm_tot.total_monthly_rent
    //     ')
    //         ->groupBy('rhpm_tot.rental_party_id')
    //         ->first();

    //     // Pagination for details
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     $details = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->leftJoin('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //     )
    //         ->select('rp.id', 'rp.transaction_type', 'rpy.party_name', 'rp.amount_bdt', 'rp.posting_date', 'rp.note', 'rp.status', 'rp.entry_type')
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }

    // public function getRentalLedger(Request $request)
    // {
    //     $filters = $request->query();

    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '' && $filters['filter']['head_id'] !== 'all') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Total count for pagination
    //     $total = $applyFilters(DB::table('rental_postings'))->count();

    //     // Summary
    //     $summaryQuery = DB::table('rental_house_party_maps as rhpm')
    // ->selectRaw('
    //     SUM(rhpm.security_money) AS total_security_money,
    //     SUM(rhpm.remaining_security_money) AS total_remaining_security_money,
    //     SUM(rhpm.monthly_rent) AS total_monthly_rent,
    //     (SELECT SUM(amount_bdt) FROM rental_postings WHERE transaction_type="received" AND status="approved") AS total_rent_received,
    //     (SELECT SUM(amount_bdt) FROM rental_postings WHERE entry_type="auto_adjustment" AND status="approved") AS total_auto_adjustment,
    //     (SELECT SUM(amount_bdt) FROM rental_postings WHERE entry_type="security_money_refund" AND status="approved") AS total_security_refund
    // ')
    // ->whereIn('rhpm.rental_party_id', function ($q) use ($filters) {
    //     $q->select('head_id')->from('rental_postings')->where('status', 'approved');
    // })
    // ->first();


    //     // Pagination for details
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     $details = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->leftJoin('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //     )
    //         ->select('rp.id', 'rp.transaction_type', 'rpy.party_name', 'rp.amount_bdt', 'rp.posting_date', 'rp.note', 'rp.status', 'rp.entry_type')
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summaryQuery,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }


    // running last

    // public function getRentalLedger(Request $request)
    // {
    //     $filters = $request->query();
    //     $headId = $filters['filter']['head_id'] ?? null;

    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('status', 'approved');

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '' && $filters['filter']['head_id'] !== 'all') {
    //             $query->where('head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Total count for pagination
    //     $total = $applyFilters(DB::table('rental_postings'))->count();

    //     // Summary
    //     $summaryQuery = DB::table('rental_house_party_maps as rhpm')
    //         ->selectRaw('
    //         SUM(rhpm.security_money) AS total_security_money,
    //         SUM(rhpm.remaining_security_money) AS total_remaining_security_money,
    //         SUM(rhpm.monthly_rent) AS total_monthly_rent,
    //         (SELECT SUM(amount_bdt) FROM rental_postings 
    //             WHERE transaction_type="received" 
    //             AND status="approved" 
    //             ' . ($headId && $headId !== 'all' ? "AND head_id = {$headId}" : "") . '
    //         ) AS total_rent_received,
    //         (SELECT SUM(amount_bdt) FROM rental_postings 
    //             WHERE entry_type="auto_adjustment" 
    //             AND status="approved"
    //             ' . ($headId && $headId !== 'all' ? "AND head_id = {$headId}" : "") . '
    //         ) AS total_auto_adjustment,
    //         (SELECT SUM(amount_bdt) FROM rental_postings 
    //             WHERE entry_type="security_money_refund" 
    //             AND status="approved"
    //             ' . ($headId && $headId !== 'all' ? "AND head_id = {$headId}" : "") . '
    //         ) AS total_security_refund
    //     ')
    //         ->when($headId && $headId !== 'all', function ($query) use ($headId) {
    //             $query->where('rhpm.rental_party_id', $headId);
    //         })
    //         ->first();

    //     // Pagination for details
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     $details = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->leftJoin('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
    //     )
    //         ->select('rp.id', 'rp.transaction_type', 'rpy.party_name', 'rp.amount_bdt', 'rp.posting_date', 'rp.note', 'rp.status', 'rp.entry_type')
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summaryQuery,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }



    // public function getRentalLedgerData(Request $request)
    // {
    //     $filters = $request->query();

    //     // Closure to apply filters
    //     $applyFilters = function ($query) use ($filters) {
    //         $query->where('rp.status', 'approved');

    //         if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
    //             $query->where('rp.entry_type', $filters['filter']['transaction_type']);
    //         }

    //         if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
    //             $query->where('rp.head_id', $filters['filter']['head_id']);
    //         }

    //         if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //             $query->whereBetween('rp.posting_date', [$filters['filter']['start_date'], $filters['filter']['end_date']]);
    //         }

    //         return $query;
    //     };

    //     // Total count - use DISTINCT to avoid counting duplicates
    //     $total = $applyFilters(DB::table('rental_postings as rp'))->count(DB::raw('DISTINCT rp.id'));

    //     // Get current date for month calculation
    //     $currentDate = now();
    //     $currentYear = $currentDate->year;
    //     $currentMonth = $currentDate->month;

    //     // Summary query with proper receivable calculation for YYYY-MM format
    //     $summaryQuery = "
    //  SELECT
    //     SUM(CASE WHEN rhpm.monthly_rent IS NOT NULL THEN rhpm.monthly_rent ELSE 0 END) AS total_monthly_rent,
    //     SUM(CASE WHEN rp.entry_type = 'rent_received' THEN rp.amount_bdt ELSE 0 END) AS total_rent_received,
    //     SUM(CASE WHEN rhpm.security_money IS NOT NULL THEN rhpm.security_money ELSE 0 END) AS total_security_money,
    //     SUM(CASE WHEN rp.entry_type = 'auto_adjustment' THEN rp.amount_bdt ELSE 0 END) AS total_auto_adjustment,
    //     SUM(CASE WHEN rhpm.remaining_security_money IS NOT NULL THEN rhpm.remaining_security_money ELSE 0 END) AS total_remaining_security_money,
    //     SUM(CASE WHEN rp.entry_type = 'security_money_refund' THEN rp.amount_bdt ELSE 0 END) AS total_security_refund,

    //     -- Total Receivable = (Total Expected Rent from start date to current month) - Total Rent Received
    //     -- Only calculate if there are rental houses with rent_start_date
    //     CASE 
    //         WHEN (
    //             SELECT COUNT(*) 
    //             FROM rental_house_party_maps rhpm_check 
    //             WHERE rhpm_check.status = 'active' 
    //             AND rhpm_check.rent_start_date IS NOT NULL
    //         ) > 0
    //         THEN (
    //             SELECT COALESCE(SUM(
    //                 CASE 
    //                     WHEN rhpm_inner.rent_start_date IS NOT NULL AND rhpm_inner.monthly_rent IS NOT NULL 
    //                     THEN 
    //                         GREATEST(
    //                             (
    //                                 ($currentYear - SUBSTRING(rhpm_inner.rent_start_date, 1, 4)) * 12 
    //                                 + ($currentMonth - SUBSTRING(rhpm_inner.rent_start_date, 6, 2))
    //                                 + 1
    //                             ),
    //                             0
    //                         ) * rhpm_inner.monthly_rent
    //                     ELSE 0 
    //                 END
    //             ), 0)
    //             FROM rental_house_party_maps rhpm_inner
    //             WHERE rhpm_inner.status = 'active'
    //         ) - SUM(CASE WHEN rp.entry_type = 'rent_received' THEN rp.amount_bdt ELSE 0 END)
    //         ELSE 0 
    //     END AS total_receivable,

    //     -- Already Adjusted (sum of all auto adjustments)
    //     SUM(CASE WHEN rp.entry_type = 'auto_adjustment' THEN rp.amount_bdt ELSE 0 END) AS already_adjusted,

    //     -- Total Due Amount (same as receivable for now)
    //     CASE 
    //         WHEN (
    //             SELECT COUNT(*) 
    //             FROM rental_house_party_maps rhpm_check 
    //             WHERE rhpm_check.status = 'active' 
    //             AND rhpm_check.rent_start_date IS NOT NULL
    //         ) > 0
    //         THEN (
    //             SELECT COALESCE(SUM(
    //                 CASE 
    //                     WHEN rhpm_inner.rent_start_date IS NOT NULL AND rhpm_inner.monthly_rent IS NOT NULL 
    //                     THEN 
    //                         GREATEST(
    //                             (
    //                                 ($currentYear - SUBSTRING(rhpm_inner.rent_start_date, 1, 4)) * 12 
    //                                 + ($currentMonth - SUBSTRING(rhpm_inner.rent_start_date, 6, 2))
    //                                 + 1
    //                             ),
    //                             0
    //                         ) * rhpm_inner.monthly_rent
    //                     ELSE 0 
    //                 END
    //             ), 0)
    //             FROM rental_house_party_maps rhpm_inner
    //             WHERE rhpm_inner.status = 'active'
    //         ) - SUM(CASE WHEN rp.entry_type = 'rent_received' THEN rp.amount_bdt ELSE 0 END)
    //         ELSE 0 
    //     END AS total_due_amount,

    //     -- Total Due Month (calculated based on receivable and monthly rent) - Only if rent_start_date exists
    //     CASE 
    //         WHEN (
    //             SELECT COUNT(*) 
    //             FROM rental_house_party_maps rhpm_check 
    //             WHERE rhpm_check.status = 'active' 
    //             AND rhpm_check.rent_start_date IS NOT NULL
    //         ) > 0
    //         AND SUM(CASE WHEN rhpm.monthly_rent IS NOT NULL THEN rhpm.monthly_rent ELSE 0 END) > 0 
    //         THEN CEILING(
    //             (
    //                 (
    //                     SELECT COALESCE(SUM(
    //                         CASE 
    //                             WHEN rhpm_inner.rent_start_date IS NOT NULL AND rhpm_inner.monthly_rent IS NOT NULL 
    //                             THEN 
    //                                 GREATEST(
    //                                     (
    //                                         ($currentYear - SUBSTRING(rhpm_inner.rent_start_date, 1, 4)) * 12 
    //                                         + ($currentMonth - SUBSTRING(rhpm_inner.rent_start_date, 6, 2))
    //                                         + 1
    //                                     ),
    //                                     0
    //                                 ) * rhpm_inner.monthly_rent
    //                             ELSE 0 
    //                         END
    //                     ), 0)
    //                     FROM rental_house_party_maps rhpm_inner
    //                     WHERE rhpm_inner.status = 'active'
    //                 ) 
    //                 - SUM(CASE WHEN rp.entry_type = 'rent_received' THEN rp.amount_bdt ELSE 0 END)
    //             )
    //             / SUM(CASE WHEN rhpm.monthly_rent IS NOT NULL THEN rhpm.monthly_rent ELSE 0 END)
    //         )
    //         ELSE 0 
    //     END AS total_due_month,

    //     -- Partial Due Amount (remainder after full months) - Only if rent_start_date exists
    //     CASE 
    //         WHEN (
    //             SELECT COUNT(*) 
    //             FROM rental_house_party_maps rhpm_check 
    //             WHERE rhpm_check.status = 'active' 
    //             AND rhpm_check.rent_start_date IS NOT NULL
    //         ) > 0
    //         AND SUM(CASE WHEN rhpm.monthly_rent IS NOT NULL THEN rhpm.monthly_rent ELSE 0 END) > 0 
    //         THEN MOD(
    //             (
    //                 (
    //                     SELECT COALESCE(SUM(
    //                         CASE 
    //                             WHEN rhpm_inner.rent_start_date IS NOT NULL AND rhpm_inner.monthly_rent IS NOT NULL 
    //                             THEN 
    //                                 GREATEST(
    //                                     (
    //                                         ($currentYear - SUBSTRING(rhpm_inner.rent_start_date, 1, 4)) * 12 
    //                                         + ($currentMonth - SUBSTRING(rhpm_inner.rent_start_date, 6, 2))
    //                                         + 1
    //                                     ),
    //                                     0
    //                                 ) * rhpm_inner.monthly_rent
    //                             ELSE 0 
    //                         END
    //                     ), 0)
    //                     FROM rental_house_party_maps rhpm_inner
    //                     WHERE rhpm_inner.status = 'active'
    //                 ) 
    //                 - SUM(CASE WHEN rp.entry_type = 'rent_received' THEN rp.amount_bdt ELSE 0 END)
    //             ),
    //             SUM(CASE WHEN rhpm.monthly_rent IS NOT NULL THEN rhpm.monthly_rent ELSE 0 END)
    //         )
    //         ELSE 0 
    //     END AS partial_due_amount

    //     FROM rental_postings rp
    //     LEFT JOIN rental_parties rparty ON rp.head_id = rparty.id
    //     LEFT JOIN rental_house_party_maps rhpm ON rp.head_id = rhpm.rental_party_id
    //     WHERE rp.status = 'approved'
    //     ";

    //     // Apply the same filters to summary query
    //     $whereConditions = [];

    //     // Transaction type filter
    //     if (isset($filters['filter']['transaction_type']) && $filters['filter']['transaction_type'] !== 'all') {
    //         $transactionType = $filters['filter']['transaction_type'];
    //         $whereConditions[] = "rp.entry_type = '$transactionType'";
    //     }

    //     // Head ID filter
    //     if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
    //         $headId = $filters['filter']['head_id'];
    //         $whereConditions[] = "rp.head_id = $headId";
    //     }

    //     // Date range filter
    //     if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
    //         $startDate = $filters['filter']['start_date'];
    //         $endDate = $filters['filter']['end_date'];
    //         $whereConditions[] = "rp.posting_date BETWEEN '$startDate' AND '$endDate'";
    //     }

    //     // Add WHERE conditions if any exist
    //     if (!empty($whereConditions)) {
    //         $summaryQuery .= " AND " . implode(" AND ", $whereConditions);
    //     }

    //     // Run the filtered summary query
    //     $summary = DB::selectOne($summaryQuery);

    //     // Pagination
    //     $page = $filters['page'] ?? 1;
    //     $pageSize = $filters['pageSize'] ?? 10;

    //     // Subquery to calculate aggregates per party
    //     $partyAggSubquery = DB::table('rental_postings')
    //         ->select(
    //             'head_id',
    //             DB::raw("SUM(CASE WHEN entry_type = 'rent_received' THEN amount_bdt ELSE 0 END) AS total_rent_received"),
    //             DB::raw("SUM(CASE WHEN entry_type = 'auto_adjustment' THEN amount_bdt ELSE 0 END) AS total_auto_adjustment"),
    //             DB::raw("SUM(CASE WHEN entry_type = 'security_money_refund' THEN amount_bdt ELSE 0 END) AS total_security_refund"),
    //             DB::raw("SUM(CASE WHEN entry_type = 'security_money_amount' THEN amount_bdt ELSE 0 END) AS total_security_money_received"),
    //             DB::raw("COUNT(CASE WHEN entry_type = 'rent_received' THEN id END) AS rent_received_count")
    //         )
    //         ->where('status', 'approved')
    //         ->groupBy('head_id');

    //     // Subquery to calculate expected rent per rental house based on rent_start_date
    //     $expectedRentPerHouseSubquery = DB::table('rental_house_party_maps')
    //         ->select(
    //             'id',
    //             'rental_party_id',
    //             'rental_house_id',
    //             'monthly_rent',
    //             'security_money',
    //             'remaining_security_money',
    //             'rent_start_date',
    //             DB::raw(
    //                 "
    //         CASE 
    //             WHEN rent_start_date IS NOT NULL AND monthly_rent IS NOT NULL 
    //             THEN 
    //                 GREATEST(
    //                     (
    //                         ($currentYear - SUBSTRING(rent_start_date, 1, 4)) * 12 
    //                         + ($currentMonth - SUBSTRING(rent_start_date, 6, 2))
    //                         + 1
    //                     ),
    //                     0
    //                 ) * monthly_rent
    //             ELSE 0 
    //         END AS expected_rent_for_house"
    //             ),
    //             DB::raw("CASE WHEN rent_start_date IS NOT NULL THEN 1 ELSE 0 END AS has_rent_start_date")
    //         )
    //         ->where('status', 'active');

    //     // Subquery to aggregate expected rent per party
    //     $expectedRentAggSubquery = DB::table('rental_house_party_maps')
    //         ->select(
    //             'rental_party_id',
    //             DB::raw("SUM(
    //         CASE 
    //             WHEN rent_start_date IS NOT NULL AND monthly_rent IS NOT NULL 
    //             THEN 
    //                 GREATEST(
    //                     (
    //                         ($currentYear - SUBSTRING(rent_start_date, 1, 4)) * 12 
    //                         + ($currentMonth - SUBSTRING(rent_start_date, 6, 2))
    //                         + 1
    //                     ),
    //                     0
    //                 ) * monthly_rent
    //             ELSE 0 
    //         END
    //     ) AS total_expected_rent"),
    //             DB::raw("SUM(monthly_rent) AS total_monthly_rent"),
    //             DB::raw("SUM(security_money) AS total_security_money"),
    //             DB::raw("SUM(remaining_security_money) AS total_remaining_security_money"),
    //             DB::raw("MAX(CASE WHEN rent_start_date IS NOT NULL THEN 1 ELSE 0 END) AS has_any_rent_start_date")
    //         )
    //         ->where('status', 'active')
    //         ->groupBy('rental_party_id');

    //     // Get unique rental_postings first to avoid duplicates
    //     $baseDetailsQuery = $applyFilters(
    //         DB::table('rental_postings as rp')
    //             ->select('rp.id')
    //             ->distinct()
    //     );

    //     // Detailed query with all calculated fields - Rent shows "--" for security money AND auto adjustment transactions
    //     $details = DB::table('rental_postings as rp')
    //         ->joinSub($baseDetailsQuery, 'unique_postings', function ($join) {
    //             $join->on('unique_postings.id', '=', 'rp.id');
    //         })
    //         ->leftJoin('rental_parties as rparty', 'rparty.id', '=', 'rp.head_id')
    //         // Join with individual rental houses to get specific data
    //         ->leftJoinSub($expectedRentPerHouseSubquery, 'rhpm', function ($join) {
    //             $join->on('rhpm.rental_party_id', '=', 'rp.head_id');
    //         })
    //         ->leftJoin('rental_houses as rh', 'rh.id', '=', 'rhpm.rental_house_id')
    //         ->leftJoinSub($partyAggSubquery, 'party_agg', function ($join) {
    //             $join->on('party_agg.head_id', '=', 'rp.head_id');
    //         })
    //         ->leftJoinSub($expectedRentAggSubquery, 'expected_rent', function ($join) {
    //             $join->on('expected_rent.rental_party_id', '=', 'rp.head_id');
    //         })
    //         ->select(
    //             'rp.id',
    //             'rp.entry_type',
    //             'rparty.party_name',
    //             'rp.amount_bdt',
    //             'rp.posting_date',
    //             'rp.note',
    //             'rp.status',
    //             'rhpm.rental_house_id',

    //             // Use specific rental house data - Rent should be "--" for security money AND auto adjustment transactions
    //             DB::raw(
    //                 "
    //             CASE 
    //                 WHEN rp.entry_type IN ('security_money_amount', 'security_money_refund', 'auto_adjustment') 
    //                 THEN '--' 
    //                 ELSE COALESCE(CAST(rhpm.monthly_rent AS CHAR), '0') 
    //             END AS rent"
    //             ),
    //             DB::raw("COALESCE(rhpm.security_money, 0) AS security_money"),
    //             DB::raw("COALESCE(rhpm.remaining_security_money, 0) AS remaining_security_money"),
    //             DB::raw("CASE WHEN rp.entry_type = 'auto_adjustment' THEN rp.amount_bdt ELSE 0 END AS auto_adjust_amount"),
    //             DB::raw("COALESCE(party_agg.total_auto_adjustment, 0) AS already_adjusted"),
    //             DB::raw("COALESCE(party_agg.total_rent_received, 0) AS total_received"),
    //             DB::raw("CASE WHEN rp.entry_type = 'security_money_refund' THEN rp.amount_bdt ELSE 0 END AS refund_amount"),

    //             // Total Receivable - Only calculate if THIS rental house has rent_start_date
    //             DB::raw("
    //             CASE 
    //                 WHEN rhpm.has_rent_start_date = 1 
    //                 THEN GREATEST(COALESCE(rhpm.expected_rent_for_house, 0) - COALESCE(party_agg.total_rent_received, 0), 0)
    //                 ELSE 0 
    //             END AS total_receivable
    //         "),

    //             // Total Due Amount - Only calculate if THIS rental house has rent_start_date
    //             DB::raw("
    //             CASE 
    //                 WHEN rhpm.has_rent_start_date = 1 
    //                 THEN GREATEST(COALESCE(rhpm.expected_rent_for_house, 0) - COALESCE(party_agg.total_rent_received, 0), 0)
    //                 ELSE 0 
    //             END AS total_due_amount
    //         "),

    //             // Total Due Month - Only calculate if THIS rental house has rent_start_date
    //             DB::raw("
    //             CASE 
    //                 WHEN rhpm.has_rent_start_date = 1 
    //                 AND COALESCE(rhpm.monthly_rent, 0) > 0 
    //                 THEN CEILING(
    //                     GREATEST(COALESCE(rhpm.expected_rent_for_house, 0) - COALESCE(party_agg.total_rent_received, 0), 0)
    //                     / COALESCE(rhpm.monthly_rent, 0)
    //                 )
    //                 ELSE 0 
    //             END AS total_due_month
    //         "),

    //             // Partial Due Amount - Only calculate if THIS rental house has rent_start_date
    //             DB::raw("
    //             CASE 
    //                 WHEN rhpm.has_rent_start_date = 1 
    //                 AND COALESCE(rhpm.monthly_rent, 0) > 0 
    //                 THEN MOD(
    //                     GREATEST(COALESCE(rhpm.expected_rent_for_house, 0) - COALESCE(party_agg.total_rent_received, 0), 0),
    //                     COALESCE(rhpm.monthly_rent, 0)
    //                 )
    //                 ELSE 0 
    //             END AS partial_due_amount
    //         ")
    //         )
    //         ->orderBy('rp.posting_date', 'DESC')
    //         ->orderBy('rp.id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get();

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }


    public function getRentalLedgerData(Request $request)
    {
        $filters = $request->query('filter', []);

        // Retrieve filter values
        $houseId = $filters['house_id'] ?? '';
        $headId = $filters['head_id'] ?? '';
        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $transactionType = $filters['transaction_type'] ?? 'all';

        $page = $request->query('page', 1);
        $pageSize = $request->query('pageSize', 10);

        // Get filtered house mappings
        $houseMappingsQuery = RentalHousePartyMap::with(['rentalHouse', 'rentalParty'])
            ->where('status', 'active');

        if (!empty($headId)) {
            $houseMappingsQuery->where('rental_party_id', $headId);
        }

        if (!empty($houseId)) {
            $houseMappingsQuery->where('rental_house_id', $houseId);
        }

        $houseMappings = $houseMappingsQuery->get();

        // Get party IDs from house mappings
        $partyIdsFromMappings = $houseMappings->pluck('rental_party_id')->unique()->toArray();

        $details = collect();
        $total = 0;

        if (!empty($partyIdsFromMappings)) {
            // Get postings for filtered parties
            $postingsQuery = RentalPosting::with(['rentalParty'])
                ->where('status', 'approved')
                ->whereIn('head_id', $partyIdsFromMappings);

            if ($transactionType !== 'all') {
                $postingsQuery->where('entry_type', $transactionType);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $postingsQuery->whereBetween('posting_date', [$startDate, $endDate]);
            }

            $postings = $postingsQuery
                ->orderBy('posting_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();

            // Process actual postings
            $postingDetails = collect();
            foreach ($postings as $posting) {
                $postingDetails->push($this->formatPostingData($posting, $houseMappings));
            }

            // Create synthetic records for parties with no postings
            $syntheticDetails = collect();

            if (!empty($headId)) {
                // If filtering by specific party and it has no postings
                $hasPostings = $postings->where('head_id', $headId)->isNotEmpty();
                if (!$hasPostings) {
                    foreach ($houseMappings as $mapping) {
                        if ($mapping->rental_party_id == $headId) {
                            if (empty($houseId) || $mapping->rental_house_id == $houseId) {
                                $syntheticDetails->push($this->createSyntheticDetail($mapping));
                            }
                        }
                    }
                }
            } else {
                // For all parties, find those without postings
                $partiesWithPostings = $postings->pluck('head_id')->unique()->toArray();
                $partiesWithoutPostings = array_diff($partyIdsFromMappings, $partiesWithPostings);

                foreach ($partiesWithoutPostings as $partyId) {
                    foreach ($houseMappings as $mapping) {
                        if ($mapping->rental_party_id == $partyId) {
                            $syntheticDetails->push($this->createSyntheticDetail($mapping));
                        }
                    }
                }
            }

            // Combine both collections
            $details = $postingDetails->merge($syntheticDetails);
            $total = $details->count();

            // Apply pagination
            $details = $details->slice(($page - 1) * $pageSize, $pageSize)->values();
        }

        // Calculate summary
        $summaryPostingsQuery = RentalPosting::where('status', 'approved');

        if (!empty($partyIdsFromMappings)) {
            $summaryPostingsQuery->whereIn('head_id', $partyIdsFromMappings);
        }

        if ($transactionType !== 'all' && $transactionType !== 'No Transaction') {
            $summaryPostingsQuery->where('entry_type', $transactionType);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $summaryPostingsQuery->whereBetween('posting_date', [$startDate, $endDate]);
        }

        $summary = $this->calculateLedgerSummary($summaryPostingsQuery, $houseMappings);

        return response()->json([
            'summary' => $summary,
            'details' => $details,
            'total' => $total,
        ]);
    }


    private function createSyntheticDetail($houseMapping)
    {
        // Convert to object if it's an array
        if (is_array($houseMapping)) {
            $houseMapping = (object) $houseMapping;
        }

        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Calculate expected rent
        $expectedRentForHouse = 0;
        $hasRentStartDate = false;

        if (
            isset($houseMapping->rent_start_date) && $houseMapping->rent_start_date &&
            isset($houseMapping->monthly_rent) && $houseMapping->monthly_rent
        ) {
            $hasRentStartDate = true;
            $rentStartDate = \Carbon\Carbon::parse($houseMapping->rent_start_date);
            $rentStartYear = $rentStartDate->year;
            $rentStartMonth = $rentStartDate->month;

            $monthsDifference = (($currentYear - $rentStartYear) * 12) + ($currentMonth - $rentStartMonth) + 1;

            if ($monthsDifference > 0) {
                $expectedRentForHouse = $monthsDifference * $houseMapping->monthly_rent;
            }
        }

        // Calculate due month and partial due amount
        $totalDueMonth = 0;
        $partialDueAmount = 0;

        if ($hasRentStartDate && isset($houseMapping->monthly_rent) && $houseMapping->monthly_rent > 0) {
            $totalDueMonth = ceil($expectedRentForHouse / $houseMapping->monthly_rent);
            $partialDueAmount = fmod($expectedRentForHouse, $houseMapping->monthly_rent);
        }

        // Get party name and house name safely
        $partyName = '--';
        $houseName = '--';

        if (isset($houseMapping->rentalParty) && is_object($houseMapping->rentalParty)) {
            $partyName = $houseMapping->rentalParty->party_name ?? '--';
        } elseif (isset($houseMapping->party_name)) {
            $partyName = $houseMapping->party_name;
        }

        if (isset($houseMapping->rentalHouse) && is_object($houseMapping->rentalHouse)) {
            $houseName = $houseMapping->rentalHouse->house_name ?? '--';
        } elseif (isset($houseMapping->house_name)) {
            $houseName = $houseMapping->house_name;
        }

        return [
            'id' => null,
            'entry_type' => 'No Transaction',
            'party_name' => $partyName,
            'house_name' => $houseName,
            'rent' => $houseMapping->monthly_rent ?? 0,
            'security_money' => $houseMapping->security_money ?? 0,
            'remaining_security_money' => $houseMapping->remaining_security_money ?? 0,
            'auto_adjust_amount' => 0,
            'already_adjusted' => 0,
            'total_received' => 0,
            'refund_amount' => 0,
            'total_receivable' => $expectedRentForHouse,
            'total_due_amount' => $expectedRentForHouse,
            'total_due_month' => $totalDueMonth,
            'partial_due_amount' => $partialDueAmount,
            'posting_date' => '--',
            'amount_bdt' => 0,
            'note' => 'No transactions yet',
            'is_synthetic' => true,
        ];
    }





    private function calculateLedgerSummary($postingsQuery, $houseMappings)
    {
        // Instead of cloning and getting all postings, calculate aggregates directly from the query
        $aggregates = $postingsQuery->selectRaw("
        COALESCE(SUM(CASE WHEN entry_type = 'rent_received' THEN amount_bdt ELSE 0 END), 0) as total_rent_received,
        COALESCE(SUM(CASE WHEN entry_type = 'auto_adjustment' THEN amount_bdt ELSE 0 END), 0) as total_auto_adjustment,
        COALESCE(SUM(CASE WHEN entry_type = 'security_money_amount' THEN amount_bdt ELSE 0 END), 0) as total_security_money,
        COALESCE(SUM(CASE WHEN entry_type = 'security_money_refund' THEN amount_bdt ELSE 0 END), 0) as total_security_refund
        ")->first();

        // Calculate from house mappings
        $totalMonthlyRent = $houseMappings->sum('monthly_rent');
        $totalSecurityFromMappings = $houseMappings->sum('security_money');
        $totalRemainingSecurity = $houseMappings->sum('remaining_security_money');

        // Get current date for month calculation
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Calculate total expected rent based on rent_start_date
        $totalExpectedRent = 0;
        $hasAnyRentStartDate = false;

        foreach ($houseMappings as $mapping) {
            if ($mapping->rent_start_date && $mapping->monthly_rent) {
                $hasAnyRentStartDate = true;

                // Parse rent_start_date
                $rentStartDate = \Carbon\Carbon::parse($mapping->rent_start_date);
                $rentStartYear = $rentStartDate->year;
                $rentStartMonth = $rentStartDate->month;

                // Calculate months difference including current month
                $monthsDifference = (($currentYear - $rentStartYear) * 12) + ($currentMonth - $rentStartMonth) + 1;

                // Only count positive months
                if ($monthsDifference > 0) {
                    $totalExpectedRent += $monthsDifference * $mapping->monthly_rent;
                }
            }
        }

        // Calculate receivable based on expected rent
        $totalReceivable = $hasAnyRentStartDate ? max(0, $totalExpectedRent - $aggregates->total_rent_received) : 0;

        // Calculate due month and partial due amount
        $totalDueMonth = 0;
        $partialDueAmount = 0;

        if ($hasAnyRentStartDate && $totalMonthlyRent > 0) {
            $totalDueMonth = ceil($totalReceivable / $totalMonthlyRent);
            $partialDueAmount = fmod($totalReceivable, $totalMonthlyRent);
        }

        return [
            'total_monthly_rent' => $totalMonthlyRent,
            'total_rent_received' => $aggregates->total_rent_received,
            'total_security_money' => $totalSecurityFromMappings,
            'total_auto_adjustment' => $aggregates->total_auto_adjustment,
            'total_remaining_security_money' => $totalRemainingSecurity,
            'total_security_refund' => $aggregates->total_security_refund,
            'total_receivable' => $totalReceivable,
            'total_due_amount' => $totalReceivable,
            'total_due_month' => $totalDueMonth,
            'partial_due_amount' => $partialDueAmount,
            'already_adjusted' => $aggregates->total_auto_adjustment,
        ];
    }

    private function formatPostingData($posting, $houseMappings)
    {
        $houseMapping = $houseMappings->where('rental_party_id', $posting->head_id)->first();

        $monthlyRent = $houseMapping?->monthly_rent ?? 0;
        $securityMoney = $houseMapping?->security_money ?? 0;
        $remainingSecurity = $houseMapping?->remaining_security_money ?? 0;
        $houseName = $houseMapping?->rentalHouse?->house_name ?? '--';

        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        $expectedRentForHouse = 0;
        $totalReceivable = 0;
        $totalDueMonth = 0;
        $partialDueAmount = 0;
        $partyRentReceived = 0;
        $hasRentStartDate = false;

        if ($houseMapping && $houseMapping->rent_start_date && $monthlyRent > 0) {
            $hasRentStartDate = true;

            $rentStartDate = \Carbon\Carbon::parse($houseMapping->rent_start_date);
            $rentStartYear = $rentStartDate->year;
            $rentStartMonth = $rentStartDate->month;

            // ✅ Total months since rent start (inclusive)
            $monthsDifference = (($currentYear - $rentStartYear) * 12) + ($currentMonth - $rentStartMonth) + 1;
            $monthsDifference = max(0, $monthsDifference); // Ensure non-negative

            // ✅ Total rent_received postings count by distinct months
            $paidMonthsCount = RentalPosting::where('head_id', $posting->head_id)
                ->where('entry_type', 'rent_received')
                ->where('status', 'approved')
                ->selectRaw('COUNT(DISTINCT DATE_FORMAT(posting_date, "%Y-%m")) as paid_months')
                ->value('paid_months') ?? 0;

            // ✅ Due months = total months - received months
            $totalDueMonth = max(0, $monthsDifference - $paidMonthsCount);

            // ✅ Expected total rent till now
            $expectedRentForHouse = $monthsDifference * $monthlyRent;

            // ✅ Total rent received so far
            $partyRentReceived = RentalPosting::where('head_id', $posting->head_id)
                ->where('entry_type', 'rent_received')
                ->where('status', 'approved')
                ->sum('amount_bdt');

            // ✅ Remaining receivable
            $totalReceivable = max(0, $expectedRentForHouse - $partyRentReceived);

            // ✅ Partial due amount (if incomplete month paid)
            $partialDueAmount = $monthlyRent > 0 ? fmod($totalReceivable, $monthlyRent) : 0;
        }

        // 🔹 Show due only for rent_received entries
        $showDue = $posting->entry_type === 'rent_received';

        return [
            'id' => $posting->id,
            'entry_type' => $posting->entry_type,
            'party_name' => $posting->rentalParty->party_name ?? '--',
            'house_name' => $houseName,
            'rent' => in_array($posting->entry_type, ['security_money_amount', 'security_money_refund', 'auto_adjustment'])
                ? '--'
                : $monthlyRent,
            'security_money' => $securityMoney,
            'remaining_security_money' => $remainingSecurity,
            'auto_adjust_amount' => $posting->entry_type === 'auto_adjustment' ? $posting->amount_bdt : 0,
            'already_adjusted' => RentalPosting::where('head_id', $posting->head_id)
                ->where('entry_type', 'auto_adjustment')
                ->where('status', 'approved')
                ->sum('amount_bdt'),
            'total_received' => $partyRentReceived,
            'refund_amount' => $posting->entry_type === 'security_money_refund' ? $posting->amount_bdt : 0,
            'total_receivable' => $showDue ? $totalReceivable : '--',
            'total_due_amount' => $showDue ? $totalReceivable : '--',
            'total_due_month' => $showDue ? $totalDueMonth : '--',
            'partial_due_amount' => $showDue ? $partialDueAmount : '--',
            'posting_date' => $posting->posting_date,
            'amount_bdt' => $posting->amount_bdt,
            'note' => $posting->note,
        ];
    }




    // public function getRentalLedgerData(Request $request)
    // {
    //     $filters = $request->query('filter', []);

    //     // Retrieve filter values
    //     $houseId = $filters['house_id'] ?? '';
    //     $headId = $filters['head_id'] ?? '';
    //     $startDate = $filters['start_date'] ?? '';
    //     $endDate = $filters['end_date'] ?? '';
    //     $transactionType = $filters['transaction_type'] ?? 'all';
    //     $viewType = $filters['view_type'] ?? 'summary';

    //     $page = $request->query('page', 1);
    //     $pageSize = $request->query('pageSize', 10);

    //     // Base query for rental postings
    //     $postingsQuery = RentalPosting::with(['rentalParty'])
    //         ->where('status', 'approved');

    //     // Apply transaction type filter
    //     if ($transactionType !== 'all') {
    //         $postingsQuery->where('entry_type', $transactionType);
    //     }

    //     // Apply head_id filter
    //     if (!empty($headId)) {
    //         $postingsQuery->where('head_id', $headId);
    //     }

    //     // Apply date range filter
    //     if (!empty($startDate) && !empty($endDate)) {
    //         $postingsQuery->whereBetween('posting_date', [$startDate, $endDate]);
    //     }

    //     // Get ALL active house mappings, not just those with postings
    //     $houseMappingsQuery = RentalHousePartyMap::with(['rentalHouse', 'rentalParty'])
    //         ->where('status', 'active');

    //     // Apply head_id filter to house mappings if specified
    //     if (!empty($headId)) {
    //         $houseMappingsQuery->where('rental_party_id', $headId);
    //     }

    //     // Apply house filter if specified
    //     if (!empty($houseId)) {
    //         $houseMappingsQuery->where('rental_house_id', $houseId);
    //     }

    //     $houseMappings = $houseMappingsQuery->get();

    //     // Get party IDs from house mappings to ensure we include all active parties
    //     $partyIdsFromMappings = $houseMappings->pluck('rental_party_id')->unique()->toArray();

    //     // If we have specific party IDs from mappings, filter the postings by them
    //     if (!empty($partyIdsFromMappings) && empty($headId)) {
    //         $postingsQuery->whereIn('head_id', $partyIdsFromMappings);
    //     }

    //     // Calculate summary data
    //     $summary = $this->calculateLedgerSummary($postingsQuery, $houseMappings);

    //     // Get paginated details
    //     $total = $postingsQuery->count();
    //     $details = $postingsQuery
    //         ->orderBy('posting_date', 'DESC')
    //         ->orderBy('id', 'DESC')
    //         ->offset(($page - 1) * $pageSize)
    //         ->limit($pageSize)
    //         ->get()
    //         ->map(function ($posting) use ($houseMappings) {
    //             return $this->formatPostingData($posting, $houseMappings);
    //         });

    //     return response()->json([
    //         'summary' => $summary,
    //         'details' => $details,
    //         'total' => $total,
    //     ]);
    // }










    public function getRentalLedgerSummary(Request $request)
    {
        $filters = $request->query();

        // Get current date for month calculation
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Create a subquery that aggregates all rental house data per party
        $rentalHouseAggregates = DB::table('rental_house_party_maps as rhpm')
            ->select(
                'rhpm.rental_party_id',
                DB::raw("SUM(rhpm.monthly_rent) AS total_rent"),
                DB::raw("SUM(rhpm.security_money) AS security_money"),
                DB::raw("SUM(rhpm.remaining_security_money) AS remaining_security_money"),
                DB::raw("SUM(rhpm.refund_security_money) AS refund_security_money"),
                DB::raw("SUM(rhpm.auto_adjustment) AS auto_adjustment"),
                DB::raw("
                SUM(
                    CASE 
                        WHEN rhpm.rent_start_date IS NOT NULL AND rhpm.monthly_rent IS NOT NULL AND rhpm.monthly_rent > 0
                        THEN 
                            -- Handle YYYY-MM format by converting to first day of month
                            (
                                (YEAR('$currentDate') - YEAR(STR_TO_DATE(CONCAT(rhpm.rent_start_date, '-01'), '%Y-%m-%d'))) * 12 
                                + (MONTH('$currentDate') - MONTH(STR_TO_DATE(CONCAT(rhpm.rent_start_date, '-01'), '%Y-%m-%d')))
                                + 1  -- Include the start month
                            ) * rhpm.monthly_rent
                        ELSE 0 
                    END
                ) AS total_expected_rent
            ")
            )
            ->where('rhpm.status', 'active')
            ->groupBy('rhpm.rental_party_id');

        // Apply filters to rental house aggregates
        if (isset($filters['filter']['head_id']) && $filters['filter']['head_id'] !== '') {
            $rentalHouseAggregates->where('rhpm.rental_party_id', $filters['filter']['head_id']);
        }

        if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
            $rentalHouseAggregates->where(function ($query) use ($filters) {
                $query->where('rhpm.rent_start_date', '<=', $filters['filter']['end_date'])
                    ->orWhereNull('rhpm.rent_start_date');
            });
        }

        // Subquery for party aggregates from rental_postings
        $partyPostingAggregates = DB::table('rental_postings')
            ->select(
                'head_id',
                DB::raw("SUM(CASE WHEN entry_type = 'rent_received' THEN amount_bdt ELSE 0 END) AS total_rent_received"),
                DB::raw("SUM(CASE WHEN entry_type = 'auto_adjustment' THEN amount_bdt ELSE 0 END) AS total_auto_adjustment"),
                DB::raw("SUM(CASE WHEN entry_type = 'security_money_refund' THEN amount_bdt ELSE 0 END) AS total_security_refund"),
                DB::raw("SUM(CASE WHEN entry_type = 'security_money_amount' THEN amount_bdt ELSE 0 END) AS total_security_money_received")
            )
            ->where('status', 'approved');

        // Apply date filter to party aggregates if provided
        if (isset($filters['filter']['start_date']) && isset($filters['filter']['end_date'])) {
            $partyPostingAggregates->whereBetween('posting_date', [
                $filters['filter']['start_date'],
                $filters['filter']['end_date']
            ]);
        }

        $partyPostingAggregates->groupBy('head_id');

        // Debug: Check what's happening with the rental house aggregates
        $debugRentalData = DB::table('rental_house_party_maps')
            ->where('rental_party_id', 12)
            ->where('status', 'active')
            ->get();

        // Main query joining both aggregates
        $baseQuery = DB::table('rental_parties as rp')
            ->joinSub($rentalHouseAggregates, 'house_agg', function ($join) {
                $join->on('house_agg.rental_party_id', '=', 'rp.id');
            })
            ->leftJoinSub($partyPostingAggregates, 'posting_agg', function ($join) {
                $join->on('posting_agg.head_id', '=', 'rp.id');
            });

        // Total count for pagination
        $total = $baseQuery->count();

        // Pagination
        $page = $filters['page'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;

        $summaryData = $baseQuery
            ->select(
                'rp.id as party_id',
                'rp.party_name',
                'house_agg.total_rent',
                'house_agg.security_money',
                'house_agg.remaining_security_money',
                'house_agg.refund_security_money',
                'house_agg.auto_adjustment',
                'house_agg.total_expected_rent',
                DB::raw("COALESCE(posting_agg.total_auto_adjustment, 0) AS auto_adjust_amount"),
                DB::raw("COALESCE(posting_agg.total_auto_adjustment, 0) AS already_adjusted"),
                DB::raw("COALESCE(posting_agg.total_rent_received, 0) AS total_received"),
                DB::raw("COALESCE(posting_agg.total_security_refund, 0) AS refund_amount"),
                // Total receivable calculation with COALESCE to handle NULL
                DB::raw("COALESCE(house_agg.total_expected_rent, 0) - COALESCE(posting_agg.total_rent_received, 0) AS total_receivable")
            )
            ->orderBy('rp.party_name', 'ASC')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get();

        // Calculate overall summary
        $overallSummary = DB::table('rental_parties as rp')
            ->joinSub($rentalHouseAggregates, 'house_agg', function ($join) {
                $join->on('house_agg.rental_party_id', '=', 'rp.id');
            })
            ->leftJoinSub($partyPostingAggregates, 'posting_agg', function ($join) {
                $join->on('posting_agg.head_id', '=', 'rp.id');
            })
            ->select(
                DB::raw("SUM(house_agg.total_rent) AS total_monthly_rent"),
                DB::raw("SUM(COALESCE(posting_agg.total_rent_received, 0)) AS total_rent_received"),
                DB::raw("SUM(house_agg.security_money) AS total_security_money"),
                DB::raw("SUM(COALESCE(posting_agg.total_auto_adjustment, 0)) AS total_auto_adjustment"),
                DB::raw("SUM(house_agg.remaining_security_money) AS total_remaining_security_money"),
                DB::raw("SUM(COALESCE(posting_agg.total_security_refund, 0)) AS total_security_refund"),
                DB::raw("SUM(COALESCE(house_agg.total_expected_rent, 0) - COALESCE(posting_agg.total_rent_received, 0)) AS total_receivable")
            )
            ->first();

        return response()->json([
            'data' => $summaryData,
            'summary' => $overallSummary,
            'total' => $total,
            'debug' => [ // Remove this in production
                'rental_data_for_party_12' => $debugRentalData
            ]
        ]);
    }


    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $status = $request->input('status');

        if (empty($status) && $status !== 'all') {
            $status = 'pending';
        }

        $query = DB::table('rental_postings as rp')
            ->join('rental_parties as rpy', 'rpy.id', '=', 'rp.head_id')
            ->join('rental_houses as rh', 'rh.id', '=', 'rp.house_id')
            // ->join('rental_house_party_maps as rhpm', 'rhpm.rental_house_id', '=', 'rh.id')
            ->join('rental_house_party_maps as rhpm', function ($join) {
                $join->on('rhpm.rental_house_id', '=', 'rh.id')
                    ->on('rhpm.rental_party_id', '=', 'rpy.id');
            })
            ->join('payment_channel_details as pcd', 'pcd.id', '=', 'rp.payment_channel_id')
            ->join('account_numbers as ac', 'ac.id', '=', 'rp.account_id')
            ->select(
                'rp.*',
                'rpy.party_name',
                'rpy.id as party_id',
                'rhpm.security_money',
                'rhpm.auto_adjustment',
                'rhpm.remaining_security_money',
                'rhpm.monthly_rent',
                'rhpm.refund_security_money',
                'rh.house_name',
                'pcd.method_name',
                'ac.ac_name',
                'ac.ac_no'
            );




        if ($status !== 'all') {
            $query->where('rp.status', $status);
        }
        $query->orderBy('rp.id', 'desc');
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

    //     $party = RentalParty::findOrFail($request->input('head_id'));
    //     $monthlyRent = $party->monthly_rent;
    //     $autoAdjustment = $party->auto_adjustment;
    //     $securityMoney = $party->security_money;

    //     $mapping = [
    //         'rent_received'   => 'received',
    //         'security_money_refund'   => 'payment',
    //         'auto_adjustment'   => 'payment',
    //     ];

    //     try {

    //         DB::beginTransaction();

    //         $finalAmount = $monthlyRent;

    //         if ($autoAdjustment > 0) {
    //             if ($autoAdjustment > $securityMoney) {
    //                 return response()->json([
    //                     'error' => 'Not enough security money to adjust.'
    //                 ], 400);
    //             }

    //             $finalAmount = $monthlyRent - $autoAdjustment;

    //             $party->remaining_security_money -= $autoAdjustment;
    //             $party->save();
    //         }

    //         $autoAdjustPosting = RentalPosting::create([
    //             'transaction_type'   => $mapping['auto_adjustment'],
    //             'head_id'            => $request->input('head_id'),
    //             'payment_channel_id' => $request->input('payment_channel_id'),
    //             'account_id'         => $request->input('account_id'),
    //             'receipt_number'     => $request->input('receipt_number'),
    //             'amount_bdt'         => $autoAdjustment,
    //             'other_cost_bdt'     => $request->input('other_cost_bdt'),
    //             'posting_date'       => $request->input('posting_date'),
    //             'note'               => $request->input('note'),
    //             'entry_type'         => 'auto_adjustment'
    //         ]);

    //         $cashReceivedPosting = RentalPosting::create([
    //             'transaction_type'   => $mapping[$request->input('transaction_type')],
    //             'head_id'            => $request->input('head_id'),
    //             'payment_channel_id' => $request->input('payment_channel_id'),
    //             'account_id'         => $request->input('account_id'),
    //             'receipt_number'     => $request->input('receipt_number'),
    //             'amount_bdt'         => $finalAmount,
    //             'other_cost_bdt'     => $request->input('other_cost_bdt'),
    //             'posting_date'       => $request->input('posting_date'),
    //             'note'               => $request->input('note'),
    //             'entry_type'         => $request->input('transaction_type')
    //         ]);

    //         DB::commit();
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Created successfully.',
    //             'data'    => $cashReceivedPosting,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Transaction failed. ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     $houseId = $request->input('house_id');

    //     // $party = RentalHousePartyMap::where('rental_house_id', $houseId)->firstOrFail();
    //     $party = RentalHousePartyMap::where('rental_party_id', $request->input('head_id'))->firstOrFail();



    //     $monthlyRent = $party->monthly_rent;
    //     $autoAdjustment = $party->auto_adjustment;
    //     $securityMoney = $party->security_money;

    //     $mapping = [
    //         'rent_received'          => 'received',
    //         'security_money_refund'  => 'payment',
    //         'auto_adjustment'        => 'payment',
    //         'other_amount'           => 'received',
    //         'security_money_amount'  => 'received',
    //     ];


    //     try {
    //         DB::beginTransaction();

    //         $transactionType = $request->input('transaction_type');
    //         $finalAmount = $monthlyRent;

    //         /**
    //          * Only apply auto-adjustment if transaction type is 'rent_received'
    //          */
    //         if ($transactionType === 'rent_received') {
    //             if ($autoAdjustment > 0 && $autoAdjustment <= $securityMoney) {

    //                 $finalAmount = $monthlyRent - $autoAdjustment;

    //                 $party->remaining_security_money -= $autoAdjustment;
    //                 $party->save();

    //                 // Create auto adjustment posting
    //                 RentalPosting::create([
    //                     'transaction_type'   => $mapping['auto_adjustment'],
    //                     'head_id'            => $request->input('head_id'),
    //                     'house_id'            => $request->input('house_id'),
    //                     'payment_channel_id' => $request->input('payment_channel_id'),
    //                     'account_id'         => $request->input('account_id'),
    //                     'receipt_number'     => $request->input('receipt_number'),
    //                     'amount_bdt'         => $autoAdjustment,
    //                     'posting_date'       => $request->input('posting_date'),
    //                     'note'               => $request->input('note'),
    //                     'entry_type'         => 'auto_adjustment'
    //                 ]);
    //             } else {
    //                 $finalAmount = $monthlyRent;
    //             }
    //         } else {
    //             // If not rent_received, we don’t apply adjustment
    //             $finalAmount = $request->input('amount_bdt');
    //             $party->update([
    //                 'remaining_security_money' => $party->remaining_security_money - $finalAmount,
    //                 'refund_security_money'    => $party->security_money,
    //             ]);
    //         }


    //         // Always create main posting
    //         $cashReceivedPosting = RentalPosting::create([
    //             'transaction_type'   => $mapping[$transactionType],
    //             'head_id'            => $request->input('head_id'),
    //             'house_id'            => $request->input('house_id'),
    //             'payment_channel_id' => $request->input('payment_channel_id'),
    //             'account_id'         => $request->input('account_id'),
    //             'receipt_number'     => $request->input('receipt_number'),
    //             'amount_bdt'         => $monthlyRent,
    //             'posting_date'       => $request->input('posting_date'),
    //             'note'               => $request->input('note'),
    //             'entry_type'         => $transactionType
    //         ]);


    //         if (!empty($request->input('other_cost_bdt')) && $request->input('other_cost_bdt') > 0) {
    //             RentalPosting::create([
    //                 'transaction_type'   => $mapping['other_amount'],
    //                 'head_id'            => $request->input('head_id'),
    //                 'house_id'            => $request->input('house_id'),
    //                 'payment_channel_id' => $request->input('payment_channel_id'),
    //                 'account_id'         => $request->input('account_id'),
    //                 'receipt_number'     => $request->input('receipt_number'),
    //                 'amount_bdt'         => $request->input('other_cost_bdt'),
    //                 'posting_date'       => $request->input('posting_date'),
    //                 'note'               => $request->input('note'),
    //                 'entry_type'         => 'other_amount'
    //             ]);
    //         }


    //         // if (!empty($securityMoney) && $securityMoney > 0) {

    //         //     $exist = RentalPosting::where('head_id', $request->input('head_id'))
    //         //         ->where('house_id', $request->input('house_id'))
    //         //         ->where('entry_type', 'security_money_amount')->exists();
    //         //     if (!$exist) {
    //         //         RentalPosting::create([
    //         //             'transaction_type'   => $mapping['security_money_amount'],
    //         //             'head_id'            => $request->input('head_id'),
    //         //             'house_id'           => $request->input('house_id'),
    //         //             'payment_channel_id' => $request->input('payment_channel_id'),
    //         //             'account_id'         => $request->input('account_id'),
    //         //             'receipt_number'     => $request->input('receipt_number'),
    //         //             'amount_bdt'         => $securityMoney,
    //         //             'posting_date'       => $request->input('posting_date'),
    //         //             'note'               => $request->input('note'),
    //         //             'entry_type'         => 'security_money_amount'
    //         //         ]);
    //         //     }
    //         // }

    //         DB::commit();

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Created successfully.',
    //             'data'    => $cashReceivedPosting,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Transaction failed. ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        $houseId = $request->input('house_id');

        // $party = RentalHousePartyMap::where('rental_house_id', $houseId)->firstOrFail();
        $party = RentalHousePartyMap::where('rental_party_id', $request->input('head_id'))->firstOrFail();

        $monthlyRent = $party->monthly_rent;
        $autoAdjustment = $party->auto_adjustment;
        $securityMoney = $party->security_money;

        $mapping = [
            'rent_received'          => 'received',
            'security_money_refund'  => 'payment',
            'auto_adjustment'        => 'payment',
            'other_amount'           => 'received',
            'security_money_amount'  => 'received',
        ];

        try {
            DB::beginTransaction();

            $transactionType = $request->input('transaction_type');
            $finalAmount = $monthlyRent;

            /**
             * Only apply auto-adjustment if transaction type is 'rent_received'
             */
            if ($transactionType === 'rent_received') {
                if ($autoAdjustment > 0 && $autoAdjustment <= $securityMoney) {

                    $finalAmount = $monthlyRent - $autoAdjustment;

                    $party->remaining_security_money -= $autoAdjustment;
                    $party->save();

                    // Create auto adjustment posting
                    RentalPosting::create([
                        'transaction_type'   => $mapping['auto_adjustment'],
                        'head_id'            => $request->input('head_id'),
                        'house_id'            => $request->input('house_id'),
                        'payment_channel_id' => $request->input('payment_channel_id'),
                        'account_id'         => $request->input('account_id'),
                        'receipt_number'     => $request->input('receipt_number'),
                        'amount_bdt'         => $autoAdjustment,
                        'posting_date'       => $request->input('posting_date'),
                        'note'               => $request->input('note'),
                        'entry_type'         => 'auto_adjustment'
                    ]);
                } else {
                    $finalAmount = $monthlyRent;
                }
            } else {
                // If not rent_received, we don't apply adjustment
                $finalAmount = $request->input('amount_bdt');
                $party->update([
                    'remaining_security_money' => $party->remaining_security_money - $finalAmount,
                    'refund_security_money'    => $party->security_money,
                ]);
            }

            // Always create main posting
            $cashReceivedPosting = RentalPosting::create([
                'transaction_type'   => $mapping[$transactionType],
                'head_id'            => $request->input('head_id'),
                'house_id'            => $request->input('house_id'),
                'payment_channel_id' => $request->input('payment_channel_id'),
                'account_id'         => $request->input('account_id'),
                'receipt_number'     => $request->input('receipt_number'),
                'amount_bdt'         => $monthlyRent,
                'posting_date'       => $request->input('posting_date'),
                'note'               => $request->input('note'),
                'entry_type'         => $transactionType
            ]);

            if (!empty($request->input('other_cost_bdt')) && $request->input('other_cost_bdt') > 0) {
                RentalPosting::create([
                    'transaction_type'   => $mapping['other_amount'],
                    'head_id'            => $request->input('head_id'),
                    'house_id'            => $request->input('house_id'),
                    'payment_channel_id' => $request->input('payment_channel_id'),
                    'account_id'         => $request->input('account_id'),
                    'receipt_number'     => $request->input('receipt_number'),
                    'amount_bdt'         => $request->input('other_cost_bdt'),
                    'posting_date'       => $request->input('posting_date'),
                    'note'               => $request->input('note'),
                    'entry_type'         => 'other_amount'
                ]);
            }

            // Update Account Current Balance for main posting
            $accountId = $request->input('account_id');
            $mainAmount = (float) $monthlyRent;
            $mainTransactionType = $mapping[$transactionType];

            $currentBalance = AccountCurrentBalance::where('account_id', $accountId)->first();

            if ($currentBalance) {
                // Update balance for main posting
                if ($mainTransactionType === 'received') {
                    $currentBalance->balance += $mainAmount;
                } elseif ($mainTransactionType === 'payment') {
                    $currentBalance->balance -= $mainAmount;
                }

                // Update balance for auto adjustment if applicable
                if ($transactionType === 'rent_received' && $autoAdjustment > 0 && $autoAdjustment <= $securityMoney) {
                    $currentBalance->balance -= $autoAdjustment; // auto_adjustment is always 'payment'
                }

                // Update balance for other cost if applicable
                if (!empty($request->input('other_cost_bdt')) && $request->input('other_cost_bdt') > 0) {
                    $otherAmount = (float) $request->input('other_cost_bdt');
                    $currentBalance->balance += $otherAmount; // other_amount is always 'received'
                }

                $currentBalance->save();
            } else {
                throw new \Exception("No current balance record found for account ID: $accountId");
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Created successfully.',
                'data'    => $cashReceivedPosting,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

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
        $source = RentalPosting::find($id);

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

    // public function update(Request $request, string $id)
    // {

    //     $posting = RentalPosting::find($id);

    //     if (!$posting) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Posting not found.'
    //         ], 404);
    //     }

    //     if ($posting->status !== 'pending') {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Cannot update a posting that is not in pending status.',
    //         ], 403);
    //     }

    //     $mapping = [
    //         'rent_received' => 'received',
    //         'security_money_refund' => 'payment',
    //     ];

    //     $updateData = $request->only([
    //         'head_id',
    //         'payment_channel_id',
    //         'account_id',
    //         'receipt_number',
    //         'amount_bdt',
    //         'other_cost_bdt',
    //         'posting_date',
    //         'note',
    //         'remaining_security_money_bdt'
    //     ]);

    //     if ($request->has('transaction_type')) {
    //         $transactionTypeInput = $request->input('transaction_type');

    //         if (!isset($mapping[$transactionTypeInput])) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Invalid transaction type provided for update.',
    //             ], 422);
    //         }

    //         $updateData['transaction_type'] = $mapping[$transactionTypeInput];
    //         $updateData['entry_type'] = $transactionTypeInput;
    //     }

    //     try {
    //         $posting->update($updateData);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Updated successfully.',
    //             'data' => $posting->fresh(),
    //         ], 200);
    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred during the update.',
    //             'error_details' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function update(Request $request, string $id)
    {
        $posting = RentalPosting::find($id);

        if (!$posting) {
            return response()->json([
                'status' => false,
                'message' => 'Posting not found.'
            ], 404);
        }

        if ($posting->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot update a posting that is not in pending status.',
            ], 403);
        }

        $mapping = [
            'rent_received' => 'received',
            'security_money_refund' => 'payment',
        ];

        $updateData = $request->only([
            'head_id',
            'payment_channel_id',
            'account_id',
            'receipt_number',
            'amount_bdt',
            'other_cost_bdt',
            'posting_date',
            'note',
            'remaining_security_money_bdt'
        ]);

        if ($request->has('transaction_type')) {
            $transactionTypeInput = $request->input('transaction_type');

            if (!isset($mapping[$transactionTypeInput])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid transaction type provided for update.',
                ], 422);
            }

            $updateData['transaction_type'] = $mapping[$transactionTypeInput];
            $updateData['entry_type'] = $transactionTypeInput;
        }

        try {
            DB::beginTransaction();

            // Store old values for calculation
            $oldAmount = (float) $posting->amount_bdt;
            $oldTransactionType = $posting->transaction_type;
            $accountId = $posting->account_id;

            // Update the RentalPosting
            $posting->update($updateData);

            // Get new values
            $newAmount = (float) $request->input('amount_bdt', $posting->amount_bdt);
            $newTransactionType = $request->input('transaction_type') ? $mapping[$request->input('transaction_type')] : $posting->transaction_type;

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
                'data' => $posting->fresh(),
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
        $source = RentalPosting::find($id);

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
        $posting = RentalPosting::find($id);

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
