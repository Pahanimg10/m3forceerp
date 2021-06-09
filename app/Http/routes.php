<?php

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

//Route::get('test/update_item_details', 'TestController@update_item_details');
//Route::get('test/price_update', 'TestController@price_update');
//Route::get('test/add_hikvision_items', 'TestController@add_hikvision_items');
//Route::get('test/add_items', 'TestController@add_items');
//Route::get('test/add_cctv_item', 'TestController@add_cctv_item');
//Route::get('test/add_ids_item', 'TestController@add_ids_item');
//Route::get('test/add_installation_item', 'TestController@add_installation_item');
//Route::get('test/add_contact', 'TestController@add_contact');
//
//Route::get('cron/generate_monitoring_invoice', 'CronController@generate_monitoring_invoice');

Route::get('/validateUsername', 'MainController@validateUsername');
Route::get('/checkUsername', 'MainController@checkUsername');
Route::get('/validateLogin', 'MainController@validateLogin');
Route::get('/home', 'MainController@home');
Route::get('/logout', 'MainController@logout');

Route::get('/find_inventory_issue', 'DataController@find_inventory_issue');
Route::get('/get_inventory_issues', 'DataController@get_inventory_issues');
Route::get('/find_inventory_code', 'DataController@find_inventory_code');
Route::get('/get_inventory_codes', 'DataController@get_inventory_codes');
Route::get('/get_inventory_data', 'DataController@get_inventory_data');
Route::get('/find_petty_cash_issue_no', 'DataController@find_petty_cash_issue_no');
Route::get('/get_petty_cash_issue_nos', 'DataController@get_petty_cash_issue_nos');
Route::get('/validate_tech_response_installation_quantity', 'DataController@validate_tech_response_installation_quantity');
Route::get('/get_filter_data', 'DataController@get_filter_data');
Route::get('/get_driving_types', 'DataController@get_driving_types');
Route::get('/get_fault_types', 'DataController@get_fault_types');
Route::get('/find_item_issue', 'DataController@find_item_issue');
Route::get('/get_item_issues', 'DataController@get_item_issues');
Route::get('/validate_job_no', 'DataController@validate_job_no');
Route::get('/find_tech_response_no', 'DataController@find_tech_response_no');
Route::get('/get_tech_response_nos', 'DataController@get_tech_response_nos');
Route::get('/find_job_no', 'DataController@find_job_no');
Route::get('/get_job_nos', 'DataController@get_job_nos');
Route::get('/find_technical_team', 'DataController@find_technical_team');
Route::get('/get_technical_teams', 'DataController@get_technical_teams');
Route::get('/get_payment_modes', 'DataController@get_payment_modes');
Route::get('/find_purchase_order_no', 'DataController@find_purchase_order_no');
Route::get('/get_purchase_order_nos', 'DataController@get_purchase_order_nos');
Route::get('/validate_purchase_order_no', 'DataController@validate_purchase_order_no');
Route::get('/validate_good_request_no', 'DataController@validate_good_request_no');
Route::get('/validate_technical_team_name', 'DataController@validate_technical_team_name');
Route::get('/validate_contact_name', 'DataController@validate_contact_name');
Route::get('/find_good_request_no', 'DataController@find_good_request_no');
Route::get('/get_good_request_nos', 'DataController@get_good_request_nos');
Route::get('/validate_installation_quantity', 'DataController@validate_installation_quantity');
Route::get('/get_installation_rates', 'DataController@get_installation_rates');
Route::get('/find_item_code', 'DataController@find_item_code');
Route::get('/get_item_codes', 'DataController@get_item_codes');
Route::get('/find_item_name', 'DataController@find_item_name');
Route::get('/get_item_names', 'DataController@get_item_names');
Route::get('/validate_item_name', 'DataController@validate_item_name');
Route::get('/validate_item_code', 'DataController@validate_item_code');
Route::get('/get_document_types', 'DataController@get_document_types');
Route::post('/find_customer', 'DataController@find_customer');
Route::post('/get_customers', 'DataController@get_customers');
Route::get('/contact_contact_types', 'DataController@contact_contact_types');
Route::get('/item_categories', 'DataController@item_categories');

Route::get('/dashboard/get_line_data', 'DashboardController@get_line_data');
Route::get('/dashboard/get_sales_target_data', 'DashboardController@get_sales_target_data');
Route::get('/dashboard/get_donut_data', 'DashboardController@get_donut_data');
Route::get('/dashboard/get_bar_data', 'DashboardController@get_bar_data');
Route::get('/dashboard/change_password', 'DashboardController@change_password');
Route::get('/dashboard/job_positions_list', 'DashboardController@job_positions_list');
Route::post('/dashboard/image_upload', 'DashboardController@image_upload');
Route::get('/dashboard/find_user', 'DashboardController@find_user');
Route::get('/dashboard/user_profile', 'DashboardController@user_profile');
Route::post('/dashboard/update_user_profile', 'DashboardController@update_user_profile');
Route::get('/dashboard/validate_username', 'DashboardController@validate_username');
Route::get('/dashboard/validate_old_password', 'DashboardController@validate_old_password');
Route::get('/dashboard/update_new_password', 'DashboardController@update_new_password');

Route::get('/user/users_list', 'UserController@users_list');
Route::get('/user/job_positions_list', 'UserController@job_positions_list');
Route::get('/user/validate_username', 'UserController@validate_username');
Route::get('/user/group_list', 'UserController@group_list');
Route::post('/user/image_upload', 'UserController@image_upload');
Route::get('/user/find_user', 'UserController@find_user');
Route::resource('user', 'UserController');

