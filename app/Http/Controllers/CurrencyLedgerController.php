<?php

namespace App\Http\Controllers;

use App\Models\CurrencyPosting;
use App\Models\Transfer;
use App\Models\IncomeExpensePosting;
use App\Models\BankDeposit;
use App\Models\InvestmentPosting;
use App\Models\LoanPosting;
use App\Models\RentalPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CurrencyLedgerController extends Controller
{



    // public function index(Request $request)
    // {
    //     $startDate = $request->start_date ?? Carbon::now()->subDays(30)->toDateString();
    //     $endDate   = $request->end_date ?? Carbon::now()->toDateString();

    //     // Always filter by approved status
    //     $baseQuery = CurrencyPosting::with(['currency', 'currencyParty', 'accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->where('business_type_id', 2)
    //         ->whereBetween('posting_date', [$startDate, $endDate]);

    //     if ($request->filled('currency_id')) {
    //         $baseQuery->where('currency_id', $request->currency_id);
    //     }

    //     if ($request->filled('currency_party_id')) {
    //         $baseQuery->where('currency_party_id', $request->currency_party_id);
    //     }

    //     if ($request->filled('transaction_type')) {
    //         $baseQuery->where('transaction_type', $request->transaction_type);
    //     }

    //     // Filter by ac_no if provided
    //     if ($request->filled('ac_no')) {
    //         $baseQuery->whereHas('accountNumber', function ($query) use ($request) {
    //             $query->where('ac_no', $request->ac_no);
    //         });
    //     }

    //     $rows = (clone $baseQuery)->get();

    //     // Summary for BDT
    //     $summaryData = (clone $baseQuery)
    //         ->selectRaw("
    //             COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN amount_bdt ELSE 0 END), 0) AS buy_total,
    //             COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN amount_bdt ELSE 0 END), 0) AS sell_total,
    //             COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //             COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //         ")
    //         ->first();

    //     $payable = ($summaryData->buy_total + $summaryData->received_total)
    //         - ($summaryData->sell_total + $summaryData->payment_total);

    //     // Currency-specific totals if currency_id is selected
    //     $currencyBuyTotal = 0;
    //     $currencySellTotal = 0;
    //     $remainCurrency = 0;

    //     if ($request->filled('currency_id')) {
    //         $currencySummary = (clone $baseQuery)
    //             ->selectRaw("
    //                 COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN currency_amount ELSE 0 END), 0) AS currency_buy_total,
    //                 COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN currency_amount ELSE 0 END), 0) AS currency_sell_total
    //             ")
    //             ->first();

    //         $currencyBuyTotal = $currencySummary->currency_buy_total;
    //         $currencySellTotal = $currencySummary->currency_sell_total;
    //         $remainCurrency = $currencyBuyTotal - $currencySellTotal;
    //     }

    //     return response()->json([
    //         'rows'       => $rows,
    //         'summary'    => [
    //             'buy'      => $summaryData->buy_total,
    //             'sell'     => $summaryData->sell_total,
    //             'payment'  => $summaryData->payment_total,
    //             'received' => $summaryData->received_total,
    //             'payable'    => $payable,
    //         ],
    //         // 'payable'    => $payable,
    //         'currency_summary' => [
    //             'currency_buy'    => $currencyBuyTotal,
    //             'currency_sell'   => $currencySellTotal,
    //             'remain_currency' => $remainCurrency,
    //         ],
    //         'date_range' => [
    //             'start' => $startDate,
    //             'end'   => $endDate
    //         ]
    //     ]);
    // }


    public function index(Request $request)
    {
        // Remove default date range - only use provided dates
        $baseQuery = CurrencyPosting::with(['currency', 'currencyParty', 'accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->where('business_type_id', 2);

        // Apply date filters only if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('posting_date', [$request->start_date, $request->end_date]);
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        } else {
            // If no dates provided, don't filter by date (get all data)
            $startDate = null;
            $endDate = null;
        }

        if ($request->filled('currency_id')) {
            $baseQuery->where('currency_id', $request->currency_id);
        }

        if ($request->filled('currency_party_id')) {
            $baseQuery->where('currency_party_id', $request->currency_party_id);
        }

        if ($request->filled('transaction_type')) {
            $baseQuery->where('transaction_type', $request->transaction_type);
        }

        // Filter by ac_no if provided
        if ($request->filled('ac_no')) {
            $baseQuery->whereHas('accountNumber', function ($query) use ($request) {
                $query->where('ac_no', $request->ac_no);
            });
        }

        $rows = (clone $baseQuery)->get();

        // Summary for BDT
        $summaryData = (clone $baseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN amount_bdt ELSE 0 END), 0) AS buy_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN amount_bdt ELSE 0 END), 0) AS sell_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")
            ->first();

        $payable = ($summaryData->buy_total + $summaryData->received_total)
            - ($summaryData->sell_total + $summaryData->payment_total);

        // Currency-specific totals if currency_id is selected
        $currencyBuyTotal = 0;
        $currencySellTotal = 0;
        $remainCurrency = 0;

        if ($request->filled('currency_id')) {
            $currencySummary = (clone $baseQuery)
                ->selectRaw("
                COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN currency_amount ELSE 0 END), 0) AS currency_buy_total,
                COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN currency_amount ELSE 0 END), 0) AS currency_sell_total
            ")
                ->first();

            $currencyBuyTotal = $currencySummary->currency_buy_total;
            $currencySellTotal = $currencySummary->currency_sell_total;
            $remainCurrency = $currencyBuyTotal - $currencySellTotal;
        }

        return response()->json([
            'rows'       => $rows,
            'summary'    => [
                'buy'      => $summaryData->buy_total,
                'sell'     => $summaryData->sell_total,
                'payment'  => $summaryData->payment_total,
                'received' => $summaryData->received_total,
                'payable'    => $payable,
            ],
            'currency_summary' => [
                'currency_buy'    => $currencyBuyTotal,
                'currency_sell'   => $currencySellTotal,
                'remain_currency' => $remainCurrency,
            ],
            'date_range' => [
                'start' => $startDate,
                'end'   => $endDate
            ]
        ]);
    }




    public function currencyLedgerSummary(Request $request)
    {
        $groupBy = $request->input('group_by'); // no default

        // Return empty if summary_by not selected
        if (!$groupBy) {
            return response()->json([
                'rows' => [],
                'summary' => [],
                'currency_summary' => null,
                'payable' => 0,
                'date_range' => [],
            ]);
        }

        // --- Base query ---
        $summaryQuery = CurrencyPosting::with(['currency', 'currencyParty'])
            ->where('status', 'approved')
            ->where('business_type_id', 2);

        // Filters
        if ($request->filled('currency_id')) {
            $summaryQuery->where('currency_id', $request->currency_id);
        }

        if ($request->filled('currency_party_id')) {
            $summaryQuery->where('currency_party_id', $request->currency_party_id);
        }

        if ($request->filled('transaction_type')) {
            $summaryQuery->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('ac_no')) {
            $summaryQuery->whereHas('accountNumber', function ($query) use ($request) {
                $query->where('ac_no', $request->ac_no);
            });
        }

        // --- GROUPING LOGIC (no change) ---
        if ($groupBy === 'currency') {
            $groupedRows = (clone $summaryQuery)
                ->whereNotNull('currency_id') // <-- add this line
                ->select([
                    'currency_id',
                    DB::raw("SUM(CASE WHEN transaction_type = 'buy' THEN currency_amount ELSE 0 END) as currency_amount_buy"),
                    DB::raw("SUM(CASE WHEN transaction_type = 'sell' THEN currency_amount ELSE 0 END) as currency_amount_sell")
                ])
                ->with(['currency'])
                ->groupBy('currency_id')
                ->get();

            $consolidatedRows = $groupedRows->map(fn($row) => [
                'currency_id'         => $row->currency_id,
                'currency'            => $row->currency ? $row->currency->currency : 'N/A',
                'currency_amount_buy' => $row->currency_amount_buy,
                'currency_amount_sell' => $row->currency_amount_sell,
                'balance'              => $row->currency_amount_buy - $row->currency_amount_sell,
            ]);
        } else {
            $groupedRows = (clone $summaryQuery)
                ->select([
                    'currency_party_id',
                    DB::raw('MAX(posting_date) as last_posting_date'),
                    // DB::raw("SUM(CASE WHEN transaction_type IN ('buy','payment') THEN amount_bdt ELSE 0 END) as payment_bdt"),
                    // DB::raw("SUM(CASE WHEN transaction_type IN ('sell','received') THEN amount_bdt ELSE 0 END) as received_bdt"),
                    DB::raw("SUM(CASE WHEN transaction_type IN ('payment') THEN amount_bdt ELSE 0 END) as payment_bdt"),
                    DB::raw("SUM(CASE WHEN transaction_type IN ('received') THEN amount_bdt ELSE 0 END) as received_bdt"),
                    DB::raw("SUM(CASE WHEN transaction_type = 'buy' THEN amount_bdt ELSE 0 END) as currency_buy_bdt"),
                    DB::raw("SUM(CASE WHEN transaction_type = 'sell' THEN amount_bdt ELSE 0 END) as currency_sell_bdt"),
                    DB::raw("SUM(CASE WHEN transaction_type = 'buy' THEN currency_amount ELSE 0 END) as currency_buy_amount"),
                    DB::raw("SUM(CASE WHEN transaction_type = 'sell' THEN currency_amount ELSE 0 END) as currency_sell_amount")
                ])
                ->with(['currencyParty'])
                ->groupBy('currency_party_id')
                ->get();

            $consolidatedRows = $groupedRows->map(fn($row) => [
                'party'            => $row->currencyParty ? $row->currencyParty->party_name : 'N/A',
                'last_posting_date' => $row->last_posting_date,
                'payment_bdt'      => $row->payment_bdt,
                'received_bdt'     => $row->received_bdt,
                'currency_buy'     => $row->currency_buy_amount,
                'currency_sell'    => $row->currency_sell_amount,
                'currency_buy_bdt'     => $row->currency_buy_bdt,
                'currency_sell_bdt'    => $row->currency_sell_bdt,
                'remain_currency'  => $row->currency_buy_amount - $row->currency_sell_amount,
                'balanceParty'          => ($row->currency_buy_bdt + $row->received_bdt) - ($row->currency_sell_bdt + $row->payment_bdt),
            ]);
        }

        // --- Global Totals (no change) ---
        $summaryData = (clone $summaryQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN amount_bdt ELSE 0 END), 0) AS buy_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN amount_bdt ELSE 0 END), 0) AS sell_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'buy' THEN currency_amount ELSE 0 END), 0) AS currency_buy_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'sell' THEN currency_amount ELSE 0 END), 0) AS currency_sell_total
        ")->first();

        $payable = ($summaryData->buy_total + $summaryData->received_total)
            - ($summaryData->sell_total + $summaryData->payment_total);

        $balance = ($summaryData->received_total + $summaryData->sell_total)
            - ($summaryData->payment_total + $summaryData->buy_total);

        return response()->json([
            'rows' => $consolidatedRows,
            'summary' => [
                'buy'      => $summaryData->buy_total,
                'sell'     => $summaryData->sell_total,
                'payment'  => $summaryData->payment_total,
                'received' => $summaryData->received_total,
                'payable'  => $payable,
                'balance'  => $balance,
            ],
            'currency_summary' => [
                'currency_buy'    => $summaryData->currency_buy_total,
                'currency_sell'   => $summaryData->currency_sell_total,
                'remain_currency' => $summaryData->currency_buy_total - $summaryData->currency_sell_total,
            ]
        ]);
    }



    // change from 30 days to all

    // public function bankLedgerDetails(Request $request)
    // {
    //     $loanEntryTypeMapping = [
    //         'loan_taken'   => 'Loan Taken',
    //         'loan_given'   => 'Loan Given',
    //         'loan_payment' => 'Loan Payment',
    //         'loan_received' => 'Loan Received',
    //     ];

    //     $investmentEntryTypeMapping = [
    //         'investment'        => 'Investment',
    //         'investment_return' => 'Investment Return',
    //         'investment_profit' => 'Investment Profit',
    //     ];

    //     // Debug: Log the incoming request parameters
    //     Log::debug('Bank Ledger Request:', $request->all());

    //     $startDate = $request->filled('start_date')
    //         ? Carbon::parse($request->start_date)->toDateString()
    //         : Carbon::now()->subDays(30000)->toDateString();

    //     $endDate = $request->filled('end_date')
    //         ? Carbon::parse($request->end_date)->toDateString()
    //         : Carbon::now()->toDateString();

    //     // Debug: Log the calculated date range
    //     Log::debug('Date Range:', ['start' => $startDate, 'end' => $endDate]);

    //     $baseQuery = CurrencyPosting::with(['currency', 'currencyParty', 'accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('posting_date', '>=', $startDate)
    //         ->whereDate('posting_date', '<=', $endDate);


    //     // Transfers base query
    //     $transfersQuery = Transfer::with(['accountNumber', 'paymentChannelDetails'])
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('transfer_date', '>=', $startDate)
    //         ->whereDate('transfer_date', '<=', $endDate);

    //     // Income/Expense base query
    //     $incomeExpenseQuery = IncomeExpensePosting::with(['accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('posting_date', '>=', $startDate)
    //         ->whereDate('posting_date', '<=', $endDate);

    //     // Bank Deposit base query
    //     $bankDepositQuery = BankDeposit::with(['accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('posting_date', '>=', $startDate)
    //         ->whereDate('posting_date', '<=', $endDate);

    //     // Loan Deposit base query
    //     $loanPaymentQuery = LoanPosting::with(['loanBankParty', 'accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('posting_date', '>=', $startDate)
    //         ->whereDate('posting_date', '<=', $endDate);

    //     // Investment Deposit base query
    //     $investmentPaymentQuery = InvestmentPosting::with(['accountNumber', 'paymentChannelDetails'])
    //         ->where('status', 'approved')
    //         ->whereIn('transaction_type', ['payment', 'received'])
    //         ->whereDate('posting_date', '>=', $startDate)
    //         ->whereDate('posting_date', '<=', $endDate);

    //     // Apply filters
    //     if ($request->filled('currency_id')) {
    //         $baseQuery->where('currency_id', $request->currency_id);
    //     }

    //     if ($request->filled('currency_party_id')) {
    //         $baseQuery->where('currency_party_id', $request->currency_party_id);
    //     }

    //     if ($request->filled('ac_no')) {
    //         $acNo = $request->ac_no;
    //         $baseQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //         $transfersQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //         $incomeExpenseQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //         $bankDepositQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //         $loanPaymentQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //         $investmentPaymentQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
    //     }

    //     if ($request->filled('payment_channel_id')) {
    //         $channelId = $request->payment_channel_id;
    //         $baseQuery->where('payment_channel_id', $channelId);
    //         $transfersQuery->where('payment_channel_id', $channelId);
    //         $incomeExpenseQuery->where('payment_channel_id', $channelId);
    //         $bankDepositQuery->where('payment_channel_id', $channelId);
    //         $loanPaymentQuery->where('payment_channel_id', $channelId);
    //         $investmentPaymentQuery->where('payment_channel_id', $channelId);
    //     }

    //     // Debug: Check what the base query returns
    //     $testResults = (clone $baseQuery)->get();
    //     Log::debug('Base Query Results:', [
    //         'count' => $testResults->count(),
    //         'records' => $testResults->map(fn($item) => ['id' => $item->id, 'posting_date' => $item->posting_date])->toArray()
    //     ]);

    //     // Fetch filtered currency postings
    //     $currencyPostings = (clone $baseQuery)->get();

    //     // Fetch filtered transfers and map transfer_date to posting_date
    //     $transfers = (clone $transfersQuery)->get()->map(function ($transfer) {
    //         $transfer->posting_date = $transfer->transfer_date;
    //         return $transfer;
    //     });

    //     // Fetch filtered income/expense postings
    //     $incomeExpenses = (clone $incomeExpenseQuery)->get();

    //     // Fetch filtered bank deposits
    //     $bankDeposits = (clone $bankDepositQuery)->get();

    //     // Fetch filtered loan payments
    //     $loanPayments = (clone $loanPaymentQuery)->get();

    //     // Fetch filtered investment payments
    //     $investmentPayments = (clone $investmentPaymentQuery)->get();

    //     // Combine all records
    //     // $rows = $currencyPostings->values()
    //     //     ->merge($transfers->values())
    //     //     ->merge($incomeExpenses->values())
    //     //     ->merge($bankDeposits->values())
    //     //     ->merge($loanPayments->values())
    //     //     ->merge($investmentPayments->values())
    //     //     ->sortBy('posting_date')
    //     //     ->values();


    //     // $rows = collect()
    //     //     ->concat($currencyPostings)
    //     //     ->concat($transfers)
    //     //     ->concat($incomeExpenses)
    //     //     ->concat($bankDeposits)
    //     //     ->concat($loanPayments)
    //     //     ->concat($investmentPayments)
    //     //     ->sortBy('posting_date')
    //     //     ->values();


    //     $rows = collect()
    //         // Currency Postings
    //         ->concat($currencyPostings->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->currencyParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $r->entry_type ?? 'Currency Trading',
    //         ]))
    //         // Loan Payments
    //         ->concat($loanPayments->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->loanBankParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $loanEntryTypeMapping[$r->entry_type] ?? 'Loan',
    //         ]))
    //         // Transfers
    //         ->concat($transfers->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->transferParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $r->entry_type ?? 'Transfer',
    //         ]))
    //         // Income & Expenses
    //         ->concat($incomeExpenses->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->currencyParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $r->transaction_type == "payment" ? 'Expense' : 'Income',
    //         ]))
    //         // Bank Deposits
    //         ->concat($bankDeposits->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->depositParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $r->entry_type ?? 'Deposit',
    //         ]))
    //         // Investment Payments
    //         ->concat($investmentPayments->map(fn($r) => [
    //             'id' => $r->id,
    //             'posting_date' => $r->posting_date,
    //             'transaction_type' => $r->transaction_type,
    //             'amount_bdt' => $r->amount_bdt,
    //             'payment_channel_details' => $r->paymentChannelDetails,
    //             'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
    //                 ? (object) ['ac_no' => '--', 'ac_name' => '--']
    //                 : ($r->accountNumber ?? null),
    //             'party_name' => $r->investmentParty?->party_name ?? '--',
    //             'party_account_number' => $r->party_account_number ?? '--',
    //             'note' => $r->note ?? '--',
    //             'entry_type' => $investmentEntryTypeMapping[$r->entry_type] ?? 'Investment Posting',
    //         ]))
    //         ->sortBy('posting_date')
    //         ->values();






    //     // Debug: Log combined results
    //     Log::debug('Combined Results:', [
    //         'currency_count' => $currencyPostings->count(),
    //         'transfers_count' => $transfers->count(),
    //         'income_expense_count' => $incomeExpenses->count(),
    //         'bank_deposits_count' => $bankDeposits->count(),
    //         'loan_payments_count' => $loanPayments->count(),
    //         'investment_payments_count' => $investmentPayments->count(),
    //         'total_count' => $rows->count()
    //     ]);

    //     // Summary totals - using whereDate for all summary queries too
    //     $summaryData = (clone $baseQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $transferSummary = (clone $transfersQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $incomeExpenseSummary = (clone $incomeExpenseQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $bankDepositSummary = (clone $bankDepositQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $loanPaymentSummary = (clone $loanPaymentQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $investmentPaymentSummary = (clone $investmentPaymentQuery)
    //         ->selectRaw("
    //     COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
    //     COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
    //     ")->first();

    //     $paymentTotal = $summaryData->payment_total + $transferSummary->payment_total + $incomeExpenseSummary->payment_total + $bankDepositSummary->payment_total + $loanPaymentSummary->payment_total + $investmentPaymentSummary->payment_total;
    //     $receivedTotal = $summaryData->received_total + $transferSummary->received_total + $incomeExpenseSummary->received_total + $bankDepositSummary->received_total + $loanPaymentSummary->received_total + $investmentPaymentSummary->received_total;
    //     $payable = $receivedTotal - $paymentTotal;

    //     return response()->json([
    //         'rows' => $rows,
    //         'summary' => [
    //             'payment' => $paymentTotal,
    //             'received' => $receivedTotal,
    //         ],
    //         'payable' => $payable,
    //         'date_range' => [
    //             'start' => $startDate,
    //             'end' => $endDate
    //         ]
    //     ]);
    // }

    // all
    public function bankLedgerDetails(Request $request)
    {
        $loanEntryTypeMapping = [
            'loan_taken'   => 'Loan Taken',
            'loan_given'   => 'Loan Given',
            'loan_payment' => 'Loan Payment',
            'loan_received' => 'Loan Received',
        ];

        $investmentEntryTypeMapping = [
            'investment'        => 'Investment',
            'investment_return' => 'Investment Return',
            'investment_profit' => 'Investment Profit',
        ];


        $rentalEntryTypeMapping = [
            'rent_received'   => 'Rent Received',
            'security_money_amount'   => 'Security Money',
        ];


        Log::debug('Bank Ledger Request:', $request->all());


        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->toDateString() : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->toDateString() : null;

        Log::debug('Date Range:', ['start' => $startDate, 'end' => $endDate]);

        // Base queries
        $baseQuery = CurrencyPosting::with(['currency', 'currencyParty', 'accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        $transfersQuery = Transfer::with(['accountNumber', 'paymentChannelDetails'])
            ->whereIn('transaction_type', ['payment', 'received']);

        $incomeExpenseQuery = IncomeExpensePosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        $bankDepositQuery = BankDeposit::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        $loanPaymentQuery = LoanPosting::with(['loanBankParty', 'accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        $investmentPaymentQuery = InvestmentPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);


        $rentalPaymentQuery = RentalPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $baseQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);

            $transfersQuery->whereDate('transfer_date', '>=', $startDate)
                ->whereDate('transfer_date', '<=', $endDate);

            $incomeExpenseQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);

            $bankDepositQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);

            $loanPaymentQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);

            $investmentPaymentQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);

            $rentalPaymentQuery->whereDate('posting_date', '>=', $startDate)
                ->whereDate('posting_date', '<=', $endDate);
        }

        // Apply other filters
        if ($request->filled('currency_id')) {
            $baseQuery->where('currency_id', $request->currency_id);
        }

        if ($request->filled('currency_party_id')) {
            $baseQuery->where('currency_party_id', $request->currency_party_id);
        }

        if ($request->filled('ac_no')) {
            $acNo = $request->ac_no;
            $queries = [
                $baseQuery,
                $transfersQuery,
                $incomeExpenseQuery,
                $bankDepositQuery,
                $loanPaymentQuery,
                $investmentPaymentQuery,
                $rentalPaymentQuery
            ];
            foreach ($queries as $query) {
                $query->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            }
        }

        if ($request->filled('payment_channel_id')) {
            $channelId = $request->payment_channel_id;
            $queries = [
                $baseQuery,
                $transfersQuery,
                $incomeExpenseQuery,
                $bankDepositQuery,
                $loanPaymentQuery,
                $investmentPaymentQuery,
                $rentalPaymentQuery
            ];
            foreach ($queries as $query) {
                $query->where('payment_channel_id', $channelId);
            }
        }

        // Fetch data
        $currencyPostings = $baseQuery->get();
        $transfers = $transfersQuery->get()->map(function ($transfer) {
            $transfer->posting_date = $transfer->transfer_date;
            return $transfer; // return the object, not a string
        });

        $incomeExpenses = $incomeExpenseQuery->get();
        $bankDeposits = $bankDepositQuery->get();
        $loanPayments = $loanPaymentQuery->get();
        $investmentPayments = $investmentPaymentQuery->get();
        $rentalPayments = $rentalPaymentQuery->get();

        // Combine all records
        $rows = collect()
            ->concat($currencyPostings->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->currencyParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $r->entry_type ?? 'Currency Trading',
            ]))
            ->concat($loanPayments->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->loanBankParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $loanEntryTypeMapping[$r->entry_type] ?? 'Loan',
            ]))
            ->concat($transfers->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->transferParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $r->entry_type ?? 'Transfer',
            ]))
            ->concat($incomeExpenses->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->currencyParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $r->transaction_type == "payment" ? 'Expense' : 'Income',
            ]))
            ->concat($bankDeposits->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->depositParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $r->entry_type ?? 'Deposit',
            ]))
            ->concat($investmentPayments->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->investmentParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                'entry_type' => $investmentEntryTypeMapping[$r->entry_type] ?? 'Investment Posting',
            ]))
            ->concat($rentalPayments->map(fn($r) => [
                'id' => $r->id,
                'posting_date' => $r->posting_date,
                'transaction_type' => $r->transaction_type,
                'amount_bdt' => $r->amount_bdt,
                'payment_channel_details' => $r->paymentChannelDetails,
                'account_number' => (trim(strtolower($r->paymentChannelDetails?->method_name ?? '')) === 'cash')
                    ? (object) ['ac_no' => '--', 'ac_name' => '--']
                    : ($r->accountNumber ?? null),
                'party_name' => $r->rentalParty?->party_name ?? '--',
                'party_account_number' => $r->party_account_number ?? '--',
                'note' => $r->note ?? '--',
                // 'entry_type' => $r->entry_type ?? 'Rental Posting',
                'entry_type' => $rentalEntryTypeMapping[$r->entry_type] ?? 'Rental Posting',
            ]))
            ->sortBy('posting_date')
            ->values();

        // Summary totals
        $summaryData = (clone $baseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $transferSummary = (clone $transfersQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $incomeExpenseSummary = (clone $incomeExpenseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $bankDepositSummary = (clone $bankDepositQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $loanPaymentSummary = (clone $loanPaymentQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $investmentPaymentSummary = (clone $investmentPaymentQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $rentalPaymentSummary = (clone $rentalPaymentQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $paymentTotal = $summaryData->payment_total + $transferSummary->payment_total + $incomeExpenseSummary->payment_total + $bankDepositSummary->payment_total + $loanPaymentSummary->payment_total + $investmentPaymentSummary->payment_total + $rentalPaymentSummary->payment_total;

        $receivedTotal = $summaryData->received_total + $transferSummary->received_total + $incomeExpenseSummary->received_total + $bankDepositSummary->received_total + $loanPaymentSummary->received_total + $investmentPaymentSummary->received_total + $rentalPaymentSummary->received_total;

        $payable = $receivedTotal - $paymentTotal;

        return response()->json([
            'rows' => $rows,
            'summary' => [
                'payment' => $paymentTotal,
                'received' => $receivedTotal,
            ],
            'payable' => $payable,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }






    //summary bank ledger


    // all
    public function bankLedgerSummary(Request $request)
    {
        $startDate = $request->start_date ?? '';
        $endDate = $request->end_date ?? '';

        // Currency Postings base query
        $baseQuery = CurrencyPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        // Income/Expense base query
        $incomeExpenseQuery = IncomeExpensePosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        // Transfers base query
        $transfersQuery = Transfer::with(['accountNumber', 'paymentChannelDetails'])
            ->whereIn('transaction_type', ['payment', 'received']);

        // Bank Deposit base query
        $bankDepositQuery = BankDeposit::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);


        // Loan Payment base query
        $loanPaymentQuery = LoanPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);


        // Loadn Payment base query
        $investmentPostingQuery = InvestmentPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);


        $rentalPostingQuery = RentalPosting::with(['accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        // Apply date filters
        if ($startDate && $endDate) {
            $baseQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $incomeExpenseQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $transfersQuery->whereBetween('transfer_date', [$startDate, $endDate]);
            $bankDepositQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $loanPaymentQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $investmentPostingQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $rentalPostingQuery->whereBetween('posting_date', [$startDate, $endDate]);
        }

        // Apply account & channel filters
        if ($request->filled('currency_id')) {
            $baseQuery->where('currency_id', $request->currency_id);
        }

        if ($request->filled('currency_party_id')) {
            $baseQuery->where('currency_party_id', $request->currency_party_id);
        }

        if ($request->filled('ac_no')) {
            $acNo = $request->ac_no;
            $baseQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $incomeExpenseQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $transfersQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $bankDepositQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $loanPaymentQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $investmentPostingQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
            $rentalPostingQuery->whereHas('accountNumber', fn($q) => $q->where('ac_no', $acNo));
        }

        if ($request->filled('payment_channel_id')) {
            $channelId = $request->payment_channel_id;
            $baseQuery->where('payment_channel_id', $channelId);
            $incomeExpenseQuery->where('payment_channel_id', $channelId);
            $transfersQuery->where('payment_channel_id', $channelId);
            $bankDepositQuery->where('payment_channel_id', $channelId);
            $loanPaymentQuery->where('payment_channel_id', $channelId);
            $investmentPostingQuery->where('payment_channel_id', $channelId);
            $rentalPostingQuery->where('payment_channel_id', $channelId);
        }

        // Aggregated queries
        $currencyPostings = (clone $baseQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();

        $incomeExpenses = (clone $incomeExpenseQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();

        $transfers = (clone $transfersQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(transfer_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();

        $bankDeposits = (clone $bankDepositQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();


        $loanPayment = (clone $loanPaymentQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();



        $investmentPayment = (clone $investmentPostingQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();

        $rentalPayment = (clone $rentalPostingQuery)
            ->select([
                'account_id',
                'payment_channel_id',
                DB::raw('MAX(posting_date) as posting_date'),
                DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
            ])
            ->with(['accountNumber', 'paymentChannelDetails'])
            ->groupBy('account_id', 'payment_channel_id')
            ->get();

        // Combine all collections
        $combinedRows = $currencyPostings->concat($incomeExpenses)->concat($transfers)->concat($bankDeposits)->concat($loanPayment)->concat($investmentPayment)->concat($rentalPayment);

        // Consolidate by account_id & payment_channel_id
        $consolidatedRows = $combinedRows->groupBy(fn($item) => $item->account_id . '-' . $item->payment_channel_id)
            ->map(fn($group) => [
                'id' => $group->first()->account_id,
                'posting_date' => $group->max('posting_date'),
                'payment_amount' => $group->sum('payment_amount'),
                'received_amount' => $group->sum('received_amount'),
                'account_number' => $group->first()->accountNumber,
                'payment_channel_details' => $group->first()->paymentChannelDetails,
            ])->values();

        // Calculate summary totals
        $currencySummary = (clone $baseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $incomeExpenseSummary = (clone $incomeExpenseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $transferSummary = (clone $transfersQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $bankDepositSummary = (clone $bankDepositQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();


        $loanPaymentSummary = (clone $loanPaymentQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();


        $investmentPaymentSummary = (clone $investmentPostingQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();

        $rentalPaymentSummary = (clone $rentalPostingQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")->first();


        $paymentTotal = $currencySummary->payment_total + $incomeExpenseSummary->payment_total + $transferSummary->payment_total + $bankDepositSummary->payment_total + $loanPaymentSummary->payment_total + $investmentPaymentSummary->payment_total + $rentalPaymentSummary->payment_total;

        $receivedTotal = $currencySummary->received_total + $incomeExpenseSummary->received_total + $transferSummary->received_total + $bankDepositSummary->received_total + $loanPaymentSummary->received_total + $investmentPaymentSummary->received_total + $rentalPaymentSummary->received_total;


        $payable = $receivedTotal - $paymentTotal;

        return response()->json([
            'rows' => $consolidatedRows,
            'summary' => [
                'payment' => $paymentTotal,
                'received' => $receivedTotal,
            ],
            'payable' => $payable,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }





    public function bankLedger(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->toDateString();
        $isSummaryView = $request->view === 'summary';

        // Currency Postings base query
        $baseQuery = CurrencyPosting::with(['currency', 'currencyParty', 'accountNumber', 'paymentChannelDetails'])
            ->where('status', 'approved')
            ->whereIn('transaction_type', ['payment', 'received']);

        // Transfers base query
        $transfersQuery = Transfer::with(['accountNumber', 'paymentChannelDetails'])
            ->whereIn('transaction_type', ['payment', 'received']);

        // Apply date filters based on view
        if (!$isSummaryView) {
            $baseQuery->whereBetween('posting_date', [$startDate, $endDate]);
            $transfersQuery->whereBetween('transfer_date', [$startDate, $endDate]);
        }

        // Apply common filters
        if ($request->filled('currency_id')) {
            $baseQuery->where('currency_id', $request->currency_id);
        }

        if ($request->filled('currency_party_id')) {
            $baseQuery->where('currency_party_id', $request->currency_party_id);
        }

        if ($request->filled('ac_no')) {
            $baseQuery->whereHas('accountNumber', function ($query) use ($request) {
                $query->where('ac_no', $request->ac_no);
            });
            $transfersQuery->whereHas('accountNumber', function ($query) use ($request) {
                $query->where('ac_no', $request->ac_no);
            });
        }

        if ($request->filled('payment_channel_id')) {
            $baseQuery->where('payment_channel_id', $request->payment_channel_id);
            $transfersQuery->where('payment_channel_id', $request->payment_channel_id);
        }

        // Calculate summary totals including transfers
        $currencySummary = (clone $baseQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")
            ->first();

        $transferSummary = (clone $transfersQuery)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN transaction_type = 'payment' THEN amount_bdt ELSE 0 END), 0) AS payment_total,
            COALESCE(SUM(CASE WHEN transaction_type = 'received' THEN amount_bdt ELSE 0 END), 0) AS received_total
        ")
            ->first();

        $paymentTotal = $currencySummary->payment_total + $transferSummary->payment_total;
        $receivedTotal = $currencySummary->received_total + $transferSummary->received_total;
        $payable = $receivedTotal - $paymentTotal;

        // Prepare response data
        $response = [
            'summary' => [
                'payment' => $paymentTotal,
                'received' => $receivedTotal,
            ],
            'payable' => $payable,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];

        if ($isSummaryView) {
            // For summary view - get consolidated data grouped by account and payment channel
            $currencyPostings = (clone $baseQuery)
                ->select([
                    'account_id',
                    'payment_channel_id',
                    DB::raw('MAX(posting_date) as posting_date'),
                    DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                    DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
                ])
                ->with(['accountNumber', 'paymentChannelDetails'])
                ->groupBy('account_id', 'payment_channel_id')
                ->get();

            $transfers = (clone $transfersQuery)
                ->select([
                    'account_id',
                    'payment_channel_id',
                    DB::raw('MAX(transfer_date) as posting_date'),
                    DB::raw('SUM(CASE WHEN transaction_type = "payment" THEN amount_bdt ELSE 0 END) as payment_amount'),
                    DB::raw('SUM(CASE WHEN transaction_type = "received" THEN amount_bdt ELSE 0 END) as received_amount')
                ])
                ->with(['accountNumber', 'paymentChannelDetails'])
                ->groupBy('account_id', 'payment_channel_id')
                ->get();

            // Combine and group the results
            $combinedRows = $currencyPostings->concat($transfers);

            $consolidatedRows = $combinedRows->groupBy(function ($item) {
                return $item->account_id . '-' . $item->payment_channel_id;
            })->map(function ($group) {
                $first = $group->first();
                $paymentAmount = $group->sum('payment_amount');
                $receivedAmount = $group->sum('received_amount');
                $balance = $receivedAmount - $paymentAmount;

                return [
                    'id' => $first->account_id,
                    'posting_date' => $group->max('posting_date'),
                    'payment_amount' => $paymentAmount,
                    'received_amount' => $receivedAmount,
                    'balance' => $balance,
                    'account_number' => $first->accountNumber,
                    'payment_channel_details' => $first->paymentChannelDetails,
                ];
            })->values();

            $response['rows'] = $consolidatedRows;
        } else {
            // For details view - get all individual records
            $currencyPostings = (clone $baseQuery)
                ->whereBetween('posting_date', [$startDate, $endDate])
                ->get();

            $transfers = (clone $transfersQuery)
                ->whereBetween('transfer_date', [$startDate, $endDate])
                ->get();

            $response['rows'] = $currencyPostings->merge($transfers);
        }

        return response()->json($response);
    }



    public function currencyAnalysis(Request $request)
    {
        $currencyId = $request->input('currency_id');
        $currencyPartyId = $request->input('currency_party_id');

        $baseQuery = CurrencyPosting::where('status', 'approved');

        $baseQuery->where('business_type_id', 2);

        if ($currencyId) {
            $baseQuery->where('currency_id', $currencyId);
        }

        if ($currencyPartyId) {
            $baseQuery->where('currency_party_id', $currencyPartyId);
        }

        $groupByColumns = ['currency_id', 'transaction_type'];

        $selectColumns = [
            'currency_id',
            'transaction_type',
            DB::raw('SUM(amount_bdt) as total_amount_bdt'),
            DB::raw('SUM(currency_amount) as total_currency_amount')
        ];

        if ($currencyPartyId) {
            $groupByColumns[] = 'currency_party_id';
            $selectColumns[] = 'currency_party_id';
        }

        $transactions = $baseQuery
            ->select($selectColumns)
            ->groupBy($groupByColumns)
            ->get();

        $results = [];

        $groupedTransactions = $transactions->groupBy(function ($item) use ($currencyPartyId) {
            return $currencyPartyId ? $item->currency_id . '-' . $item->currency_party_id : $item->currency_id;
        });

        foreach ($groupedTransactions as $key => $group) {
            $buyData = $group->where('transaction_type', 'buy')->first();
            $sellData = $group->where('transaction_type', 'sell')->first();

            if ($buyData || $sellData) {
                $totalBuyAmountBdt = $buyData ? $buyData->total_amount_bdt : 0;
                $totalBuyCurrencyAmount = $buyData ? $buyData->total_currency_amount : 0;
                $totalSellAmountBdt = $sellData ? $sellData->total_amount_bdt : 0;
                $totalSellCurrencyAmount = $sellData ? $sellData->total_currency_amount : 0;

                $avgBuyRate = ($totalBuyCurrencyAmount != 0)
                    ? $totalBuyAmountBdt / $totalBuyCurrencyAmount
                    : 0;
                $avgSellRate = ($totalSellCurrencyAmount != 0)
                    ? $totalSellAmountBdt / $totalSellCurrencyAmount
                    : 0;

                // Calculate P&L
                $pnlRate = $avgSellRate - $avgBuyRate;
                $pnlAmount = $pnlRate * $totalSellCurrencyAmount;

                // Calculate remaining currency
                $remainingCurrency = $totalBuyCurrencyAmount - $totalSellCurrencyAmount;

                $resultItem = [
                    'currency_id' => $group->first()->currency_id,
                    'total_buy_amount_bdt' => round($totalBuyAmountBdt, 2),
                    'total_buy_currency_amount' => round($totalBuyCurrencyAmount, 2),
                    'total_sell_amount_bdt' => round($totalSellAmountBdt, 2),
                    'total_sell_currency_amount' => round($totalSellCurrencyAmount, 2),
                    'avg_buy_rate' => round($avgBuyRate, 4),
                    'avg_sell_rate' => round($avgSellRate, 4),
                    'pnl_rate' => round($pnlRate, 4),
                    'pnl_amount' => round($pnlAmount, 2),
                    'remaining_currency_amount' => round($remainingCurrency, 2),
                ];

                if ($currencyPartyId) {
                    $resultItem['currency_party_id'] = $group->first()->currency_party_id;
                }

                $results[] = $resultItem;
            }
        }

        return response()->json([
            'success' => true,
            'filters' => [
                'currency_id' => $currencyId,
                'currency_party_id' => $currencyPartyId,
            ],
            'data' => $results,
        ]);
    }



    // ================= SHOW SINGLE RECORD =================
    public function show($id)
    {
        $posting = CurrencyPosting::find($id);

        if (!$posting) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($posting);
    }

    // ================= CREATE NEW RECORD =================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_type_id' => 'required|integer',
            'transaction_type' => 'required|in:buy,sell,payment,received',
            'currency_id'      => 'required|integer',
            'currency_party_id' => 'required|integer',
            'payment_channel_id' => 'required|integer',
            'account_id'       => 'required|integer',
            'currency_amount'  => 'required|numeric',
            'exchange_rate'    => 'required|numeric',
            'amount_bdt'       => 'required|numeric',
            'posting_date'     => 'required|date',
            'status'           => 'in:approved,rejected,pending',
            'note'             => 'nullable|string'
        ]);

        $posting = CurrencyPosting::create($validated);

        return response()->json($posting, 201);
    }

    // ================= UPDATE RECORD =================
    public function update(Request $request, $id)
    {
        $posting = CurrencyPosting::find($id);

        if (!$posting) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $validated = $request->validate([
            'business_type_id' => 'sometimes|integer',
            'transaction_type' => 'sometimes|in:buy,sell,payment,received',
            'currency_id'      => 'sometimes|integer',
            'currency_party_id' => 'sometimes|integer',
            'payment_channel_id' => 'sometimes|integer',
            'account_id'       => 'sometimes|integer',
            'currency_amount'  => 'sometimes|numeric',
            'exchange_rate'    => 'sometimes|numeric',
            'amount_bdt'       => 'sometimes|numeric',
            'posting_date'     => 'sometimes|date',
            'status'           => 'sometimes|in:approved,rejected,pending',
            'note'             => 'nullable|string'
        ]);

        $posting->update($validated);

        return response()->json($posting);
    }

    // ================= DELETE RECORD =================
    public function destroy($id)
    {
        $posting = CurrencyPosting::find($id);

        if (!$posting) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $posting->delete();

        return response()->json(['message' => 'Record deleted successfully']);
    }
}
