<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\unitController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinishProductController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\stockcontroller;
use App\Http\Controllers\machineController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\designController;
use App\Http\Controllers\karhaiController;
use App\Http\Controllers\packing;
use App\Http\Controllers\BankController;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ReceiptVoucherController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\payrollController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\roznamchaController;
use App\Http\Controllers\reportsController;


use Illuminate\Support\Facades\Artisan;

Route::get('/clear-all', function () {
    // Clear View Cache
    Artisan::call('view:clear');

    // Clear Cache
    Artisan::call('cache:clear');

    // Clear Config Cache
    Artisan::call('config:clear');

    // Clear Route Cache
    Artisan::call('route:clear');

    // Clear Compiled Classes
    Artisan::call('clear-compiled');

    // Optionally, clear other caches as needed
    // Artisan::call('optimize:clear'); // Laravel 8+ mein yeh command sab kuch clear kar deti hai

    return "All caches cleared successfully!";
});

//suppliers
Route::resource('suppliers', SupplierController::class);
Route::get('/supplierledger/{id}', [SupplierController::class, 'ledger'])->name('supplier.ledger');
Route::post('/supplierledger/{id}/list', [SupplierController::class, 'ledgerlist'])->name('supplier.ledgerlist');
Route::post('/supplierListing', [SupplierController::class, 'listing'])->name('supplierListing');
Route::get('/supplier/{id}', [SupplierController::class, 'getsupplier'])->name('supplier.get');
Route::post('/supplier/{id}/purchasedetail', [SupplierController::class, 'purchasedetail']);
Route::get('/supplier/{id}/purchasedetail/{purchase_detail_id}', [SupplierController::class, 'materialdetail']);
Route::post('/supplier/{id}/purchasedetail/{purchase_detail_id}/detail', [SupplierController::class, 'materialdetailinfo']);
Route::post('/supplier/paymentsave', [SupplierController::class, 'supplierpaymentstore'])->name('supplier.paymentstore');
Route::post('/supplier/{id}/paymentlist', [SupplierController::class, 'supplierpaymentlist'])->name('supplier.paymentlist');
Route::get('/supplier/{id}/payments', [SupplierController::class, 'supplierview']);
Route::get('/supplier/{supplier_id}/ledger/purchasedetail/{purchase_material_id}', [SupplierController::class, 'purchasebyid']);
Route::get('/supplier/{supplier_id}/ledger/paymentdetail/{paymentvoucher_id}', [SupplierController::class, 'paymentbyid']);

