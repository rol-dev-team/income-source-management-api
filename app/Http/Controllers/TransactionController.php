<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    // public function getTransactions(Request $request)
    // {
    //     try {
    //         // Validate the request
    //         $request->validate([
    //             'transaction_type' => 'nullable|in:all,income,expense',
    //             'category_id' => 'nullable|integer|exists:source_categories,id',
    //             'subcategory_id' => 'nullable|integer|exists:source_subcategories,id',
    //             'start_date' => 'nullable|date',
    //             'end_date' => 'nullable|date|after_or_equal:start_date',
    //             'per_page' => 'nullable|integer|min:1|max:100',
    //             'page' => 'nullable|integer|min:1',
    //         ]);


    //         // Set default values
    //         $transactionType = $request->input('transaction_type', 'all');
    //         $categoryId = $request->input('category_id');
    //         $subcategoryId = $request->input('subcategory_id');
    //         $perPage = $request->input('per_page', 50);
    //         $page = $request->input('page', 1);

    //         // Set default date range (last 30 days) if not provided
    //         $startDate = $request->input('start_date', Carbon::now()->subDays(30)->startOfDay());
    //         $endDate = $request->input('end_date', Carbon::now()->endOfDay());

    //         // Convert string dates to Carbon instances if they were provided as strings
    //         if (is_string($startDate)) {
    //             $startDate = Carbon::parse($startDate)->startOfDay();
    //         }
    //         if (is_string($endDate)) {
    //             $endDate = Carbon::parse($endDate)->endOfDay();
    //         }

    //         // Build the base query conditions
    //         $baseQuery = function ($query) use ($startDate, $endDate, $transactionType, $categoryId, $subcategoryId) {
    //             $query->whereBetween('p.posting_date', [$startDate, $endDate]);

    //             // Apply transaction type filter if not 'all'
    //             if ($transactionType === 'income') {
    //                 $query->where('ty.id', 1);
    //             } elseif ($transactionType === 'expense') {
    //                 $query->where('ty.id', 2);
    //             }

    //             // Apply category filter if provided
    //             if ($categoryId) {
    //                 $query->where('sc.id', $categoryId);
    //             }

    //             if ($subcategoryId) {
    //                 $query->where('ssc.id', $subcategoryId);
    //             }
    //         };

    //         // 1. Main transactions query
    //         $transactionsQuery = DB::table('postings as p')
    //             ->select(
    //                 's.source_name',
    //                 'ty.transaction_type',
    //                 'sc.cat_name',
    //                 'ssc.subcat_name',
    //                 'poc.contact_name',
    //                 'pcd.method_name',
    //                 'pc.channel_name',
    //                 'p.total_amount',
    //                 'p.recived_ac',
    //                 'p.from_ac',
    //                 'p.exchange_rate',
    //                 'p.posting_date',
    //                 'p.note'
    //             )
    //             ->join('sources as s', 'p.source_id', '=', 's.id')
    //             ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
    //             ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
    //             ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
    //             ->leftJoin('point_of_contacts as poc', 'p.point_of_contact_id', '=', 'poc.id')
    //             ->leftJoin('payment_channel_details as pcd', 'p.channel_detail_id', '=', 'pcd.id')
    //             ->leftJoin('payment_channels as pc', 'pcd.channel_id', '=', 'pc.id')
    //             ->where($baseQuery)
    //             ->orderBy('p.posting_date', 'desc');

    //         // Get paginated transactions
    //         $transactions = $transactionsQuery->paginate($perPage, ['*'], 'page', $page);

    //         // 2. Summary query (using same conditions)
    //         $summary = DB::table('postings as p')
    //             ->selectRaw('
    //                 COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) AS total_income,
    //                 COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS total_expense,
    //                 COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) -
    //                 COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS current_balance
    //             ')
    //             ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
    //             ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
    //             ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
    //             ->where($baseQuery)
    //             ->first();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Transactions retrieved successfully',
    //             'data' => [
    //                 'transactions' => $transactions->items(),
    //                 'summary' => $summary,
    //                 'pagination' => [
    //                     'current_page' => $transactions->currentPage(),
    //                     'per_page' => $transactions->perPage(),
    //                     'total' => $transactions->total(),
    //                     'last_page' => $transactions->lastPage(),
    //                 ],
    //                 'filters' => [
    //                     'transaction_type' => $transactionType,
    //                     'category_id' => $categoryId,
    //                     'subcategory_id' => $subcategoryId,
    //                     'start_date' => $startDate->format('Y-m-d H:i:s'),
    //                     'end_date' => $endDate->format('Y-m-d H:i:s'),
    //                 ]
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve transactions',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    // public function getTransactions(Request $request)
    // {
    //     try {
    //         // Validate the request
    //         $request->validate([
    //             'transaction_type' => 'nullable|in:all,income,expense',
    //             'category_id' => 'nullable|integer|exists:source_categories,id',
    //             'subcategory_id' => 'nullable|integer|exists:source_subcategories,id',
    //             'start_date' => 'nullable|date',
    //             'end_date' => 'nullable|date|after_or_equal:start_date',
    //         ]);

    //         // Set default values
    //         $transactionType = $request->input('transaction_type', 'all');
    //         $categoryId = $request->input('category_id');
    //         $subcategoryId = $request->input('subcategory_id');

    //         // Set default date range (last 30 days) if not provided
    //         $startDate = $request->input('start_date', Carbon::now()->subDays(30)->startOfDay());
    //         $endDate = $request->input('end_date', Carbon::now()->endOfDay());

    //         // Convert string dates to Carbon instances if they were provided as strings
    //         if (is_string($startDate)) {
    //             $startDate = Carbon::parse($startDate)->startOfDay();
    //         }
    //         if (is_string($endDate)) {
    //             $endDate = Carbon::parse($endDate)->endOfDay();
    //         }

    //         // Build the base query conditions
    //         $baseQuery = function ($query) use ($startDate, $endDate, $transactionType, $categoryId, $subcategoryId) {
    //             $query->whereBetween('p.posting_date', [$startDate, $endDate]);

    //             // Apply transaction type filter if not 'all'
    //             if ($transactionType === 'income') {
    //                 $query->where('ty.id', 1);
    //             } elseif ($transactionType === 'expense') {
    //                 $query->where('ty.id', 2);
    //             }

    //             // Apply category filter if provided
    //             if ($categoryId) {
    //                 $query->where('sc.id', $categoryId);
    //             }

    //             if ($subcategoryId) {
    //                 $query->where('ssc.id', $subcategoryId);
    //             }
    //         };

    //         // 1. Main transactions query - changed paginate() to get()
    //         $transactions = DB::table('postings as p')
    //             ->select(
    //                 's.source_name',
    //                 'ty.transaction_type',
    //                 'sc.cat_name',
    //                 'ssc.subcat_name',
    //                 'poc.contact_name',
    //                 'pcd.method_name',
    //                 'pc.channel_name',
    //                 'p.total_amount',
    //                 'p.recived_ac',
    //                 'p.from_ac',
    //                 'p.exchange_rate',
    //                 'p.posting_date',
    //                 'p.note'
    //             )
    //             ->join('sources as s', 'p.source_id', '=', 's.id')
    //             ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
    //             ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
    //             ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
    //             ->leftJoin('point_of_contacts as poc', 'p.point_of_contact_id', '=', 'poc.id')
    //             ->leftJoin('payment_channel_details as pcd', 'p.channel_detail_id', '=', 'pcd.id')
    //             ->leftJoin('payment_channels as pc', 'pcd.channel_id', '=', 'pc.id')
    //             ->where($baseQuery)
    //             ->orderBy('p.posting_date', 'desc')
    //             ->get(); // Changed from paginate() to get()

    //         // 2. Summary query (using same conditions)
    //         $summary = DB::table('postings as p')
    //             ->selectRaw('
    //             COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) AS total_income,
    //             COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS total_expense,
    //             COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) -
    //             COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS current_balance
    //         ')
    //             ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
    //             ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
    //             ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
    //             ->where($baseQuery)
    //             ->first();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Transactions retrieved successfully',
    //             'data' => [
    //                 'transactions' => $transactions, // Now returning all transactions directly
    //                 'summary' => $summary,
    //                 'filters' => [
    //                     'transaction_type' => $transactionType,
    //                     'category_id' => $categoryId,
    //                     'subcategory_id' => $subcategoryId,
    //                     'start_date' => $startDate->format('Y-m-d H:i:s'),
    //                     'end_date' => $endDate->format('Y-m-d H:i:s'),
    //                 ]
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve transactions',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getTransactions(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'transaction_type' => 'nullable|in:all,income,expense',
                'category_id' => 'nullable|integer|exists:source_categories,id',
                'subcategory_id' => 'nullable|integer|exists:source_subcategories,id',
                'point_of_contact' => 'nullable|integer|exists:point_of_contacts,id',
                'account_number' => 'nullable|integer|exists:account_numbers,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            // Set default values
            $transactionType = $request->input('transaction_type', 'all');
            $categoryId = $request->input('category_id');
            $subcategoryId = $request->input('subcategory_id');
            $pointOfContactId = $request->input('point_of_contact');
            $accountNoId = $request->input('account_number');

            // return $request->all();

            // Set default date range (last 30 days) if not provided
            $startDate = $request->input('start_date', Carbon::now()->subDays(30)->startOfDay());
            $endDate = $request->input('end_date', Carbon::now()->endOfDay());

            // Convert string dates to Carbon instances if they were provided as strings
            if (is_string($startDate)) {
                $startDate = Carbon::parse($startDate)->startOfDay();
            }
            if (is_string($endDate)) {
                $endDate = Carbon::parse($endDate)->endOfDay();
            }

            // Build the base query conditions
            $baseQuery = function ($query) use (
                $startDate, 
                $endDate, 
                $transactionType, 
                $categoryId, 
                $subcategoryId,
                $pointOfContactId,
                $accountNoId,
            ) {
                $query->whereBetween('p.posting_date', [$startDate, $endDate]);

                // Apply transaction type filter if not 'all'
                if ($transactionType === 'income') {
                    $query->where('ty.id', 1);
                } elseif ($transactionType === 'expense') {
                    $query->where('ty.id', 2);
                }

                // Apply category filter if provided
                if ($categoryId) {
                    $query->where('sc.id', $categoryId);
                }

                if ($subcategoryId) {
                    $query->where('ssc.id', $subcategoryId);
                }

                if ($pointOfContactId) {
                    $query->where('p.point_of_contact_id', $pointOfContactId);
                }

                if ($accountNoId) {
                    $query->where('p.recived_ac', $accountNoId);
                }
            };

            // 1. Main transactions query
            $transactions = DB::table('postings as p')
                ->select(
                    's.source_name',
                    'ty.transaction_type',
                    'sc.cat_name',
                    'ssc.subcat_name',
                    'poc.contact_name',
                    'p.point_of_contact_id',
                    'pcd.method_name',
                    'pc.channel_name',
                    'p.total_amount',
                    'p.recived_ac',
                    'p.from_ac',
                    'p.exchange_rate',
                    'p.posting_date',
                    'p.note'
                )
                ->join('sources as s', 'p.source_id', '=', 's.id')
                ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
                ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
                ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
                ->join('account_numbers as an', 'p.recived_ac', '=', 'an.id')
                ->leftJoin('point_of_contacts as poc', 'p.point_of_contact_id', '=', 'poc.id')
                ->leftJoin('payment_channel_details as pcd', 'p.channel_detail_id', '=', 'pcd.id')
                ->leftJoin('payment_channels as pc', 'pcd.channel_id', '=', 'pc.id')
                ->where($baseQuery)
                ->orderBy('p.posting_date', 'desc')
                ->get();

            // 2. Summary query (using same conditions)
            $summary = DB::table('postings as p')
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) AS total_income,
                    COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS total_expense,
                    COALESCE(SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END), 0) AS current_balance
                ')
                ->join('transaction_types as ty', 'p.transaction_type_id', '=', 'ty.id')
                ->join('source_categories as sc', 'p.source_cat_id', '=', 'sc.id')
                ->join('source_subcategories as ssc', 'p.source_subcat_id', '=', 'ssc.id')
                ->where($baseQuery)
                ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Transactions retrieved successfully',
                'data' => [
                    'transactions' => $transactions,
                    'summary' => $summary,
                    'filters' => [
                        'transaction_type' => $transactionType,
                        'category_id' => $categoryId,
                        'subcategory_id' => $subcategoryId,
                        'point_of_contact' => $pointOfContactId,
                        'start_date' => $startDate->format('Y-m-d H:i:s'),
                        'end_date' => $endDate->format('Y-m-d H:i:s'),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionsRaw(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_type' => 'nullable|in:all,income,expense',
            'category_id' => 'nullable|integer|exists:source_categories,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        // Set default values
        $transactionType = $request->input('transaction_type', 'all');
        $categoryId = $request->input('category_id');
        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);

        // Set default date range (last 30 days) if not provided
        $startDate = $request->input('start_date', now()->subDays(30)->startOfDay()->toDateTimeString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());

        // Build base conditions and parameters
        $conditions = ["p.posting_date BETWEEN ? AND ?"];
        $params = [$startDate, $endDate];

        // Add transaction type condition
        if ($transactionType === 'income') {
            $conditions[] = "ty.id = ?";
            $params[] = 1;
        } elseif ($transactionType === 'expense') {
            $conditions[] = "ty.id = ?";
            $params[] = 2;
        }

        // Add category condition
        if ($categoryId) {
            $conditions[] = "sc.id = ?";
            $params[] = $categoryId;
        }

        // Combine all conditions
        $whereClause = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        // 1. Main transactions query
        $sql = "
        SELECT
            s.source_name,
            ty.transaction_type,
            sc.cat_name,
            poc.contact_name,
            pcd.method_name,
            pc.channel_name,
            p.total_amount,
            p.recived_ac,
            p.from_ac,
            p.exchange_rate,
            p.posting_date,
            p.note
        FROM postings p
        INNER JOIN sources s ON p.source_id = s.id
        INNER JOIN transaction_types ty ON p.transaction_type_id = ty.id
        INNER JOIN source_categories sc ON p.source_cat_id = sc.id
        INNER JOIN source_subcategories ssc ON p.source_subcat_id = ssc.id
        LEFT JOIN point_of_contacts poc ON p.point_of_contact_id = poc.id
        LEFT JOIN payment_channel_details pcd ON p.channel_detail_id = pcd.id
        LEFT JOIN payment_channels pc ON pcd.channel_id = pc.id
        $whereClause
        ORDER BY p.posting_date DESC
        ";

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as count_query";
        $total = DB::select($countSql, $params)[0]->total;

        // Add pagination
        $offset = ($page - 1) * $perPage;
        $paginatedSql = $sql . " LIMIT ? OFFSET ?";
        $paginatedParams = array_merge($params, [$perPage, $offset]);

        // Execute main query
        $transactions = DB::select($paginatedSql, $paginatedParams);

        // 2. Summary query (using same conditions)
        $summarySql = "
        SELECT
            SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END) AS total_income,
            SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END) AS total_expense,
            SUM(CASE WHEN ty.id = 1 THEN p.total_amount ELSE 0 END) -
            SUM(CASE WHEN ty.id = 2 THEN p.total_amount ELSE 0 END) AS current_balance
        FROM postings p
        INNER JOIN transaction_types ty ON p.transaction_type_id = ty.id
        INNER JOIN source_categories sc ON p.source_cat_id = sc.id
        $whereClause
        ";
        $summary = DB::select($summarySql, $params)[0];

        // Calculate last page
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'summary' => $summary,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => (int)$total,
                'last_page' => (int)$lastPage,
            ],
            'filters' => [
                'transaction_type' => $transactionType,
                'category_id' => $categoryId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }
}
