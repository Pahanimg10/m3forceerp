<?php

use App\Http\Controllers\CContactTypeController;
use App\Http\Controllers\CGroupController;
use App\Http\Controllers\CollectionManagerController;
use App\Http\Controllers\CollectionPersonController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ComplainTypeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CostSheetController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\CTaxTypeController;
use App\Http\Controllers\CustomerComplainController;
use App\Http\Controllers\CustomerStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\GoodRequestController;
use App\Http\Controllers\IBusinessTypeController;
use App\Http\Controllers\IClientTypeController;
use App\Http\Controllers\IInquiryTypeController;
use App\Http\Controllers\IModeOfInquiryController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InstallationRateController;
use App\Http\Controllers\InstallationSheetController;
use App\Http\Controllers\InventoryIssueController;
use App\Http\Controllers\InventoryRegisterController;
use App\Http\Controllers\InventoryReturnController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemIssueController;
use App\Http\Controllers\ItemReceiveController;
use App\Http\Controllers\JobAttendanceController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobDoneCustomerController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MainItemCategoryController;
use App\Http\Controllers\MonitoringCustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonResponsibleController;
use App\Http\Controllers\PettyCashIssueController;
use App\Http\Controllers\PettyCashReturnController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesTeamController;
use App\Http\Controllers\SubItemCategoryController;
use App\Http\Controllers\TechnicalTeamController;
use App\Http\Controllers\TechResponseController;
use App\Http\Controllers\TechResponseCustomerController;
use App\Http\Controllers\TechResponseFaultController;
use App\Http\Controllers\TechResponseInstallationSheetController;
use App\Http\Controllers\TechResponseJobCardController;
use App\Http\Controllers\TechResponseQuotationController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('login');
});

//Route::get('test/update_item_details', [TestController::class, 'update_item_details']);
//Route::get('test/price_update', [TestController::class, 'price_update']);
//Route::get('test/add_hikvision_items', [TestController::class, 'add_hikvision_items']);
//Route::get('test/add_items', [TestController::class, 'add_items']);
//Route::get('test/add_cctv_item', [TestController::class, 'add_cctv_item']);
//Route::get('test/add_ids_item', [TestController::class, 'add_ids_item']);
//Route::get('test/add_installation_item', [TestController::class, 'add_installation_item']);
//Route::get('test/add_contact', [TestController::class, 'add_contact']);
//
//Route::get('cron/generate_monitoring_invoice', [CronController::class, 'generate_monitoring_invoice']);

Route::get('/validateUsername', [MainController::class, 'validateUsername']);
Route::get('/checkUsername', [MainController::class, 'checkUsername']);
Route::get('/validateLogin', [MainController::class, 'validateLogin']);
Route::get('/home', [MainController::class, 'home']);
Route::get('/logout', [MainController::class, 'logout']);

Route::get('/find_inventory_issue', [DataController::class, 'find_inventory_issue']);
Route::get('/get_inventory_issues', [DataController::class, 'get_inventory_issues']);
Route::get('/find_inventory_code', [DataController::class, 'find_inventory_code']);
Route::get('/get_inventory_codes', [DataController::class, 'get_inventory_codes']);
Route::get('/get_inventory_data', [DataController::class, 'get_inventory_data']);
Route::get('/find_petty_cash_issue_no', [DataController::class, 'find_petty_cash_issue_no']);
Route::get('/get_petty_cash_issue_nos', [DataController::class, 'get_petty_cash_issue_nos']);
Route::get('/validate_tech_response_installation_quantity', [DataController::class, 'validate_tech_response_installation_quantity']);
Route::get('/get_filter_data', [DataController::class, 'get_filter_data']);
Route::get('/get_driving_types', [DataController::class, 'get_driving_types']);
Route::get('/get_fault_types', [DataController::class, 'get_fault_types']);
Route::get('/find_item_issue', [DataController::class, 'find_item_issue']);
Route::get('/get_item_issues', [DataController::class, 'get_item_issues']);
Route::get('/validate_job_no', [DataController::class, 'validate_job_no']);
Route::get('/find_tech_response_no', [DataController::class, 'find_tech_response_no']);
Route::get('/get_tech_response_nos', [DataController::class, 'get_tech_response_nos']);
Route::get('/find_job_no', [DataController::class, 'find_job_no']);
Route::get('/get_job_nos', [DataController::class, 'get_job_nos']);
Route::get('/find_technical_team', [DataController::class, 'find_technical_team']);
Route::get('/get_technical_teams', [DataController::class, 'get_technical_teams']);
Route::get('/get_payment_modes', [DataController::class, 'get_payment_modes']);
Route::get('/find_purchase_order_no', [DataController::class, 'find_purchase_order_no']);
Route::get('/get_purchase_order_nos', [DataController::class, 'get_purchase_order_nos']);
Route::get('/validate_purchase_order_no', [DataController::class, 'validate_purchase_order_no']);
Route::get('/validate_good_request_no', [DataController::class, 'validate_good_request_no']);
Route::get('/validate_technical_team_name', [DataController::class, 'validate_technical_team_name']);
Route::get('/validate_contact_name', [DataController::class, 'validate_contact_name']);
Route::get('/find_good_request_no', [DataController::class, 'find_good_request_no']);
Route::get('/get_good_request_nos', [DataController::class, 'get_good_request_nos']);
Route::get('/validate_installation_quantity', [DataController::class, 'validate_installation_quantity']);
Route::get('/get_installation_rates', [DataController::class, 'get_installation_rates']);
Route::get('/find_item_code', [DataController::class, 'find_item_code']);
Route::get('/get_item_codes', [DataController::class, 'get_item_codes']);
Route::get('/find_item_name', [DataController::class, 'find_item_name']);
Route::get('/get_item_names', [DataController::class, 'get_item_names']);
Route::get('/validate_item_name', [DataController::class, 'validate_item_name']);
Route::get('/validate_item_code', [DataController::class, 'validate_item_code']);
Route::get('/get_document_types', [DataController::class, 'get_document_types']);
Route::post('/find_customer', [DataController::class, 'find_customer']);
Route::post('/get_customers', [DataController::class, 'get_customers']);
Route::get('/contact_contact_types', [DataController::class, 'contact_contact_types']);
Route::get('/item_categories', [DataController::class, 'item_categories']);

