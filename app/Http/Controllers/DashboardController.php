<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getNetIncome(Request $request)
    {

        $source = DB::select("SELECT
        YEAR(posting_date) AS year,
        DATE_FORMAT(posting_date, '%b') AS month,
        ROUND(SUM(CASE WHEN transaction_type_id = 1 THEN total_amount ELSE 0 END)) AS total_income,
        ROUND(SUM(CASE WHEN transaction_type_id = 2 THEN total_amount ELSE 0 END)) AS total_expense,
        ROUND(
            SUM(CASE WHEN transaction_type_id = 1 THEN total_amount ELSE 0 END) -
            SUM(CASE WHEN transaction_type_id = 2 THEN total_amount ELSE 0 END)
        ) AS net_income
        FROM postings
        WHERE posting_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        AND status = 'approved'
        GROUP BY YEAR(posting_date), MONTH(posting_date), DATE_FORMAT(posting_date, '%b')
        ORDER BY YEAR(posting_date), MONTH(posting_date)
        ");


        return response()->json([
            'status' => true,
            'data' =>$source,
        ], 200);
    }
    public function getBalanceSourceWise(Request $request)
    {

        $source = DB::select("SELECT
    s.source_name,
    ROUND(SUM(CASE
        WHEN p.transaction_type_id = 1 THEN p.total_amount
        WHEN p.transaction_type_id = 2 THEN -p.total_amount
        ELSE 0
    END)) AS balance
FROM postings p
JOIN sources s ON s.id = p.source_id
AND p.status = 'approved'
GROUP BY s.source_name

UNION ALL

SELECT
    'Total' AS source_name,
    ROUND(SUM(CASE
        WHEN p.transaction_type_id = 1 THEN p.total_amount
        WHEN p.transaction_type_id = 2 THEN -p.total_amount
        ELSE 0
    END)) AS balance
FROM postings p
JOIN sources s ON s.id = p.source_id
AND p.status = 'approved'
");




        return response()->json([
            'status' => true,
            'data' =>$source,
        ], 200);
    }
    public function getPaymentChannel(Request $request)
{
    $source = DB::select("
        SELECT
            pc.id AS channel_id,
            pc.channel_name,
            DATE_FORMAT(p.posting_date, '%Y-%m') AS month_name,
            ROUND(SUM(CASE WHEN p.transaction_type_id = 1 THEN p.total_amount ELSE 0 END)) AS total_income
        FROM postings p
        JOIN payment_channel_details pcd ON p.channel_detail_id = pcd.id
        JOIN payment_channels pc ON pcd.channel_id = pc.id
        WHERE p.posting_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        AND p.status = 'approved'
        GROUP BY pc.id, pc.channel_name, DATE_FORMAT(p.posting_date, '%Y-%m')
        ORDER BY DATE_FORMAT(p.posting_date, '%Y-%m') DESC, pc.channel_name
    ");

    return response()->json([
        'status' => true,
        'data' => $source,
    ], 200);
}

    public function getCurrency(Request $request)
    {

        $source = DB::select("WITH category_totals AS (
    SELECT s.source_name,ss.subcat_name,
        ROUND(SUM(
            IF(p.transaction_type_id = 2, p.foreign_currency,
               IF(p.transaction_type_id = 1, -p.foreign_currency, 0))
        )) AS stock
    FROM postings p
    INNER JOIN source_subcategories ss ON ss.id = p.source_subcat_id
    INNER JOIN sources s ON s.id = p.source_id
    WHERE s.id = 2
    AND p.status = 'approved'
    GROUP BY s.source_name, ss.subcat_name
),
grand_total AS (
    SELECT SUM(stock) AS total_stock
    FROM category_totals
)
SELECT ct.source_name,ct.subcat_name,ct.stock,gt.total_stock,
    ROUND((ct.stock / gt.total_stock) * 100, 2) AS percentage
FROM category_totals ct
CROSS JOIN grand_total gt
ORDER BY ct.stock DESC");


        return response()->json([
            'status' => true,
            'data' =>$source,
        ], 200);
    }
}