//customer
Route::resource('customers', CustomerController::class);
Route::get('/customerledger/{id}', [CustomerController::class, 'ledger'])->name('customer.ledger');
Route::post('/customerledger/{id}/list', [CustomerController::class, 'ledgerlist'])->name('customer.ledgerlist');
Route::post('/customersListing', [CustomerController::class, 'listing'])->name('customersListing');
Route::get('/customerdetail/{id}', [CustomerController::class, 'detail'])->name('customers.detail');
Route::post('/customerdetail/{id}/list', [CustomerController::class, 'detaillist'])->name('customers.detaillist');
Route::get('/customerdetail/{id}/sell/{sellid}', [CustomerController::class, 'selldetail'])->name('customers.sell');
Route::post('/customerdetail/{id}/selllist/{sellid}', [CustomerController::class, 'selldetaillist'])->name('customers.selllist');
Route::post('/customer/payment', [CustomerController::class, 'customerpaymentstore'])->name('customers.payment');
Route::get('/customer/{id}/payments', [CustomerController::class, 'paymentview'])->name('customers.paymentsview');
Route::post('/customer/{id}/paymentslist', [CustomerController::class, 'paymentlist'])->name('customers.paymentslist');
Route::get('/customer/{id}/ledger/sell/{sell_id}', [CustomerController::class, 'sellby_id']);
Route::get('/customer/{id}/ledger/payment/{paymentvoucher_id}', [CustomerController::class, 'paymentbyid']);
// vendors
Route::resource('vendors', VendorController::class);
Route::post('/vendorListing', [VendorController::class, 'listing'])->name('vendorListing');
Route::get('/vendor/{id}', [VendorController::class, 'vendordetail'])->name('vendor.detail');
Route::post('/vendor/{id}', [VendorController::class, 'vendordetail'])->name('vendor.detail');
Route::get('/vendor/{id}/payment', [VendorController::class, 'vendorpayment'])->name('vendor.payment');
Route::post('/vendor/{id}/payment', [VendorController::class, 'vendorpayment'])->name('vendor.paymentlist');
Route::post('/vendor/payment/store', [VendorController::class, 'vendorpaymentstore'])->name('vendor.paymentstore');
Route::get('/vendor/{id}/ledger', [VendorController::class, 'ledger'])->name('vendor.ledger');
Route::post('/vendor/{id}/ledgerlist', [VendorController::class, 'ledgerlist'])->name('vendor.ledgerlist');
Route::get('/vendor/{id}/ledger/payment/{paymentvoucher_id}', [VendorController::class, 'paymentbyid']);
Route::get('/vendor/{id}/ledger/issue/{issue_id}', [VendorController::class, 'issuebyid']);
//karahi vendir
Route::resource('karahivendor', karhaiController::class);
Route::post('/karahivendorlist', [karhaiController::class, 'list'])->name('karahivendorlist');
Route::post('karahivendor/issue', [karhaiController::class, 'karahiissue'])->name('karahivendorissue');
Route::get('karahivendor/issue/{id}', [karhaiController::class, 'karahiissuebyid']);
Route::get('karahivendor/{id}/payment', [karhaiController::class, 'karahipayment']);
Route::post('karahivendor/{id}/payment', [karhaiController::class, 'karahipaymentdetail'])->name('karahivendor.payment');
Route::post('karahivendor/payment/store', [karhaiController::class, 'karahipaymentstore'])->name('karahivendor.paymentstore');
Route::get('karahivendor/recieve/{id}', [karhaiController::class, 'karahirec']);
Route::post('karahivendor/recieve/{id}', [karhaiController::class, 'karahirec'])->name('karahivendor.rec');
Route::get('karahivendor/recieve/{id}/detail/{receive_karahi_material_id}', [karhaiController::class, 'karahirecdetail'])->name('karahivendor.recd');
Route::post('karahivendor/recieve/{id}/detail/{receive_karahi_material_id}', [karhaiController::class, 'karahirecdetail'])->name('karahivendor.recdetail');
Route::post('karahivendor/detail/{id}', [karhaiController::class, 'karahiissuebyid'])->name('karahivendor.issue');
Route::get('karahivedor/receiving', [karhaiController::class, 'receiving'])->name('karahivendor.receiving');
Route::post('karahivedor/receiving/store', [karhaiController::class, 'receivingstore'])->name('karahivendor.receivingstore');
Route::get('/karahivedor/{id}/ledger', [karhaiController::class, 'ledger'])->name('karahivedor.ledger');
Route::post('/karahivedor/{id}/list', [karhaiController::class, 'ledgerlist'])->name('karahivedor.ledgerlist');
Route::get('/karahivedor/{id}/ledger/recieve/{recieve_id}', [karhaiController::class, 'recievebyid']);
Route::get('/karahivedor/{id}/ledger/payment/{paymentvoucher_id}', [karhaiController::class, 'paymentbyid']);
Route::get('/reckarahi/print/{id}', [karhaiController::class, 'print'])->name('rec.printsslip');
Route::get('/reckarahi/issue/{id}/print', [karhaiController::class, 'issueprint'])->name('rec.print');

// parties
Route::resource('parties', PartyController::class);
Route::post('/partieslisting', [PartyController::class, 'listing'])->name('parties.Listing');
Route::get('/party/{id}/ledger', [PartyController::class, 'ledger'])->name('party.ledger');
Route::post('/party/{id}/ledgerlist', [PartyController::class, 'ledgerlist'])->name('party.ledgerlist');
// raw material
Route::resource('raw_material', RawMaterialController::class);
Route::post('/rawmaterialListing', [RawMaterialController::class, 'listing'])->name('rawmaterialListing');
Route::post("/getAvailableQtyOfRawMaterial",[RawMaterialController::class, 'getAvailableQtyOfRawMaterial'])->name('getAvailableQtyOfRawMaterial');
//purchase
Route::resource('purchase', PurchaseController::class);
Route::post('/purchase/getUnits', [PurchaseController::class, 'getunits'])->name('getunits');
Route::get('/purchase/return/{id}', [PurchaseController::class, 'p_return_view'])->name('purchase.return');
Route::post('/purchaseListing', [PurchaseController::class, 'listing'])->name('purchaseListing');
Route::post('/purchasereturn/store', [PurchaseController::class, 'return_store'])->name('return.store');
Route::get('/rubber', [PurchaseController::class, 'rubber'])->name('rubber.purchase');
Route::post('/rubber/store', [PurchaseController::class, 'rubberstore'])->name('rubber.store');

