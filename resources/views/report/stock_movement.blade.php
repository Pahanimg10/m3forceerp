@extends('layouts.main')

@section('title')
<title>M3Force | Stock Movements</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Stock Movements</a></li>  
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
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Stock Movements</strong></h3>
                    <ul class="panel-controls">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>                                          
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default pull-left" id="btn_daterange" style="padding: 6px; border-radius: 5px;" class="form-control">
                                            <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                            </span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                        <input type="hidden" ng-model="data.from" id="from" name="from" class="form-control" >
                                        <input type="hidden" ng-model="data.to" id="to" name="to" class="form-control number" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Main Category</label>
                                    <select name="main_category" id="main_category" ng-options="option.name for option in main_category_array track by option.id" ng-model="data.main_category" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Sub Category</label>
                                    <select name="sub_category" id="sub_category" ng-options="option.name for option in sub_category_array track by option.id" ng-model="data.sub_category" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Purchase Type</label>
                                    <select name="purchase_type" id="purchase_type" ng-options="option.name for option in purchase_type_array track by option.id" ng-model="data.purchase_type" ng-change="main_refresh()" class="form-control" ></select>
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
    function getNumberFormattedDate(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();

        if (month < 10) {
            month = '0' + month;
        }
        if (day < 10) {
            day = '0' + day;
        }

        return year + '-' + month + '-' + day;
    }

    function getStringFormattedDate(date) {
        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();

        var monthNames = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
        ];

        if (day < 10) {
            day = '0' + day;
        }

        return monthNames[month] + ' ' + day + ', ' + year;
    }
    
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
        angular.element(document.querySelector('#main_menu_report')).addClass('active');
        angular.element(document.querySelector('#sub_menu_stock_movement')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
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
            $.each(response.data.main_item_categories, function (index, value) {
                main_category_array.push({
                    id: value.id,
                    name: value.name
                });
            });  
            var sub_category_array = [{
                    id: -1,
                    name: 'All'
            }];
            $.each(response.data.sub_item_categories, function (index, value) {
                sub_category_array.push({
                    id: value.id,
                    name: value.name
                });
            });  
            var purchase_type_array = [{
                    id: -1,
                    name: 'All'
            }];
            $.each(response.data.purchase_types, function (index, value) {
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
        }, function errorCallback(response) {
            console.log(response);
        });
            
        $('#btn_daterange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 2 Weeks': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        }, function (start, end) {
            $('#btn_daterange span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
            $scope.data.from = start.format('YYYY-MM-DD');
            $scope.data.to = end.format('YYYY-MM-DD');
            $scope.main_refresh();
        });
        
            
        var to = new Date();
        var from = new Date();
        from.setDate(from.getDate() - 29);
        $('#btn_daterange span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.from = getNumberFormattedDate(from);
        $scope.data.to = getNumberFormattedDate(to);
        
        $scope.gridOptions = {
            columnDefs: [
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
                    field: 'opening_quantity', 
                    displayName: 'Opening Quantity', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalOpeningQuantity() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'opening_value', 
                    displayName: 'Opening Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalOpeningValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'grn_quantity', 
                    displayName: 'GRN Quantity', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalGRNQuantity() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'grn_value', 
                    displayName: 'GRN Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalGRNValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'issue_quantity', 
                    displayName: 'Issue Quantity', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalIssueQuantity() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'issue_value', 
                    displayName: 'Issue Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalIssueValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'return_quantity', 
                    displayName: 'Return Quantity', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalReturnQuantity() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'return_value', 
                    displayName: 'Return Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalReturnValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'balance_quantity', 
                    displayName: 'Balance Quantity', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalBalanceQuantity() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'balance_value', 
                    displayName: 'Balance Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalBalanceValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                // {
                //     field: 'stock',
                //     displayName: 'Stock',
                //     cellClass: 'grid-align-right',
                //     width: '15%',
                //     enableCellEdit: false
                // },
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
                    field: 'location',
                    displayName: 'Location',
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
        
        $scope.getAggregationTotalOpeningQuantity = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].opening_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalOpeningValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].opening_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalGRNQuantity = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].grn_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalGRNValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].grn_value);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalIssueQuantity = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].issue_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalIssueValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].issue_value);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalReturnQuantity = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].return_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalReturnValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].return_value);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalBalanceQuantity = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].balance_quantity);
            }
            return total_value;
        };
        
        $scope.getAggregationTotalBalanceValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].balance_value);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var main_category = $scope.data.main_category && $scope.data.main_category.id != -1 ? $scope.data.main_category.name : 'All';
            var sub_category = $scope.data.sub_category && $scope.data.sub_category.id != -1 ? $scope.data.sub_category.name : 'All';
            var purchase_type = $scope.data.purchase_type && $scope.data.purchase_type.id != -1 ? $scope.data.purchase_type.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Stock Movements From - ' + $scope.data.from + ' To - ' + $scope.data.to + ' Main Category - ' + main_category + ' Sub Category - ' + sub_category + ' Purchase Type - ' + purchase_type + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/report/stock_movement_details',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to,
                    main_category: $scope.data.main_category ? $scope.data.main_category.id : -1,                    
                    sub_category: $scope.data.sub_category ? $scope.data.sub_category.id : -1,                    
                    purchase_type: $scope.data.purchase_type ? $scope.data.purchase_type.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    data_array.push({
                        id: value.id,
                        main_category: value.main_category,
                        sub_category: value.sub_category,
                        purchase_type: value.purchase_type,
                        code: value.code,
                        name: value.name,
                        model_no: value.model_no,
                        brand: value.brand,
                        origin: value.origin,
                        unit_type: value.unit_type,
                        opening_quantity: value.opening_quantity,
                        opening_value: value.opening_value,
                        grn_quantity: value.grn_quantity,
                        grn_value: value.grn_value,
                        issue_quantity: value.issue_quantity,
                        issue_value: value.issue_value,
                        return_quantity: value.return_quantity,
                        return_value: value.return_value,
                        balance_quantity: value.balance_quantity,
                        balance_value: value.balance_value,
                        stock: value.stock,
                        available_quantity: value.available_quantity,
                        serial_no_count: value.serial_no_count,
                        location: value.location
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