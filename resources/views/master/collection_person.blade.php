@extends('layouts.main')

@section('title')
<title>M3Force | Collection Person</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">Collection Person</li>
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
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Collection Person</strong></h3>
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

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Name</label>             
                                        <div class="col-md-10">
                                            <input type="text" id="name" name="name" placeholder="Mark Edwin" ng-model="data.name" class="form-control" required="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Contact No</label>             
                                        <div class="col-md-8">
                                            <input type="text" id="contact_no" name="contact_no" placeholder="0712345678" ng-model="data.contact_no" class="form-control" required="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Active</label>             
                                        <div class="col-md-8">
                                            <input id="is_active" bs-switch emit-change="is_active" ng-model="data.is_active" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
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
        angular.element(document.querySelector('#sub_menu_collection_person')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        name: {
                            required: true,
                            remote: {
                                url: base_url + '/collection_person/validate_collection_person',
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
                        contact_no: {
                            required: true,
                            validContactNo: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        name: {
                            required: 'Name is required',
                            remote: 'Name already exist'
                        },
                        contact_no: {
                            required: 'Contact No is required',
                            validContactNo: 'Invalid number format'
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
            $scope.data = {
                id: 0,
                value: '',
                name: '',
                contact_no: '',
                is_active: true
            };
            $scope.resetCopy = angular.copy($scope.data);
        
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
                        field: 'code', 
                        displayName: 'Code', 
                        cellClass: 'grid-align',
                        width: '10%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'name', 
                        displayName: 'Name', 
                        width: '50%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'contact_no', 
                        displayName: 'Contact No', 
                        cellClass: 'grid-align',
                        width: '20%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'is_active', 
                        displayName: 'Active', 
                        cellClass: 'grid-align',
                        width: '10%', 
                        enableCellEdit: false
                    },
                    {
                        field: 'options', 
                        displayName: '', 
                        enableFiltering: false, 
                        enableSorting: false, 
                        enableCellEdit: false,
                        width: '10%', 
                        cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
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
                exporterCsvFilename: 'Collection Person.csv',
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
                    url: base_url + '/collection_person/find_collection_person',
                    params: {
                        id: row.entity.id
                    }
                }).then(function successCallback(response) {
                    $scope.data = {
                        id: response.data.id,
                        value: response.data.name,
                        name: response.data.name,
                        contact_no: response.data.contact_no,
                        is_active: response.data.is_active == 1 ? true : false
                    };
                }, function errorCallback(response) {
                    console.log(response);
                });
            };

            $scope.deleteRecord = function (row) {
                $scope.resetForm();
                $http({
                    method: 'GET',
                    url: base_url + '/collection_person/find_collection_person',
                    params: {
                        id: row.entity.id
                    }
                }).then(function successCallback(response) {
                    $scope.data = {
                        id: response.data.id,
                        value: response.data.name,
                        name: response.data.name,
                        contact_no: response.data.contact_no,
                        is_active: response.data.is_active == 1 ? true : false
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.name + "</strong> collection person!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/collection_person/'+$scope.data.id, $scope.data).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.name + " collection person has been deleted.", 
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
                    $http.post(base_url + '/collection_person', $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Collection Person',
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
                    $http.put(base_url + '/collection_person/'+$scope.data.id, $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Collection Person',
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
                    url: base_url + '/collection_person/collection_person_list'
                }).then(function successCallback(response) {
                    var data_array = [];
                    $.each(response.data.collection_persons, function (index, value) {
                        data_array.push({
                            id: value.id,
                            permission: response.data.permission ? 1 : 0,
                            code: value.code,
                            name: value.name,
                            contact_no: value.contact_no,
                            is_active: value.is_active == 1 ? 'Yes' : 'No'
                        });
                    });    
                    $scope.gridOptions.data = data_array;
                    document.getElementById('data_load').style.visibility = "hidden";
                }, function errorCallback(response) {
                    console.log(response);
                });
            };
            
            $scope.main_refresh(); 
            
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