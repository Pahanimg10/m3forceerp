@extends('layouts.main')

@section('title')
<title>M3Force | C-Groups</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">C-Groups</li>
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
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>C-Groups</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="data_value" name="data_value" ng-model="data.value" class="form-control" />

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ng-model="data.code" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name="address" ng-model="data.address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Contact No</label>
                                            <input type="text" id="contact_no" name="contact_no" ng-model="data.contact_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name="email" ng-model="data.email" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">1. Contact Person</label>
                                            <input type="text" id="contact_person_1" name="contact_person_1" ng-model="data.contact_person_1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">1. Contact Person No</label>
                                            <input type="text" id="contact_person_no_1" name="contact_person_no_1" ng-model="data.contact_person_no_1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">2. Contact Person</label>
                                            <input type="text" id="contact_person_2" name="contact_person_2" ng-model="data.contact_person_2" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">2. Contact Person No</label>
                                            <input type="text" id="contact_person_no_2" name="contact_person_no_2" ng-model="data.contact_person_no_2" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">3. Contact Person</label>
                                            <input type="text" id="contact_person_3" name="contact_person_3" ng-model="data.contact_person_3" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">3. Contact Person No</label>
                                            <input type="text" id="contact_person_no_3" name="contact_person_no_3" ng-model="data.contact_person_no_3" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">VAT No</label>
                                            <input type="text" id="vat_no" name="vat_no" ng-model="data.vat_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">SVAT No</label>
                                            <input type="text" id="svat_no" name="svat_no" ng-model="data.svat_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Region</label>
                                            <select name="region" id="region" ng-options="option.name for option in region_array track by option.id" ng-model="data.region" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Collection Manager</label>
                                            <select name="collection_manager" id="collection_manager" ng-options="option.name for option in collection_manager_array track by option.id" ng-model="data.collection_manager" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Monitoring Fee</label>
                                            <input type="text" id="monitoring_fee" name="monitoring_fee" ng-model="data.monitoring_fee" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Name</label>
                                            <input type="text" id="invoice_name" name="invoice_name" ng-model="data.invoice_name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Delivering Address</label>
                                            <input type="text" id="invoice_delivering_address" name="invoice_delivering_address" ng-model="data.invoice_delivering_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Collection Address</label>
                                            <input type="text" id="collection_address" name="collection_address" ng-model="data.collection_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Invoice E mailing - E Mail Address</label>
                                            <input type="text" id="invoice_email" name="invoice_email" ng-model="data.invoice_email" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Months</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_1">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;&nbsp;&nbsp;</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_2">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;&nbsp;&nbsp;</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_3">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Taxes</label>
                                            <label style="display: block;" ng-repeat="tax in data.taxes">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= tax.description %>"><input type="checkbox" name="selectedTaxes[]" ng-checked="tax.selected" ng-model="tax.selected" />&nbsp;<%= tax.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label> 
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

    </div>
</div> 

