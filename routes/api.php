<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\PointsOfContactController;
use App\Http\Controllers\TransactionTypesController;
use App\Http\Controllers\PaymentChannelController;
use App\Http\Controllers\PaymentChannelDetailsController;
use \App\Http\Controllers\SourceCategoryController;
use \App\Http\Controllers\SourceSubCategoryController;
use \App\Http\Controllers\SourceSubCategoryDetailController;
use \App\Http\Controllers\PostingController;
use \App\Http\Controllers\DashboardController;
use \App\Http\Controllers\AccountNumberController;
use \App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdvancedPaymentController;
use App\Http\Controllers\BankDepositController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CurrencyPartyController;
use App\Http\Controllers\CurrencyLedgerController;
use App\Http\Controllers\CurrencyPostingsController;
use App\Http\Controllers\IncomeExpenseLedgerController;
use App\Http\Controllers\IncomeExpensePostingController;
use App\Http\Controllers\IncomeExpenseHeadController;
use App\Http\Controllers\InvestmentPartyController;
use App\Http\Controllers\InvestmentPostingController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\LoanBankPartyController;
use App\Http\Controllers\LoanPostingController;
use App\Http\Controllers\RentalPartyController;
use App\Http\Controllers\RentalHouseController;
use App\Http\Controllers\RentalPostingController;
use App\Http\Controllers\RentalHousePartyMappingController;



Route::get('/bank-ledger', [CurrencyLedgerController::class, 'bankLedger']);