Route::get('/job_position/job_position_list', 'JobPositionController@job_position_list');
Route::get('/job_position/validate_job_position', 'JobPositionController@validate_job_position');
Route::get('/job_position/find_job_position', 'JobPositionController@find_job_position');
Route::resource('job_position', 'JobPositionController');

Route::get('/company/company_list', 'CompanyController@company_list');
Route::get('/company/validate_company', 'CompanyController@validate_company');
Route::post('/company/image_upload', 'CompanyController@image_upload');
Route::get('/company/find_company', 'CompanyController@find_company');
Route::resource('company', 'CompanyController');

Route::get('/i_mode_of_inquiry/i_mode_of_inquiry_list', 'IModeOfInquiryController@i_mode_of_inquiry_list');
Route::get('/i_mode_of_inquiry/validate_i_mode_of_inquiry', 'IModeOfInquiryController@validate_i_mode_of_inquiry');
Route::get('/i_mode_of_inquiry/find_i_mode_of_inquiry', 'IModeOfInquiryController@find_i_mode_of_inquiry');
Route::resource('i_mode_of_inquiry', 'IModeOfInquiryController');

Route::get('/i_inquiry_type/i_inquiry_type_list', 'IInquiryTypeController@i_inquiry_type_list');
Route::get('/i_inquiry_type/validate_i_inquiry_type', 'IInquiryTypeController@validate_i_inquiry_type');
Route::get('/i_inquiry_type/find_i_inquiry_type', 'IInquiryTypeController@find_i_inquiry_type');
Route::resource('i_inquiry_type', 'IInquiryTypeController');

Route::get('/sales_team/sales_team_list', 'SalesTeamController@sales_team_list');
Route::get('/sales_team/validate_sales_team', 'SalesTeamController@validate_sales_team');
Route::get('/sales_team/find_sales_team', 'SalesTeamController@find_sales_team');
Route::resource('sales_team', 'SalesTeamController');

Route::get('/technical_team/technical_team_list', 'TechnicalTeamController@technical_team_list');
Route::get('/technical_team/validate_technical_team', 'TechnicalTeamController@validate_technical_team');
Route::get('/technical_team/find_technical_team', 'TechnicalTeamController@find_technical_team');
Route::resource('technical_team', 'TechnicalTeamController');

Route::get('/i_client_type/i_client_type_list', 'IClientTypeController@i_client_type_list');
Route::get('/i_client_type/validate_i_client_type', 'IClientTypeController@validate_i_client_type');
Route::get('/i_client_type/find_i_client_type', 'IClientTypeController@find_i_client_type');
Route::resource('i_client_type', 'IClientTypeController');

Route::get('/i_business_type/i_business_type_list', 'IBusinessTypeController@i_business_type_list');
Route::get('/i_business_type/validate_i_business_type', 'IBusinessTypeController@validate_i_business_type');
Route::get('/i_business_type/find_i_business_type', 'IBusinessTypeController@find_i_business_type');
Route::resource('i_business_type', 'IBusinessTypeController');

Route::get('/c_contact_type/c_contact_type_list', 'CContactTypeController@c_contact_type_list');
Route::get('/c_contact_type/validate_c_contact_type', 'CContactTypeController@validate_c_contact_type');
Route::get('/c_contact_type/find_c_contact_type', 'CContactTypeController@find_c_contact_type');
Route::resource('c_contact_type', 'CContactTypeController');

Route::get('/c_tax_type/c_tax_type_list', 'CTaxTypeController@c_tax_type_list');
Route::get('/c_tax_type/validate_c_tax_type', 'CTaxTypeController@validate_c_tax_type');
Route::get('/c_tax_type/find_c_tax_type', 'CTaxTypeController@find_c_tax_type');
Route::resource('c_tax_type', 'CTaxTypeController');

Route::get('/region/region_list', 'RegionController@region_list');
Route::get('/region/validate_region', 'RegionController@validate_region');
Route::get('/region/find_region', 'RegionController@find_region');
Route::resource('region', 'RegionController');

Route::get('/complain_type/complain_type_list', 'ComplainTypeController@complain_type_list');
Route::get('/complain_type/validate_complain_type', 'ComplainTypeController@validate_complain_type');
Route::get('/complain_type/find_complain_type', 'ComplainTypeController@find_complain_type');
Route::resource('complain_type', 'ComplainTypeController');

Route::get('/person_responsible/person_responsible_list', 'PersonResponsibleController@person_responsible_list');
Route::get('/person_responsible/validate_person_responsible', 'PersonResponsibleController@validate_person_responsible');
Route::get('/person_responsible/find_person_responsible', 'PersonResponsibleController@find_person_responsible');
Route::resource('person_responsible', 'PersonResponsibleController');

Route::get('/contact/get_data', 'ContactController@get_data');
Route::get('/contact/validate_contact', 'ContactController@validate_contact');
Route::get('/contact/find_contact', 'ContactController@find_contact');
Route::get('/contact/add_new', 'ContactController@add_new');
Route::get('/contact/contact_list', 'ContactController@contact_list');
Route::resource('contact', 'ContactController');

Route::get('/c_group/get_data', 'CGroupController@get_data');
Route::get('/c_group/c_group_list', 'CGroupController@c_group_list');
Route::get('/c_group/validate_c_group', 'CGroupController@validate_c_group');
Route::get('/c_group/find_c_group', 'CGroupController@find_c_group');
Route::resource('c_group', 'CGroupController');

