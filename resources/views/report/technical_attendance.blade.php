@extends('layouts.main')

@section('title')
<title>M3Force | Technical Attendance</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Technical Attendance</a></li>  
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
                    <h3 class="panel-title"><strong>Technical Attendance</strong></h3>
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
                            <div class="col-md-6 col-md-offset-3">
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
                                <h3 class="panel-title">Job Details <strong><%= technical_team_name %></strong> From : <%= data.from %> To : <%= data.to %></h3>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body"> 
                                <div class="row" style="width: 100%;" ng-bind-html="job_details_data | unsafe"></div>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

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
        angular.element(document.querySelector('#sub_menu_technical_attendance')).addClass('active');
    }); 
        
    myApp.filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
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
        
        $scope.gridOptions = {
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

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/report/technical_attendance_details',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to
                }
            }).then(function successCallback(response) {
                $scope.gridOptions.exporterCsvFilename = 'Technical Attendance From - ' + $scope.data.from + ' To - ' + $scope.data.to;
                var columnDefs = [
                    {
                        field: 'id', 
                        type: 'number', 
                        sort: {direction: 'desc', priority: 0},
                        visible: false
                    },
                    {
                        field: 'technical_name', 
                        displayName: 'Technical Name', 
                        width: '25%', 
                        enableCellEdit: false,
                        cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="#" ng-click="grid.appScope.getJobDetails(row.entity.id)"><%= row.entity.technical_name %></a></div>'
                    }
                ];
                $.each(response.data.attended_date, function (index, value) {
                    columnDefs.push({
                        field: value.attended_date_id, 
                        displayName: value.attended_date, 
                        cellClass: 'grid-align',
                        width: '12%', 
                        footerCellTemplate: '<div class="ui-grid-cell-contents text-center" ><%= grid.appScope.getAggregationDateTotal('+value.attended_date_id+') | number:2 %></div>',
                        enableCellEdit: false
                    });
                });
                columnDefs.push({
                    field: 'total_attendance', 
                    displayName: 'Total Attendance',  
                    cellClass: 'grid-align',
                    width: '12%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-center" ><%= grid.appScope.getAggregationTotalAttendance() | number:2 %></div>',
                    enableCellEdit: false
                });
                $scope.gridOptions.columnDefs = columnDefs;
                $scope.gridOptions.data = response.data.attendances;
                
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.getAggregationTotalAttendance = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].total_attendance);
            }
            return total_value;
        };
        
        $scope.getAggregationDateTotal = function(attended_date_id){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i][attended_date_id]);
            }
            return total_value;
        };
        
        $scope.getJobDetails = function(id){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.job_details_data = '';   
            $scope.technical_team_name = ''; 
            $http({
                method: 'GET',
                url: base_url + '/report/technical_job_details',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to,
                    technical_id : id
                }
            }).then(function successCallback(response) { 
                $scope.job_details_data = response.data.view;
                $scope.technical_team_name = response.data.technical_team_name;
                $('#data_table').dataTable({
                    "aaSorting": [[0, 'asc']],
                    "paging": false,
                    "searching": false,
                    "info": false
                });
                document.getElementById('data_load').style.visibility = "hidden";
                $('#dataModal').modal('show');
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