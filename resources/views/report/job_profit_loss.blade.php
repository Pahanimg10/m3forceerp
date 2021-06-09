@extends('layouts.main')

@section('title')
<title>M3Force | Job Profit/Loss</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Job Profit/Loss</a></li>  
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
                    <h3 class="panel-title"><strong>Job Profit/Loss</strong></h3>
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
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Sales Person</label>
                                    <select name="sales_person" id="sales_person" ng-options="option.name for option in sales_person_array track by option.id" ng-model="data.sales_person" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <select name="status" id="status" ng-options="option.name for option in status_array track by option.id" ng-model="data.status" ng-change="main_refresh()" class="form-control" ></select>
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
        angular.element(document.querySelector('#sub_menu_job_profit_loss')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.data = [];
        $scope.sales_person_array = [];
        $scope.status_array = [{id: -1, name: 'All'}, {id: 0, name: 'Loss'}, {id: 1, name: 'Profit'}];
        $scope.data.status = $scope.status_array.length > 0 ? $scope.status_array[0] : {};
            
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
        
        $http({
            method: 'GET',
            url: base_url + '/get_filter_data'
        }).then(function successCallback(response) {
            var sales_person_array = [];
            sales_person_array.push({
                id: -1,
                name: 'All'
            });
            $.each(response.data.sales_team, function (index, value) {
                sales_person_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            
            $scope.sales_person_array = sales_person_array;
            $scope.data.sales_person = $scope.sales_person_array.length > 0 ? $scope.sales_person_array[0] : {};
//            $scope.main_refresh();
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
                    field: 'inquiry_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'job_no', 
                    displayName: 'Job No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'job_date_time', 
                    displayName: 'Job Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_name', 
                    displayName: 'Customer Name', 
                    width: '20%', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+ base_url + '/customer_status/get_customer_status_details?id=<%= row.entity.inquiry_id %>" target="_blank"><%= row.entity.customer_name %></a></div>'
                },
                {
                    field: 'customer_address', 
                    displayName: 'Address', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'job_type', 
                    displayName: 'Job Type', 
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'quoted_price', 
                    displayName: 'Quoted Price', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationQuotedPrice() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'estimated_cost', 
                    displayName: 'Estimated Cost', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationEstimatedCost() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'estimated_gp', 
                    displayName: 'Estimated GP', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationEstimatedGP() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'estimated_gp_percentage', 
                    displayName: 'Estimated GP Percentage %', 
                    cellClass: 'grid-align-right',
                    width: '20%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationEstimatedGPPercentage() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'actual_cost', 
                    displayName: 'Actual Cost', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationActualCost() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'actual_gp', 
                    displayName: 'Actual GP', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationActualGP() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'actual_gp_percentage', 
                    displayName: 'Actual GP Percentage %', 
                    cellClass: 'grid-align-right',
                    width: '20%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationActualGPPercentage() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'sales_person', 
                    displayName: 'Sales Person', 
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
        
        $scope.getAggregationQuotedPrice = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].quoted_price);
            }
            return total_value;
        };
        
        $scope.getAggregationEstimatedCost = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].estimated_cost);
            }
            return total_value;
        };
        
        $scope.getAggregationEstimatedGP = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].estimated_gp);
            }
            return total_value;
        };
        
        $scope.getAggregationEstimatedGPPercentage = function(){
            var total_quoted_price = 0;
            var total_estimated_gp = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_quoted_price += Number($scope.gridOptions.data[i].quoted_price);
                total_estimated_gp += Number($scope.gridOptions.data[i].estimated_gp);
            }
            return total_quoted_price != 0 ? (total_estimated_gp/total_quoted_price)*100 : 0;
        };
        
        $scope.getAggregationActualCost = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].actual_cost);
            }
            return total_value;
        };
        
        $scope.getAggregationActualGP = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].actual_gp);
            }
            return total_value;
        };
        
        $scope.getAggregationActualGPPercentage = function(){
            var total_quoted_price = 0;
            var total_actual_gp = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_quoted_price += Number($scope.gridOptions.data[i].quoted_price);
                total_actual_gp += Number($scope.gridOptions.data[i].actual_gp);
            }
            return total_quoted_price != 0 ? (total_actual_gp/total_quoted_price)*100 : 0;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var sales_person = $scope.data.sales_person && $scope.data.sales_person.id != -1 ? $scope.data.sales_person.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Job Profit/Loss From - ' + $scope.data.from + ' To - ' + $scope.data.to + ' Sales Person - ' + sales_person + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/report/job_profit_loss_details',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to,                    
                    sales_team_id: $scope.data.sales_person ? $scope.data.sales_person.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    if($scope.data.status.id == -1 || $scope.data.status.id == value.gp_status_id){
                        data_array.push({
                            id: value.id,
                            inquiry_id: value.inquiry_id,
                            job_no: value.job_no,
                            job_date_time: value.job_date_time,
                            customer_name: value.customer_name,
                            customer_address: value.customer_address,
                            job_type: value.job_type,
                            quoted_price: parseFloat(Math.round(value.quoted_price * 100) / 100).toFixed(2),
                            estimated_cost: parseFloat(Math.round(value.estimated_cost * 100) / 100).toFixed(2),
                            estimated_gp: parseFloat(Math.round(value.estimated_gp * 100) / 100).toFixed(2),
                            estimated_gp_percentage: parseFloat(Math.round(value.estimated_gp_percentage * 100) / 100).toFixed(2),
                            actual_cost: parseFloat(Math.round(value.actual_cost * 100) / 100).toFixed(2),
                            actual_gp: parseFloat(Math.round(value.actual_gp * 100) / 100).toFixed(2),
                            actual_gp_percentage: parseFloat(Math.round(value.actual_gp_percentage * 100) / 100).toFixed(2),
                            sales_person: value.sales_person
                        });
                    }
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