Route::get('/main_item_category/main_item_category_list', 'MainItemCategoryController@main_item_category_list');
Route::get('/main_item_category/validate_main_item_category', 'MainItemCategoryController@validate_main_item_category');
Route::get('/main_item_category/find_main_item_category', 'MainItemCategoryController@find_main_item_category');
Route::resource('main_item_category', 'MainItemCategoryController');

Route::get('/sub_item_category/sub_item_category_list', 'SubItemCategoryController@sub_item_category_list');
Route::get('/sub_item_category/validate_sub_item_category', 'SubItemCategoryController@validate_sub_item_category');
Route::get('/sub_item_category/find_sub_item_category', 'SubItemCategoryController@find_sub_item_category');
Route::resource('sub_item_category', 'SubItemCategoryController');

Route::get('/unit_type/unit_type_list', 'UnitTypeController@unit_type_list');
Route::get('/unit_type/validate_unit_type', 'UnitTypeController@validate_unit_type');
Route::get('/unit_type/find_unit_type', 'UnitTypeController@find_unit_type');
Route::resource('unit_type', 'UnitTypeController');

Route::get('/document_type/document_type_list', 'DocumentTypeController@document_type_list');
Route::get('/document_type/validate_document_type', 'DocumentTypeController@validate_document_type');
Route::get('/document_type/find_document_type', 'DocumentTypeController@find_document_type');
Route::resource('document_type', 'DocumentTypeController');

Route::get('/installation_rate/installation_rate_list', 'InstallationRateController@installation_rate_list');
Route::get('/installation_rate/validate_installation_rate', 'InstallationRateController@validate_installation_rate');
Route::get('/installation_rate/find_installation_rate', 'InstallationRateController@find_installation_rate');
Route::resource('installation_rate', 'InstallationRateController');

Route::get('/tech_response_fault/tech_response_fault_list', 'TechResponseFaultController@tech_response_fault_list');
Route::get('/tech_response_fault/validate_tech_response_fault', 'TechResponseFaultController@validate_tech_response_fault');
Route::get('/tech_response_fault/find_tech_response_fault', 'TechResponseFaultController@find_tech_response_fault');
Route::resource('tech_response_fault', 'TechResponseFaultController');

Route::get('/item/get_data', 'ItemController@get_data');
Route::get('/item/validate_model_no', 'ItemController@validate_model_no');
Route::get('/item/validate_item', 'ItemController@validate_item');
Route::get('/item/find_item', 'ItemController@find_item');
Route::get('/item/add_new', 'ItemController@add_new');
Route::get('/item/item_list', 'ItemController@item_list');
Route::resource('item', 'ItemController');

Route::get('/collection_manager/collection_manager_list', 'CollectionManagerController@collection_manager_list');
Route::get('/collection_manager/validate_collection_manager', 'CollectionManagerController@validate_collection_manager');
Route::get('/collection_manager/find_collection_manager', 'CollectionManagerController@find_collection_manager');
Route::resource('collection_manager', 'CollectionManagerController');

Route::get('/collection_person/collection_person_list', 'CollectionPersonController@collection_person_list');
Route::get('/collection_person/validate_collection_person', 'CollectionPersonController@validate_collection_person');
Route::get('/collection_person/find_collection_person', 'CollectionPersonController@find_collection_person');
Route::resource('collection_person', 'CollectionPersonController');

Route::get('inquiry/quotation', 'InquiryController@quotation');
Route::get('inquiry/installation_sheet', 'InquiryController@installation_sheet');
Route::get('inquiry/cost_sheet', 'InquiryController@cost_sheet');
Route::get('inquiry/job_card', 'InquiryController@job_card');
Route::get('inquiry/upload_document_list', 'InquiryController@upload_document_list');
Route::post('inquiry/file_upload', 'InquiryController@file_upload');
Route::get('inquiry/validate_document_upload', 'InquiryController@validate_document_upload');
Route::get('inquiry/upload_documents', 'InquiryController@upload_documents');
Route::get('inquiry/ongoing_inquiry_list', 'InquiryController@ongoing_inquiry_list');
Route::get('inquiry/ongoing_inquiry', 'InquiryController@ongoing_inquiry');
Route::get('inquiry/inquiry_status_list', 'InquiryController@inquiry_status_list');
Route::get('inquiry/find_inquiry_status', 'InquiryController@find_inquiry_status');
Route::get('inquiry/validate_inquiry_status', 'InquiryController@validate_inquiry_status');
Route::get('inquiry/update_inquiry', 'InquiryController@update_inquiry');
Route::get('inquiry/new_inquiry_list', 'InquiryController@new_inquiry_list');
Route::get('inquiry/find_inquiry', 'InquiryController@find_inquiry');
Route::get('inquiry/get_data', 'InquiryController@get_data');
Route::get('inquiry/new_inquiry', 'InquiryController@new_inquiry');
Route::resource('inquiry', 'InquiryController');

Route::get('job/print_handover_documents', 'JobController@print_handover_documents');
Route::get('job/handover_documents', 'JobController@handover_documents');
Route::get('job/print_advance_receipt', 'JobController@print_advance_receipt');
Route::get('job/advance_receipt_list', 'JobController@advance_receipt_list');
Route::get('job/advance_receipt', 'JobController@advance_receipt');
Route::get('job/upload_documents', 'JobController@upload_documents');
Route::get('job/ongoing_job_list', 'JobController@ongoing_job_list');
Route::get('job/ongoing_job', 'JobController@ongoing_job');
Route::get('job/job_status_list', 'JobController@job_status_list');
Route::get('job/find_job_status', 'JobController@find_job_status');
Route::get('job/find_job', 'JobController@find_job');
Route::get('job/get_data', 'JobController@get_data');
Route::get('job/validate_job_status', 'JobController@validate_job_status');
Route::get('job/update_job', 'JobController@update_job');
Route::get('job/new_job_list', 'JobController@new_job_list');
Route::get('job/new_job', 'JobController@new_job');
Route::resource('job', 'JobController');