Route::get('/dashboard/get_line_data', [DashboardController::class, 'get_line_data']);
Route::get('/dashboard/get_sales_target_data', [DashboardController::class, 'get_sales_target_data']);
Route::get('/dashboard/get_donut_data', [DashboardController::class, 'get_donut_data']);
Route::get('/dashboard/get_bar_data', [DashboardController::class, 'get_bar_data']);
Route::get('/dashboard/change_password', [DashboardController::class, 'change_password']);
Route::get('/dashboard/job_positions_list', [DashboardController::class, 'job_positions_list']);
Route::post('/dashboard/image_upload', [DashboardController::class, 'image_upload']);
Route::get('/dashboard/find_user', [DashboardController::class, 'find_user']);
Route::get('/dashboard/user_profile', [DashboardController::class, 'user_profile']);
Route::post('/dashboard/update_user_profile', [DashboardController::class, 'update_user_profile']);
Route::get('/dashboard/validate_username', [DashboardController::class, 'validate_username']);
Route::get('/dashboard/validate_old_password', [DashboardController::class, 'validate_old_password']);
Route::get('/dashboard/update_new_password', [DashboardController::class, 'update_new_password']);

Route::get('/user/users_list', [UserController::class, 'users_list']);
Route::get('/user/job_positions_list', [UserController::class, 'job_positions_list']);
Route::get('/user/validate_username', [UserController::class, 'validate_username']);
Route::get('/user/group_list', [UserController::class, 'group_list']);
Route::post('/user/image_upload', [UserController::class, 'image_upload']);
Route::get('/user/find_user', [UserController::class, 'find_user']);
Route::resource('user', UserController::class);

Route::get('/job_position/job_position_list', [JobPositionController::class, 'job_position_list']);
Route::get('/job_position/validate_job_position', [JobPositionController::class, 'validate_job_position']);
Route::get('/job_position/find_job_position', [JobPositionController::class, 'find_job_position']);
Route::resource('job_position', JobPositionController::class);

Route::get('/company/company_list', [CompanyController::class, 'company_list']);
Route::get('/company/validate_company', [CompanyController::class, 'validate_company']);
Route::post('/company/image_upload', [CompanyController::class, 'image_upload']);
Route::get('/company/find_company', [CompanyController::class, 'find_company']);
Route::resource('company', CompanyController::class);

Route::get('/i_mode_of_inquiry/i_mode_of_inquiry_list', [IModeOfInquiryController::class, 'i_mode_of_inquiry_list']);
Route::get('/i_mode_of_inquiry/validate_i_mode_of_inquiry', [IModeOfInquiryController::class, 'validate_i_mode_of_inquiry']);
Route::get('/i_mode_of_inquiry/find_i_mode_of_inquiry', [IModeOfInquiryController::class, 'find_i_mode_of_inquiry']);
Route::resource('i_mode_of_inquiry', IModeOfInquiryController::class);

Route::get('/i_inquiry_type/i_inquiry_type_list', [IInquiryTypeController::class, 'i_inquiry_type_list']);
Route::get('/i_inquiry_type/validate_i_inquiry_type', [IInquiryTypeController::class, 'validate_i_inquiry_type']);
Route::get('/i_inquiry_type/find_i_inquiry_type', [IInquiryTypeController::class, 'find_i_inquiry_type']);
Route::resource('i_inquiry_type', IInquiryTypeController::class);

Route::get('/sales_team/sales_team_list', [SalesTeamController::class, 'sales_team_list']);
Route::get('/sales_team/validate_sales_team', [SalesTeamController::class, 'validate_sales_team']);
Route::get('/sales_team/find_sales_team', [SalesTeamController::class, 'find_sales_team']);
Route::resource('sales_team', SalesTeamController::class);

Route::get('/technical_team/technical_team_list', [TechnicalTeamController::class, 'technical_team_list']);
Route::get('/technical_team/validate_technical_team', [TechnicalTeamController::class, 'validate_technical_team']);
Route::get('/technical_team/find_technical_team', [TechnicalTeamController::class, 'find_technical_team']);
Route::resource('technical_team', TechnicalTeamController::class);

Route::get('/i_client_type/i_client_type_list', [IClientTypeController::class, 'i_client_type_list']);
Route::get('/i_client_type/validate_i_client_type', [IClientTypeController::class, 'validate_i_client_type']);
Route::get('/i_client_type/find_i_client_type', [IClientTypeController::class, 'find_i_client_type']);
Route::resource('i_client_type', IClientTypeController::class);

Route::get('/i_business_type/i_business_type_list', [IBusinessTypeController::class, 'i_business_type_list']);
Route::get('/i_business_type/validate_i_business_type', [IBusinessTypeController::class, 'validate_i_business_type']);
Route::get('/i_business_type/find_i_business_type', [IBusinessTypeController::class, 'find_i_business_type']);
Route::resource('i_business_type', IBusinessTypeController::class);

Route::get('/c_contact_type/c_contact_type_list', [CContactTypeController::class, 'c_contact_type_list']);
Route::get('/c_contact_type/validate_c_contact_type', [CContactTypeController::class, 'validate_c_contact_type']);
Route::get('/c_contact_type/find_c_contact_type', [CContactTypeController::class, 'find_c_contact_type']);
Route::resource('c_contact_type', CContactTypeController::class);

Route::get('/c_tax_type/c_tax_type_list', [CTaxTypeController::class, 'c_tax_type_list']);
Route::get('/c_tax_type/validate_c_tax_type', [CTaxTypeController::class, 'validate_c_tax_type']);
Route::get('/c_tax_type/find_c_tax_type', [CTaxTypeController::class, 'find_c_tax_type']);
Route::resource('c_tax_type', CTaxTypeController::class);

Route::get('/region/region_list', [RegionController::class, 'region_list']);
Route::get('/region/validate_region', [RegionController::class, 'validate_region']);
Route::get('/region/find_region', [RegionController::class, 'find_region']);
Route::resource('region', RegionController::class);

Route::get('/complain_type/complain_type_list', [ComplainTypeController::class, 'complain_type_list']);
Route::get('/complain_type/validate_complain_type', [ComplainTypeController::class, 'validate_complain_type']);
Route::get('/complain_type/find_complain_type', [ComplainTypeController::class, 'find_complain_type']);
Route::resource('complain_type', ComplainTypeController::class);

