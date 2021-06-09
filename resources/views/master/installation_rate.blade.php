@extends('layouts.main')

@section('title')
<title>M3Force | Installation Rates</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">Installation Rates</li>
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
                    <h3 class="panel-title"><strong>Installation Rate</strong></h3>
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
                                            <input type="text" id="code" name="code" ng-model="data.code" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Installation Cost</label>
                                            <input type="text" id="installation_cost" name="installation_cost" ng-model="data.installation_cost" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Labour</label>
                                            <input type="text" id="labour" name="labour" ng-model="data.labour" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Rate</label>
                                            <input type="text" id="rate" name="rate" ng-model="data.rate" class="form-control text-right" />
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
        angular.element(document.querySelector('#sub_menu_installation_rate')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        name: {
                            required: true,
                            remote: {
                                url: base_url + '/installation_rate/validate_installation_rate',
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
                        installation_cost: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        labour: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        rate: {
                            required: true,
                            number: true,
                            min: 0
                        },
                        errorClass:'error'
                    },
                    messages: {
                        name: {
                            required: 'Name is required',
                            remote: 'Name already exist'
                        },
                        installation_cost: {
                            required: 'Installation Cost is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        labour: {
                            required: 'Labour is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        rate: {
                            required: 'Rate is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
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
            code: '',
            name: '',
            installation_cost: '',
            labour: '',
            rate: ''
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
                    width: '40%', 
                    enableCellEdit: false
                },
                {
                    field: 'installation_cost', 
                    displayName: 'Installation Cost', 
                    cellClass: 'grid-align-right',
                    cellFilter: 'number: 2',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'labour', 
                    displayName: 'Labour', 
                    cellClass: 'grid-align-right',
                    cellFilter: 'number: 2',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'rate', 
                    displayName: 'Rate', 
                    cellClass: 'grid-align-right',
                    cellFilter: 'number: 2',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '5%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-show="false"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
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
            exporterCsvFilename: 'Installation Rates.csv',
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
                url: base_url + '/installation_rate/find_installation_rate',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                $scope.data = {
                    id: response.data.id,
                    value: response.data.name,
                    code: response.data.code,
                    name: response.data.name,
                    installation_cost: parseFloat(Math.round(response.data.installation_cost * 100) / 100).toFixed(2),
                    labour: parseFloat(Math.round(response.data.labour * 100) / 100).toFixed(2),
                    rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2)
                };
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/installation_rate/find_installation_rate',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                $scope.data = {
                    id: response.data.id,
                    value: response.data.name,
                    code: response.data.code,
                    name: response.data.name,
                    installation_cost: parseFloat(Math.round(response.data.installation_cost * 100) / 100).toFixed(2),
                    labour: parseFloat(Math.round(response.data.labour * 100) / 100).toFixed(2),
                    rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2)
                };
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + response.data.code + "</strong> installation rate!",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $http.delete(base_url + '/installation_rate/'+$scope.data.id, $scope.data).success(function (response_delete) {
                        swal({
                            title: "Deleted!", 
                            text: response.data.code + " installation rate has been deleted.", 
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
                $http.post(base_url + '/installation_rate', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Installation Rates',
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
                $http.put(base_url + '/installation_rate/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Installation Rates',
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
                url: base_url + '/installation_rate/installation_rate_list'
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.installation_rates, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        code: value.code,
                        name: value.name,
                        installation_cost: value.installation_cost,
                        labour: value.labour,
                        rate: value.rate
                    });
                });    
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();
    }]);
</script>
@endsection