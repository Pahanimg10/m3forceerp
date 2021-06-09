@extends('layouts.main')

@section('title')
<title>M3Force | Item</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>
    <li class="active">Item</li>
</ul>
@endsection

@section('content')
<style>
    .grid {
        width: 100%;
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

    .help-block {
        color: red;
        text-align: right;
    }

    .form-horizontal .form-group {
        margin-left: 0;
        margin-right: 0;
    }

    .control-label {
        padding: 15px 0 5px 0;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Item</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new()" class="btn btn-primary">Add New</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="col-md-12">Filter Details</h4>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Main Category</label>
                                    <select name="main_category" id="main_category" ng-options="option.name for option in main_category_array track by option.id" ng-model="data.main_category" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Sub Category</label>
                                    <select name="sub_category" id="sub_category" ng-options="option.name for option in sub_category_array track by option.id" ng-model="data.sub_category" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Purchase Type</label>
                                    <select name="purchase_type" id="purchase_type" ng-options="option.name for option in purchase_type_array track by option.id" ng-model="data.purchase_type" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<script type="text/javascript">
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
    ]).config(function($interpolateProvider) {
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

    myApp.controller('menuController', function($scope) {
        angular.element(document.querySelector('#main_menu_master')).addClass('active');
        angular.element(document.querySelector('#sub_menu_item')).addClass('active');
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.purchase_type_array = [];
        $http({
            method: 'GET',
            url: base_url + '/item/get_data'
        }).then(function successCallback(response) {
            var main_category_array = [{
                id: -1,
                name: 'All'
            }];
            $.each(response.data.main_item_categories, function(index, value) {
                main_category_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            var sub_category_array = [{
                id: -1,
                name: 'All'
            }];
            $.each(response.data.sub_item_categories, function(index, value) {
                sub_category_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            var purchase_type_array = [{
                id: -1,
                name: 'All'
            }];
            $.each(response.data.purchase_types, function(index, value) {
                purchase_type_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            $scope.main_category_array = main_category_array;
            $scope.sub_category_array = sub_category_array;
            $scope.purchase_type_array = purchase_type_array;
            $scope.data.main_category = $scope.main_category_array.length > 1 ? $scope.main_category_array[1] : {};
            $scope.data.sub_category = $scope.sub_category_array.length > 1 ? $scope.sub_category_array[1] : {};
            $scope.data.purchase_type = $scope.purchase_type_array.length > 1 ? $scope.purchase_type_array[1] : {};

            $scope.main_refresh();
        }, function errorCallback(response) {
            console.log(response);
        });

        $scope.gridOptions = {
            columnDefs: [{
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
                    field: 'id',
                    type: 'number',
                    //                    sort: {direction: 'desc', priority: 0},
                    //                    visible: false
                    displayName: 'Item ID',
                    cellClass: 'grid-align',
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'main_category',
                    displayName: 'Main Item Category',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'sub_category',
                    displayName: 'Sub Item Category',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'purchase_type',
                    displayName: 'Purchase Type',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'code',
                    displayName: 'Code',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'name',
                    displayName: 'Name',
                    width: '40%',
                    enableCellEdit: false
                },
                {
                    field: 'supplier',
                    displayName: 'Supplier',
                    width: '30%',
                    enableCellEdit: false
                },
                {
                    field: 'model_no',
                    displayName: 'Model No',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'brand',
                    displayName: 'Brand',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'origin',
                    displayName: 'Origin',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'unit_type',
                    displayName: 'Unit Type',
                    cellClass: 'grid-align',
                    width: '10%',
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
                    field: 'reorder_level',
                    displayName: 'Reorder Level',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'stock',
                    displayName: 'Stock',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'location',
                    displayName: 'Location',
                    width: '15%',
                    enableCellEdit: false
                },
                // {
                //     field: 'available_quantity', 
                //     displayName: 'Available Quantity', 
                //     cellClass: 'grid-align-right',
                //     width: '15%', 
                //     enableCellEdit: false
                // },
                // {
                //     field: 'serial_no_count', 
                //     displayName: 'Serial No Count', 
                //     cellClass: 'grid-align-right',
                //     width: '15%', 
                //     enableCellEdit: false
                // },
                {
                    field: 'serial_nos',
                    displayName: 'Serial Nos',
                    width: '30%',
                    enableCellEdit: false
                },
                {
                    field: 'is_active',
                    displayName: 'Active',
                    cellClass: 'grid-align',
                    width: '10%',
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
            onRegisterApi: function(gridApi) {
                $scope.gridApi = gridApi;
            }
        };

        $scope.export = function() {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function() {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.data.main_category.name + ' / ' + $scope.data.sub_category.name + ' Items.csv';
            $http({
                method: 'GET',
                url: base_url + '/item/item_list',
                params: {
                    main_category: $scope.data.main_category ? $scope.data.main_category.id : -1,
                    sub_category: $scope.data.sub_category ? $scope.data.sub_category.id : -1,
                    purchase_type: $scope.data.purchase_type ? $scope.data.purchase_type.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.items, function(index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        main_category: value.main_category,
                        sub_category: value.sub_category,
                        purchase_type: value.purchase_type,
                        code: value.code,
                        name: value.name,
                        supplier: value.supplier,
                        model_no: value.model_no,
                        brand: value.brand,
                        origin: value.origin,
                        unit_type: value.unit_type,
                        rate: value.rate,
                        reorder_level: value.reorder_level,
                        stock: value.stock,
                        location: value.location,
                        available_quantity: value.available_quantity,
                        serial_no_count: value.serial_no_count,
                        serial_nos: value.serial_nos,
                        is_active: value.is_active
                    });
                });
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        // window.onblur = function() {
        //     $timeout(function() {
        //         $scope.main_refresh();
        //     }, 1500, false);
        // }

        // window.onfocus = function() {
        //     $timeout(function() {
        //         $scope.main_refresh();
        //     }, 1500, false);
        // }

        $timeout(function() {
            $scope.main_refresh();
        }, 1500, false);

        $scope.add_new = function() {
            $window.location.href = base_url + '/item/add_new';
        };

        $scope.editRecord = function(row) {
            $window.open(base_url + '/item/add_new?id=' + row.entity.id, '_blank');
        };

        $scope.deleteRecord = function(row) {
            $http({
                method: 'GET',
                url: base_url + '/item/find_item',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.code + "</strong> item !",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function() {
                        $http.delete(base_url + '/item/' + row.entity.id, row.entity).success(function(response_delete) {
                            swal({
                                title: "Deleted!",
                                text: response.data.code + " item has been deleted.",
                                type: "success",
                                confirmButtonColor: "#9ACD32"
                            });
                            $scope.main_refresh();
                        });
                    });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
    }]);
</script>
@endsection