Route::get('/person_responsible/person_responsible_list', [PersonResponsibleController::class, 'person_responsible_list']);
Route::get('/person_responsible/validate_person_responsible', [PersonResponsibleController::class, 'validate_person_responsible']);
Route::get('/person_responsible/find_person_responsible', [PersonResponsibleController::class, 'find_person_responsible']);
Route::resource('person_responsible', PersonResponsibleController::class);

Route::get('/contact/get_data', [ContactController::class, 'get_data']);
Route::get('/contact/validate_contact', [ContactController::class, 'validate_contact']);
Route::get('/contact/find_contact', [ContactController::class, 'find_contact']);
Route::get('/contact/add_new', [ContactController::class, 'add_new']);
Route::get('/contact/contact_list', [ContactController::class, 'contact_list']);
Route::resource('contact', ContactController::class);

Route::get('/c_group/get_data', [CGroupController::class, 'get_data']);
Route::get('/c_group/c_group_list', [CGroupController::class, 'c_group_list']);
Route::get('/c_group/validate_c_group', [CGroupController::class, 'validate_c_group']);
Route::get('/c_group/find_c_group', [CGroupController::class, 'find_c_group']);
Route::resource('c_group', CGroupController::class);

Route::get('/main_item_category/main_item_category_list', [MainItemCategoryController::class, 'main_item_category_list']);
Route::get('/main_item_category/validate_main_item_category', [MainItemCategoryController::class, 'validate_main_item_category']);
Route::get('/main_item_category/find_main_item_category', [MainItemCategoryController::class, 'find_main_item_category']);
Route::resource('main_item_category', MainItemCategoryController::class);

Route::get('/sub_item_category/sub_item_category_list', [SubItemCategoryController::class, 'sub_item_category_list']);
Route::get('/sub_item_category/validate_sub_item_category', [SubItemCategoryController::class, 'validate_sub_item_category']);
Route::get('/sub_item_category/find_sub_item_category', [SubItemCategoryController::class, 'find_sub_item_category']);
Route::resource('sub_item_category', SubItemCategoryController::class);

Route::get('/unit_type/unit_type_list', [UnitTypeController::class, 'unit_type_list']);
Route::get('/unit_type/validate_unit_type', [UnitTypeController::class, 'validate_unit_type']);
Route::get('/unit_type/find_unit_type', [UnitTypeController::class, 'find_unit_type']);
Route::resource('unit_type', UnitTypeController::class);

Route::get('/document_type/document_type_list', [DocumentTypeController::class, 'document_type_list']);
Route::get('/document_type/validate_document_type', [DocumentTypeController::class, 'validate_document_type']);
Route::get('/document_type/find_document_type', [DocumentTypeController::class, 'find_document_type']);
Route::resource('document_type', DocumentTypeController::class);

Route::get('/installation_rate/installation_rate_list', [InstallationRateController::class, 'installation_rate_list']);
Route::get('/installation_rate/validate_installation_rate', [InstallationRateController::class, 'validate_installation_rate']);
Route::get('/installation_rate/find_installation_rate', [InstallationRateController::class, 'find_installation_rate']);
Route::resource('installation_rate', InstallationRateController::class);

Route::get('/tech_response_fault/tech_response_fault_list', [TechResponseFaultController::class, 'tech_response_fault_list']);
Route::get('/tech_response_fault/validate_tech_response_fault', [TechResponseFaultController::class, 'validate_tech_response_fault']);
Route::get('/tech_response_fault/find_tech_response_fault', [TechResponseFaultController::class, 'find_tech_response_fault']);
Route::resource('tech_response_fault', TechResponseFaultController::class);

Route::get('/item/get_data', [ItemController::class, 'get_data']);
Route::get('/item/validate_model_no', [ItemController::class, 'validate_model_no']);
Route::get('/item/validate_item', [ItemController::class, 'validate_item']);
Route::get('/item/find_item', [ItemController::class, 'find_item']);
Route::get('/item/add_new', [ItemController::class, 'add_new']);
Route::get('/item/item_list', [ItemController::class, 'item_list']);
Route::resource('item', ItemController::class);

Route::get('/collection_manager/collection_manager_list', [CollectionManagerController::class, 'collection_manager_list']);
Route::get('/collection_manager/validate_collection_manager', [CollectionManagerController::class, 'validate_collection_manager']);
Route::get('/collection_manager/find_collection_manager', [CollectionManagerController::class, 'find_collection_manager']);
Route::resource('collection_manager', CollectionManagerController::class);

Route::get('/collection_person/collection_person_list', [CollectionPersonController::class, 'collection_person_list']);
Route::get('/collection_person/validate_collection_person', [CollectionPersonController::class, 'validate_collection_person']);
Route::get('/collection_person/find_collection_person', [CollectionPersonController::class, 'find_collection_person']);
Route::resource('collection_person', CollectionPersonController::class);

Route::get('inquiry/quotation', [InquiryController::class, 'quotation']);
Route::get('inquiry/installation_sheet', [InquiryController::class, 'installation_sheet']);
Route::get('inquiry/cost_sheet', [InquiryController::class, 'cost_sheet']);
Route::get('inquiry/job_card', [InquiryController::class, 'job_card']);
Route::get('inquiry/upload_document_list', [InquiryController::class, 'upload_document_list']);
Route::post('inquiry/file_upload', [InquiryController::class, 'file_upload']);
Route::get('inquiry/validate_document_upload', [InquiryController::class, 'validate_document_upload']);
Route::get('inquiry/upload_documents', [InquiryController::class, 'upload_documents']);
Route::get('inquiry/ongoing_inquiry_list', [InquiryController::class, 'ongoing_inquiry_list']);
Route::get('inquiry/ongoing_inquiry', [InquiryController::class, 'ongoing_inquiry']);
Route::get('inquiry/inquiry_status_list', [InquiryController::class, 'inquiry_status_list']);
Route::get('inquiry/find_inquiry_status', [InquiryController::class, 'find_inquiry_status']);
Route::get('inquiry/validate_inquiry_status', [InquiryController::class, 'validate_inquiry_status']);
Route::get('inquiry/update_inquiry', [InquiryController::class, 'update_inquiry']);
Route::get('inquiry/new_inquiry_list', [InquiryController::class, 'new_inquiry_list']);
Route::get('inquiry/find_inquiry', [InquiryController::class, 'find_inquiry']);
Route::get('inquiry/get_data', [InquiryController::class, 'get_data']);
Route::get('inquiry/new_inquiry', [InquiryController::class, 'new_inquiry']);
Route::resource('inquiry', InquiryController::class);