Route::get('/bank-ledger/summary', [CurrencyLedgerController::class, 'bankLedgerSummary']);
Route::get('/bank-ledger/details', [CurrencyLedgerController::class, 'bankLedgerDetails']);
Route::get('/currency-analysis', [CurrencyLedgerController::class, 'currencyAnalysis']);
// Route::get('/currency-postings/{id}', [CurrencyLedgerController::class, 'show']);
// Route::post('/currency-postings', [CurrencyLedgerController::class, 'store']);
// Route::put('/currency-postings/{id}', [CurrencyLedgerController::class, 'update']);
// Route::delete('/currency-postings/{id}', [CurrencyLedgerController::class, 'destroy']);




Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Source
    Route::get('sources', [SourceController::class, 'index']);
    Route::get('sources', [SourceController::class, 'getAllSources']);
    Route::post('sources', [SourceController::class, 'store']);
    Route::get('sources/{id}', [SourceController::class, 'show']);
    Route::put('sources/{id}', [SourceController::class, 'update']);
    Route::delete('sources/{id}', [SourceController::class, 'destroy']);

    // Expense Type
    Route::get('expense-types', [ExpenseTypeController::class, 'index']);
    Route::post('expense-types', [ExpenseTypeController::class, 'store']);
    Route::get('expense-types/{id}', [ExpenseTypeController::class, 'show']);
    Route::get('expense-types-by-source/{id}', [ExpenseTypeController::class, 'getExpenseTypesBySourceId']);
    Route::put('expense-types/{id}', [ExpenseTypeController::class, 'update']);
    Route::delete('expense-types/{id}', [ExpenseTypeController::class, 'destroy']);

    // transaction-type
    Route::get('transaction-types', [TransactionTypesController::class, 'index']);
    Route::post('transaction-types', [TransactionTypesController::class, 'store']);
    Route::get('transaction-types/{id}', [TransactionTypesController::class, 'show']);
    Route::put('transaction-types/{id}', [TransactionTypesController::class, 'update']);
    Route::delete('transaction-types/{id}', [TransactionTypesController::class, 'destroy']);

    // Point Of Contact
    Route::get('points-of-contact', [PointsOfContactController::class, 'index']);
    Route::post('points-of-contact', [PointsOfContactController::class, 'store']);
    Route::get('points-of-contact/{id}', [PointsOfContactController::class, 'show']);
    Route::put('points-of-contact/{id}', [PointsOfContactController::class, 'update']);
    Route::delete('points-of-contact/{id}', [PointsOfContactController::class, 'destroy']);
    Route::get('/points-of-contact/by-subcategory/{subCatId}', [PointsOfContactController::class, 'getBySubCategory']);

    // Payment Channel

    Route::get('payment-mode-list', [PaymentChannelController::class, 'getPaymentModeList']);
    Route::get('payment-channels', [PaymentChannelController::class, 'index']);
    Route::post('payment-channels', [PaymentChannelController::class, 'store']);
    Route::get('payment-channels/{id}', [PaymentChannelController::class, 'show']);
    Route::put('payment-channels/{id}', [PaymentChannelController::class, 'update']);
    Route::delete('payment-channels/{id}', [PaymentChannelController::class, 'destroy']);

    // Payment Channel Details
    Route::get('payment-channels-details', [PaymentChannelDetailsController::class, 'index']);
    Route::post('payment-channels-details', [PaymentChannelDetailsController::class, 'store']);
    Route::get('payment-channels-details/{id}', [PaymentChannelDetailsController::class, 'show']);
    Route::get('payment-channels-list/', [PaymentChannelDetailsController::class, 'getPaymentChannels']);
    Route::put('payment-channels-details/{id}', [PaymentChannelDetailsController::class, 'update']);
    Route::delete('payment-channels-details/{id}', [PaymentChannelDetailsController::class, 'destroy']);

    // Account Number
    Route::get('payment-account-number', [AccountNumberController::class, 'index']);
    Route::get('account-number-all', [AccountNumberController::class, 'showAllAcNo']);
    Route::post('payment-account-number', [AccountNumberController::class, 'store']);
    Route::get('payment-account-number/{id}', [AccountNumberController::class, 'show']);
    Route::get('payment/account-numbers/channel/{id}', [AccountNumberController::class, 'accountNumbersByChannel']);
    Route::get('/balance/{account_id}', [AccountNumberController::class, 'balanceCheck']);

    Route::put('payment-account-number/{id}', [AccountNumberController::class, 'update']);
    Route::delete('payment-account-number/{id}', [AccountNumberController::class, 'destroy']);

    // Source Category
    Route::get('source-category', [SourceCategoryController::class, 'index']);
    Route::post('source-category', [SourceCategoryController::class, 'store']);
    Route::get('source-category/{id}', [SourceCategoryController::class, 'show']);
    Route::get('source-categories/{id}', [SourceCategoryController::class, 'getCategoriesBySourceId']);
    Route::put('source-category/{id}', [SourceCategoryController::class, 'update']);
    Route::delete('source-category/{id}', [SourceCategoryController::class, 'destroy']);

    // Subcategory
    Route::get('source-sub-category', [SourceSubCategoryController::class, 'index']);
    Route::post('source-sub-category', [SourceSubCategoryController::class, 'store']);
    Route::get('source-sub-category/{id}', [SourceSubCategoryController::class, 'show']);
    Route::get('source-sub-categories/{id}', [SourceSubCategoryController::class, 'getSubCategoriesBySourceId']);
    Route::put('source-sub-category/{id}', [SourceSubCategoryController::class, 'update']);
    Route::delete('source-sub-category/{id}', [SourceSubCategoryController::class, 'destroy']);

    // Subcategory Details
    Route::get('source-sub-category-details', [SourceSubCategoryDetailController::class, 'index']);
    Route::post('source-sub-category-details', [SourceSubCategoryDetailController::class, 'store']);
    Route::get('source-sub-category-details/{id}', [SourceSubCategoryDetailController::class, 'show']);
    Route::put('source-sub-category-details/{id}', [SourceSubCategoryDetailController::class, 'update']);
    Route::delete('source-sub-category-details/{id}', [SourceSubCategoryDetailController::class, 'destroy']);

    // Posting
    Route::get('posting', [PostingController::class, 'index']);
    Route::post('posting', [PostingController::class, 'store']);
    Route::get('posting/{id}', [PostingController::class, 'show']);
    Route::put('posting/{id}', [PostingController::class, 'update']);
    Route::delete('posting/{id}', [PostingController::class, 'destroy']);
    Route::put('posting/status/{id}', [PostingController::class, 'statusUpdate']);


    // Dashboard
    Route::get('dashboard/net-income', [DashboardController::class, 'getNetIncome']);
    Route::get('dashboard/source-balance', [DashboardController::class, 'getBalanceSourceWise']);
    Route::get('dashboard/payment-channel', [DashboardController::class, 'getPaymentChannel']);
    Route::get('dashboard/currency', [DashboardController::class, 'getCurrency']);

    // ledger
    Route::get('/transactions', [TransactionController::class, 'getTransactions']);
    Route::get('source-category-dropdown', [SourceCategoryController::class, 'dropdown']);
    Route::get('source-subcategory-dropdown', [SourceSubCategoryController::class, 'dropdownSubCat']);

    Route::post('/advanced-payments-by-point-of-contact-id', [AdvancedPaymentController::class, 'advancedPaymentByPointOfContactId']);

    Route::prefix('advanced-payments')->group(function () {
        Route::get('/', [AdvancedPaymentController::class, 'index']);
        Route::post('/', [AdvancedPaymentController::class, 'store']);
        Route::get('/{id}', [AdvancedPaymentController::class, 'show']);
        Route::put('/{id}', [AdvancedPaymentController::class, 'update']);
        Route::delete('/{id}', [AdvancedPaymentController::class, 'destroy']);
    });


    Route::prefix('currency-trading')->group(function () {

        Route::get('/currency-list', [CurrencyController::class, 'getAllCurrencies']);
        Route::get('/currency', [CurrencyController::class, 'index']);
        Route::post('/currency', [CurrencyController::class, 'store']);
        Route::get('/currency/{id}', [CurrencyController::class, 'show']);
        Route::put('/currency/{id}', [CurrencyController::class, 'update']);
        Route::delete('/currency/{id}', [CurrencyController::class, 'destroy']);

        // Currency Party
        Route::get('/currency-party-list', [CurrencyPartyController::class, 'getAllCurrencies']);
        Route::get('/currency-party', [CurrencyPartyController::class, 'index']);
        Route::post('/currency-party', [CurrencyPartyController::class, 'store']);
        Route::get('/currency-party/{id}', [CurrencyPartyController::class, 'show']);
        Route::put('/currency-party/{id}', [CurrencyPartyController::class, 'update']);
        Route::delete('/currency-party/{id}', [CurrencyPartyController::class, 'destroy']);

        // Currency Postings
        Route::get('/currency-posting', [CurrencyPostingsController::class, 'index']);
        Route::post('/currency-posting', [CurrencyPostingsController::class, 'store']);
        Route::get('/currency-posting/{id}', [CurrencyPostingsController::class, 'show']);
        Route::put('/currency-posting/{id}', [CurrencyPostingsController::class, 'update']);
        Route::delete('/currency-posting/{id}', [CurrencyPostingsController::class, 'destroy']);
        Route::put('/currency-posting/status/{id}', [CurrencyPostingsController::class, 'statusUpdate']);

        Route::get('/currency-ledger', [CurrencyLedgerController::class, 'index']);
        Route::get('/currency-analysis', [CurrencyLedgerController::class, 'currencyAnalysis']);


        Route::get('/currency-ledger-summary', [CurrencyLedgerController::class, 'currencyLedgerSummary']);
    });

    // Transfer

    Route::get('/transfer-list', [TransferController::class, 'getAllTransfer']);
    Route::get('/transfer', [TransferController::class, 'index']);
    Route::post('/transfer', [TransferController::class, 'store']);
    Route::get('/transfer/{id}', [TransferController::class, 'show']);
    Route::put('/transfer/{id}', [TransferController::class, 'update']);
    Route::delete('/transfer/{id}', [TransferController::class, 'destroy']);

    // Income and Expense
    Route::prefix('income-expense')->group(function () {

        Route::get('/head', [IncomeExpenseHeadController::class, 'getAllIncomes']);
        Route::get('/head', [IncomeExpenseHeadController::class, 'index']);
        Route::post('/head', [IncomeExpenseHeadController::class, 'store']);
        Route::get('/head/{id}', [IncomeExpenseHeadController::class, 'show']);
        Route::put('/head/{id}', [IncomeExpenseHeadController::class, 'update']);
        Route::delete('/head/{id}', [IncomeExpenseHeadController::class, 'destroy']);

        Route::get('/posting', [IncomeExpensePostingController::class, 'index']);
        Route::post('/posting', [IncomeExpensePostingController::class, 'store']);
        Route::get('/posting/{id}', [IncomeExpensePostingController::class, 'show']);
        Route::put('/posting/{id}', [IncomeExpensePostingController::class, 'update']);
        Route::delete('/posting/{id}', [IncomeExpensePostingController::class, 'destroy']);
        Route::put('/posting/status/{id}', [IncomeExpensePostingController::class, 'statusUpdate']);

        // Ledger
        Route::get('/ledger', [IncomeExpenseLedgerController::class, 'getLedgerData']);
    });

    // Ioan
    Route::prefix('loan')->group(function () {

        Route::get('/party-all', [LoanBankPartyController::class, 'getAllParties']);
        Route::get('/party', [LoanBankPartyController::class, 'index']);
        Route::post('/party', [LoanBankPartyController::class, 'store']);
        Route::get('/party/{id}', [LoanBankPartyController::class, 'show']);
        Route::put('/party/{id}', [LoanBankPartyController::class, 'update']);
        Route::delete('/party/{id}', [LoanBankPartyController::class, 'destroy']);

        Route::get('/posting', [LoanPostingController::class, 'index']);
        Route::post('/posting', [LoanPostingController::class, 'store']);
        Route::get('/posting/{id}', [LoanPostingController::class, 'show']);
        Route::put('/posting/{id}', [LoanPostingController::class, 'update']);
        Route::delete('/posting/{id}', [LoanPostingController::class, 'destroy']);
        Route::put('/posting/status/{id}', [LoanPostingController::class, 'statusUpdate']);

        // calculate interest and balance - LoanController
        Route::get('/loan-calculation/{loan_party_id}/{interest_rate_date}', [LoanPostingController::class, 'getLoanCalculation']);

        // Ledger
        Route::get('/ledger', [LoanPostingController::class, 'getLoanLedgerData']);
        Route::get('/ledger/summary', [LoanPostingController::class, 'getLoanSummary']);
    });


    // Rental
    Route::prefix('rental')->group(function () {

        Route::post('/parties-info', [RentalPartyController::class, 'getPartyInfo']);
        Route::get('/parties-refund-info/{id}', [RentalPartyController::class, 'getPartyRefundInfo']);

        Route::get('/house-mappings-by-party/{partyId}', [RentalPartyController::class, 'getHouseMappingsByParty']);

        Route::get('/parties-all', [RentalPartyController::class, 'getAllParties']);
        Route::get('/parties', [RentalPartyController::class, 'index']);
        Route::post('/parties', [RentalPartyController::class, 'store']);
        Route::get('/parties/{id}', [RentalPartyController::class, 'show']);
        Route::put('/parties/{id}', [RentalPartyController::class, 'update']);
        Route::delete('/parties/{id}', [RentalPartyController::class, 'destroy']);

        Route::get('/houses-all', [RentalHouseController::class, 'getAllHouses']);
        Route::get('/houses', [RentalHouseController::class, 'index']);
        Route::post('/houses', [RentalHouseController::class, 'store']);
        Route::get('/houses/{id}', [RentalHouseController::class, 'show']);
        Route::put('/houses/{id}', [RentalHouseController::class, 'update']);
        Route::delete('/houses/{id}', [RentalHouseController::class, 'destroy']);

        Route::get('/house-party-mapping-all', [RentalHousePartyMappingController::class, 'getAllMappings']);
        Route::get('/house-party-mapping', [RentalHousePartyMappingController::class, 'index']);
        Route::post('/house-party-mapping', [RentalHousePartyMappingController::class, 'store']);
        Route::get('/house-party-mapping/{id}', [RentalHousePartyMappingController::class, 'show']);
        Route::put('/house-party-mapping/{id}', [RentalHousePartyMappingController::class, 'update']);
        Route::delete('/house-party-mapping/{id}', [RentalHousePartyMappingController::class, 'destroy']);

        Route::get('/postings', [RentalPostingController::class, 'index']);
        Route::post('/postings', [RentalPostingController::class, 'store']);
        Route::get('/postings/{id}', [RentalPostingController::class, 'show']);
        Route::put('/postings/{id}', [RentalPostingController::class, 'update']);
        Route::delete('/postings/{id}', [RentalPostingController::class, 'destroy']);
        Route::put('/postings/status/{id}', [RentalPostingController::class, 'statusUpdate']);


        // Ledger
        Route::get('/ledger', [RentalPostingController::class, 'getRentalLedgerData']);
        // Route::get('/ledger/summary', [RentalPostingController::class, 'getLoanSummary']);
        Route::get('/rental-ledger-summary', [RentalPostingController::class, 'getRentalLedgerSummary']);
    });

    // Investment
    Route::prefix('investment')->group(function () {

        Route::get('/party-all', [InvestmentPartyController::class, 'getAllParties']);
        Route::get('/party', [InvestmentPartyController::class, 'index']);
        Route::post('/party', [InvestmentPartyController::class, 'store']);
        Route::get('/party/{id}', [InvestmentPartyController::class, 'show']);
        Route::put('/party/{id}', [InvestmentPartyController::class, 'update']);
        Route::delete('/party/{id}', [InvestmentPartyController::class, 'destroy']);

        Route::get('/posting', [InvestmentPostingController::class, 'index']);
        Route::post('/posting', [InvestmentPostingController::class, 'store']);
        Route::get('/posting/{id}', [InvestmentPostingController::class, 'show']);
        Route::put('/posting/{id}', [InvestmentPostingController::class, 'update']);
        Route::delete('/posting/{id}', [InvestmentPostingController::class, 'destroy']);
        Route::put('/posting/status/{id}', [InvestmentPostingController::class, 'statusUpdate']);
        // calculate investment return and balance - InvestmentController
        Route::get('/investment-calculation/{investment_party_id}', [InvestmentPostingController::class, 'getInvestmentCalculation']);
        // Ledger
        Route::get('/ledger', [InvestmentPostingController::class, 'getInvestmentLedgerData']);
    });
    // Bank Deposit
    Route::prefix('bank-deposit')->group(function () {
        Route::get('/posting', [BankDepositController::class, 'index']);
        Route::post('/posting', [BankDepositController::class, 'store']);
        Route::get('/posting/{id}', [BankDepositController::class, 'show']);
        Route::put('/posting/{id}', [BankDepositController::class, 'update']);
        Route::delete('/posting/{id}', [BankDepositController::class, 'destroy']);
        Route::put('/posting/status/{id}', [BankDepositController::class, 'statusUpdate']);
    });
});
