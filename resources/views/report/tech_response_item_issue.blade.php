@extends('layouts.main')

@section('title')
<title>M3Force | Tech Response Item Issues</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Tech Response Item Issues</a></li>
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
                    <h3 class="panel-title"><strong>Tech Response Item Issues</strong></h3>
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
                                        <input type="hidden" ng-model="data.from" id="from" name="from" class="form-control">
                                        <input type="hidden" ng-model="data.to" id="to" name="to" class="form-control number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Tech Response</label>
                                    <select name="tech_response" id="tech_response" ng-options="option.name for option in tech_response_array track by option.id" ng-model="data.tech_response" ng-change="main_refresh()" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Code</label>
                                    <input type="text" id="code" name="code" ui-grid-edit-auto ng-model="data.code" typeahead="name as code_array.name for code_array in code_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onCodeSelect($item, $model, $label)" ng-keyup="get_codes(data.code)" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Item</label>
                                    <input type="text" id="item" name="item" ui-grid-edit-auto ng-model="data.item" typeahead="name as item_array.name for item_array in item_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onItemSelect($item, $model, $label)" ng-keyup="get_items(data.item)" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_report')).addClass('active');
        angular.element(document.querySelector('#sub_menu_tech_response_item_issue')).addClass('active');
    });

    myApp.directive('jValidate', function() {
        return {
            link: function(scope, element, attr) {
                element.validate({
                    rules: {
                        code: {
                            remote: {
                                url: base_url + '/validate_item_code',
                                type: 'GET',
                                data: {
                                    code: function() {
                                        return scope.data.code && scope.data.code.name ? scope.data.code.name : scope.data.code;
                                    }
                                }
                            }
                        },
                        item: {
                            remote: {
                                url: base_url + '/validate_item_name',
                                type: 'GET',
                                data: {
                                    name: function() {
                                        return scope.data.item && scope.data.item.name ? scope.data.item.name : scope.data.item;
                                    }
                                }
                            }
                        },
                        errorClass: 'error'
                    },
                    messages: {
                        code: {
                            remote: 'Invalid Code'
                        },
                        item: {
                            remote: 'Invalid Item'
                        }
                    },
                    highlight: function(element) {
                        $(element).removeClass("valid");
                        $(element).addClass("error");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("error");
                        $(element).addClass("valid");
                    },
                    errorElement: 'label',
                    errorClass: 'message_lable',
                    submitHandler: function(form) {
                        submitForm();
                    },
                    invalidHandler: function(event, validator) {
                        //
                    }

                });

                scope.$on('$destroy', function() {
                    // Perform cleanup.
                    // (Not familiar with the plugin so don't know what should to be 
                });
            }
        }
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {
        $scope.data = [];
        $scope.tech_response_array = [];
        $scope.code_array = [];
        $scope.item_array = [];

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
            $scope.refresh_tech_response_data();
            $timeout(function() {
                $scope.main_refresh();
            }, 1500, false);
        });


        var to = new Date();
        var from = new Date();
        from.setDate(from.getDate() - 29);
        $('#btn_daterange span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.from = getNumberFormattedDate(from);
        $scope.data.to = getNumberFormattedDate(to);

        $scope.refresh_tech_response_data = function() {
            $http({
                method: 'GET',
                url: base_url + '/report/get_tech_response_data',
                params: {
                    from: $scope.data.from,
                    to: $scope.data.to
                }
            }).then(function successCallback(response) {
                var tech_response_array = [];
                tech_response_array.push({
                    id: -1,
                    name: 'All'
                });
                $.each(response.data.item_issues, function(index, value) {
                    tech_response_array.push({
                        id: value.tech_response ? value.tech_response.id : 0,
                        name: value.tech_response && value.tech_response.contact ? value.tech_response.tech_response_no + ' ' + value.tech_response.contact.name : ''
                    });
                });

                $scope.tech_response_array = tech_response_array;
                $scope.data.tech_response = $scope.tech_response_array.length > 0 ? $scope.tech_response_array[0] : {};
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.refresh_tech_response_data();

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
                    field: 'tech_response_no',
                    displayName: 'Tech Response No',
                    cellClass: 'grid-align',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'customer_name',
                    displayName: 'Customer Name',
                    width: '25%',
                    enableCellEdit: false,
                },
                {
                    field: 'customer_address',
                    displayName: 'Customer Address',
                    width: '35%',
                    enableCellEdit: false
                },
                {
                    field: 'item_code',
                    displayName: 'Item Code',
                    width: '15%',
                    enableCellEdit: false
                },
                {
                    field: 'item_name',
                    displayName: 'Item Name',
                    width: '40%',
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
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'quantity',
                    displayName: 'Quantity',
                    cellClass: 'grid-align-right',
                    width: '10%',
                    enableCellEdit: false
                },
                {
                    field: 'value',
                    displayName: 'Value',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'invoice_value',
                    displayName: 'Invoice Value',
                    cellClass: 'grid-align-right',
                    width: '15%',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotaInvoicelValue() | number:2 %></div>',
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

        $scope.getAggregationTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.getAggregationTotalInvoiceValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].invoice_value);
            }
            return total_value;
        };

        $scope.get_codes = function(code) {
            if (code && code.length > 0) {
                $scope.code_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_codes',
                    params: {
                        type: 1,
                        code: code,
                        main_category: '',
                        sub_category: ''
                    }
                }).then(function successCallback(response) {
                    $scope.code_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.code_array.push({
                            id: value.id,
                            name: value.code,
                            item_name: value.name
                        });
                    });
                    $scope.find_code(code);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_code = function(code) {
            $http({
                method: 'GET',
                url: base_url + '/find_item_code',
                params: {
                    type: 1,
                    code: code,
                    main_category: '',
                    sub_category: ''
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.data.code = {
                        id: response.data.id,
                        name: response.data.code
                    };
                    $scope.data.item = {
                        id: response.data.id,
                        name: response.data.name
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_items = function(name) {
            if (name && name.length > 0) {
                $scope.item_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_names',
                    params: {
                        type: 1,
                        name: name,
                        main_category: '',
                        sub_category: ''
                    }
                }).then(function successCallback(response) {
                    $scope.item_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.item_array.push({
                            id: value.id,
                            name: value.name,
                            code: value.code
                        });
                    });
                    $scope.find_name(name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_name = function(name) {
            $http({
                method: 'GET',
                url: base_url + '/find_item_name',
                params: {
                    type: 1,
                    name: name,
                    main_category: '',
                    sub_category: ''
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.data.code = {
                        id: response.data.id,
                        name: response.data.code
                    };
                    $scope.data.item = {
                        id: response.data.id,
                        name: response.data.name
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.onCodeSelect = function($item, $model, $label) {
            $scope.data.code = {
                id: $item.id,
                name: $item.name
            };
            $scope.data.item = {
                id: $item.id,
                name: $item.item_name
            };

            $timeout(function() {
                $scope.find_code($scope.data.code.name);
                $scope.main_refresh();
            }, 200, false);
        };

        $scope.onItemSelect = function($item, $model, $label) {
            $scope.data.code = {
                id: $item.id,
                name: $item.code
            };
            $scope.data.item = {
                id: $item.id,
                name: $item.name
            };

            $timeout(function() {
                $scope.find_name($scope.data.item.name);
                $scope.main_refresh();
            }, 200, false);
        };

        $scope.main_refresh = function() {
            document.getElementById('data_load').style.visibility = "visible";
            var tech_response = $scope.data.tech_response && $scope.data.tech_response.id != '' ? $scope.data.tech_response.name : 'All';
            var item = $scope.data.item ? $scope.data.item.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'Tech Response Item Issues From - ' + $scope.data.from + ' To - ' + $scope.data.to + ' Tech Response - ' + tech_response + ' Item - ' + item + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/report/tech_response_item_issue_details',
                params: {
                    from: $scope.data.from,
                    to: $scope.data.to,
                    tech_response_id: $scope.data.tech_response ? $scope.data.tech_response.id : -1,
                    item_id: $scope.data.item ? $scope.data.item.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function(index, value) {
                    data_array.push({
                        id: value.id,
                        tech_response_no: value.tech_response_no,
                        customer_name: value.customer_name,
                        customer_address: value.customer_address,
                        item_code: value.item_code,
                        item_name: value.item_name,
                        unit_type: value.unit_type,
                        rate: parseFloat(Math.round(value.rate * 100) / 100).toFixed(2),
                        quantity: value.quantity,
                        value: parseFloat(Math.round(value.value * 100) / 100).toFixed(2),
                        invoice_value: parseFloat(Math.round(value.invoice_value * 100) / 100).toFixed(2)
                    });
                });
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function() {
            $scope.main_refresh();
        }, 1500, false);

    }]);
</script>
@endsection