Route::get('job/print_handover_documents', [JobController::class, 'print_handover_documents']);
Route::get('job/handover_documents', [JobController::class, 'handover_documents']);
Route::get('job/print_advance_receipt', [JobController::class, 'print_advance_receipt']);
Route::get('job/advance_receipt_list', [JobController::class, 'advance_receipt_list']);
Route::get('job/advance_receipt', [JobController::class, 'advance_receipt']);
Route::get('job/upload_documents', [JobController::class, 'upload_documents']);
Route::get('job/ongoing_job_list', [JobController::class, 'ongoing_job_list']);
Route::get('job/ongoing_job', [JobController::class, 'ongoing_job']);
Route::get('job/job_status_list', [JobController::class, 'job_status_list']);
Route::get('job/find_job_status', [JobController::class, 'find_job_status']);
Route::get('job/find_job', [JobController::class, 'find_job']);
Route::get('job/get_data', [JobController::class, 'get_data']);
Route::get('job/validate_job_status', [JobController::class, 'validate_job_status']);
Route::get('job/update_job', [JobController::class, 'update_job']);
Route::get('job/new_job_list', [JobController::class, 'new_job_list']);
Route::get('job/new_job', [JobController::class, 'new_job']);
Route::resource('job', JobController::class);

Route::get('job_card/print_job_card_no_price', [JobCardController::class, 'print_job_card_no_price']);
Route::get('job_card/print_job_card', [JobCardController::class, 'print_job_card']);
Route::get('job_card/job_card_detail_list', [JobCardController::class, 'job_card_detail_list']);
Route::get('job_card/find_job_card_detail', [JobCardController::class, 'find_job_card_detail']);
Route::get('job_card/find_job_card', [JobCardController::class, 'find_job_card']);
Route::get('job_card/add_new', [JobCardController::class, 'add_new']);
Route::get('job_card/job_card_list', [JobCardController::class, 'job_card_list']);
Route::resource('job_card', JobCardController::class);

Route::get('cost_sheet/get_data', [CostSheetController::class, 'get_data']);
Route::get('cost_sheet/print_cost_sheet', [CostSheetController::class, 'print_cost_sheet']);
Route::get('cost_sheet/find_cost_sheet', [CostSheetController::class, 'find_cost_sheet']);
Route::get('cost_sheet/add_new', [CostSheetController::class, 'add_new']);
Route::get('cost_sheet/cost_sheet_list', [CostSheetController::class, 'cost_sheet_list']);
Route::resource('cost_sheet', CostSheetController::class);

Route::get('quotation/print_file_quotation', [QuotationController::class, 'print_file_quotation']);
Route::get('quotation/print_quotation', [QuotationController::class, 'print_quotation']);
Route::post('quotation/revise_quotation', [QuotationController::class, 'revise_quotation']);
Route::get('quotation/confirm_quotation', [QuotationController::class, 'confirm_quotation']);
Route::post('quotation/preview_quotation', [QuotationController::class, 'preview_quotation']);
Route::get('quotation/discount_detail', [QuotationController::class, 'discount_detail']);
Route::get('quotation/find_quotation', [QuotationController::class, 'find_quotation']);
Route::get('quotation/add_new', [QuotationController::class, 'add_new']);
Route::get('quotation/quotation_list', [QuotationController::class, 'quotation_list']);
Route::get('quotation/get_data', [QuotationController::class, 'get_data']);
Route::resource('quotation', QuotationController::class);

Route::get('installation_sheet/print_installation_sheet_no_price', [InstallationSheetController::class, 'print_installation_sheet_no_price']);
Route::post('installation_sheet/approve_installation_sheet', [InstallationSheetController::class, 'approve_installation_sheet']);
Route::get('installation_sheet/get_authorize_data', [InstallationSheetController::class, 'get_authorize_data']);
Route::get('installation_sheet/print_installation_sheet', [InstallationSheetController::class, 'print_installation_sheet']);
Route::get('installation_sheet/post_installation_sheet', [InstallationSheetController::class, 'post_installation_sheet']);
Route::get('installation_sheet/installation_sheet_detail_list', [InstallationSheetController::class, 'installation_sheet_detail_list']);
Route::get('installation_sheet/find_installation_sheet_detail', [InstallationSheetController::class, 'find_installation_sheet_detail']);
Route::get('installation_sheet/find_installation_sheet', [InstallationSheetController::class, 'find_installation_sheet']);
Route::get('installation_sheet/add_new', [InstallationSheetController::class, 'add_new']);
Route::get('installation_sheet/installation_sheet_list', [InstallationSheetController::class, 'installation_sheet_list']);
Route::resource('installation_sheet', InstallationSheetController::class);

Route::get('good_request/print_good_request', [GoodRequestController::class, 'print_good_request']);
Route::get('good_request/post_good_request', [GoodRequestController::class, 'post_good_request']);
Route::get('good_request/get_details', [GoodRequestController::class, 'get_details']);
Route::get('good_request/find_good_request', [GoodRequestController::class, 'find_good_request']);
Route::get('good_request/add_new', [GoodRequestController::class, 'add_new']);
Route::get('good_request/good_request_list', [GoodRequestController::class, 'good_request_list']);
Route::resource('good_request', GoodRequestController::class);

Route::get('purchase_order/validate_item_name', [PurchaseOrderController::class, 'validate_item_name']);
Route::get('purchase_order/validate_item_code', [PurchaseOrderController::class, 'validate_item_code']);
Route::get('purchase_order/print_purchase_order_stock', [PurchaseOrderController::class, 'print_purchase_order_stock']);
Route::get('purchase_order/print_purchase_order', [PurchaseOrderController::class, 'print_purchase_order']);
Route::get('purchase_order/post_purchase_order', [PurchaseOrderController::class, 'post_purchase_order']);
Route::get('purchase_order/purchase_order_detail_list', [PurchaseOrderController::class, 'purchase_order_detail_list']);
Route::get('purchase_order/find_purchase_order_detail', [PurchaseOrderController::class, 'find_purchase_order_detail']);
Route::get('purchase_order/find_purchase_order', [PurchaseOrderController::class, 'find_purchase_order']);
Route::get('purchase_order/add_new', [PurchaseOrderController::class, 'add_new']);
Route::get('purchase_order/purchase_order_list', [PurchaseOrderController::class, 'purchase_order_list']);
Route::resource('purchase_order', PurchaseOrderController::class);

