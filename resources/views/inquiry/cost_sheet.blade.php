@extends('layouts.main')

@section('title')
<title>M3Force | Cost Sheet</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">Cost Sheet</li>
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
                    <h3 class="panel-title"><strong>Cost Sheet</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new()" class="btn btn-primary">Add New</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset($type == 0 ? 'inquiry/ongoing_inquiry' : 'job/ongoing_job')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <input type="hidden" id="inquiry_id" name="inquiry_id" ng-model="data.inquiry_id" class="form-control" />
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
    var type = <?php echo $type ? $type : 0; ?>;
    
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
        angular.element(document.querySelector(type == 0 ? '#main_menu_inquiry' : '#main_menu_job')).addClass('active');
        angular.element(document.querySelector(type == 0 ? '#sub_menu_ongoing_inquiry' : '#sub_menu_ongoing_job')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.inquiry_id = <?php echo $inquiry_id ? $inquiry_id : 0; ?>;
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
                    field: 'is_used', 
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_used == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'cost_sheet_no', 
                    displayName: 'Cost Sheet No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'cost_sheet_date_time', 
                    displayName: 'Cost Sheet Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'remarks', 
                    displayName: 'Remarks', 
                    width: '40%', 
                    enableCellEdit: false
                },
                {
                    field: 'cost_sheet_value', 
                    displayName: 'Cost Sheet Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationCostSheetTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'installation_value', 
                    displayName: 'Installation Value', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationInstallationTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'mandays', 
                    displayName: 'Mandays', 
                    cellClass: 'grid-align-right',
                    width: '10%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationMandaysTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'log_user', 
                    displayName: 'Log User', 
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
        
        $scope.getAggregationCostSheetTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].cost_sheet_value);
            }
            return total_value;
        };
        
        $scope.getAggregationInstallationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].installation_value);
            }
            return total_value;
        };
        
        $scope.getAggregationMandaysTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].mandays);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/cost_sheet/cost_sheet_list',
                params: {
                    id: <?php echo $inquiry_id ? $inquiry_id : 0; ?>
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.cost_sheets, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_used: value.is_used,
                        cost_sheet_no: value.cost_sheet_no,
                        cost_sheet_date_time: value.cost_sheet_date_time,
                        remarks: value.remarks,
                        cost_sheet_value: value.cost_sheet_value != 0 ? parseFloat(Math.round(value.cost_sheet_value * 100) / 100).toFixed(2) : '',
                        installation_value: value.installation_value != 0 ? parseFloat(Math.round(value.installation_value * 100) / 100).toFixed(2) : '',
                        mandays: value.mandays != 0 ? parseFloat(Math.round(value.mandays * 100) / 100).toFixed(2) : '',
                        log_user: value.user ? value.user.first_name : ''
                    });
                });    
                $scope.gridOptions.data = data_array;
                $scope.gridOptions.exporterCsvFilename = response.data.inquiry ? response.data.inquiry.inquiry_no + ' Cost Sheets.csv' : 'Cost Sheets.csv';
                $scope.inquiry_id = response.data.inquiry ? response.data.inquiry.id : 0;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1500, false);

        $scope.add_new = function () {
            $window.location.href = base_url + '/cost_sheet/add_new?type='+type+'&view=0&inquiry_id='+$scope.inquiry_id;
        };

        $scope.editRecord = function (row) {
            $window.location.href = base_url + '/cost_sheet/add_new?type='+type+'&view=0&id='+row.entity.id+'&inquiry_id='+$scope.inquiry_id;
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/cost_sheet/find_cost_sheet',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + response.data.cost_sheet_no + "</strong> job card !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $http.delete(base_url + '/cost_sheet/'+row.entity.id, row.entity).success(function (response_delete) {
                        swal({
                            title: "Deleted!", 
                            text: response.data.cost_sheet_no + " job card has been deleted.", 
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