// finish product 
Route::resource('finishproduct', FinishProductController::class);
Route::get('/oldproduct', [FinishProductController::class, 'oldview'])->name('old.view');
Route::post('/oldproductstore', [FinishProductController::class, 'oldstore'])->name('oldproduct.store');
Route::post('/reprcessproduct', [FinishProductController::class, 'reprcessproduct'])->name('reprcess.product');
Route::post('/finishproductlisting', [FinishProductController::class, 'listing'])->name('finishproductlisting');
Route::get('/finishproduct/{id}/detail', [FinishProductController::class, 'detail'])->name('finishproduct.detail');
Route::post('/finishproduct/{id}detaillist', [FinishProductController::class, 'detaillist'])->name('finishproduct.detaillist');
Route::post('/get-product-quantity', [FinishProductController::class, 'getProductQuantity'])->name('get-product-quantity');
Route::get('/oldproductlist', [FinishProductController::class, 'oldlistview'])->name('old.listview');
Route::get('finishproduct/{id}/edit', [FinishProductController::class, 'edit'])->name('finishproduct.edit');
Route::put('finishproduct/{id}', [FinishProductController::class, 'update'])->name('finishproduct.update');
// issue
Route::resource('issue', IssueController::class);
Route::get('/issue/{ids}/printss', [IssueController::class, 'printissuess'])->name('pissueprintsudden');
Route::post('/issue/getfinishproductqty', [IssueController::class, 'getfinishproductqty'])->name('getfinishproductqty');
Route::post('/issue/getfinishproductqty', [IssueController::class, 'getfinishproductqty'])->name('getfinishproductqty');
Route::post('/issueListing', [IssueController::class, 'listing'])->name('issueListing');
Route::get('/getdetail/{id}', [IssueController::class, 'getdetail'])->name('issue.detail');
Route::post('/getdetail/{id}/detail', [IssueController::class, 'listdetail']);
Route::post('/issuematerial', [IssueController::class, 'addrawmaterial'])->name('issue.material');
Route::post('/issuerecived', [IssueController::class, 'rceqty'])->name('issue.recived');
Route::get('/issueprint/{id}', [IssueController::class, 'printindividual'])->name('issue.print');
Route::get('/issue/single/{id}/{timestamp}', [IssueController::class, 'printsingle'])->name('issue.single');
Route::get('/print/receive-product/{issue_material_id}/{quantity}', [IssueController::class, 'printReceiveProduct'])->name('print.receive.product');
Route::get('/print/{issue_material_id}/table', [IssueController::class, 'printReceiveProducttable'])->name('print.receive.producttable');
Route::post('/issue/{id}/delete', [IssueController::class, 'destroy'])->name('issue.destroy');

// stock
Route::get('stock', [stockcontroller::class, 'show'])->name('stock');
Route::post('/stockdetail', [stockcontroller::class, 'sdetail'])->name('stockdetail');
Route::get('/oldstock', [stockcontroller::class, 'oldstock'])->name('oldstock.view');
Route::post('/oldstockdetail', [stockcontroller::class, 'oldsdetail'])->name('oldsdetail');