Route::get('good_receive/validate_item_rate', [GoodReceiveController::class, 'validate_item_rate']);
Route::get('good_receive/validate_item_quantity', [GoodReceiveController::class, 'validate_item_quantity']);
Route::get('good_receive/validate_item_name', [GoodReceiveController::class, 'validate_item_name']);
Route::get('good_receive/validate_item_code', [GoodReceiveController::class, 'validate_item_code']);
Route::get('good_receive/validate_purchase_order_no', [GoodReceiveController::class, 'validate_purchase_order_no']);
Route::get('good_receive/print_good_receive', [GoodReceiveController::class, 'print_good_receive']);
Route::get('good_receive/post_good_receive', [GoodReceiveController::class, 'post_good_receive']);
Route::get('good_receive/good_receive_detail_list', [GoodReceiveController::class, 'good_receive_detail_list']);
Route::get('good_receive/find_good_receive_detail', [GoodReceiveController::class, 'find_good_receive_detail']);
Route::get('good_receive/find_good_receive', [GoodReceiveController::class, 'find_good_receive']);
Route::get('good_receive/add_new', [GoodReceiveController::class, 'add_new']);
Route::get('good_receive/good_receive_list', [GoodReceiveController::class, 'good_receive_list']);
Route::resource('good_receive', GoodReceiveController::class);

Route::get('payment/print_credit_supplier_good_receive', [PaymentController::class, 'print_credit_supplier_good_receive']);
Route::get('payment/print_credit_supplier_payment', [PaymentController::class, 'print_credit_supplier_payment']);
Route::get('payment/find_credit_supplier_payment', [PaymentController::class, 'find_credit_supplier_payment']);
Route::get('payment/add_new_payment', [PaymentController::class, 'add_new_payment']);
Route::get('payment/get_credit_supplier_detail', [PaymentController::class, 'get_credit_supplier_detail']);
Route::get('payment/credit_supplier_detail', [PaymentController::class, 'credit_supplier_detail']);
Route::get('payment/credit_supplier_list', [PaymentController::class, 'credit_supplier_list']);
Route::get('payment/credit_supplier', [PaymentController::class, 'credit_supplier']);
Route::resource('payment', PaymentController::class);

Route::get('job_attendance/validate_job_no', [JobAttendanceController::class, 'validate_job_no']);
Route::get('job_attendance/find_job_attendance_detail', [JobAttendanceController::class, 'find_job_attendance_detail']);
Route::get('job_attendance/job_attendance_detail_list', [JobAttendanceController::class, 'job_attendance_detail_list']);
Route::get('job_attendance/print_tech_work_sheet', [JobAttendanceController::class, 'print_tech_work_sheet']);
Route::get('job_attendance/print_work_sheet', [JobAttendanceController::class, 'print_work_sheet']);
Route::get('job_attendance/add_new', [JobAttendanceController::class, 'add_new']);
Route::get('job_attendance/job_attendance_list', [JobAttendanceController::class, 'job_attendance_list']);
Route::resource('job_attendance', JobAttendanceController::class);

Route::get('job_done_customer/get_data', [JobDoneCustomerController::class, 'get_data']);
Route::get('job_done_customer/find_job_done_customer_payment', [JobDoneCustomerController::class, 'find_job_done_customer_payment']);
Route::get('job_done_customer/add_new_payment', [JobDoneCustomerController::class, 'add_new_payment']);
Route::get('job_done_customer/get_job_done_customer_detail', [JobDoneCustomerController::class, 'get_job_done_customer_detail']);
Route::get('job_done_customer/print_job_done_customer_payment', [JobDoneCustomerController::class, 'print_job_done_customer_payment']);
Route::get('job_done_customer/print_job_done_customer_invoice', [JobDoneCustomerController::class, 'print_job_done_customer_invoice']);
Route::get('job_done_customer/job_done_customer_detail', [JobDoneCustomerController::class, 'job_done_customer_detail']);
Route::get('job_done_customer/job_done_customer_list', [JobDoneCustomerController::class, 'job_done_customer_list']);
Route::resource('job_done_customer', JobDoneCustomerController::class);

Route::get('monitoring_customer/get_data', [MonitoringCustomerController::class, 'get_data']);
Route::get('monitoring_customer/find_monitoring_customer_payment', [MonitoringCustomerController::class, 'find_monitoring_customer_payment']);
Route::get('monitoring_customer/add_new_payment', [MonitoringCustomerController::class, 'add_new_payment']);
Route::get('monitoring_customer/get_monitoring_customer_detail', [MonitoringCustomerController::class, 'get_monitoring_customer_detail']);
Route::get('monitoring_customer/print_monitoring_customer_payment', [MonitoringCustomerController::class, 'print_monitoring_customer_payment']);
Route::get('monitoring_customer/print_monitoring_customer_invoice', [MonitoringCustomerController::class, 'print_monitoring_customer_invoice']);
Route::get('monitoring_customer/monitoring_customer_detail', [MonitoringCustomerController::class, 'monitoring_customer_detail']);
Route::get('monitoring_customer/monitoring_customer_list', [MonitoringCustomerController::class, 'monitoring_customer_list']);
Route::resource('monitoring_customer', MonitoringCustomerController::class);

Route::post('item_issue/bulk_item_issue', [ItemIssueController::class, 'bulk_item_issue']);
Route::get('item_issue/find_balance_items', [ItemIssueController::class, 'find_balance_items']);
Route::get('item_issue/find_serial_no', [ItemIssueController::class, 'find_serial_no']);
Route::get('item_issue/get_serial_nos', [ItemIssueController::class, 'get_serial_nos']);
Route::get('item_issue/get_data', [ItemIssueController::class, 'get_data']);
Route::get('item_issue/validate_serial_no', [ItemIssueController::class, 'validate_serial_no']);
Route::get('item_issue/validate_item_quantity', [ItemIssueController::class, 'validate_item_quantity']);
Route::get('item_issue/validate_item_name', [ItemIssueController::class, 'validate_item_name']);
Route::get('item_issue/validate_item_code', [ItemIssueController::class, 'validate_item_code']);
Route::get('item_issue/validate_document_no', [ItemIssueController::class, 'validate_document_no']);
Route::get('item_issue/print_item_issue', [ItemIssueController::class, 'print_item_issue']);
Route::get('item_issue/post_item_issue', [ItemIssueController::class, 'post_item_issue']);
Route::get('item_issue/item_issue_detail_list', [ItemIssueController::class, 'item_issue_detail_list']);
Route::get('item_issue/find_item_issue_detail', [ItemIssueController::class, 'find_item_issue_detail']);
Route::get('item_issue/find_item_issue', [ItemIssueController::class, 'find_item_issue']);
Route::get('item_issue/add_new', [ItemIssueController::class, 'add_new']);
Route::get('item_issue/item_issue_list', [ItemIssueController::class, 'item_issue_list']);
Route::resource('item_issue', ItemIssueController::class);