Route::get('job_card/print_job_card_no_price', 'JobCardController@print_job_card_no_price');
Route::get('job_card/print_job_card', 'JobCardController@print_job_card');
Route::get('job_card/job_card_detail_list', 'JobCardController@job_card_detail_list');
Route::get('job_card/find_job_card_detail', 'JobCardController@find_job_card_detail');
Route::get('job_card/find_job_card', 'JobCardController@find_job_card');
Route::get('job_card/add_new', 'JobCardController@add_new');
Route::get('job_card/job_card_list', 'JobCardController@job_card_list');
Route::resource('job_card', 'JobCardController');

Route::get('cost_sheet/get_data', 'CostSheetController@get_data');
Route::get('cost_sheet/print_cost_sheet', 'CostSheetController@print_cost_sheet');
Route::get('cost_sheet/find_cost_sheet', 'CostSheetController@find_cost_sheet');
Route::get('cost_sheet/add_new', 'CostSheetController@add_new');
Route::get('cost_sheet/cost_sheet_list', 'CostSheetController@cost_sheet_list');
Route::resource('cost_sheet', 'CostSheetController');

Route::get('quotation/print_file_quotation', 'QuotationController@print_file_quotation');
Route::get('quotation/print_quotation', 'QuotationController@print_quotation');
Route::post('quotation/revise_quotation', 'QuotationController@revise_quotation');
Route::get('quotation/confirm_quotation', 'QuotationController@confirm_quotation');
Route::post('quotation/preview_quotation', 'QuotationController@preview_quotation');
Route::get('quotation/discount_detail', 'QuotationController@discount_detail');
Route::get('quotation/find_quotation', 'QuotationController@find_quotation');
Route::get('quotation/add_new', 'QuotationController@add_new');
Route::get('quotation/quotation_list', 'QuotationController@quotation_list');
Route::get('quotation/get_data', 'QuotationController@get_data');
Route::resource('quotation', 'QuotationController');

Route::get('installation_sheet/print_installation_sheet_no_price', 'InstallationSheetController@print_installation_sheet_no_price');
Route::post('installation_sheet/approve_installation_sheet', 'InstallationSheetController@approve_installation_sheet');
Route::get('installation_sheet/get_authorize_data', 'InstallationSheetController@get_authorize_data');
Route::get('installation_sheet/print_installation_sheet', 'InstallationSheetController@print_installation_sheet');
Route::get('installation_sheet/post_installation_sheet', 'InstallationSheetController@post_installation_sheet');
Route::get('installation_sheet/installation_sheet_detail_list', 'InstallationSheetController@installation_sheet_detail_list');
Route::get('installation_sheet/find_installation_sheet_detail', 'InstallationSheetController@find_installation_sheet_detail');
Route::get('installation_sheet/find_installation_sheet', 'InstallationSheetController@find_installation_sheet');
Route::get('installation_sheet/add_new', 'InstallationSheetController@add_new');
Route::get('installation_sheet/installation_sheet_list', 'InstallationSheetController@installation_sheet_list');
Route::resource('installation_sheet', 'InstallationSheetController');

Route::get('good_request/print_good_request', 'GoodRequestController@print_good_request');
Route::get('good_request/post_good_request', 'GoodRequestController@post_good_request');
Route::get('good_request/get_details', 'GoodRequestController@get_details');
Route::get('good_request/find_good_request', 'GoodRequestController@find_good_request');
Route::get('good_request/add_new', 'GoodRequestController@add_new');
Route::get('good_request/good_request_list', 'GoodRequestController@good_request_list');
Route::resource('good_request', 'GoodRequestController');

Route::get('purchase_order/validate_item_name', 'PurchaseOrderController@validate_item_name');
Route::get('purchase_order/validate_item_code', 'PurchaseOrderController@validate_item_code');
Route::get('purchase_order/print_purchase_order_stock', 'PurchaseOrderController@print_purchase_order_stock');
Route::get('purchase_order/print_purchase_order', 'PurchaseOrderController@print_purchase_order');
Route::get('purchase_order/post_purchase_order', 'PurchaseOrderController@post_purchase_order');
Route::get('purchase_order/purchase_order_detail_list', 'PurchaseOrderController@purchase_order_detail_list');
Route::get('purchase_order/find_purchase_order_detail', 'PurchaseOrderController@find_purchase_order_detail');
Route::get('purchase_order/find_purchase_order', 'PurchaseOrderController@find_purchase_order');
Route::get('purchase_order/add_new', 'PurchaseOrderController@add_new');
Route::get('purchase_order/purchase_order_list', 'PurchaseOrderController@purchase_order_list');
Route::resource('purchase_order', 'PurchaseOrderController');

Route::get('good_receive/validate_item_rate', 'GoodReceiveController@validate_item_rate');
Route::get('good_receive/validate_item_quantity', 'GoodReceiveController@validate_item_quantity');
Route::get('good_receive/validate_item_name', 'GoodReceiveController@validate_item_name');
Route::get('good_receive/validate_item_code', 'GoodReceiveController@validate_item_code');
Route::get('good_receive/validate_purchase_order_no', 'GoodReceiveController@validate_purchase_order_no');
Route::get('good_receive/print_good_receive', 'GoodReceiveController@print_good_receive');
Route::get('good_receive/post_good_receive', 'GoodReceiveController@post_good_receive');
Route::get('good_receive/good_receive_detail_list', 'GoodReceiveController@good_receive_detail_list');
Route::get('good_receive/find_good_receive_detail', 'GoodReceiveController@find_good_receive_detail');
Route::get('good_receive/find_good_receive', 'GoodReceiveController@find_good_receive');
Route::get('good_receive/add_new', 'GoodReceiveController@add_new');
Route::get('good_receive/good_receive_list', 'GoodReceiveController@good_receive_list');
Route::resource('good_receive', 'GoodReceiveController');

