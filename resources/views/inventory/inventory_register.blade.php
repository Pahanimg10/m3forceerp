@extends('layouts.main')

@section('title')
<title>M3Force | Inventory Register</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inventory</a></li>                    
    <li class="active">Inventory Register</li>
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
                    <h3 class="panel-title"><strong>Inventory Register</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ng-model="data.code" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Location</label>
                                            <select name="inventory_location" id="inventory_location" ng-options="option.name for option in inventory_location_array track by option.id" ng-model="data.inventory_location" ng-disabled="data.id != 0" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Type</label>
                                            <select name="inventory_type" id="inventory_type" ng-options="option.name for option in inventory_type_array track by option.id" ng-model="data.inventory_type" ng-disabled="data.id != 0" class="form-control" ></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>             
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Model No</label>             
                                            <input type="text" id="model_no" name="model_no" ng-model="data.model_no" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">IMEI</label>             
                                            <input type="text" id="imei" name="imei" ng-model="data.imei" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Serial No</label>             
                                            <input type="text" id="serial_no" name="serial_no" ng-model="data.serial_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4" ng-show="data.inventory_type && data.inventory_type.id == 5">
                                        <div class="form-group">
                                            <label class="control-label">Credit Limit</label>             
                                            <input type="text" id="credit_limit" name="credit_limit" ng-model="data.credit_limit" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>             
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <h4 class="col-md-12">Filter Details</h4>
                        </div>                      
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Inventory Location</label>
                                    <select name="filter_inventory_location" id="filter_inventory_location" ng-options="option.name for option in filter_inventory_location_array track by option.id" ng-model="data.filter_inventory_location" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Inventory Type</label>
                                    <select name="filter_inventory_type" id="filter_inventory_type" ng-options="option.name for option in filter_inventory_type_array track by option.id" ng-model="data.filter_inventory_type" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <select name="filter_status" id="filter_status" ng-options="option.name for option in filter_status_array track by option.id" ng-model="data.filter_status" ng-change="main_refresh()" class="form-control" ></select>
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
        angular.element(document.querySelector('#main_menu_inventory')).addClass('active');
        angular.element(document.querySelector('#sub_menu_inventory_register')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        inventory_location: {
                            required: true
                        },
                        inventory_type: {
                            required: true
                        },
                        name: {
                            required: true
                        },
                        serial_no: {
                            required: true,
                            remote: {
                                url: base_url + '/inventory_register/validate_inventory_register',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    serial_no: function() {
                                      return scope.data.serial_no;
                                    }
                                }
                            }
                        },
                        credit_limit: {
                            number: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        inventory_location: {
                            required: 'Inventory Location is required'
                        },
                        inventory_type: {
                            required: 'Inventory Type is required'
                        },
                        name: {
                            required: 'Name is required'
                        },
                        serial_no: {
                            remote: 'Serial No already exist'
                        },
                        credit_limit: {
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
        $scope.inventory_location_array = [];
        $scope.inventory_type_array = [];
        $scope.filter_inventory_location_array = [];
        $scope.filter_inventory_type_array = [];
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/get_inventory_data'
            }).then(function successCallback(response) {
                var inventory_location_array = [];
                inventory_location_array.push({
                    id: '',
                    code: '',
                    name: 'Inventory Location'
                });
                var filter_inventory_location_array = [];
                filter_inventory_location_array.push({
                    id: -1,
                    name: 'All'
                });
                var inventory_type_array = [];
                inventory_type_array.push({
                    id: '',
                    code: '',
                    name: 'Inventory Type'
                });
                var filter_inventory_type_array = [];
                filter_inventory_type_array.push({
                    id: -1,
                    name: 'All'
                });
                $.each(response.data.inventory_locations, function (index, value) {
                    inventory_location_array.push({
                        id: value.id,
                        code: value.code,
                        name: value.name
                    });
                    filter_inventory_location_array.push({
                        id: value.id,
                        name: value.name
                    });
                });
                $.each(response.data.inventory_types, function (index, value) {
                    inventory_type_array.push({
                        id: value.id,
                        code: value.code,
                        name: value.name
                    });
                    filter_inventory_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.inventory_location_array = inventory_location_array;
                $scope.inventory_type_array = inventory_type_array;
                $scope.filter_inventory_location_array = filter_inventory_location_array;
                $scope.filter_inventory_type_array = filter_inventory_type_array;
                $scope.filter_status_array = [{id: -1, name: 'All'}, {id: 0, name: 'Available'}, {id: 1, name: 'Issued'}];
                $scope.data = {
                    id: 0,
                    value: '',
                    code: '',
                    inventory_location: $scope.inventory_location_array.length > 0 ? $scope.inventory_location_array[0] : {},
                    inventory_type: $scope.inventory_type_array.length > 0 ? $scope.inventory_type_array[0] : {},
                    name: '',
                    model_no: '',
                    imei: '',
                    serial_no: '',
                    credit_limit: '',
                    remarks: '',
                    filter_inventory_location: $scope.filter_inventory_location_array.length > 0 ? $scope.filter_inventory_location_array[0] : {},
                    filter_inventory_type: $scope.filter_inventory_type_array.length > 0 ? $scope.filter_inventory_type_array[0] : {},
                    filter_status: $scope.filter_status_array.length > 0 ? $scope.filter_status_array[0] : {}
                };
                $scope.resetCopy = angular.copy($scope.data);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();

        $scope.resetForm = function(){
            $scope.data = angular.copy($scope.resetCopy);
            $scope.main_refresh();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'is_issued', 
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-disabled="row.entity.is_issued == 1"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.is_issued == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'code', 
                    displayName: 'Code', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_location', 
                    displayName: 'Inventory Location', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_type', 
                    displayName: 'Inventory Type', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'name', 
                    displayName: 'Name', 
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'model_no', 
                    displayName: 'Model No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'imei', 
                    displayName: 'IMEI', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'serial_no', 
                    displayName: 'Serial No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'credit_limit', 
                    displayName: 'Credit Limit', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'remarks', 
                    displayName: 'Remarks', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'issued', 
                    displayName: 'Issued', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'issued_to', 
                    displayName: 'Issued To', 
                    width: '25%', 
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
                url: base_url + '/inventory_register/find_inventory_register',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                $scope.data = {
                    id: response.data.id,
                    value: response.data.serial_no,
                    code: response.data.code,
                    inventory_location: response.data.inventory_location ? {id:response.data.inventory_location.id, code:response.data.inventory_location.code, name:response.data.inventory_location.name} : {},
                    inventory_type: response.data.inventory_type ? {id:response.data.inventory_type.id, code:response.data.inventory_type.code, name:response.data.inventory_type.name} : {},
                    name: response.data.name,
                    model_no: response.data.model_no,
                    imei: response.data.imei,
                    serial_no: response.data.serial_no,
                    credit_limit: response.data.credit_limit != 0 ? parseFloat(Math.round(response.data.credit_limit * 100) / 100).toFixed(2) : '',
                    remarks: response.data.remarks,
                    filter_inventory_location: $scope.data.filter_inventory_location,
                    filter_inventory_type: $scope.data.filter_inventory_type,
                    filter_status: $scope.data.filter_status
                };
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/inventory_register/find_inventory_register',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                $scope.data = {
                    id: response.data.id,
                    value: response.data.serial_no,
                    code: response.data.code,
                    inventory_location: response.data.inventory_location ? {id:response.data.inventory_location.id, code:response.data.inventory_location.code, name:response.data.inventory_location.name} : {},
                    inventory_type: response.data.inventory_type ? {id:response.data.inventory_type.id, code:response.data.inventory_type.code, name:response.data.inventory_type.name} : {},
                    name: response.data.name,
                    model_no: response.data.model_no,
                    imei: response.data.imei,
                    serial_no: response.data.serial_no,
                    credit_limit: response.data.credit_limit != 0 ? parseFloat(Math.round(response.data.credit_limit * 100) / 100).toFixed(2) : '',
                    remarks: response.data.remarks,
                    filter_inventory_location: $scope.data.filter_inventory_location,
                    filter_inventory_type: $scope.data.filter_inventory_type,
                    filter_status: $scope.data.filter_status
                };
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + response.data.name + "</strong> inventory register!",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $http.delete(base_url + '/inventory_register/'+$scope.data.id, $scope.data).success(function (response_delete) {
                        swal({
                            title: "Deleted!", 
                            text: response.data.name + " inventory register has been deleted.", 
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

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/inventory_register', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Inventory Register',
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
                $http.put(base_url + '/inventory_register/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Inventory Register',
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
            var inventory_location = $scope.data.filter_inventory_location ? $scope.data.filter_inventory_location.name : 'All';
            var inventory_type = $scope.data.filter_inventory_type ? $scope.data.filter_inventory_type.name : 'All';
            var status = $scope.data.filter_status ? $scope.data.filter_status.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Inventory Register Inventory Location - '+inventory_location+' Inventory Type - '+inventory_type+' Status - '+status+'.csv';
            $http({
                method: 'GET',
                url: base_url + '/inventory_register/inventory_register_list',
                params: {
                    inventory_location_id: $scope.data.filter_inventory_location ? $scope.data.filter_inventory_location.id : -1, 
                    inventory_type_id: $scope.data.filter_inventory_type ? $scope.data.filter_inventory_type.id : -1,                   
                    status_id: $scope.data.filter_status ? $scope.data.filter_status.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    var last_issue_id = value.inventory_issue_details.length;
                    data_array.push({
                        id: value.id,
                        is_issued: value.is_issued,
                        code: value.code,
                        inventory_location: value.inventory_location ? value.inventory_location.name : '',
                        inventory_type: value.inventory_type ? value.inventory_type.name : '',
                        name: value.name,
                        model_no: value.model_no,
                        imei: value.imei,
                        serial_no: value.serial_no,
                        credit_limit: value.credit_limit != 0 ? parseFloat(Math.round(value.credit_limit * 100) / 100).toFixed(2) : '',
                        remarks: value.remarks,
                        issued: value.is_issued == 1 ? 'Yes' : 'No',
                        issued_to: last_issue_id > 0 && value.inventory_issue_details[last_issue_id-1].inventory_issue ? value.inventory_issue_details[last_issue_id-1].inventory_issue.issued_to : ''
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
    }]);
</script>
@endsection