Route::get('item_receive/find_serial_no', [ItemReceiveController::class, 'find_serial_no']);
Route::get('item_receive/get_serial_nos', [ItemReceiveController::class, 'get_serial_nos']);
Route::get('item_receive/get_data', [ItemReceiveController::class, 'get_data']);
Route::get('item_receive/validate_serial_no', [ItemReceiveController::class, 'validate_serial_no']);
Route::get('item_receive/validate_item_quantity', [ItemReceiveController::class, 'validate_item_quantity']);
Route::get('item_receive/validate_item_name', [ItemReceiveController::class, 'validate_item_name']);
Route::get('item_receive/validate_item_code', [ItemReceiveController::class, 'validate_item_code']);
Route::get('item_receive/validate_item_issue_no', [ItemReceiveController::class, 'validate_item_issue_no']);
Route::get('item_receive/print_item_receive', [ItemReceiveController::class, 'print_item_receive']);
Route::get('item_receive/post_item_receive', [ItemReceiveController::class, 'post_item_receive']);
Route::get('item_receive/item_receive_detail_list', [ItemReceiveController::class, 'item_receive_detail_list']);
Route::get('item_receive/find_item_receive_detail', [ItemReceiveController::class, 'find_item_receive_detail']);
Route::get('item_receive/find_item_receive', [ItemReceiveController::class, 'find_item_receive']);
Route::get('item_receive/add_new', [ItemReceiveController::class, 'add_new']);
Route::get('item_receive/item_receive_list', [ItemReceiveController::class, 'item_receive_list']);
Route::resource('item_receive', ItemReceiveController::class);

Route::get('repair/print_repair', [RepairController::class, 'print_repair']);
Route::get('repair/repair_status_list', [RepairController::class, 'repair_status_list']);
Route::get('repair/find_repair_status', [RepairController::class, 'find_repair_status']);
Route::get('repair/validate_repair_status', [RepairController::class, 'validate_repair_status']);
Route::get('repair/find_item_name', [RepairController::class, 'find_item_name']);
Route::get('repair/get_item_names', [RepairController::class, 'get_item_names']);
Route::get('repair/find_item_code', [RepairController::class, 'find_item_code']);
Route::get('repair/get_item_codes', [RepairController::class, 'get_item_codes']);
Route::get('repair/find_tech_response_no', [RepairController::class, 'find_tech_response_no']);
Route::get('repair/get_tech_response_nos', [RepairController::class, 'get_tech_response_nos']);
Route::get('repair/find_job_no', [RepairController::class, 'find_job_no']);
Route::get('repair/get_job_nos', [RepairController::class, 'get_job_nos']);
Route::get('repair/find_repair', [RepairController::class, 'find_repair']);
Route::get('repair/update_status', [RepairController::class, 'update_status']);
Route::get('repair/add_new', [RepairController::class, 'add_new']);
Route::get('repair/repair_list', [RepairController::class, 'repair_list']);
Route::get('repair/get_data', [RepairController::class, 'get_data']);
Route::resource('repair', RepairController::class);

Route::get('tech_response/get_issed_items', [TechResponseController::class, 'get_issed_items']);
Route::get('tech_response/tech_response_status_list', [TechResponseController::class, 'tech_response_status_list']);
Route::get('tech_response/find_tech_response_status', [TechResponseController::class, 'find_tech_response_status']);
Route::get('tech_response/get_data', [TechResponseController::class, 'get_data']);
Route::get('tech_response/validate_tech_response_status', [TechResponseController::class, 'validate_tech_response_status']);
Route::get('tech_response/ongoing_tech_response_list', [TechResponseController::class, 'ongoing_tech_response_list']);
Route::get('tech_response/tech_response_quotation', [TechResponseController::class, 'tech_response_quotation']);
Route::get('tech_response/tech_response_installation_sheet', [TechResponseController::class, 'tech_response_installation_sheet']);
Route::get('tech_response/tech_response_job_card', [TechResponseController::class, 'tech_response_job_card']);
Route::get('tech_response/update_tech_response', [TechResponseController::class, 'update_tech_response']);
Route::get('tech_response/ongoing_tech_response', [TechResponseController::class, 'ongoing_tech_response']);
Route::get('tech_response/validate_fault_type', [TechResponseController::class, 'validate_fault_type']);
Route::get('tech_response/find_tech_response', [TechResponseController::class, 'find_tech_response']);
Route::get('tech_response/add_new_tech_response', [TechResponseController::class, 'add_new_tech_response']);
Route::get('tech_response/validate_customer_name', [TechResponseController::class, 'validate_customer_name']);
Route::get('tech_response/add_new_contact', [TechResponseController::class, 'add_new_contact']);
Route::get('tech_response/new_fault', [TechResponseController::class, 'new_fault']);
Route::resource('tech_response', TechResponseController::class);

Route::post('tech_response_job_card/approve_tech_response_items', [TechResponseJobCardController::class, 'approve_tech_response_items']);
Route::get('tech_response_job_card/get_authorize_data', [TechResponseJobCardController::class, 'get_authorize_data']);
Route::get('tech_response_job_card/post_tech_response_job_card', [TechResponseJobCardController::class, 'post_tech_response_job_card']);
Route::get('tech_response_job_card/print_tech_response_job_card', [TechResponseJobCardController::class, 'print_tech_response_job_card']);
Route::get('tech_response_job_card/tech_response_job_card_detail_list', [TechResponseJobCardController::class, 'tech_response_job_card_detail_list']);
Route::get('tech_response_job_card/find_tech_response_job_card_detail', [TechResponseJobCardController::class, 'find_tech_response_job_card_detail']);
Route::get('tech_response_job_card/find_tech_response_job_card', [TechResponseJobCardController::class, 'find_tech_response_job_card']);
Route::get('tech_response_job_card/add_new', [TechResponseJobCardController::class, 'add_new']);
Route::get('tech_response_job_card/tech_response_job_card_list', [TechResponseJobCardController::class, 'tech_response_job_card_list']);
Route::resource('tech_response_job_card', TechResponseJobCardController::class);

