@extends('layouts.main')

@section('title')
<title>M3Force | Repair</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Repair</a></li>
    <li class="active">Repair List</li>
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

    .danger {
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
                    <h3 class="panel-title"><strong>Repair List</strong></h3>
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
                                        <input type="hidden" ng-model="data.from" id="from" name="from" class="form-control">
                                        <input type="hidden" ng-model="data.to" id="to" name="to" class="form-control number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Repair Status</label>
                                <div class="col-md-8">
                                    <select name="repair_status" id="repair_status" ng-options="option.name for option in repair_status_array track by option.id" ng-model="data.repair_status" ng-change="main_refresh()" class="form-control"></select>
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
        angular.element(document.querySelector('#main_menu_repair')).addClass('active');
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.repair_status_array = [];

        $http({
            method: 'GET',
            url: base_url + '/repair/get_data'
        }).then(function successCallback(response) {
            var repair_status_array = [{
                id: -1,
                name: 'All'
            }];
            $.each(response.data.repair_status, function(index, value) {
                repair_status_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            $scope.repair_status_array = repair_status_array;
            $scope.data.repair_status = $scope.repair_status_array.length > 1 ? $scope.repair_status_array[1] : {};
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
        }, function(start, end) {
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
                    field: 'is_completed',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'update_status_id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'options',
                    displayName: '',
                    enableFiltering: false,
                    enableSorting: false,
                    enableCellEdit: false,
                    width: '12%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-warning btn-sm grid-btn text-center" ng-click="grid.appScope.updateRecord(row)"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_completed == 1"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_completed == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'repair_type',
                    displayName: 'Repair Type',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'repair_no',
                    displayName: 'Repair No',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'repair_date_time',
                    displayName: 'Repair Date & Time',
                    cellClass: 'grid-align',
                    width: '20%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'document_no',
                    displayName: 'Document No',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
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
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_address',
                    displayName: 'Address',
                    width: '30%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'customer_contact_no',
                    displayName: 'Contact No',
                    cellClass: 'grid-align',
                    width: '10%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'item_code',
                    displayName: 'Item Code',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'item_name',
                    displayName: 'Item Name',
                    width: '30%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'item_model_no',
                    displayName: 'Item Model No',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'item_brand',
                    displayName: 'Item Brand',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'item_serial_no',
                    displayName: 'Item Serial No',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
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
                    width: '40%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'handed_over_taken_over',
                    displayName: 'Handed Over / Taken Over',
                    width: '30%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
                            return 'green';
                        } else {
                            return 'yellow';
                        }
                    },
                    enableCellEdit: false
                },
                {
                    field: 'update_date_time',
                    displayName: 'Update Date & Time',
                    cellClass: 'grid-align',
                    width: '15%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
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
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
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
                    width: '40%',
                    cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {
                        if (row.entity.update_status_id == 4) {
                            return 'danger';
                        } else if (row.entity.update_status_id == 1) {
                            return 'red';
                        } else if (row.entity.update_status_id == 5) {
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

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = 'Repairs ' + $scope.data.repair_status.name + '  From ' + $scope.data.from + ' To ' + $scope.data.to + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/repair/repair_list',
                params: {
                    from: $scope.data.from,
                    to: $scope.data.to,
                    repair_status_id: $scope.data.repair_status.id
                }
            }).then(function successCallback(response) {
                $scope.gridOptions.data = response.data;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function() {
            $scope.main_refresh();
        }, 1500, false);

        $scope.add_new = function() {
            $window.location.href = base_url + '/repair/add_new';
        };

        $scope.updateRecord = function(row) {
            $window.open(base_url + '/repair/update_status?id=' + row.entity.id, '_blank');
        };

        $scope.editRecord = function(row) {
            $window.open(base_url + '/repair/add_new?id=' + row.entity.id, '_blank');
        };

        $scope.deleteRecord = function(row) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + row.entity.repair_no + "</strong> repair !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function() {
                    $http.delete(base_url + '/repair/' + row.entity.id, {
                        params: {
                            type: 0
                        }
                    }).success(function(response_delete) {
                        swal({
                            title: "Deleted!",
                            text: row.entity.repair_no + " repair has been deleted.",
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