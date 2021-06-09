@extends('layouts.main')

@section('title')
<title>M3Force | Tech Response Quotation Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Tech Response</a></li>                    
    <li class="active">Tech Response Quotation Details</li>
</ul>
@endsection

@section('content')
<style>
    .grid {
        width:100%;
        height: 405px;
    }
    .grid-align-left {
        text-align: left;
    }
    .grid-align-right {
        text-align: right;
    }
    .grid-align {
        text-align: center;
    }
    .ui-grid-header-cell {
        text-align: center;
    }
    .help-block{
        color: red; 
        text-align: right;
    }
    .form-horizontal .form-group{
        margin-left: 0;
        margin-right: 0;
    }
    .control-label{
        padding: 15px 0 5px 0;
    }
    input[type="text"]:disabled{
        color: #1CB09A;
    }     
    #data_table{
        width: 100%;
    }
    #data_table tr{
        page-break-inside: avoid;
    }
    #data_table tfoot{
        display: table-row-group;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Tech Response Quotation Detail</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="previewTechResponseQuotation()" ng-disabled="edit_disable" class="btn btn-info" data-toggle="modal" data-target="#dataModal">Preview</button></li>
                        <li><button type="button" ng-click="confirmForm()" ng-show="data.tech_response_quotation_id && is_confirmed == 0" class="btn btn-warning" id="confirm_button">Confirmed</button></li>
                        <li><button type="button" ng-click="reviseForm()" ng-show="data.tech_response_quotation_id && is_confirmed == 1" class="btn btn-danger" id="revise_button">Revised</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-default">Reset</button></li>
                        <li><div><a ng-show="data.tech_response_quotation_id" target="_blank" class="btn btn-success" ng-href="<%=base_url%>/tech_response_quotation/print_tech_response_quotation?id=<%=data.tech_response_quotation_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('tech_response/tech_response_quotation?id='.$tech_response_id)}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="tech_response_id" name="tech_response_id" ng-model="data.tech_response_id" class="form-control" />
                                <input type="hidden" id="tech_response_quotation_id" name="tech_response_quotation_id" ng-model="data.tech_response_quotation_id" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Customer Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" ng-model="customer_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Customer Address</label>
                                            <input type="text" id="customer_address" name="customer_address" ng-model="customer_address" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Customer Contact No</label>
                                            <input type="text" id="customer_contact_no" name="customer_contact_no" ng-model="customer_contact_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Tech Response Quotation Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Quotation No</label>
                                            <input type="text" id="tech_response_quotation_no" name="tech_response_quotation_no" ng-model="data.tech_response_quotation_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Quotation Date</label>
                                            <input type="text" id="tech_response_quotation_date" name="tech_response_quotation_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.tech_response_quotation_date" ng-disabled="edit_disable" is-open="techResponseQuotationDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenTechResponseQuotationDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Quotation Time</label>
                                            <input type="text" id="tech_response_quotation_time" name="tech_response_quotation_time" ng-model="data.tech_response_quotation_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Job Cards</label>
                                            <label style="display: block;" ng-repeat="tech_response_job_card in data.tech_response_job_cards">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= tech_response_job_card.description %>"><input type="checkbox" name="selectedTechResponseJobCards[]" ng-checked="tech_response_job_card.selected" ng-model="tech_response_job_card.selected" ng-disabled="edit_disable" />&nbsp;<%= tech_response_job_card.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Installation Charge</label>
                                            <input type="text" id="installation_charge" name="installation_charge" ng-model="data.installation_charge" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Transport Charge</label>
                                            <input type="text" id="transport_charge" name="transport_charge" ng-model="data.transport_charge" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Attendence Fee</label>
                                            <input type="text" id="attendance_fee" name="attendance_fee" ng-model="data.attendance_fee" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Currency</label><br/>
                                            <input id="is_currency" bs-switch emit-change="is_currency" ng-model="data.is_currency" switch-readonly="edit_disable" switch-active="true" switch-on-text="LKR" switch-off-text="USD" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_currency">
                                        <div class="form-group">
                                            <label class="control-label">USD Rate</label>
                                            <input type="text" id="usd_rate" name="usd_rate" ng-model="data.usd_rate" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Show Brand</label><br/>
                                            <input id="show_brand" bs-switch emit-change="show_brand" ng-model="data.show_brand" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Show Origin</label><br/>
                                            <input id="show_origin" bs-switch emit-change="show_origin" ng-model="data.show_origin" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Show Transport</label><br/>
                                            <input id="show_transport" bs-switch emit-change="show_transport" ng-model="data.show_transport" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Special Notes</label>
                                            <input type="text" id="special_notes" name="special_notes" ng-model="data.special_notes" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Show Installation Charge</label><br/>
                                            <input id="show_installation_charge" bs-switch emit-change="show_installation_charge" ng-model="data.show_installation_charge" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-10" ng-show="data.show_installation_charge">
                                        <div class="form-group">
                                            <label class="control-label">Installation Charge Text</label>
                                            <input type="text" id="installation_charge_text" name="installation_charge_text" ng-model="data.installation_charge_text" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Discount Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Discount Type</label>
                                            <select name="discount_type" id="discount_type" ng-options="option.name for option in discount_type_array track by option.id" ng-model="data.discount_type" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Description</label>
                                            <input type="text" id="description" name="description" ng-model="data.description" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Percentage</label>
                                            <input type="text" id="percentage" name="percentage" ng-model="data.percentage" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <button type="button" ng-click="addDiscountData()" ng-disabled="edit_disable" class="btn btn-info" style="margin-top: 30px;" >Add</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div id="dataModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-body">

                    <div class="col-md-12">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong>Preview</strong> Tech Response Quotation</h3>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body"> 
                                <div class="row" style="width: 100%;" ng-bind-html="preview_data | unsafe"></div>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary pull-right" data-dismiss="modal" ng-click="submitForm()" id="save_button" style="margin-right: 5px;">Save</button> 
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div> 
</div> 

