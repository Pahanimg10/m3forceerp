@extends('layouts.main')

@section('title')
<title>M3Force | Customer Status</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Status</a></li>  
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
                    <h3 class="panel-title"><strong>Customer Status</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Date Range</label>             
                                <div class="col-md-8">                                                
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
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Status Type</label>           
                                <div class="col-md-8"> 
                                    <select name="status_type" id="status_type" ng-options="option.name for option in status_type_array track by option.id" ng-model="data.status_type" class="form-control" ng-change="main_refresh()" ></select>
                                </div>
                            </div>
                        </div>
                    </div>       
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
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
        angular.element(document.querySelector('#main_menu_customer_status')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.data = [];
            
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
        
        $scope.status_type_array = [{id: 1, name: 'Job'}, {id: 2, name: 'Tech Response'}];
        $scope.data.status_type = $scope.status_type_array[0];
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'status_type_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'record_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'date_time', 
                    displayName: 'Date Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'document_no', 
                    displayName: 'Document No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_name', 
                    displayName: 'Customer Name', 
                    width: '25%', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+ base_url + '/customer_status/get_customer_status_details?record_id=<%= row.entity.record_id %>&status_type_id=<%= row.entity.status_type_id %>" target="_blank"><%= row.entity.customer_name %></a></div>'
                },
                {
                    field: 'customer_address', 
                    displayName: 'Customer Address', 
                    width: '35%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_contact_no', 
                    displayName: 'Customer Contact No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'type', 
                    displayName: 'Type', 
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

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = 'Customer Status From ' + $scope.data.from + ' To ' + $scope.data.to + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/customer_status/get_customer_list',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to,
                    status_type: $scope.data.status_type.id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                if($scope.data.status_type.id == 1){
                    $.each(response.data, function (index, value) {
                        data_array.push({
                            id: value.id,
                            status_type_id: 1,
                            record_id: value.id,
                            date_time: value.date_time,
                            document_no: value.document_no,
                            customer_name: value.customer_name,
                            customer_address: value.customer_address,
                            customer_contact_no: value.customer_contact_no,
                            type: value.type
                        });
                    });
                } else if($scope.data.status_type.id == 2){
                    $.each(response.data, function (index, value) {
                        data_array.push({
                            id: value.id,
                            status_type_id: 2,
                            record_id: value.id,
                            date_time: value.date_time,
                            document_no: value.document_no,
                            customer_name: value.customer_name,
                            customer_address: value.customer_address,
                            customer_contact_no: value.customer_contact_no,
                            type: value.type
                        });
                    });
                }
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