Route::get('payment/print_credit_supplier_good_receive', 'PaymentController@print_credit_supplier_good_receive');
Route::get('payment/print_credit_supplier_payment', 'PaymentController@print_credit_supplier_payment');
Route::get('payment/find_credit_supplier_payment', 'PaymentController@find_credit_supplier_payment');
Route::get('payment/add_new_payment', 'PaymentController@add_new_payment');
Route::get('payment/get_credit_supplier_detail', 'PaymentController@get_credit_supplier_detail');
Route::get('payment/credit_supplier_detail', 'PaymentController@credit_supplier_detail');
Route::get('payment/credit_supplier_list', 'PaymentController@credit_supplier_list');
Route::get('payment/credit_supplier', 'PaymentController@credit_supplier');
Route::resource('payment', 'PaymentController');

Route::get('job_attendance/validate_job_no', 'JobAttendanceController@validate_job_no');
Route::get('job_attendance/find_job_attendance_detail', 'JobAttendanceController@find_job_attendance_detail');
Route::get('job_attendance/job_attendance_detail_list', 'JobAttendanceController@job_attendance_detail_list');
Route::get('job_attendance/print_tech_work_sheet', 'JobAttendanceController@print_tech_work_sheet');
Route::get('job_attendance/print_work_sheet', 'JobAttendanceController@print_work_sheet');
Route::get('job_attendance/add_new', 'JobAttendanceController@add_new');
Route::get('job_attendance/job_attendance_list', 'JobAttendanceController@job_attendance_list');
Route::resource('job_attendance', 'JobAttendanceController');

Route::get('job_done_customer/get_data', 'JobDoneCustomerController@get_data');
Route::get('job_done_customer/find_job_done_customer_payment', 'JobDoneCustomerController@find_job_done_customer_payment');
Route::get('job_done_customer/add_new_payment', 'JobDoneCustomerController@add_new_payment');
Route::get('job_done_customer/get_job_done_customer_detail', 'JobDoneCustomerController@get_job_done_customer_detail');
Route::get('job_done_customer/print_job_done_customer_payment', 'JobDoneCustomerController@print_job_done_customer_payment');
Route::get('job_done_customer/print_job_done_customer_invoice', 'JobDoneCustomerController@print_job_done_customer_invoice');
Route::get('job_done_customer/job_done_customer_detail', 'JobDoneCustomerController@job_done_customer_detail');
Route::get('job_done_customer/job_done_customer_list', 'JobDoneCustomerController@job_done_customer_list');
Route::resource('job_done_customer', 'JobDoneCustomerController');

Route::get('monitoring_customer/get_data', 'MonitoringCustomerController@get_data');
Route::get('monitoring_customer/find_monitoring_customer_payment', 'MonitoringCustomerController@find_monitoring_customer_payment');
Route::get('monitoring_customer/add_new_payment', 'MonitoringCustomerController@add_new_payment');
Route::get('monitoring_customer/get_monitoring_customer_detail', 'MonitoringCustomerController@get_monitoring_customer_detail');
Route::get('monitoring_customer/print_monitoring_customer_payment', 'MonitoringCustomerController@print_monitoring_customer_payment');
Route::get('monitoring_customer/print_monitoring_customer_invoice', 'MonitoringCustomerController@print_monitoring_customer_invoice');
Route::get('monitoring_customer/monitoring_customer_detail', 'MonitoringCustomerController@monitoring_customer_detail');
Route::get('monitoring_customer/monitoring_customer_list', 'MonitoringCustomerController@monitoring_customer_list');
Route::resource('monitoring_customer', 'MonitoringCustomerController');

Route::post('item_issue/bulk_item_issue', 'ItemIssueController@bulk_item_issue');
Route::get('item_issue/find_balance_items', 'ItemIssueController@find_balance_items');
Route::get('item_issue/find_serial_no', 'ItemIssueController@find_serial_no');
Route::get('item_issue/get_serial_nos', 'ItemIssueController@get_serial_nos');
Route::get('item_issue/get_data', 'ItemIssueController@get_data');
Route::get('item_issue/validate_serial_no', 'ItemIssueController@validate_serial_no');
Route::get('item_issue/validate_item_quantity', 'ItemIssueController@validate_item_quantity');
Route::get('item_issue/validate_item_name', 'ItemIssueController@validate_item_name');
Route::get('item_issue/validate_item_code', 'ItemIssueController@validate_item_code');
Route::get('item_issue/validate_document_no', 'ItemIssueController@validate_document_no');
Route::get('item_issue/print_item_issue', 'ItemIssueController@print_item_issue');
Route::get('item_issue/post_item_issue', 'ItemIssueController@post_item_issue');
Route::get('item_issue/item_issue_detail_list', 'ItemIssueController@item_issue_detail_list');
Route::get('item_issue/find_item_issue_detail', 'ItemIssueController@find_item_issue_detail');
Route::get('item_issue/find_item_issue', 'ItemIssueController@find_item_issue');
Route::get('item_issue/add_new', 'ItemIssueController@add_new');
Route::get('item_issue/item_issue_list', 'ItemIssueController@item_issue_list');
Route::resource('item_issue', 'ItemIssueController');

