@extends('layouts.main')

@section('title')
<title>M3Force | Contact Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">Contact Details</li>
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
                    <h3 class="panel-title"><strong>Contact Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('contact')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="data_value" name="data_value" ng-model="data.value" class="form-control" />

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Contact Type</label>
                                            <select name="contact_type" id="contact_type" ng-options="option.name for option in contact_type_array track by option.id" ng-model="data.contact_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.contact_type && data.contact_type.id == 2">
                                        <div class="form-group">
                                            <label class="control-label">Business Type</label>
                                            <select name="business_type" id="business_type" ng-options="option.name for option in business_type_array track by option.id" ng-model="data.business_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ng-model="data.code" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Contact ID</label>
                                            <input type="text" id="contact_id" name="contact_id" ng-model="data.contact_id" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name="email" ng-model="data.email" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">NIC</label>
                                            <input type="text" id="nic" name="nic" ng-model="data.nic" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Group</label><br/>
                                            <input id="is_group" bs-switch emit-change="is_group" ng-model="data.is_group" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">1. Contact Person</label>
                                            <input type="text" id="contact_person_1" name="contact_person_1" ng-model="data.contact_person_1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">1. Contact Person No</label>
                                            <input type="text" id="contact_person_no_1" name="contact_person_no_1" ng-model="data.contact_person_no_1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">2. Contact Person</label>
                                            <input type="text" id="contact_person_2" name="contact_person_2" ng-model="data.contact_person_2" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">2. Contact Person No</label>
                                            <input type="text" id="contact_person_no_2" name="contact_person_no_2" ng-model="data.contact_person_no_2" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">3. Contact Person</label>
                                            <input type="text" id="contact_person_3" name="contact_person_3" ng-model="data.contact_person_3" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">3. Contact Person No</label>
                                            <input type="text" id="contact_person_no_3" name="contact_person_no_3" ng-model="data.contact_person_no_3" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3" ng-show="data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">Group Name</label>
                                            <select name="group" id="group" ng-options="option.name for option in group_array track by option.id" ng-model="data.group" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">VAT No</label>
                                            <input type="text" id="vat_no" name="vat_no" ng-model="data.vat_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">SVAT No</label>
                                            <input type="text" id="svat_no" name="svat_no" ng-model="data.svat_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group && data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Region</label>
                                            <select name="region" id="region" ng-options="option.name for option in region_array track by option.id" ng-model="data.region" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group && data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Collection Manager</label>
                                            <select name="collection_manager" id="collection_manager" ng-options="option.name for option in collection_manager_array track by option.id" ng-model="data.collection_manager" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="!data.is_group && data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Monitoring Fee</label>
                                            <input type="text" id="monitoring_fee" name="monitoring_fee" ng-model="data.monitoring_fee" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Active</label><br/>
                                            <input id="is_active" bs-switch emit-change="is_active" ng-model="data.is_active" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" ng-show="data.contact_type && data.contact_type.id != 3">
                                    <div class="col-md-3" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Name</label>
                                            <input type="text" id="invoice_name" name="invoice_name" ng-model="data.invoice_name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Delivering Address</label>
                                            <input type="text" id="invoice_delivering_address" name="invoice_delivering_address" ng-model="data.invoice_delivering_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">Collection Address</label>
                                            <input type="text" id="collection_address" name="collection_address" ng-model="data.collection_address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group">
                                        <div class="form-group">
                                            <label class="control-label">Invoice E mailing - E Mail Address</label>
                                            <input type="text" id="invoice_email" name="invoice_email" ng-model="data.invoice_email" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3" ng-show="data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Service Mode</label>
                                            <select name="service_mode" id="service_mode" ng-options="option.name for option in service_mode_array track by option.id" ng-model="data.service_mode" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Client Type</label>
                                            <select name="client_type" id="client_type" ng-options="option.name for option in client_type_array track by option.id" ng-model="data.client_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Contract Start Date</label>
                                            <input type="text" id="start_date" name="start_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.start_date" is-open="startDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenStartDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.contact_type && (data.contact_type.id == 1 || data.contact_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Contract End Date</label>
                                            <input type="text" id="end_date" name="end_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.end_date" is-open="endDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenEndDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3" ng-show="!data.is_group && data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Months</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_1">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group && data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;&nbsp;&nbsp;</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_2">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group && data.contact_type && data.contact_type.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;&nbsp;&nbsp;</label>
                                            <label style="display: block;" ng-repeat="month in data.inv_months_3">
                                                <span data-toggle="tooltip" style="cursor: pointer;" title="<%= month.description %>"><input type="checkbox" name="selectedInvMonths[]" ng-checked="month.selected" ng-model="month.selected" />&nbsp;<%= month.name %>&nbsp;&nbsp;&nbsp;</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="!data.is_group">
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
        angular.element(document.querySelector('#main_menu_master')).addClass('active');
        angular.element(document.querySelector('#sub_menu_contact')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        contact_type: {
                            required: true
                        },
                        business_type: {
                            required: true
                        },
                        contact_id: {
                            required: true,
                            number: true,
                            remote: {
                                url: base_url + '/contact/validate_contact',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    contact_id: function() {
                                      return scope.data.contact_id;
                                    }
                                }
                            }
                        },
                        name: {
                            required: true
                        },
                        address: {
                            required: true
                        },
                        contact_no: {
                            required: true,
                            validContactNo: true
                        },
                        email: {
                            email: true
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
                        start_date: {
                            date: true
                        },
                        end_date: {
                            date: true
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
                        service_mode: {
                            required: true
                        },
                        client_type: {
                            required: true
                        },
                        group: {
                            required: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        contact_type: {
                            required: 'Contact Type is required'
                        },
                        business_type: {
                            required: 'Business Type is required'
                        },
                        contact_id: {
                            required: 'Contact ID is required',
                            number: 'Invalid number format',
                            remote: 'Contact ID already exist'
                        },
                        name: {
                            required: 'Name is required'
                        },
                        address: {
                            required: 'Address is required'
                        },
                        contact_no: {
                            required: 'Contact No is required',
                            validContactNo: 'Invalid number format'
                        },
                        email: {
                            email: 'Invalid email format'
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
                        start_date: {
                            date: 'Invalid date format'
                        },
                        end_date: {
                            date: 'Invalid date format'
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
                        },
                        service_mode: {
                            required: 'Service Mode is required'
                        },
                        client_type: {
                            required: 'Client Type is required'
                        },
                        group: {
                            required: 'Group Name is required'
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
        $scope.contact_type_array = [];
        $scope.business_type_array = [];
        $scope.region_array = [];
        $scope.collection_manager_array = [];
        $scope.service_mode_array = [];
        $scope.client_type_array = [];
        $scope.group_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.startDatePopup = {
            opened: false
        };        
        $scope.OpenStartDate = function () {
            $scope.startDatePopup.opened = !$scope.startDatePopup.opened;
        };
        
        $scope.endDatePopup = {
            opened: false
        };        
        $scope.OpenEndDate = function () {
            $scope.endDatePopup.opened = !$scope.endDatePopup.opened;
        };
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/contact/get_data'
            }).then(function successCallback(response) {
                var contact_type_array = [];
                contact_type_array.push({
                    id: '',
                    name: 'Select Contact Type'
                });
                var business_type_array = [];
                business_type_array.push({
                    id: '',
                    name: 'Select Business Type'
                });
                var region_array = [];
                region_array.push({
                    id: '',
                    name: 'Select Region'
                });
                var collection_manager_array = [];
                collection_manager_array.push({
                    id: '',
                    name: 'Select Collection Manager'
                });
                var service_mode_array = [];
                service_mode_array.push({
                    id: '',
                    name: 'Select Service Mode'
                });
                var client_type_array = [];
                client_type_array.push({
                    id: '',
                    name: 'Select Client Type'
                });
                var group_array = [];
                group_array.push({
                    id: '',
                    name: 'Select Group Name'
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

                $.each(response.data.contact_types, function (index, value) {
                    contact_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                $.each(response.data.business_types, function (index, value) {
                    business_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });
                $.each(response.data.regions, function (index, value) {
                    region_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                $.each(response.data.collection_managers, function (index, value) {
                    collection_manager_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                $.each(response.data.service_modes, function (index, value) {
                    service_mode_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                $.each(response.data.client_types, function (index, value) {
                    client_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                $.each(response.data.taxes, function (index, value) {
                    tax_array.push({
                        id: value.id,
                        name: value.code,
                        description: value.name+' - '+parseFloat(Math.round(value.percentage * 100) / 100).toFixed(2)+'%',
                        selected: false
                    });
                }); 
                $.each(response.data.groups, function (index, value) {
                    group_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 

                $scope.contact_type_array = contact_type_array;
                $scope.business_type_array = business_type_array;
                $scope.region_array = region_array;
                $scope.collection_manager_array = collection_manager_array;
                $scope.service_mode_array = service_mode_array;
                $scope.client_type_array = client_type_array;
                $scope.group_array = group_array;
                $scope.data = {
                    id: 0,
                    value: '',
                    contact_type: $scope.contact_type_array.length > 0 ? $scope.contact_type_array[0] : {},
                    business_type: $scope.business_type_array.length > 0 ? $scope.business_type_array[0] : {},
                    code: '',
                    contact_id: '',
                    name: '',
                    nic: '',
                    address: '',
                    contact_no: '',
                    email: '',
                    contact_person_1: '',
                    contact_person_no_1: '',
                    contact_person_2: '',
                    contact_person_no_2: '',
                    contact_person_3: '',
                    contact_person_no_3: '',
                    start_date: new Date(),
                    end_date: new Date(),
                    invoice_name: '',
                    invoice_delivering_address: '',
                    collection_address: '',
                    invoice_email: '',
                    vat_no: '',
                    svat_no: '',
                    region: $scope.region_array.length > 0 ? $scope.region_array[0] : {},
                    collection_manager: $scope.collection_manager_array.length > 0 ? $scope.collection_manager_array[0] : {},
                    monitoring_fee: '',
                    service_mode: $scope.service_mode_array.length > 0 ? $scope.service_mode_array[0] : {},
                    client_type: $scope.client_type_array.length > 0 ? $scope.client_type_array[0] : {},
                    group: $scope.group_array.length > 0 ? $scope.group_array[0] : {},
                    is_group: false,
                    is_active: true,
                    inv_months_1: inv_month_1_array,
                    inv_months_2: inv_month_2_array,
                    inv_months_3: inv_month_3_array,
                    taxes: tax_array
                };
                $scope.resetCopy = angular.copy($scope.data);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();

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
            console.log($scope.data);
            if($scope.data.id == 0){
                $http.post(base_url + '/contact', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Contact Details',
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
                $http.put(base_url + '/contact/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Contact Details',
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
                url: base_url + '/contact/find_contact',
                params: {
                    id: <?php echo $contact_id ? $contact_id : 0; ?>
                }
            }).then(function successCallback(response) {
                if(response.data){
                    for(var i=0; i<$scope.data.inv_months_1.length; i++){
                        $.each(response.data.contact_invoice_month, function (index, value) {
                            if($scope.data.inv_months_1[i].id == value.month){
                                $scope.data.inv_months_1[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_2.length; i++){
                        $.each(response.data.contact_invoice_month, function (index, value) {
                            if($scope.data.inv_months_2[i].id == value.month){
                                $scope.data.inv_months_2[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.inv_months_3.length; i++){
                        $.each(response.data.contact_invoice_month, function (index, value) {
                            if($scope.data.inv_months_3[i].id == value.month){
                                $scope.data.inv_months_3[i].selected = true;
                            }
                        });  
                    }
                    for(var i=0; i<$scope.data.taxes.length; i++){
                        $.each(response.data.contact_tax, function (index, value) {
                            if($scope.data.taxes[i].id == value.tax_id){
                                $scope.data.taxes[i].selected = true;
                            }
                        });  
                    }

                    $scope.data = {
                        id: response.data.id,
                        value: response.data.contact_id,
                        contact_type: response.data.c_contact_type ? {id: response.data.c_contact_type.id, name: response.data.c_contact_type.name} : {},
                        business_type: response.data.i_business_type ? {id: response.data.i_business_type.id, name: response.data.i_business_type.name} : {},
                        code: response.data.code,
                        contact_id: response.data.contact_id,
                        name: response.data.name,
                        nic: response.data.nic,
                        address: response.data.address,
                        contact_no: response.data.contact_no,
                        email: response.data.email,
                        contact_person_1: response.data.contact_person_1,
                        contact_person_no_1: response.data.contact_person_no_1,
                        contact_person_2: response.data.contact_person_2,
                        contact_person_no_2: response.data.contact_person_no_2,
                        contact_person_3: response.data.contact_person_3,
                        contact_person_no_3: response.data.contact_person_no_3,
                        start_date: response.data.start_date,
                        end_date: response.data.end_date,
                        invoice_name: response.data.invoice_name,
                        invoice_delivering_address: response.data.invoice_delivering_address,
                        collection_address: response.data.collection_address,
                        invoice_email: response.data.invoice_email,
                        vat_no: response.data.vat_no,
                        svat_no: response.data.svat_no,
                        region: response.data.region ? {id: response.data.region.id, name: response.data.region.name} : {},
                        collection_manager: response.data.collection_manager ? {id: response.data.collection_manager.id, name: response.data.collection_manager.name} : {},
                        monitoring_fee: parseFloat(Math.round(response.data.monitoring_fee * 100) / 100).toFixed(2),
                        service_mode: response.data.service_mode ? {id: response.data.service_mode.id, name: response.data.service_mode.name} : {},
                        client_type: response.data.i_client_type ? {id: response.data.i_client_type.id, name: response.data.i_client_type.name} : {},
                        group: response.data.c_group ? {id: response.data.c_group.id, name: response.data.c_group.name} : {},
                        is_group: response.data.is_group == 1 ? true : false,
                        is_active: response.data.is_active == 1 ? true : false,
                        inv_months_1: $scope.data.inv_months_1,
                        inv_months_2: $scope.data.inv_months_2,
                        inv_months_3: $scope.data.inv_months_3,
                        taxes: $scope.data.taxes
                    };   
                }
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