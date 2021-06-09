@extends('layouts.main')

@section('title')
<title>M3Force | Tech Response Quotation</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Tech Response</a></li>                    
    <li class="active">Tech Response Quotation</li>
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
                    <h3 class="panel-title"><strong>Tech Response Quotation</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new()" class="btn btn-primary">Add New</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('tech_response/ongoing_tech_response')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <input type="hidden" id="tech_response_id" name="tech_response_id" ng-model="data.tech_response_id" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_tech_response')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_tech_response')).addClass('active');
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.tech_response_id = '';
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
                    field: 'is_confirmed', 
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_confirmed == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'tech_response_quotation_no', 
                    displayName: 'Tech Response Quotation No', 
                    cellClass: 'grid-align',
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'tech_response_quotation_date_time', 
                    displayName: 'Tech Response Quotation Date & Time', 
                    cellClass: 'grid-align',
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'remarks', 
                    displayName: 'Remarks', 
                    width: '40%', 
                    enableCellEdit: false
                },
                {
                    field: 'tech_response_quotation_value', 
                    displayName: 'Tech Response Quotation Value', 
                    cellClass: 'grid-align-right',
                    width: '20%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTechResponseQuotationTotalValue() | number:2 %></div>',
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
        
        $scope.getAggregationTechResponseQuotationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].tech_response_quotation_value);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/tech_response_quotation/tech_response_quotation_list',
                params: {
                    id: <?php echo $tech_response_id ? $tech_response_id : 0; ?>
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.tech_response_quotations, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_confirmed: value.is_confirmed,
                        tech_response_quotation_no: value.tech_response_quotation_no,
                        tech_response_quotation_date_time: value.tech_response_quotation_date_time,
                        remarks: value.remarks,
                        tech_response_quotation_value: value.tech_response_quotation_value != 0 ? parseFloat(Math.round(value.tech_response_quotation_value * 100) / 100).toFixed(2) : '',
                        log_user: value.user ? value.user.first_name : ''
                    });
                });    
                $scope.gridOptions.data = data_array;
                $scope.gridOptions.exporterCsvFilename = response.data.tech_response ? response.data.tech_response.tech_response_no + ' Tech Response Quotations.csv' : 'Tech Response Quotations.csv';
                $scope.tech_response_id = response.data.tech_response ? response.data.tech_response.id : 0;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1500, false);

        $scope.add_new = function () {
            $window.location.href = base_url + '/tech_response_quotation/add_new?view=0&tech_response_id='+$scope.tech_response_id;
        };

        $scope.editRecord = function (row) {
            $window.location.href = base_url + '/tech_response_quotation/add_new?view=0&id='+row.entity.id+'&tech_response_id='+$scope.tech_response_id;
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/tech_response_quotation/find_tech_response_quotation',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + response.data.tech_response_quotation_no + "</strong> tech response quotation !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $http.delete(base_url + '/tech_response_quotation/'+row.entity.id, {params: {type: 0}}).success(function (response_delete) {
                        swal({
                            title: "Deleted!", 
                            text: response.data.tech_response_quotation_no + " tech response quotation has been deleted.", 
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