Route::get('item_receive/find_serial_no', 'ItemReceiveController@find_serial_no');
Route::get('item_receive/get_serial_nos', 'ItemReceiveController@get_serial_nos');
Route::get('item_receive/get_data', 'ItemReceiveController@get_data');
Route::get('item_receive/validate_serial_no', 'ItemReceiveController@validate_serial_no');
Route::get('item_receive/validate_item_quantity', 'ItemReceiveController@validate_item_quantity');
Route::get('item_receive/validate_item_name', 'ItemReceiveController@validate_item_name');
Route::get('item_receive/validate_item_code', 'ItemReceiveController@validate_item_code');
Route::get('item_receive/validate_item_issue_no', 'ItemReceiveController@validate_item_issue_no');
Route::get('item_receive/print_item_receive', 'ItemReceiveController@print_item_receive');
Route::get('item_receive/post_item_receive', 'ItemReceiveController@post_item_receive');
Route::get('item_receive/item_receive_detail_list', 'ItemReceiveController@item_receive_detail_list');
Route::get('item_receive/find_item_receive_detail', 'ItemReceiveController@find_item_receive_detail');
Route::get('item_receive/find_item_receive', 'ItemReceiveController@find_item_receive');
Route::get('item_receive/add_new', 'ItemReceiveController@add_new');
Route::get('item_receive/item_receive_list', 'ItemReceiveController@item_receive_list');
Route::resource('item_receive', 'ItemReceiveController');

Route::get('repair/print_repair', 'RepairController@print_repair');
Route::get('repair/repair_status_list', 'RepairController@repair_status_list');
Route::get('repair/find_repair_status', 'RepairController@find_repair_status');
Route::get('repair/validate_repair_status', 'RepairController@validate_repair_status');
Route::get('repair/find_item_name', 'RepairController@find_item_name');
Route::get('repair/get_item_names', 'RepairController@get_item_names');
Route::get('repair/find_item_code', 'RepairController@find_item_code');
Route::get('repair/get_item_codes', 'RepairController@get_item_codes');
Route::get('repair/find_tech_response_no', 'RepairController@find_tech_response_no');
Route::get('repair/get_tech_response_nos', 'RepairController@get_tech_response_nos');
Route::get('repair/find_job_no', 'RepairController@find_job_no');
Route::get('repair/get_job_nos', 'RepairController@get_job_nos');
Route::get('repair/find_repair', 'RepairController@find_repair');
Route::get('repair/update_status', 'RepairController@update_status');
Route::get('repair/add_new', 'RepairController@add_new');
Route::get('repair/repair_list', 'RepairController@repair_list');
Route::get('repair/get_data', 'RepairController@get_data');
Route::resource('repair', 'RepairController');

Route::get('tech_response/get_issed_items', 'TechResponseController@get_issed_items');
Route::get('tech_response/tech_response_status_list', 'TechResponseController@tech_response_status_list');
Route::get('tech_response/find_tech_response_status', 'TechResponseController@find_tech_response_status');
Route::get('tech_response/get_data', 'TechResponseController@get_data');
Route::get('tech_response/validate_tech_response_status', 'TechResponseController@validate_tech_response_status');
Route::get('tech_response/ongoing_tech_response_list', 'TechResponseController@ongoing_tech_response_list');
Route::get('tech_response/tech_response_quotation', 'TechResponseController@tech_response_quotation');
Route::get('tech_response/tech_response_installation_sheet', 'TechResponseController@tech_response_installation_sheet');
Route::get('tech_response/tech_response_job_card', 'TechResponseController@tech_response_job_card');
Route::get('tech_response/update_tech_response', 'TechResponseController@update_tech_response');
Route::get('tech_response/ongoing_tech_response', 'TechResponseController@ongoing_tech_response');
Route::get('tech_response/validate_fault_type', 'TechResponseController@validate_fault_type');
Route::get('tech_response/find_tech_response', 'TechResponseController@find_tech_response');
Route::get('tech_response/add_new_tech_response', 'TechResponseController@add_new_tech_response');
Route::get('tech_response/validate_customer_name', 'TechResponseController@validate_customer_name');
Route::get('tech_response/add_new_contact', 'TechResponseController@add_new_contact');
Route::get('tech_response/new_fault', 'TechResponseController@new_fault');
Route::resource('tech_response', 'TechResponseController');

Route::post('tech_response_job_card/approve_tech_response_items', 'TechResponseJobCardController@approve_tech_response_items');
Route::get('tech_response_job_card/get_authorize_data', 'TechResponseJobCardController@get_authorize_data');
Route::get('tech_response_job_card/post_tech_response_job_card', 'TechResponseJobCardController@post_tech_response_job_card');
Route::get('tech_response_job_card/print_tech_response_job_card', 'TechResponseJobCardController@print_tech_response_job_card');
Route::get('tech_response_job_card/tech_response_job_card_detail_list', 'TechResponseJobCardController@tech_response_job_card_detail_list');
Route::get('tech_response_job_card/find_tech_response_job_card_detail', 'TechResponseJobCardController@find_tech_response_job_card_detail');
Route::get('tech_response_job_card/find_tech_response_job_card', 'TechResponseJobCardController@find_tech_response_job_card');
Route::get('tech_response_job_card/add_new', 'TechResponseJobCardController@add_new');
Route::get('tech_response_job_card/tech_response_job_card_list', 'TechResponseJobCardController@tech_response_job_card_list');
Route::resource('tech_response_job_card', 'TechResponseJobCardController');