<script type="text/javascript">
    var submitForm; 
    
    var myApp = angular.module('myModule', [
        'ui.bootstrap',
        'ngAnimate', 
        'ngTouch', 
        'ui.grid', 
        'ui.grid.edit',
        'ui.grid.rowEdit',
        'ui.grid.selection', 
        'ui.grid.exporter',
        'ui.grid.pagination',
        'ui.grid.moveColumns', 
        'ui.grid.resizeColumns',
        'ui.grid.cellNav',
        'frapontillo.bootstrap-switch'
    ]).config(function ($interpolateProvider) {
        // To prevent the conflict of `{{` and `}}` symbols
        // between Blade template engine and AngularJS templating we need
        // to use different symbols for AngularJS.

        $interpolateProvider.startSymbol('<%=');
        $interpolateProvider.endSymbol('%>');
    });
    
    myApp.config(['$httpProvider', function($httpProvider) {
        //initialize get if not there
        if (!$httpProvider.defaults.headers.get) {
            $httpProvider.defaults.headers.get = {};    
        }    

        // Answer edited to include suggestions from comments
        // because previous version of code introduced browser-related errors

        //disable IE ajax request caching
        $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
        // extra
        $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
        $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
    }]);

    myApp.controller('menuController', function ($scope) {
        angular.element(document.querySelector('#main_menu_tech_response')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_tech_response')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        tech_response_quotation_date: {
                            required: true,
                            date: true
                        },
                        tech_response_quotation_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        usd_rate: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        installation_charge: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        transport_charge: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        attendance_fee: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        discount_type: {
                            required: function(element){
                                return $('#description').val() != '' || $('#percentage').val() != '';
                            }
                        }, 
                        description: {
                            required: function(element){
                                return $('#discount_type').val() != '' || $('#percentage').val() != '';
                            }
                        }, 
                        percentage: {
                            required: function(element){
                                return $('#discount_type').val() != '' || $('#description').val() != '';
                            },
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        tech_response_quotation_date: {
                            required: 'Tech Response Quotation Date is required',
                            date: 'Invalid date format'
                        },
                        tech_response_quotation_time: {
                            required: 'Tech Response Quotation Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        usd_rate: {
                            required: 'USD Rate is required',
                            number: 'Invalid number format',
                            min: 'Minimum usd rate 0'
                        },
                        installation_charge: {
                            required: 'Installation Charge is required',
                            number: 'Invalid number format',
                            min: 'Minimum usd rate 0'
                        },
                        transport_charge: {
                            required: 'Transport Charge is required',
                            number: 'Invalid number format',
                            min: 'Minimum usd rate 0'
                        },
                        attendance_fee: {
                            required: 'Attendance Fee is required',
                            number: 'Invalid number format',
                            min: 'Minimum usd rate 0'
                        },
                        discount_type: {
                            required: 'Discount Type is required'
                        },
                        description: {
                            required: 'Description is required'
                        },
                        percentage: {
                            required: 'Percentage is required',
                            number: 'Invalid number format',
                            min: 'Minimum percentage 0'
                        }
                    },
                    highlight: function(element) {
                        $(element).removeClass("valid");
                        $(element).addClass("error");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("error");
                        $(element).addClass("valid");
                    },
                    errorElement: 'label',
                    errorClass: 'message_lable',
                    submitHandler: function (form) {
                        submitForm();
                    },
                    invalidHandler: function (event, validator) {
                        //
                    }

                });

                scope.$on('$destroy', function () {
                    // Perform cleanup.
                    // (Not familiar with the plugin so don't know what should to be 
                });
            }
        }
    });
        
    myApp.filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.base_url = base_url;
        var view = <?php echo $view ? $view : 0; ?>;
        $scope.edit_disable = view == 1 ? true : false;
        $scope.permission;
        $scope.is_confirmed;
            
        $scope.data = [];
        $scope.discount_type_array = [];
        
        $scope.data.tech_response_id = <?php echo $tech_response_id ? $tech_response_id : 0; ?>;
        $scope.data.tech_response_quotation_id = <?php echo $tech_response_quotation_id ? $tech_response_quotation_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.techResponseQuotationDatePopup = {
            opened: false
        };        
        $scope.OpenTechResponseQuotationDate = function () {
            $scope.techResponseQuotationDatePopup.opened = !$scope.techResponseQuotationDatePopup.opened;
        };
        
        $('#tech_response_quotation_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/tech_response_quotation/get_data',
                params: {
                    id: $scope.data.tech_response_id
                }
            }).then(function successCallback(response) {
                var tech_response_job_card_array = [];
                var discount_type_array = [];
                 
                $.each(response.data.tech_response_job_cards, function (index, value) {
                    tech_response_job_card_array.push({
                        id: value.id,
                        name: value.tech_response_job_card_no,
                        description: parseFloat(Math.round(value.tech_response_job_card_value * 100) / 100).toFixed(2),
                        selected: false
                    });
                });                     
                discount_type_array.push({
                    id: '',
                    name: 'Select Discount Type'
                });
                $.each(response.data.discount_types, function (index, value) {
                    discount_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.discount_type_array = discount_type_array;
                
                var today = new Date();
                var hh = today.getHours();
                var mm = today.getMinutes();
                if(hh<10){
                    hh='0'+hh;
                }
                if(mm<10){
                    mm='0'+mm;
                }
                
                $scope.data = {
                    tech_response_id: $scope.data.tech_response_id,
                    tech_response_quotation_id: $scope.data.tech_response_quotation_id,
                    tech_response_quotation_no: '',
                    tech_response_quotation_date: new Date(),
                    tech_response_quotation_time: hh+':'+mm,
                    remarks: '',
                    tech_response_job_cards: tech_response_job_card_array,
                    is_currency: true,
                    usd_rate: 0,
                    installation_charge: 0,
                    transport_charge: 0,
                    attendance_fee: 0,
                    show_brand: false,
                    show_origin: false,
                    show_transport: false,
                    special_notes: '',
                    show_installation_charge: false,
                    installation_charge_text: '',
                    discount_type: $scope.discount_type_array.length > 0 ? $scope.discount_type_array[0] : {},
                    description: '',
                    percentage: '',
                    discount_data: null,
                    customer_tax: null,
                    tech_response_quotation_value: 0
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/tech_response/find_tech_response',
                    params: {
                        id: $scope.data.tech_response_id
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.customer_name = response.data.contact ? response.data.contact.name : '';
                        $scope.customer_address = response.data.contact ? response.data.contact.address : '';
                        $scope.customer_contact_no = response.data.contact ? response.data.contact.contact_no : '';
                        if(response.data.contact && response.data.contact.is_group == 1){
                            $scope.data.customer_tax = response.data.contact.c_group && response.data.contact.c_group.c_group_tax ? response.data.contact.c_group.c_group_tax : null;
                        } else{
                            $scope.data.customer_tax = response.data.contact && response.data.contact.contact_tax ? response.data.contact.contact_tax : null;
                        }
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'index', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'permission', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'is_confirmed', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'discount_type_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'discount_type_name', 
                    displayName: 'Discount Type', 
                    width: '25%', 
                    enableCellEdit: false
                },
                {
                    field: 'description', 
                    displayName: 'Description', 
                    width: '50%', 
                    enableCellEdit: false
                },
                {
                    field: 'percentage', 
                    displayName: 'Percentage %', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '10%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_confirmed == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>', 
                    visible: $scope.edit_disable ? false : true
                }
            ],
            showColumnFooter: true,
            enableCellEditOnFocus: true,
            enableRowSelection: false,
            enableRowHeaderSelection: false,
            paginationPageSizes: [10, 25, 50],
            paginationPageSize: 10,
            enableFiltering: false,
            enableSorting: true,
            enableCellEdit: false,
            enableColumnResizing: true,
            exporterLinkLabel: 'get your csv here',
            onRegisterApi: function (gridApi) {
                $scope.gridApi = gridApi;
            }
        };

        $scope.confirmForm = function () {
            swal({
                title: "Are you sure?",
                text: $scope.customer_name + "(" + $scope.data.tech_response_quotation_no + ")" + " : <strong>" + parseFloat(Math.round($scope.data.tech_response_quotation_value * 100) / 100).toFixed(2) + "</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, confirm it!",
                closeOnConfirm: false
            },
            function(){
                $http({
                    method: 'GET',
                    url: base_url + '/tech_response_quotation/confirm_tech_response_quotation',
                    params: {
                        id: $scope.data.tech_response_quotation_id
                    }
                }).then(function successCallback(response) {
                    swal({
                        title: "Confirmed!", 
                        text: $scope.customer_name + "(" + $scope.data.tech_response_quotation_no + ")" + " : <strong>" + parseFloat(Math.round($scope.data.tech_response_quotation_value * 100) / 100).toFixed(2) + "</strong>",
                        html: true,
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.edit_disable = true;
                    $scope.main_refresh();
                });
            });
        };

        $scope.reviseForm = function () {
            swal({
                title: "Are you sure?",
                text: $scope.customer_name + "(" + $scope.data.tech_response_quotation_no + ")" + " : <strong>" + parseFloat(Math.round($scope.data.tech_response_quotation_value * 100) / 100).toFixed(2) + "</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, revise it!",
                closeOnConfirm: false
            },
            function(){
                $http({
                    method: 'GET',
                    url: base_url + '/tech_response_quotation/revise_tech_response_quotation',
                    params: {
                        id: $scope.data.tech_response_quotation_id
                    }
                }).then(function successCallback(response) {
                    swal({
                        title: "Revised!", 
                        text: $scope.customer_name + "(" + $scope.data.tech_response_quotation_no + ")" + " : <strong>" + parseFloat(Math.round($scope.data.tech_response_quotation_value * 100) / 100).toFixed(2) + "</strong>",
                        html: true,
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.edit_disable = false;
                    $scope.main_refresh();

                    $timeout(function () {
                        $scope.data.tech_response_quotation_id = 0;
                        $scope.data.tech_response_quotation_no = '';
                        $scope.gridApi.core.refresh();
                    }, 1500, false);
                });
            });
        };

        $scope.previewTechResponseQuotation = function () {
            $scope.preview_data = '';
            $scope.data.discount_data = $scope.gridOptions.data;
            console.log($scope.data.customer_tax);
            $http({
                method: 'POST',
                url: base_url + '/tech_response_quotation/preview_tech_response_quotation',
                data: {
                    data: $scope.data
                }
            }).then(function successCallback(response) {
                $scope.preview_data = response.data.view;
                $scope.data.tech_response_quotation_value = response.data.tech_response_quotation_value;
                $('#data_table').dataTable({
                    "aaSorting": [[0, 'asc']],
                    "paging": false,
                    "searching": false,
                    "info": false
                });
            });
        };

        $scope.resetForm = function(){
            $scope.data.tech_response_quotation_id = 0;
            $scope.refreshForm();
            $scope.main_refresh();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.addDiscountData = function(){
            if($scope.data.discount_type && $scope.data.discount_type.id && $('#dataForm').valid()){
                $scope.gridOptions.data.push({
                    index: $scope.gridOptions.data.length,
                    permission: $scope.permission,
                    is_confirmed: $scope.is_confirmed,
                    discount_type_id: $scope.data.discount_type ? $scope.data.discount_type.id : null,
                    discount_type_name: $scope.data.discount_type ? $scope.data.discount_type.name : null,
                    description: $scope.data.description,
                    percentage: $scope.data.percentage
                });
                
                $scope.data.discount_type = $scope.discount_type_array.length > 0 ? $scope.discount_type_array[0] : {};
                $scope.data.description = '';
                $scope.data.percentage = '';
                
                $(".message_lable").remove();
                $('.form-control').removeClass("error");
                $('.form-control').removeClass("valid");
            }
        };

        $scope.deleteRecord = function (row) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover <strong>" + row.entity.discount_type_name + "</strong> discount!",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                $timeout(function () {
                    $scope.gridOptions.data.splice(row.entity.index, 1);
                    for(var i=0; i<$scope.gridOptions.data.length; i++){
                        $scope.gridOptions.data[i].index = i;
                    } 
                    $scope.gridApi.core.refresh();
                }, 100, false);
                swal({
                    title: "Deleted!", 
                    text: row.entity.discount_type_name + " discount has been deleted.", 
                    type: "success",
                    confirmButtonColor: "#9ACD32"
                });
            });
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            $('#dataModal').modal('hide');
            if($scope.data.tech_response_quotation_id == 0){
                $http.post(base_url + '/tech_response_quotation', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Quotation',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.tech_response_quotation_id = result.response ? result.data : 0;
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/tech_response_quotation/'+$scope.data.tech_response_quotation_id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Quotation',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.tech_response_quotation_id = result.response ? result.data : 0;
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.customer_name+' Discount Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/tech_response_quotation/find_tech_response_quotation',
                params: {
                    id: $scope.data.tech_response_quotation_id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    if(response.data.tech_response_quotation_job_card){
                        for(var i=0; i<$scope.data.tech_response_job_cards.length; i++){
                            for(var j=0; j<response.data.tech_response_quotation_job_card.length; j++){
                                if(response.data.tech_response_quotation_job_card[j].tech_response_job_card && $scope.data.tech_response_job_cards[i].id == response.data.tech_response_quotation_job_card[j].tech_response_job_card.id){
                                    $scope.data.tech_response_job_cards[i].selected = true;
                                }
                            }
                        }
                        for(var i=0; i<response.data.tech_response_quotation_job_card.length; i++){
                            var add = true;
                            for(var j=0; j<$scope.data.tech_response_job_cards.length; j++){
                                if(response.data.tech_response_quotation_job_card[i].tech_response_job_card && response.data.tech_response_quotation_job_card[i].tech_response_job_card.id == $scope.data.tech_response_job_cards[j].id){
                                    add = false;
                                }
                            }
                            if(response.data.tech_response_quotation_job_card[i].tech_response_job_card && add){
                                $scope.data.tech_response_job_cards.push({
                                    id: response.data.tech_response_quotation_job_card[i].tech_response_job_card.id,
                                    name: response.data.tech_response_quotation_job_card[i].tech_response_job_card.tech_response_job_card_no,
                                    description: parseFloat(Math.round(response.data.tech_response_quotation_job_card[i].tech_response_job_card.tech_response_job_card_value * 100) / 100).toFixed(2),
                                    selected: true
                                });
                            }
                        }
                    }
                    
                    var tech_response_quotation_date_time = response.data.tech_response_quotation_date_time.split(' ');
                    $scope.data.tech_response_id = response.data.tech_response_id;
                    $scope.data.tech_response_quotation_id = response.data.id;
                    $scope.data.tech_response_quotation_no = response.data.tech_response_quotation_no;
                    $scope.data.tech_response_quotation_date = tech_response_quotation_date_time[0];
                    $scope.data.tech_response_quotation_time = tech_response_quotation_date_time[1];
                    $scope.data.remarks = response.data.remarks;
                    $scope.data.is_currency = response.data.is_currency == 1 ? true : false;
                    $scope.data.usd_rate = response.data.usd_rate;
                    $scope.data.show_brand = response.data.show_brand == 1 ? true : false;
                    $scope.data.show_origin = response.data.show_origin == 1 ? true : false;
                    $scope.data.show_transport = response.data.show_transport == 1 ? true : false;
                    $scope.data.installation_charge = parseFloat(Math.round(response.data.installation_charge * 100) / 100).toFixed(2);
                    $scope.data.transport_charge = parseFloat(Math.round(response.data.transport_charge * 100) / 100).toFixed(2);
                    $scope.data.attendance_fee = parseFloat(Math.round(response.data.attendance_fee * 100) / 100).toFixed(2);
                    $scope.data.special_notes = response.data.special_notes;
                    $scope.data.show_installation_charge = response.data.show_installation_charge == 1 ? true : false;
                    $scope.data.installation_charge_text = response.data.installation_charge_text;
                    $scope.data.tech_response_quotation_value = response.data.tech_response_quotation_value;
                    $scope.edit_disable = $scope.edit_disable || response.data.is_confirmed == 1;
                }
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
            $http({
                method: 'GET',
                url: base_url + '/tech_response_quotation/discount_detail',
                params: {
                    id: $scope.data.tech_response_quotation_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.discounts, function (index, value) {
                    data_array.push({
                        index: index,
                        permission: response.data.permission ? 1 : 0,
                        is_confirmed: response.data.tech_response_quotation ? response.data.tech_response_quotation.is_confirmed : 0,
                        discount_type_id: value.discount_type ? value.discount_type.id : null,
                        discount_type_name: value.discount_type ? value.discount_type.name : null,
                        description: value.description,
                        percentage: value.percentage
                    });
                });    
                $scope.gridOptions.data = data_array;
                $scope.edit_disable = response.data.tech_response_quotation ? response.data.tech_response_quotation.is_confirmed == 1 || $scope.edit_disable : $scope.edit_disable; 
                $scope.permission = response.data.permission ? 1 : 0;
                $scope.is_confirmed = response.data.tech_response_quotation ? response.data.tech_response_quotation.is_confirmed : 0;
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1500, false);

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection