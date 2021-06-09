@extends('layouts.main')

@section('title')
<title>M3Force | Ongoing Tech Response</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Tech Response</a></li>
    <li class="active">Ongoing Tech Response</li>
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

    .critical_status {
        background-color: #ff0000 !important;
        border: 1px solid #d4d4d4 !important;
    }

    .red {
        background-color: #ff9999 !important;
        border: 1px solid #d4d4d4 !important;
    }

    .yellow {
        background-color: #ffff99 !important;
        border: 1px solid #d4d4d4 !important;
    }

    .green {
        background-color: #99cc99 !important;
        border: 1px solid #d4d4d4 !important;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Ongoing Tech Response</strong></h3>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Fault Type</label>
                                    <select name="filter_fault_type" id="filter_fault_type" ng-options="option.name for option in filter_fault_type_array track by option.id" ng-model="data.filter_fault_type" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Update Status</label>
                                    <select name="filter_update_status" id="filter_update_status" ng-options="option.name for option in filter_update_status_array track by option.id" ng-model="data.filter_update_status" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-right" style="margin-top: 10px;">
                            <button type="button" class="btn btn-warning btn-sm grid-btn text-center"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Status&nbsp;<button type="button" class="btn btn-primary btn-sm grid-btn text-center"><i class="fa fa-file-word-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Job Card&nbsp;<button type="button" class="btn btn-default btn-sm grid-btn text-center"><i class="fa fa-file-powerpoint-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Installation Sheet&nbsp;<button type="button" class="btn btn-default btn-sm grid-btn text-center"><i class="fa fa-file-excel-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Quotation&nbsp;<button type="button" class="btn btn-info btn-sm grid-btn text-center"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button>&nbsp;Update&nbsp;<button type="button" class="btn btn-danger btn-sm grid-btn text-center"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Delete&nbsp;
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
        angular.element(document.querySelector('#main_menu_tech_response')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_tech_response')).addClass('active');
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.filter_fault_type_array = [];
        $scope.filter_update_status_array = [];

        $http({
            method: 'GET',
            url: base_url + '/get_filter_data'
        }).then(function successCallback(response) {
            var filter_fault_type_array = [];
            filter_fault_type_array.push({
                id: -1,
                name: 'All'
            });
            $.each(response.data.fault_types, function(index, value) {
                filter_fault_type_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            var filter_update_status_array = [];
            filter_update_status_array.push({
                id: -1,
                name: 'All'
            });
            filter_update_status_array.push({
                id: 0,
                name: 'Critical'
            });
            $.each(response.data.tech_response_status, function(index, value) {
                filter_update_status_array.push({
                    id: value.id,
                    name: value.name
                });
            });

            $scope.filter_fault_type_array = filter_fault_type_array;
            $scope.filter_update_status_array = filter_update_status_array;

            $scope.data = {
                filter_fault_type: $scope.filter_fault_type_array.length > 0 ? $scope.filter_fault_type_array[0] : {},
                filter_update_status: $scope.filter_update_status_array.length > 0 ? $scope.filter_update_status_array[0] : {}
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
                    field: 'permission',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'critical_status',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'update_status_id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'contact_id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'options',
                    displayName: '',
                    enableFiltering: false,
                    enableSorting: false,
                    enableCellEdit: false,
                    width: '25%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-warning btn-sm grid-btn text-center" ng-click="grid.appScope.updateRecord(row)"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-primary btn-sm grid-btn text-center" ng-click="grid.appScope.createJobCard(row)"><i class="fa fa-file-word-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-default btn-sm grid-btn text-center" ng-click="grid.appScope.createInstallationSheet(row)"><i class="fa fa-file-powerpoint-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-default btn-sm grid-btn text-center" ng-click="grid.appScope.createQuotation(row)"><i class="fa fa-file-excel-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'tech_response_no',
                    displayName: 'Tech Response No',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_id',
                    displayName: 'Customer ID',
                    width: '10%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_name',
                    displayName: 'Customer Name',
                    width: '20%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_address',
                    displayName: 'Customer Address',
                    width: '30%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_contact_no',
                    displayName: 'Customer Contact No',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'contract_end_date',
                    displayName: 'Contract End Date',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'record_date_time',
                    displayName: 'Record Date & Time',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'fault_type',
                    displayName: 'Fault Type',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'remarks',
                    displayName: 'Remarks',
                    width: '35%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'tech_response_value',
                    displayName: 'Tech Response Value',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTechResponseTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'update_date_time',
                    displayName: 'Update Date & Time',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'update_status',
                    displayName: 'Update Status',
                    width: '25%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'update_remarks',
                    displayName: 'Update Remarks',
                    width: '35%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'record_person',
                    displayName: 'Record Person',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'pending_days',
                    displayName: 'Dates Since last Update',
                    cellClass: 'grid-align',
                    width: '20%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.critical_status == 1 && row.entity.update_status_id != 12) {
                            return 'critical_status';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 12) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
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

        $scope.getAggregationTechResponseTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].tech_response_value);
            }
            return total_value;
        };

        $scope.updateRecord = function(row) {
            $window.open(base_url + '/tech_response/update_tech_response?id=' + row.entity.id, '_blank');
        };

        $scope.editRecord = function(row) {
            $window.open(base_url + '/tech_response/add_new_tech_response?tech_response_id=' + row.entity.id + '&contact_id=' + row.entity.contact_id, '_blank');
        };

        $scope.createJobCard = function(row) {
            $window.open(base_url + '/tech_response/tech_response_job_card?id=' + row.entity.id, '_blank');
        };

        $scope.createInstallationSheet = function(row) {
            $window.open(base_url + '/tech_response/tech_response_installation_sheet?id=' + row.entity.id, '_blank');
        };

        $scope.createQuotation = function(row) {
            $window.open(base_url + '/tech_response/tech_response_quotation?id=' + row.entity.id, '_blank');
        };

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            var filter_fault_type = $scope.data.filter_fault_type ? $scope.data.filter_fault_type.name : 'All';
            var filter_update_status = $scope.data.filter_update_status ? $scope.data.filter_update_status.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Ongoing Tech Responses (' + filter_fault_type + '-' + filter_update_status + ').csv';
            $http({
                method: 'GET',
                url: base_url + '/tech_response/ongoing_tech_response_list',
                params: {
                    fault_type_id: $scope.data.filter_fault_type ? $scope.data.filter_fault_type.id : -1,
                    update_status_id: $scope.data.filter_update_status ? $scope.data.filter_update_status.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.tech_response_list, function(index, value) {
                    var date_time = value.update_date_time.split(' ');
                    var start = new Date(date_time[0]);
                    var end = new Date();
                    var diff = new Date(end - start);
                    var days = Math.floor(diff / 1000 / 60 / 60 / 24);

                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        critical_status: value.critical_status,
                        update_status_id: value.update_status_id,
                        contact_id: value.contact_id,
                        tech_response_no: value.tech_response_no,
                        record_date_time: value.record_date_time,
                        fault_type: value.fault_type,
                        tech_response_value: parseFloat(Math.round(value.tech_response_value * 100) / 100).toFixed(2),
                        customer_id: value.customer_id,
                        customer_name: value.customer_name,
                        customer_address: value.customer_address,
                        customer_contact_no: value.customer_contact_no,
                        contract_end_date: value.contract_end_date,
                        remarks: value.remarks,
                        update_date_time: value.update_date_time,
                        update_status: value.update_status,
                        update_remarks: value.update_remarks,
                        record_person: value.record_person,
                        pending_days: days
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

        $scope.deleteRecord = function(row) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + row.entity.tech_response_no + "</strong> tech response !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function() {
                    $http.delete(base_url + '/tech_response/' + row.entity.id, {
                        params: {
                            type: 0
                        }
                    }).success(function(response_delete) {
                        swal({
                            title: "Deleted!",
                            text: row.entity.tech_response_no + " tech response has been deleted.",
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                        $scope.main_refresh();
                    });
                });
        };
    }]);
</script>
@endsection