Route::get('tech_response_installation_sheet/post_tech_response_installation_sheet', 'TechResponseInstallationSheetController@post_tech_response_installation_sheet');
Route::get('tech_response_installation_sheet/print_tech_response_installation_sheet', 'TechResponseInstallationSheetController@print_tech_response_installation_sheet');
Route::get('tech_response_installation_sheet/tech_response_installation_sheet_detail_list', 'TechResponseInstallationSheetController@tech_response_installation_sheet_detail_list');
Route::get('tech_response_installation_sheet/find_tech_response_installation_sheet_detail', 'TechResponseInstallationSheetController@find_tech_response_installation_sheet_detail');
Route::get('tech_response_installation_sheet/find_tech_response_installation_sheet', 'TechResponseInstallationSheetController@find_tech_response_installation_sheet');
Route::get('tech_response_installation_sheet/add_new', 'TechResponseInstallationSheetController@add_new');
Route::get('tech_response_installation_sheet/tech_response_installation_sheet_list', 'TechResponseInstallationSheetController@tech_response_installation_sheet_list');
Route::resource('tech_response_installation_sheet', 'TechResponseInstallationSheetController');

Route::get('tech_response_quotation/print_tech_response_quotation', 'TechResponseQuotationController@print_tech_response_quotation');
Route::get('tech_response_quotation/revise_tech_response_quotation', 'TechResponseQuotationController@revise_tech_response_quotation');
Route::get('tech_response_quotation/confirm_tech_response_quotation', 'TechResponseQuotationController@confirm_tech_response_quotation');
Route::post('tech_response_quotation/preview_tech_response_quotation', 'TechResponseQuotationController@preview_tech_response_quotation');
Route::get('tech_response_quotation/discount_detail', 'TechResponseQuotationController@discount_detail');
Route::get('tech_response_quotation/find_tech_response_quotation', 'TechResponseQuotationController@find_tech_response_quotation');
Route::get('tech_response_quotation/add_new', 'TechResponseQuotationController@add_new');
Route::get('tech_response_quotation/tech_response_quotation_list', 'TechResponseQuotationController@tech_response_quotation_list');
Route::get('tech_response_quotation/get_data', 'TechResponseQuotationController@get_data');
Route::resource('tech_response_quotation', 'TechResponseQuotationController');

Route::get('tech_response_customer/get_data', 'TechResponseCustomerController@get_data');
Route::get('tech_response_customer/find_tech_response_customer_payment', 'TechResponseCustomerController@find_tech_response_customer_payment');
Route::get('tech_response_customer/add_new_payment', 'TechResponseCustomerController@add_new_payment');
Route::get('tech_response_customer/get_tech_response_customer_detail', 'TechResponseCustomerController@get_tech_response_customer_detail');
Route::get('tech_response_customer/print_tech_response_customer_payment', 'TechResponseCustomerController@print_tech_response_customer_payment');
Route::get('tech_response_customer/print_tech_response_customer_invoice', 'TechResponseCustomerController@print_tech_response_customer_invoice');
Route::get('tech_response_customer/tech_response_customer_detail', 'TechResponseCustomerController@tech_response_customer_detail');
Route::get('tech_response_customer/tech_response_customer_list', 'TechResponseCustomerController@tech_response_customer_list');
Route::resource('tech_response_customer', 'TechResponseCustomerController');

Route::get('customer_status/actual_expenses_list', 'CustomerStatusController@actual_expenses_list');
Route::get('customer_status/find_expenses', 'CustomerStatusController@find_expenses');
Route::get('customer_status/get_data', 'CustomerStatusController@get_data');
Route::get('customer_status/print_lost_profit', 'CustomerStatusController@print_lost_profit');
Route::get('customer_status/print_item_issue_balance', 'CustomerStatusController@print_item_issue_balance');
Route::get('customer_status/get_customer_status_details', 'CustomerStatusController@get_customer_status_details');
Route::get('customer_status/get_customer_list', 'CustomerStatusController@get_customer_list');
Route::resource('customer_status', 'CustomerStatusController');

Route::get('petty_cash_issue/print_petty_cash_issue', 'PettyCashIssueController@print_petty_cash_issue');
Route::get('petty_cash_issue/post_petty_cash_issue', 'PettyCashIssueController@post_petty_cash_issue');
Route::get('petty_cash_issue/get_data', 'PettyCashIssueController@get_data');
Route::get('petty_cash_issue/validate_document_no', 'PettyCashIssueController@validate_document_no');
Route::get('petty_cash_issue/find_petty_cash_issue', 'PettyCashIssueController@find_petty_cash_issue');
Route::get('petty_cash_issue/add_new', 'PettyCashIssueController@add_new');
Route::get('petty_cash_issue/petty_cash_issue_list', 'PettyCashIssueController@petty_cash_issue_list');
Route::resource('petty_cash_issue', 'PettyCashIssueController');

Route::get('petty_cash_return/print_petty_cash_return', 'PettyCashReturnController@print_petty_cash_return');
Route::get('petty_cash_return/post_petty_cash_return', 'PettyCashReturnController@post_petty_cash_return');
Route::get('petty_cash_return/validate_petty_cash_return_value', 'PettyCashReturnController@validate_petty_cash_return_value');
Route::get('petty_cash_return/validate_petty_cash_issue_no', 'PettyCashReturnController@validate_petty_cash_issue_no');
Route::get('petty_cash_return/find_petty_cash_return', 'PettyCashReturnController@find_petty_cash_return');
Route::get('petty_cash_return/add_new', 'PettyCashReturnController@add_new');
Route::get('petty_cash_return/petty_cash_return_list', 'PettyCashReturnController@petty_cash_return_list');
Route::resource('petty_cash_return', 'PettyCashReturnController');