<script type="text/javascript">
    var submitForm; 
    
    $('[data-toggle="tooltip"]').tooltip();
    
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
        'ui.grid.cellNav'
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
        angular.element(document.querySelector('#main_menu_master')).addClass('active');
        angular.element(document.querySelector('#sub_menu_c_group')).addClass('active');
    });     
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        name: {
                            required: true,
                            remote: {
                                url: base_url + '/c_group/validate_c_group',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    name: function() {
                                      return scope.data.name;
                                    }
                                }
                            }
                        },
                        address: {
                            required: true
                        },
                        contact_no: {
                            required: true,
                            validContactNo: true
                        },
                        contact_person_no_1: {
                            validContactNo: true
                        },
                        contact_person_no_2: {
                            validContactNo: true
                        },
                        contact_person_no_3: {
                            validContactNo: true
                        },
                        region: {
                            required: true
                        },
                        collection_manager: {
                            required: true
                        },
                        monitoring_fee: {
                            required: true,
                            number: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        name: {
                            required: 'Name is required',
                            remote: 'Name already exist'
                        },
                        address: {
                            required: 'Address is required'
                        },
                        contact_no: {
                            required: 'Contact No is required',
                            validContactNo: 'Invalid number format'
                        },
                        contact_person_no_1: {
                            validContactNo: 'Invalid number format'
                        },
                        contact_person_no_2: {
                            validContactNo: 'Invalid number format'
                        },
                        contact_person_no_3: {
                            validContactNo: 'Invalid number format'
                        },
                        region: {
                            required: 'Region is required'
                        },
                        collection_manager: {
                            required: 'Collection Manager is required'
                        },
                        monitoring_fee: {
                            required: 'Monitoring Fee is required',
                            number: 'Invalid number format'
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

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
            $scope.data = [];
            $scope.region_array = [];
            $scope.collection_manager_array = [];
            $http({
                method: 'GET',
                url: base_url + '/c_group/get_data'
            }).then(function successCallback(response) {
                var region_array = [];
                region_array.push({
                    id: '',
                    name: 'Select Region'
                });
                $.each(response.data.regions, function (index, value) {
                    region_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                var collection_manager_array = [];
                collection_manager_array.push({
                    id: '',
                    name: 'Select Collection Manager'
                });
                $.each(response.data.collection_managers, function (index, value) {
                    collection_manager_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                var inv_month_1_array = [
                    {id: 1, name: 'January', description: '01', selected: false},
                    {id: 2, name: 'February', description: '02', selected: false},
                    {id: 3, name: 'March', description: '03', selected: false},
                    {id: 4, name: 'April', description: '04', selected: false}
                ];
                var inv_month_2_array = [
                    {id: 5, name: 'May', description: '05', selected: false},
                    {id: 6, name: 'June', description: '06', selected: false},
                    {id: 7, name: 'July', description: '07', selected: false},
                    {id: 8, name: 'August', description: '08', selected: false}
                ];
                var inv_month_3_array = [
                    {id: 9, name: 'September', description: '09', selected: false},
                    {id: 10, name: 'October', description: '10', selected: false},
                    {id: 11, name: 'November', description: '11', selected: false},
                    {id: 12, name: 'December', description: '12', selected: false}
                ];
                var tax_array = [];
                $.each(response.data.taxes, function (index, value) {
                    tax_array.push({
                        id: value.id,
                        name: value.code,
                        description: value.name+' - '+parseFloat(Math.round(value.percentage * 100) / 100).toFixed(2)+'%',
                        selected: false
                    });
                }); 
                
                $scope.region_array = region_array;
                $scope.collection_manager_array = collection_manager_array;
                $scope.data = {
                    id: 0,
                    value: '',
                    code: '',
                    name: '',
                    address: '',
                    contact_no: '',
                    email: '',
                    contact_person_1: '',
                    contact_person_no_1: '',
                    contact_person_2: '',
                    contact_person_no_2: '',
                    contact_person_3: '',
                    contact_person_no_3: '',
                    invoice_name: '',
                    invoice_delivering_address: '',
                    collection_address: '',
                    invoice_email: '',
                    vat_no: '',
                    svat_no: '',
                    region: $scope.region_array.length > 0 ? $scope.region_array[0] : {},
                    collection_manager: $scope.collection_manager_array.length > 0 ? $scope.collection_manager_array[0] : {},
                    monitoring_fee: '',
                    inv_months_1: inv_month_1_array,
                    inv_months_2: inv_month_2_array,
                    inv_months_3: inv_month_3_array,
                    taxes: tax_array
                };
                $scope.resetCopy = angular.copy($scope.data);
            }, function errorCallback(response) {
                console.log(response);
            });
        
            $scope.gridOptions = {
                columnDefs: [
                    {
                        field: 'id', 
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
                        field: 'options', 
                        displayName: '', 
                        enableFiltering: false, 
                        enableSorting: false, 
                        enableCellEdit: false,
                        width: '10%', 
                        cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                    },
                    {
                        field: 'code', 
                        displayName: 'Code', 
                        cellClass: 'grid-align',
                        width: '10%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'name', 
                        displayName: 'Name', 
                        width: '25%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'address', 
                        displayName: 'Address', 
                        width: '40%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'contact_no', 
                        displayName: 'Contact No', 
                        cellClass: 'grid-align',
                        width: '15%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'email', 
                        displayName: 'Email', 
                        width: '15%', 
                        enableCellEdit: false
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
                exporterCsvFilename: 'C-Groups.csv',
                onRegisterApi: function (gridApi) {
                    $scope.gridApi = gridApi;
                }
            };

            $scope.export = function () {
                var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
                $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
            };
            
            $scope.toggleFiltering = function () {
                $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
                $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
            };

            $scope.editRecord = function (row) {
                $scope.resetForm();
                $http({
                    method: 'GET',
                    url: base_url + '/c_group/find_c_group',
                    params: {
                        id: row.entity.id
                    }
                }).then(function successCallback(response) {
                    for(var i=0; i<$scope.data.inv_months_1.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_1[i].id == value.month){
                                $scope.data.inv_months_1[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_2.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_2[i].id == value.month){
                                $scope.data.inv_months_2[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_3.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_3[i].id == value.month){
                                $scope.data.inv_months_3[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.taxes.length; i++){
                        $.each(response.data.c_group_tax, function (index, value) {
                            if($scope.data.taxes[i].id == value.tax_id){
                                $scope.data.taxes[i].selected = true;
                            }
                        });  
                    }

                    $scope.data = {
                        id: response.data.id,
                        value: response.data.name,
                        code: response.data.code,
                        name: response.data.name,
                        address: response.data.address,
                        contact_no: response.data.contact_no,
                        email: response.data.email,
                        contact_person_1: response.data.contact_person_1,
                        contact_person_no_1: response.data.contact_person_no_1,
                        contact_person_2: response.data.contact_person_2,
                        contact_person_no_2: response.data.contact_person_no_2,
                        contact_person_3: response.data.contact_person_3,
                        contact_person_no_3: response.data.contact_person_no_3,
                        invoice_name: response.data.invoice_name,
                        invoice_delivering_address: response.data.invoice_delivering_address,
                        collection_address: response.data.collection_address,
                        invoice_email: response.data.invoice_email,
                        vat_no: response.data.vat_no,
                        svat_no: response.data.svat_no,
                        region: response.data.region ? {id: response.data.region.id, name: response.data.region.name} : {},
                        collection_manager: response.data.collection_manager ? {id: response.data.collection_manager.id, name: response.data.collection_manager.name} : {},
                        monitoring_fee: parseFloat(Math.round(response.data.monitoring_fee * 100) / 100).toFixed(2),
                        inv_months_1: $scope.data.inv_months_1,
                        inv_months_2: $scope.data.inv_months_2,
                        inv_months_3: $scope.data.inv_months_3,
                        taxes: $scope.data.taxes
                    };
                }, function errorCallback(response) {
                    console.log(response);
                });
            };

            $scope.deleteRecord = function (row) {
                $scope.resetForm();
                $http({
                    method: 'GET',
                    url: base_url + '/c_group/find_c_group',
                    params: {
                        id: row.entity.id
                    }
                }).then(function successCallback(response) {
                    for(var i=0; i<$scope.data.inv_months_1.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_1[i].id == value.month){
                                $scope.data.inv_months_1[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_2.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_2[i].id == value.month){
                                $scope.data.inv_months_2[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_3.length; i++){
                        $.each(response.data.c_group_invoice_month, function (index, value) {
                            if($scope.data.inv_months_3[i].id == value.month){
                                $scope.data.inv_months_3[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.taxes.length; i++){
                        $.each(response.data.c_group_tax, function (index, value) {
                            if($scope.data.taxes[i].id == value.tax_id){
                                $scope.data.taxes[i].selected = true;
                            }
                        });  
                    }

                    $scope.data = {
                        id: response.data.id,
                        value: response.data.name,
                        code: response.data.code,
                        name: response.data.name,
                        address: response.data.address,
                        contact_no: response.data.contact_no,
                        email: response.data.email,
                        contact_person_1: response.data.contact_person_1,
                        contact_person_no_1: response.data.contact_person_no_1,
                        contact_person_2: response.data.contact_person_2,
                        contact_person_no_2: response.data.contact_person_no_2,
                        contact_person_3: response.data.contact_person_3,
                        contact_person_no_3: response.data.contact_person_no_3,
                        invoice_name: response.data.invoice_name,
                        invoice_delivering_address: response.data.invoice_delivering_address,
                        collection_address: response.data.collection_address,
                        invoice_email: response.data.invoice_email,
                        vat_no: response.data.vat_no,
                        svat_no: response.data.svat_no,
                        region: response.data.region ? {id: response.data.region.id, name: response.data.region.name} : {},
                        collection_manager: response.data.collection_manager ? {id: response.data.collection_manager.id, name: response.data.collection_manager.name} : {},
                        monitoring_fee: parseFloat(Math.round(response.data.monitoring_fee * 100) / 100).toFixed(2),
                        inv_months_1: $scope.data.inv_months_1,
                        inv_months_2: $scope.data.inv_months_2,
                        inv_months_3: $scope.data.inv_months_3,
                        taxes: $scope.data.taxes
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.name + "</strong> c-group!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/c_group/'+$scope.data.id, $scope.data).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.name + " c-group has been deleted.", 
                                type: "success",
                                confirmButtonColor: "#9ACD32"
                            });
                            $scope.resetForm();
                            $scope.main_refresh();
                        });
                    });
                }, function errorCallback(response) {
                    console.log(response);
                });
            };

            $scope.resetForm = function(){
                $scope.data = angular.copy($scope.resetCopy);

                $(".message_lable").remove();
                $('.form-control').removeClass("error");
                $('.form-control').removeClass("valid");
            };

            $scope.submitForm = function(){
                $('#dataForm').submit();
            };
            
            submitForm = function(){
                $('#save_button').prop('disabled', true);
                if($scope.data.id == 0){
                    $http.post(base_url + '/c_group', $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'C-Groups',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                } else{
                    $http.put(base_url + '/c_group/'+$scope.data.id, $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'C-Groups',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                }
            };

            $scope.main_refresh = function(){
                document.getElementById('data_load').style.visibility = "visible";
                $http({
                    method: 'GET',
                    url: base_url + '/c_group/c_group_list'
                }).then(function successCallback(response) {
                    var data_array = [];
                    $.each(response.data.c_groups, function (index, value) {
                        data_array.push({
                            id: value.id,
                            permission: response.data.permission ? 1 : 0,
                            code: value.code,
                            name: value.name,
                            address: value.address,
                            contact_no: value.contact_no,
                            email: value.email
                        });
                    });    
                    $scope.gridOptions.data = data_array;
                    document.getElementById('data_load').style.visibility = "hidden";
                }, function errorCallback(response) {
                    console.log(response);
                });
            };

            $timeout(function () {
                $scope.main_refresh();
            }, 1500, false); 
            
            $.validator.addMethod('validContactNo', function (value) {                
                if(value != ''){
                    return value.match(/^[\s\+0-9]+$/); 
                } else{
                    return true;
                }
            });
        }]);
</script>
@endsection