Route::get('tech_response_installation_sheet/post_tech_response_installation_sheet', [TechResponseInstallationSheetController::class, 'post_tech_response_installation_sheet']);
Route::get('tech_response_installation_sheet/print_tech_response_installation_sheet', [TechResponseInstallationSheetController::class, 'print_tech_response_installation_sheet']);
Route::get('tech_response_installation_sheet/tech_response_installation_sheet_detail_list', [TechResponseInstallationSheetController::class, 'tech_response_installation_sheet_detail_list']);
Route::get('tech_response_installation_sheet/find_tech_response_installation_sheet_detail', [TechResponseInstallationSheetController::class, 'find_tech_response_installation_sheet_detail']);
Route::get('tech_response_installation_sheet/find_tech_response_installation_sheet', [TechResponseInstallationSheetController::class, 'find_tech_response_installation_sheet']);
Route::get('tech_response_installation_sheet/add_new', [TechResponseInstallationSheetController::class, 'add_new']);
Route::get('tech_response_installation_sheet/tech_response_installation_sheet_list', [TechResponseInstallationSheetController::class, 'tech_response_installation_sheet_list']);
Route::resource('tech_response_installation_sheet', TechResponseInstallationSheetController::class);

Route::get('tech_response_quotation/print_tech_response_quotation', [TechResponseQuotationController::class, 'print_tech_response_quotation']);
Route::get('tech_response_quotation/revise_tech_response_quotation', [TechResponseQuotationController::class, 'revise_tech_response_quotation']);
Route::get('tech_response_quotation/confirm_tech_response_quotation', [TechResponseQuotationController::class, 'confirm_tech_response_quotation']);
Route::post('tech_response_quotation/preview_tech_response_quotation', [TechResponseQuotationController::class, 'preview_tech_response_quotation']);
Route::get('tech_response_quotation/discount_detail', [TechResponseQuotationController::class, 'discount_detail']);
Route::get('tech_response_quotation/find_tech_response_quotation', [TechResponseQuotationController::class, 'find_tech_response_quotation']);
Route::get('tech_response_quotation/add_new', [TechResponseQuotationController::class, 'add_new']);
Route::get('tech_response_quotation/tech_response_quotation_list', [TechResponseQuotationController::class, 'tech_response_quotation_list']);
Route::get('tech_response_quotation/get_data', [TechResponseQuotationController::class, 'get_data']);
Route::resource('tech_response_quotation', TechResponseQuotationController::class);

Route::get('tech_response_customer/get_data', [TechResponseCustomerController::class, 'get_data']);
Route::get('tech_response_customer/find_tech_response_customer_payment', [TechResponseCustomerController::class, 'find_tech_response_customer_payment']);
Route::get('tech_response_customer/add_new_payment', [TechResponseCustomerController::class, 'add_new_payment']);
Route::get('tech_response_customer/get_tech_response_customer_detail', [TechResponseCustomerController::class, 'get_tech_response_customer_detail']);
Route::get('tech_response_customer/print_tech_response_customer_payment', [TechResponseCustomerController::class, 'print_tech_response_customer_payment']);
Route::get('tech_response_customer/print_tech_response_customer_invoice', [TechResponseCustomerController::class, 'print_tech_response_customer_invoice']);
Route::get('tech_response_customer/tech_response_customer_detail', [TechResponseCustomerController::class, 'tech_response_customer_detail']);
Route::get('tech_response_customer/tech_response_customer_list', [TechResponseCustomerController::class, 'tech_response_customer_list']);
Route::resource('tech_response_customer', TechResponseCustomerController::class);

Route::get('customer_status/actual_expenses_list', [CustomerStatusController::class, 'actual_expenses_list']);
Route::get('customer_status/find_expenses', [CustomerStatusController::class, 'find_expenses']);
Route::get('customer_status/get_data', [CustomerStatusController::class, 'get_data']);
Route::get('customer_status/print_lost_profit', [CustomerStatusController::class, 'print_lost_profit']);
Route::get('customer_status/print_item_issue_balance', [CustomerStatusController::class, 'print_item_issue_balance']);
Route::get('customer_status/get_customer_status_details', [CustomerStatusController::class, 'get_customer_status_details']);
Route::get('customer_status/get_customer_list', [CustomerStatusController::class, 'get_customer_list']);
Route::resource('customer_status', CustomerStatusController::class);

Route::get('petty_cash_issue/print_petty_cash_issue', [PettyCashIssueController::class, 'print_petty_cash_issue']);
Route::get('petty_cash_issue/post_petty_cash_issue', [PettyCashIssueController::class, 'post_petty_cash_issue']);
Route::get('petty_cash_issue/get_data', [PettyCashIssueController::class, 'get_data']);
Route::get('petty_cash_issue/validate_document_no', [PettyCashIssueController::class, 'validate_document_no']);
Route::get('petty_cash_issue/find_petty_cash_issue', [PettyCashIssueController::class, 'find_petty_cash_issue']);
Route::get('petty_cash_issue/add_new', [PettyCashIssueController::class, 'add_new']);
Route::get('petty_cash_issue/petty_cash_issue_list', [PettyCashIssueController::class, 'petty_cash_issue_list']);
Route::resource('petty_cash_issue', PettyCashIssueController::class);

Route::get('petty_cash_return/print_petty_cash_return', [PettyCashReturnController::class, 'print_petty_cash_return']);
Route::get('petty_cash_return/post_petty_cash_return', [PettyCashReturnController::class, 'post_petty_cash_return']);
Route::get('petty_cash_return/validate_petty_cash_return_value', [PettyCashReturnController::class, 'validate_petty_cash_return_value']);
Route::get('petty_cash_return/validate_petty_cash_issue_no', [PettyCashReturnController::class, 'validate_petty_cash_issue_no']);
Route::get('petty_cash_return/find_petty_cash_return', [PettyCashReturnController::class, 'find_petty_cash_return']);
Route::get('petty_cash_return/add_new', [PettyCashReturnController::class, 'add_new']);
Route::get('petty_cash_return/petty_cash_return_list', [PettyCashReturnController::class, 'petty_cash_return_list']);
Route::resource('petty_cash_return', PettyCashReturnController::class);