Route::get('customer_complain/customer_complain_status_list', 'CustomerComplainController@customer_complain_status_list');
Route::get('customer_complain/find_customer_complain_status', 'CustomerComplainController@find_customer_complain_status');
Route::get('customer_complain/get_data', 'CustomerComplainController@get_data');
Route::get('customer_complain/validate_customer_complain_status', 'CustomerComplainController@validate_customer_complain_status');
Route::get('customer_complain/ongoing_customer_complain_list', 'CustomerComplainController@ongoing_customer_complain_list');
Route::get('customer_complain/update_customer_complain', 'CustomerComplainController@update_customer_complain');
Route::get('customer_complain/ongoing_customer_complain', 'CustomerComplainController@ongoing_customer_complain');
Route::get('customer_complain/find_customer_complain', 'CustomerComplainController@find_customer_complain');
Route::get('customer_complain/get_complain_data', 'CustomerComplainController@get_complain_data');
Route::get('customer_complain/validate_complain_type', 'CustomerComplainController@validate_complain_type');
Route::get('customer_complain/add_new_customer_complain', 'CustomerComplainController@add_new_customer_complain');
Route::get('customer_complain/validate_customer_name', 'CustomerComplainController@validate_customer_name');
Route::get('customer_complain/add_new_contact', 'CustomerComplainController@add_new_contact');
Route::get('customer_complain/new_customer_complain', 'CustomerComplainController@new_customer_complain');
Route::resource('customer_complain', 'CustomerComplainController');

Route::get('report/stock_update', 'ReportController@stock_update');
Route::get('report/tech_response_item_issue_details', 'ReportController@tech_response_item_issue_details');
Route::get('report/get_tech_response_data', 'ReportController@get_tech_response_data');
Route::get('report/tech_response_item_issue', 'ReportController@tech_response_item_issue');
Route::get('report/item_purchase_history_details', 'ReportController@item_purchase_history_details');
Route::get('report/item_purchase_history', 'ReportController@item_purchase_history');
Route::get('report/ongoing_job_item_issue_details', 'ReportController@ongoing_job_item_issue_details');
Route::get('report/ongoing_job_item_issue', 'ReportController@ongoing_job_item_issue');
Route::get('report/item_issue_details', 'ReportController@item_issue_details');
Route::get('report/item_issue', 'ReportController@item_issue');
Route::get('report/stock_check_details', 'ReportController@stock_check_details');
Route::get('report/stock_check', 'ReportController@stock_check');
Route::get('report/technical_job_details', 'ReportController@technical_job_details');
Route::get('report/technical_attendance_details', 'ReportController@technical_attendance_details');
Route::get('report/technical_attendance', 'ReportController@technical_attendance');
Route::get('report/job_profit_loss_details', 'ReportController@job_profit_loss_details');
Route::get('report/job_profit_loss', 'ReportController@job_profit_loss');
Route::get('report/stock_movement_details', 'ReportController@stock_movement_details');
Route::get('report/stock_movement', 'ReportController@stock_movement');
Route::get('report/inquiry_status_details', 'ReportController@inquiry_status_details');
Route::get('report/inquiry_status', 'ReportController@inquiry_status');
Route::resource('report', 'ReportController');

Route::get('/inventory_register/inventory_register_list', 'InventoryRegisterController@inventory_register_list');
Route::get('/inventory_register/validate_inventory_register', 'InventoryRegisterController@validate_inventory_register');
Route::get('/inventory_register/find_inventory_register', 'InventoryRegisterController@find_inventory_register');
Route::resource('inventory_register', 'InventoryRegisterController');

Route::get('inventory_issue/print_inventory_issue', 'InventoryIssueController@print_inventory_issue');
Route::get('inventory_issue/post_inventory_issue', 'InventoryIssueController@post_inventory_issue');
Route::get('inventory_issue/inventory_issue_detail_list', 'InventoryIssueController@inventory_issue_detail_list');
Route::get('inventory_issue/find_inventory_issue_detail', 'InventoryIssueController@find_inventory_issue_detail');
Route::get('inventory_issue/validate_inventory_code', 'InventoryIssueController@validate_inventory_code');
Route::get('inventory_issue/find_inventory_issue', 'InventoryIssueController@find_inventory_issue');
Route::get('inventory_issue/add_new', 'InventoryIssueController@add_new');
Route::get('inventory_issue/inventory_issue_list', 'InventoryIssueController@inventory_issue_list');
Route::resource('inventory_issue', 'InventoryIssueController');

Route::get('inventory_return/print_inventory_return', 'InventoryReturnController@print_inventory_return');
Route::get('inventory_return/post_inventory_return', 'InventoryReturnController@post_inventory_return');
Route::get('inventory_return/inventory_return_detail_list', 'InventoryReturnController@inventory_return_detail_list');
Route::get('inventory_return/find_inventory_return_detail', 'InventoryReturnController@find_inventory_return_detail');
Route::get('inventory_return/validate_inventory_code', 'InventoryReturnController@validate_inventory_code');
Route::get('inventory_return/validate_inventory_issue_no', 'InventoryReturnController@validate_inventory_issue_no');
Route::get('inventory_return/find_inventory_return', 'InventoryReturnController@find_inventory_return');
Route::get('inventory_return/add_new', 'InventoryReturnController@add_new');
Route::get('inventory_return/inventory_return_list', 'InventoryReturnController@inventory_return_list');
Route::resource('inventory_return', 'InventoryReturnController');