//sell
Route::resource('sell', SellController::class);
Route::post('/sellListing', [SellController::class, 'listing'])->name('sellListing');
Route::get('/sellcreate', [SellController::class, 'createlist'])->name('sell.createlist');
Route::post('/sellcreate', [SellController::class, 'createlist'])->name('sell.createlist');
Route::get('/sellcreate/detail/{id}', [SellController::class, 'createlistdetail'])->name('sell.createlistdetail');
Route::post('/sellcreate/detail/{id}', [SellController::class, 'createlistdetail'])->name('sell.createlistdetail');
Route::get('/sell/create/{id}', [SellController::class, 'newsell'])->name('sell.nwwwww');
Route::post('/newsellstore', [SellController::class, 'newsellstore'])->name('issue.newsellstore');
Route::get('/sell/detail/{id}', [SellController::class, 'sellorderdetail'])->name('sell.orderdetail');
Route::post('/sell/detail/{id}', [SellController::class, 'sellorderdetail'])->name('sell.orderdetaillist');
Route::post('/sell/builty', [SellController::class, 'builty'])->name('sell.builty');
Route::get('/sell/{sell_id}/prints', [SellController::class, 'print'])->name('sell.prints');
// expense 
Route::resource('expense', ExpenseController::class);
Route::post('/expenseListing', [ExpenseController::class, 'listing'])->name('expenseListing');
// FORECASTING 
Route::resource('forcasting', ForecastingController::class);
Route::post('/getforcasting', [ForecastingController::class, 'getforcasting'])->name('getforcasting');
// setup 
// ----- unit
Route::resource('units', unitController::class);
Route::post('/unitListing', [unitController::class, 'listing'])->name('unitListing');
// ---- expense category 
Route::resource('expensecategory', ExpenseCategoryController::class);
Route::post('/expensecatListing', [ExpenseCategoryController::class, 'listing'])->name('expensecatListing');
//machine
Route::resource('machine', machineController::class);
Route::post('machines/list', [machineController::class, 'list'])->name('machines.list');
// design 
Route::resource('design', designController::class);
Route::post('design/list', [designController::class, 'list'])->name('design.list');
// packing
Route::resource('packing', packing::class);
Route::post('packing/list', [packing::class, 'list'])->name('packing.list');

// account 
// /banks
Route::resource('banks', BankController::class);
Route::post('api/banks', [BankController::class, 'listing'])->name('bank.list');
Route::get('/banks/{id}/ledger', [BankController::class, 'ledger'])->name('bank.ledger');
Route::post('/banks/{id}/ledgerlist', [BankController::class, 'ledgerlist'])->name('bank.ledgerlist');
// payment
Route::resource('payment', paymentController::class);
Route::get('/get-payables/{type}', [paymentController::class, 'getPayables'])->name('get.payables');
Route::post('payment/{id}/delete', [paymentController::class, 'detete'])->name('payment.delete');
Route::post('/payment/list', [paymentController::class, 'listing'])->name('payment.list');
// recienve
Route::resource('recieve', ReceiptVoucherController::class);
Route::post('/recieve/list', [ReceiptVoucherController::class, 'listing'])->name('recieve.list');
// empployeee 
Route::resource('employee', employeeController::class);
Route::post('/employee/list', [employeeController::class, 'listing'])->name('employee.list');
Route::get('/employee/{id}/ledger', [employeeController::class, 'ledger'])->name('employee.ledger');
Route::post('/employee/{id}/ledgerlist', [employeeController::class, 'ledgerlist'])->name('employee.ledgerlist');
// attendence 
Route::resource('attendence', AttendenceController::class);
Route::post('/attendance/mark', [AttendenceController::class, 'markAttendance'])->name('attendance.mark');
// payroll 
Route::resource('payroll', payrollController::class);
Route::post('/payroll/generate', [payrollController::class, 'generateSalaries'])->name('payroll.generate');
Route::post('/salary/list', [payrollController::class, 'salarylisting'])->name('salary.list');
Route::get('/salary/detail/{date}', [payrollController::class, 'salarydetail'])->name('salary.detail');
Route::post('/salary/detail/{date}/list', [payrollController::class, 'salarydetaillist'])->name('salary.detaillist');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::resource('home', homeController::class);

Route::get('/', [homeController::class, 'home'])->name('home');

//roznamcha
Route::resource('roznamcha', roznamchaController::class);
Route::get('roznamchaa', [roznamchaController::class, 'generate'])->name('roznamcha.generate');



// reports 
Route::resource('report', reportsController::class);
Route::get('profitandloss', [reportsController::class, 'profit_and_loss'])->name('profit.loss');
Route::get('summaryreport', [reportsController::class, 'summaryreport'])->name('summaryreport');
Route::get('summaryreportresult', [reportsController::class, 'summaryreportgenerate'])->name('generate.summary');