Route::get('customer_complain/customer_complain_status_list', [CustomerComplainController::class, 'customer_complain_status_list']);
Route::get('customer_complain/find_customer_complain_status', [CustomerComplainController::class, 'find_customer_complain_status']);
Route::get('customer_complain/get_data', [CustomerComplainController::class, 'get_data']);
Route::get('customer_complain/validate_customer_complain_status', [CustomerComplainController::class, 'validate_customer_complain_status']);
Route::get('customer_complain/ongoing_customer_complain_list', [CustomerComplainController::class, 'ongoing_customer_complain_list']);
Route::get('customer_complain/update_customer_complain', [CustomerComplainController::class, 'update_customer_complain']);
Route::get('customer_complain/ongoing_customer_complain', [CustomerComplainController::class, 'ongoing_customer_complain']);
Route::get('customer_complain/find_customer_complain', [CustomerComplainController::class, 'find_customer_complain']);
Route::get('customer_complain/get_complain_data', [CustomerComplainController::class, 'get_complain_data']);
Route::get('customer_complain/validate_complain_type', [CustomerComplainController::class, 'validate_complain_type']);
Route::get('customer_complain/add_new_customer_complain', [CustomerComplainController::class, 'add_new_customer_complain']);
Route::get('customer_complain/validate_customer_name', [CustomerComplainController::class, 'validate_customer_name']);
Route::get('customer_complain/add_new_contact', [CustomerComplainController::class, 'add_new_contact']);
Route::get('customer_complain/new_customer_complain', [CustomerComplainController::class, 'new_customer_complain']);
Route::resource('customer_complain', CustomerComplainController::class);

Route::get('report/stock_update', [ReportController::class, 'stock_update']);
Route::get('report/tech_response_item_issue_details', [ReportController::class, 'tech_response_item_issue_details']);
Route::get('report/get_tech_response_data', [ReportController::class, 'get_tech_response_data']);
Route::get('report/tech_response_item_issue', [ReportController::class, 'tech_response_item_issue']);
Route::get('report/item_purchase_history_details', [ReportController::class, 'item_purchase_history_details']);
Route::get('report/item_purchase_history', [ReportController::class, 'item_purchase_history']);
Route::get('report/ongoing_job_item_issue_details', [ReportController::class, 'ongoing_job_item_issue_details']);
Route::get('report/ongoing_job_item_issue', [ReportController::class, 'ongoing_job_item_issue']);
Route::get('report/item_issue_details', [ReportController::class, 'item_issue_details']);
Route::get('report/item_issue', [ReportController::class, 'item_issue']);
Route::get('report/stock_check_details', [ReportController::class, 'stock_check_details']);
Route::get('report/stock_check', [ReportController::class, 'stock_check']);
Route::get('report/technical_job_details', [ReportController::class, 'technical_job_details']);
Route::get('report/technical_attendance_details', [ReportController::class, 'technical_attendance_details']);
Route::get('report/technical_attendance', [ReportController::class, 'technical_attendance']);
Route::get('report/job_profit_loss_details', [ReportController::class, 'job_profit_loss_details']);
Route::get('report/job_profit_loss', [ReportController::class, 'job_profit_loss']);
Route::get('report/stock_movement_details', [ReportController::class, 'stock_movement_details']);
Route::get('report/stock_movement', [ReportController::class, 'stock_movement']);
Route::get('report/inquiry_status_details', [ReportController::class, 'inquiry_status_details']);
Route::get('report/inquiry_status', [ReportController::class, 'inquiry_status']);
Route::resource('report', ReportController::class);

Route::get('/inventory_register/inventory_register_list', [InventoryRegisterController::class, 'inventory_register_list']);
Route::get('/inventory_register/validate_inventory_register', [InventoryRegisterController::class, 'validate_inventory_register']);
Route::get('/inventory_register/find_inventory_register', [InventoryRegisterController::class, 'find_inventory_register']);
Route::resource('inventory_register', InventoryRegisterController::class);

Route::get('inventory_issue/print_inventory_issue', [InventoryIssueController::class, 'print_inventory_issue']);
Route::get('inventory_issue/post_inventory_issue', [InventoryIssueController::class, 'post_inventory_issue']);
Route::get('inventory_issue/inventory_issue_detail_list', [InventoryIssueController::class, 'inventory_issue_detail_list']);
Route::get('inventory_issue/find_inventory_issue_detail', [InventoryIssueController::class, 'find_inventory_issue_detail']);
Route::get('inventory_issue/validate_inventory_code', [InventoryIssueController::class, 'validate_inventory_code']);
Route::get('inventory_issue/find_inventory_issue', [InventoryIssueController::class, 'find_inventory_issue']);
Route::get('inventory_issue/add_new', [InventoryIssueController::class, 'add_new']);
Route::get('inventory_issue/inventory_issue_list', [InventoryIssueController::class, 'inventory_issue_list']);
Route::resource('inventory_issue', InventoryIssueController::class);

Route::get('inventory_return/print_inventory_return', [InventoryReturnController::class, 'print_inventory_return']);
Route::get('inventory_return/post_inventory_return', [InventoryReturnController::class, 'post_inventory_return']);
Route::get('inventory_return/inventory_return_detail_list', [InventoryReturnController::class, 'inventory_return_detail_list']);
Route::get('inventory_return/find_inventory_return_detail', [InventoryReturnController::class, 'find_inventory_return_detail']);
Route::get('inventory_return/validate_inventory_code', [InventoryReturnController::class, 'validate_inventory_code']);
Route::get('inventory_return/validate_inventory_issue_no', [InventoryReturnController::class, 'validate_inventory_issue_no']);
Route::get('inventory_return/find_inventory_return', [InventoryReturnController::class, 'find_inventory_return']);
Route::get('inventory_return/add_new', [InventoryReturnController::class, 'add_new']);
Route::get('inventory_return/inventory_return_list', [InventoryReturnController::class, 'inventory_return_list']);
Route::resource('inventory_return', InventoryReturnController::class);
