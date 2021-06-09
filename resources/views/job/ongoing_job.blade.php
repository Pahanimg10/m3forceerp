@extends('layouts.main')

@section('title')
<title>M3Force | Ongoing Job</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Job</a></li>
    <li class="active">Ongoing Job</li>
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

    input[type="text"]:disabled {
        color: #1CB09A;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Ongoing Job</strong></h3>
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
                                    <label class="control-label">Inquiry Type</label>
                                    <select name="filter_inquiry_type" id="filter_inquiry_type" ng-options="option.name for option in filter_inquiry_type_array track by option.id" ng-model="data.filter_inquiry_type" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Update Status</label>
                                    <select name="filter_update_status" id="filter_update_status" ng-options="option.name for option in filter_update_status_array track by option.id" ng-model="data.filter_update_status" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Sales Person</label>
                                    <select name="filter_sales_person" id="filter_sales_person" ng-options="option.name for option in filter_sales_person_array track by option.id" ng-model="data.filter_sales_person" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-right" style="margin-top: 10px;">
                            <button type="button" class="btn btn-warning btn-sm grid-btn text-center"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Status&nbsp;<button type="button" class="btn btn-success btn-sm grid-btn text-center"><i class="fa fa-upload" style="margin: 0; width: 12px;"></i></button>&nbsp;Upload Document&nbsp;<button type="button" class="btn btn-primary btn-sm grid-btn text-center"><i class="fa fa-file-word-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Job Card&nbsp;<button type="button" class="btn btn-default btn-sm grid-btn text-center"><i class="fa fa-money" style="margin: 0; width: 12px;"></i></button>&nbsp;Cost Sheet&nbsp;<button type="button" class="btn btn-default btn-sm grid-btn text-center"><i class="fa fa-file-powerpoint-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Installation Sheet&nbsp;<button type="button" class="btn btn-danger btn-sm grid-btn text-center"><i class="fa fa-file-excel-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Quotation&nbsp;<button type="button" class="btn btn-info btn-sm grid-btn text-center"><i class="fa fa-thumbs-o-up" style="margin: 0; width: 12px;"></i></button>&nbsp;Handover Documents&nbsp;
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
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
        angular.element(document.querySelector('#main_menu_job')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_job')).addClass('active');
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.filter_inquiry_type_array = [];
        $scope.filter_update_status_array = [];
        $scope.filter_sales_person_array = [];

        $http({
            method: 'GET',
            url: base_url + '/get_filter_data'
        }).then(function successCallback(response) {
            var filter_inquiry_type_array = [];
            filter_inquiry_type_array.push({
                id: -1,
                name: 'All'
            });
            $.each(response.data.inquiry_types, function(index, value) {
                filter_inquiry_type_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            var filter_update_status_array = [];
            filter_update_status_array.push({
                id: -1,
                name: 'All'
            });
            $.each(response.data.job_status, function(index, value) {
                filter_update_status_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            var filter_sales_person_array = [];
            filter_sales_person_array.push({
                id: -1,
                name: 'All'
            });
            $.each(response.data.sales_team, function(index, value) {
                filter_sales_person_array.push({
                    id: value.id,
                    name: value.name
                });
            });

            $scope.filter_inquiry_type_array = filter_inquiry_type_array;
            $scope.filter_update_status_array = filter_update_status_array;
            $scope.filter_sales_person_array = filter_sales_person_array;

            $scope.data = {
                filter_inquiry_type: $scope.filter_inquiry_type_array.length > 0 ? $scope.filter_inquiry_type_array[0] : {},
                filter_update_status: $scope.filter_update_status_array.length > 0 ? $scope.filter_update_status_array[0] : {},
                filter_sales_person: $scope.filter_sales_person_array.length > 0 ? $scope.filter_sales_person_array[0] : {}
            };
        }, function errorCallback(response) {
            console.log(response);
        });

        $scope.gridOptions = {
            columnDefs: [{
                    field: 'id',
                    type: 'number',
                    sort: {
                        direction: 'desc',
                        priority: 0
                    },
                    visible: false
                },
                {
                    field: 'inquiry_id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'status_id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'options',
                    displayName: '',
                    enableFiltering: false,
                    enableSorting: false,
                    enableCellEdit: false,
                    width: '30%',
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-warning btn-sm grid-btn text-center" ng-click="grid.appScope.updateRecord(row)"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-success btn-sm grid-btn text-center" ng-click="grid.appScope.uploadDocuments(row)"><i class="fa fa-upload" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-primary btn-sm grid-btn text-center" ng-click="grid.appScope.createJobCard(row)"><i class="fa fa-file-word-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-default btn-sm grid-btn text-center" ng-click="grid.appScope.createCostSheet(row)"><i class="fa fa-money" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-default btn-sm grid-btn text-center" ng-click="grid.appScope.createInstallationSheet(row)"><i class="fa fa-file-powerpoint-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.createQuotation(row)"><i class="fa fa-file-excel-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.printDocuments(row)"><i class="fa fa-thumbs-o-up" style="margin: 0; width: 12px;"></i></button></div>'
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
                    field: 'job_type',
                    displayName: 'Job Type',
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'customer_name',
                    displayName: 'Customer Name',
                    width: '20%',
                    enableCellEdit: false
                },
                {
                    field: 'customer_address',
                    displayName: 'Address',
                    width: '30%',
                    enableCellEdit: false
                },
                {
                    field: 'client_type',
                    displayName: 'Client Type',
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'business_type',
                    displayName: 'Business Type',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'update_date_time',
                    displayName: 'Update Date & Time',
                    cellClass: 'grid-align',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'update_status',
                    displayName: 'Update Status',
                    width: '25%',
                    enableCellEdit: false
                },
                {
                    field: 'remarks',
                    displayName: 'Remarks',
                    width: '35%',
                    enableCellEdit: false
                },
                {
                    field: 'sales_person',
                    displayName: 'Sales Person',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'pending_days',
                    displayName: 'Dates Since last Update',
                    cellClass: 'grid-align',
                    width: '20%',
                    enableCellEdit: false
                },
                {
                    field: 'job_value',
                    displayName: 'Job Value',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationJobTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'advance_value',
                    displayName: 'Advance Value',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationAdvanceTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'mandays',
                    displayName: 'Mandays',
                    cellClass: 'grid-align',
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'used_mandays',
                    displayName: 'Used Mandays',
                    cellClass: 'grid-align',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'pending_mandays',
                    displayName: 'Pending Mandays',
                    cellClass: 'grid-align',
                    width: '15%',
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

        $scope.getAggregationJobTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].job_value);
            }
            return total_value;
        };

        $scope.getAggregationAdvanceTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].advance_value);
            }
            return total_value;
        };

        $scope.updateRecord = function(row) {
            $window.open(base_url + '/job/update_job?id=' + row.entity.id, '_blank');
        };

        $scope.uploadDocuments = function(row) {
            $window.open(base_url + '/job/upload_documents?id=' + row.entity.id, '_blank');
        };

        $scope.createJobCard = function(row) {
            $window.open(base_url + '/inquiry/job_card?type=1&id=' + row.entity.inquiry_id, '_blank');
        };

        $scope.createCostSheet = function(row) {
            $window.open(base_url + '/inquiry/cost_sheet?type=1&id=' + row.entity.inquiry_id, '_blank');
        };

        $scope.createInstallationSheet = function(row) {
            $window.open(base_url + '/inquiry/installation_sheet?type=1&id=' + row.entity.inquiry_id, '_blank');
        };

        $scope.createQuotation = function(row) {
            $window.open(base_url + '/inquiry/quotation?type=1&id=' + row.entity.inquiry_id, '_blank');
        };

        $scope.printDocuments = function(row) {
            $window.open(base_url + '/job/handover_documents?id=' + row.entity.inquiry_id, '_blank');
        };

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            var filter_inquiry_type = $scope.data.filter_inquiry_type ? $scope.data.filter_inquiry_type.name : 'All';
            var filter_update_status = $scope.data.filter_update_status ? $scope.data.filter_update_status.name : 'All';
            var filter_sales_person = $scope.data.filter_sales_person ? $scope.data.filter_sales_person.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Ongoing Jobs (' + filter_inquiry_type + '-' + filter_update_status + '-' + filter_sales_person + ').csv';
            $http({
                method: 'GET',
                url: base_url + '/job/ongoing_job_list',
                params: {
                    inquiry_type_id: $scope.data.filter_inquiry_type ? $scope.data.filter_inquiry_type.id : -1,
                    update_status_id: $scope.data.filter_update_status ? $scope.data.filter_update_status.id : -1,
                    sales_team_id: $scope.data.filter_sales_person ? $scope.data.filter_sales_person.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function(index, value) {
                    var date_time = value.update_date_time.split(' ');
                    var start = new Date(date_time[0]);
                    var end = new Date();
                    var diff = new Date(end - start);
                    var days = Math.floor(diff / 1000 / 60 / 60 / 24);

                    data_array.push({
                        id: value.id,
                        inquiry_id: value.inquiry_id,
                        status_id: value.status_id,
                        job_no: value.job_no,
                        job_date_time: value.job_date_time,
                        job_type: value.job_type,
                        customer_name: value.customer_name,
                        customer_address: value.customer_address,
                        client_type: value.client_type,
                        business_type: value.business_type,
                        update_date_time: value.update_date_time,
                        update_status: value.update_status,
                        remarks: value.remarks,
                        sales_person: value.sales_person,
                        pending_days: days,
                        job_value: parseFloat(Math.round(value.job_value * 100) / 100).toFixed(2),
                        advance_value: parseFloat(Math.round(value.advance_value * 100) / 100).toFixed(2),
                        mandays: parseFloat(Math.round(value.mandays * 100) / 100).toFixed(2),
                        used_mandays: parseFloat(Math.round(value.used_mandays * 100) / 100).toFixed(2),
                        pending_mandays: parseFloat(Math.round((value.mandays - value.used_mandays) * 100) / 100).toFixed(2),
                        log_user: value.log_user
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
    }]);
</script>
@endsection