@extends('layouts.main')

@section('title')
<title>M3Force | Tech Response Status</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Tech Response</a></li>
    <li class="active">Tech Response Status</li>
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

    .message-block-save {
        color: lightskyblue;
        float: left;
        text-align: left;
        font-weight: bold;
    }

    .message-block-error {
        color: red;
        float: right;
        text-align: right;
        font-weight: bold;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Tech Response Status</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('tech_response/ongoing_tech_response')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="value" name="value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="tech_response_id" name="tech_response_id" ng-model="data.tech_response_id" class="form-control" />

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Customer Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" ng-model="customer_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Customer Address</label>
                                            <input type="text" id="customer_address" name="customer_address" ng-model="customer_address" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Customer Contact No</label>
                                            <input type="text" id="customer_contact_no" name="customer_contact_no" ng-model="customer_contact_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Update Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Update Date</label>
                                            <input type="text" id="update_date" name="update_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.update_date" is-open="updateDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenUpdateDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Update Time</label>
                                            <input type="text" id="update_time" name="update_time" ng-model="data.update_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Update Status</label>
                                            <select name="update_status" id="update_status" ng-options="option.name for option in update_status_array track by option.id" ng-model="data.update_status" ng-change="checkStatus()" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.update_status && data.update_status.id == 9">
                                        <div class="form-group">
                                            <label class="control-label">Job Scheduled Date</label>
                                            <input type="text" id="job_scheduled_date" name="job_scheduled_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.job_scheduled_date" is-open="jobScheduledDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenJobScheduledDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.update_status && data.update_status.id == 9">
                                        <div class="form-group">
                                            <label class="control-label">Job Scheduled Time</label>
                                            <input type="text" id="job_scheduled_time" name="job_scheduled_time" ng-model="data.job_scheduled_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.update_status && data.update_status.id == 13">
                                        <div class="form-group">
                                            <label class="control-label">Chargeable</label><br />
                                            <input id="is_active" bs-switch emit-change="is_active" ng-model="data.is_chargeable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.update_status && data.update_status.id == 13 && data.is_chargeable">
                                        <div class="form-group">
                                            <label class="control-label">Invoice No</label>
                                            <input type="text" id="invoice_no" name="invoice_no" ng-model="data.invoice_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.update_status && data.update_status.id == 13 && data.is_chargeable">
                                        <div class="form-group">
                                            <label class="control-label">Invoice Value</label>
                                            <input type="text" id="invoice_value" name="invoice_value" ng-model="data.invoice_value" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row" ng-show="data.update_status && data.update_status.id == 13">
                                    <div class="col-md-12">
                                        <span id="span_error" class="message-block-error" ng-show="error"><%= error_message %></span>
                                    </div>
                                    <div class="col-md-12" style="margin-top: 10px;">
                                        <div ui-grid="itemIssueGridOptions" ui-grid-selection ui-grid-exporter ui-grid-row-edit ui-grid-edit ui-grid-cellnav ui-grid-pagination ui-grid-move-columns class="grid"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <textarea id="remarks" name="remarks" rows="4" ng-model="data.remarks" class="form-control"></textarea>
                                        </div>
                                    </div>
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
    var submitForm;
    var number_status = true;

    $('#span_error').hide();

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
        'ui.grid.cellNav',
        'frapontillo.bootstrap-switch'
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

    myApp.directive('uiGridEditAuto', ['$timeout', '$document', 'uiGridConstants', 'uiGridEditConstants', function($timeout, $document, uiGridConstants, uiGridEditConstants) {
        return {
            require: ['?^uiGrid', '?^uiGridRenderContainer'],
            scope: true,
            compile: function() {
                return {
                    post: function($scope, $elm, $attrs, controllers) {
                        var uiGridCtrl = controllers[0];
                        var renderContainerCtrl = controllers[1];

                        $scope.$on(uiGridEditConstants.events.BEGIN_CELL_EDIT, function() {
                            $elm.focus();
                            $scope[$attrs.focusMe] = true;

                            if (uiGridCtrl.grid.api.cellNav) {
                                uiGridCtrl.grid.api.cellNav.on.navigate($scope, function(newRowCol, oldRowCol) {
                                    $scope.stopEdit();
                                });
                            } else {

                                angular.element(document.querySelectorAll('.ui-grid-cell-contents')).on('click', onCellClick);
                            }
                            //                                angular.element(window).on('click', onWindowClick);
                        });

                        //                            $scope.$on('$destroy', function () {
                        //                                angular.element(window).off('click', onWindowClick);
                        //                                $('body > .dropdown-menu, body > div > .dropdown-menu').remove();
                        //                            });

                        $scope.stopEdit = function(evt) {
                            $scope.$emit(uiGridEditConstants.events.END_CELL_EDIT);
                        };
                        $elm.on('keydown', function(evt) {
                            switch (evt.keyCode) {
                                case uiGridConstants.keymap.ESC:
                                    evt.stopPropagation();
                                    $scope.$emit(uiGridEditConstants.events.CANCEL_CELL_EDIT);
                                    break;
                            }
                            if (uiGridCtrl && uiGridCtrl.grid.api.cellNav) {
                                if (evt.keyCode === uiGridConstants.keymap.TAB) {
                                    evt.uiGridTargetRenderContainerId = renderContainerCtrl.containerId;

                                    console.log(evt.key);
                                    if (uiGridCtrl.cellNav.handleKeyDown(evt) !== null) {
                                        $scope.stopEdit(evt);
                                    }

                                }

                            } else {
                                switch (evt.keyCode) {
                                    case uiGridConstants.keymap.ENTER:
                                    case uiGridConstants.keymap.TAB:
                                        evt.stopPropagation();
                                        evt.preventDefault();
                                        $scope.stopEdit(evt);
                                        break;
                                }
                            }
                            return true;
                        });
                    }
                };
            }
        };
    }]);

    myApp.directive('focusMe', function($timeout) {
        return {
            link: function(scope, element, attrs) {
                scope.$watch(attrs.focusMe, function(value) {
                    if (value === true) {
                        //$timeout(function() {
                        element[0].focus();
                        scope[attrs.focusMe] = false;
                        //});
                    }
                });
            }
        };
    });

    myApp.directive('jValidate', function() {
        return {
            link: function(scope, element, attr) {
                element.validate({
                    rules: {
                        update_date: {
                            required: true,
                            date: true
                        },
                        update_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        update_status: {
                            required: true,
                            remote: {
                                url: base_url + '/tech_response/validate_tech_response_status',
                                type: 'GET',
                                data: {
                                    value: function() {
                                        return scope.data.value;
                                    },
                                    tech_response_id: function() {
                                        return scope.data.tech_response_id;
                                    },
                                    update_status: function() {
                                        return scope.data.update_status.id;
                                    }
                                }
                            }
                        },
                        job_scheduled_date: {
                            required: true,
                            date: true
                        },
                        job_scheduled_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        invoice_value: {
                            number: true
                        },
                        errorClass: 'error'
                    },
                    messages: {
                        update_date: {
                            required: 'Update Date is required',
                            date: 'Invalid date format'
                        },
                        update_time: {
                            required: 'Update Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        update_status: {
                            required: 'Update Status is required',
                            remote: 'Update Status already exist'
                        },
                        job_scheduled_date: {
                            required: 'Job Scheduled Date is required',
                            date: 'Invalid date format'
                        },
                        job_scheduled_time: {
                            required: 'Job Scheduled Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        invoice_value: {
                            number: 'Invalid number format'
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
        $scope.update_status_array = [];

        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };

        $scope.updateDatePopup = {
            opened: false
        };
        $scope.OpenUpdateDate = function() {
            $scope.updateDatePopup.opened = !$scope.updateDatePopup.opened;
        };

        $scope.jobScheduledDatePopup = {
            opened: false
        };
        $scope.OpenJobScheduledDate = function() {
            $scope.jobScheduledDatePopup.opened = !$scope.jobScheduledDatePopup.opened;
        };

        $('#update_time').mask('00:00');
        $('#job_scheduled_time').mask('00:00');

        $scope.refreshForm = function() {
            $http({
                method: 'GET',
                url: base_url + '/tech_response/get_data'
            }).then(function successCallback(response) {
                var update_status_array = [];
                update_status_array.push({
                    id: '',
                    name: 'Select Update Status'
                });
                $.each(response.data.tech_response_status, function(index, value) {
                    if (value.id != 13) {
                        update_status_array.push({
                            id: value.id,
                            name: value.name
                        });
                    } else if (response.data.users_id == 1 || response.data.users_id == 24) {
                        update_status_array.push({
                            id: value.id,
                            name: value.name
                        });
                    }
                });

                $scope.update_status_array = update_status_array;

                var today = new Date();
                var hh = today.getHours();
                var mm = today.getMinutes();
                if (hh < 10) {
                    hh = '0' + hh;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }

                $scope.data = {
                    type: 1,
                    id: 0,
                    value: '',
                    tech_response_id: <?php echo $tech_response_id ? $tech_response_id : 0; ?>,
                    update_date: new Date(),
                    update_time: hh + ':' + mm,
                    update_status: $scope.update_status_array.length > 0 ? $scope.update_status_array[0] : {},
                    job_scheduled_date: new Date(),
                    job_scheduled_time: hh + ':' + mm,
                    invoice_no: '',
                    invoice_value: '',
                    is_chargeable: false,
                    remarks: ''
                };
                $scope.error = false;
                $scope.error_message = '';
                $scope.itemIssueGridOptions.data = [];

                $http({
                    method: 'GET',
                    url: base_url + '/tech_response/find_tech_response',
                    params: {
                        id: <?php echo $tech_response_id ? $tech_response_id : 0; ?>
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        $scope.customer_name = response.data.contact ? response.data.contact.name : '';
                        $scope.customer_address = response.data.contact ? response.data.contact.address : '';
                        $scope.customer_contact_no = response.data.contact ? response.data.contact.contact_no : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.refreshForm();

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
                    field: 'show_update',
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
                    cellTemplate: '<div class="text-center" ng-show="row.entity.show_update == 1"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
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
                    field: 'job_scheduled_date_time',
                    displayName: 'Job Scheduled Date & Time',
                    cellClass: 'grid-align',
                    width: '20%',
                    enableCellEdit: false
                },
                {
                    field: 'remarks',
                    displayName: 'Remarks',
                    width: '35%',
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

        $scope.itemIssueGridOptions = {
            showColumnFooter: true,
            enableCellEditOnFocus: true,
            showSelectionCheckbox: true,
            enableRowSelection: false,
            enableRowHeaderSelection: false,
            paginationPageSizes: [10, 25, 50],
            paginationPageSize: 10,
            enableFiltering: true,
            enableSorting: true
        };

        $scope.define_columns = function() {
            $scope.itemIssueGridOptions.columnDefs = [{
                    field: 'index',
                    type: 'number',
                    sort: {
                        direction: 'asc',
                        priority: 0
                    },
                    visible: false
                },
                {
                    field: 'id',
                    type: 'number',
                    visible: false
                },
                {
                    field: 'column',
                    displayName: 'No#',
                    cellClass: 'grid-align',
                    width: '50',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'code',
                    displayName: 'Item Code',
                    width: '120',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'name',
                    displayName: 'Item Name',
                    width: '350',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'unit_type',
                    displayName: 'Unit Type',
                    cellClass: 'grid-align',
                    width: '100',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'rate',
                    type: 'number',
                    displayName: 'Rate',
                    cellClass: 'grid-align-right',
                    width: '100',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'quantity',
                    type: 'number',
                    displayName: 'Quantity',
                    cellClass: 'grid-align-right',
                    width: '100',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'value',
                    type: 'number',
                    displayName: 'Value',
                    cellClass: 'grid-align-right',
                    width: '100',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'invoice_value',
                    type: 'number',
                    displayName: 'Invoice Value',
                    cellClass: 'grid-align-right',
                    width: '100',
                    editableCellTemplate: '<div class="ui-grid-cell-contents" ng-class="{ \'has-error\' : grid.appScope.dataForm.invoice_value.$invalid && grid.appScope.dataForm.invoice_value.$dirty }"><input type="text" class="grid-align-right" ng-class="\'colt\' + col.uid" ui-grid-editor ng-model="MODEL_COL_FIELD" ng-keyup="grid.appScope.invoice_value_check(row.entity)" name="invoice_value" ng-validate="invoice_value" focus-me="grid.appScope.focus_invoice_value" style="text-align: right;"></div>',
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalValue() | number:2 %></div>',
                    enableCellEdit: true
                }
            ];
        };

        $scope.getAggregationTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.itemIssueGridOptions.data.length; i++) {
                total_value += Number($scope.itemIssueGridOptions.data[i].invoice_value);
            }
            return total_value;
        };

        $scope.cleanItemIssueGridData = function() {
            if ($scope.itemIssueGridOptions.data) {
                var gridRows = $scope.itemIssueGridApi.rowEdit.getDirtyRows();
                var dataRows = gridRows.map(function(gridRow) {
                    return gridRow.entity;
                });
                $scope.itemIssueGridApi.rowEdit.setRowsClean(dataRows);
            }
        };

        $scope.checkStatus = function() {
            if ($scope.data.update_status && $scope.data.update_status.id == 13) {
                $scope.error = false;
                $scope.error_message = '';
                $scope.itemIssueGridOptions.data = [];

                $http({
                    method: 'GET',
                    url: base_url + '/tech_response/get_issed_items',
                    params: {
                        tech_response_id: $scope.data.tech_response_id
                    }
                }).then(function successCallback(response) {
                    $scope.define_columns();
                    $scope.cleanItemIssueGridData();
                    $scope.itemIssueGridOptions.data = response.data;
                    $scope.itemIssueGridApi.core.refresh();
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        }

        $scope.invoice_value_check = function(value) {
            if (value.invoice_value) {
                var num_check = /^[0-9]+$/.test(value.invoice_value);

                number_status = num_check;

                if ($scope.dataForm.invoice_value) {
                    $scope.dataForm.invoice_value.$setValidity('validate', number_status);
                }

                if (!num_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.itemIssueGridOptions.data[value.index].column + ' : Invalid Invoice Value input.';
                } else {
                    $scope.error = false;
                }
            } else {
                number_status = true;
            }
        };

        $scope.itemIssueGridOptionsCheck = function() {
            for (var i = 0; i < $scope.itemIssueGridOptions.data.length; i++) {
                var num_check = /^[0-9]+$/.test($scope.itemIssueGridOptions.data[i].invoice_value);
                
                number_status = num_check;

                if (!num_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.itemIssueGridOptions.data[i].column + ' : Invalid Invoice Value input.';
                } else {
                    $scope.error = false;
                }

                if (!number_status) {
                    break;
                }
            }
            return number_status;
        };

        $scope.itemIssueGridOptions.onRegisterApi = function(itemIssueGridApi) {
            $scope.itemIssueGridApi = itemIssueGridApi;

            itemIssueGridApi.edit.on.beginCellEdit($scope, function(rowEntity, colDef) {
                if (colDef.name == 'invoice_value') {
                    $scope.invoice_value_check(rowEntity);
                    $scope.focus_invoice_value = true;
                }
            });

            itemIssueGridApi.edit.on.afterCellEdit($scope, function(rowEntity, colDef, newValue, oldValue) {
                if (colDef.name == 'invoice_value') {
                    $scope.invoice_value_check(rowEntity);
                }

                if (colDef.name == 'invoice_value' && !number_status) {
                    $scope.itemIssueGridOptions.data[rowEntity.index].invoice_value = oldValue;
                }

                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            });

            itemIssueGridApi.rowEdit.on.saveRow($scope, function(rowEntity) {
                var promise = $q.defer();
                $scope.itemIssueGridApi.rowEdit.setSavePromise(rowEntity, promise.promise);
                promise.resolve();
            });
        };

        $scope.export = function() {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function() {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.editRecord = function(row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/tech_response/find_tech_response_status',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    var update_date_time = response.data.update_date_time.split(' ');
                    var job_scheduled_date_time = response.data.job_scheduled_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.tech_response_status ? response.data.tech_response_status.id : '',
                        tech_response_id: response.data.tech_response_id,
                        update_date: update_date_time[0],
                        update_time: update_date_time[1],
                        update_status: response.data.tech_response_status ? {
                            id: response.data.tech_response_status.id,
                            name: response.data.tech_response_status.name
                        } : {},
                        job_scheduled_date: job_scheduled_date_time.length > 1 ? job_scheduled_date_time[0] : '',
                        job_scheduled_time: job_scheduled_date_time.length > 1 ? job_scheduled_date_time[1] : '',
                        is_chargeable: response.data.is_chargeable == 1 ? true : false,
                        invoice_no: response.data.invoice_no,
                        invoice_value: response.data.invoice_value,
                        remarks: response.data.remarks
                    };
                    var data_array = [];
                    $.each(response.data.tech_response_invoice_details, function(index, value) {
                        data_array.push({
                            index: index,
                            id: value.item ? value.item.id : 0,
                            column: index + 1,
                            code: value.item ? value.item.code : '',
                            name: value.item ? value.item.name : '',
                            unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                            rate: value.rate,
                            quantity: value.quantity,
                            value: value.value,
                            invoice_value: value.invoice_value
                        });
                    });
                    $scope.define_columns();
                    $scope.cleanItemIssueGridData();
                    $scope.itemIssueGridOptions.data = data_array;
                    $scope.itemIssueGridApi.core.refresh();
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function(row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/tech_response/find_tech_response_status',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    var update_date_time = response.data.update_date_time.split(' ');
                    var job_scheduled_date_time = response.data.job_scheduled_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.tech_response_status ? response.data.tech_response_status.id : '',
                        tech_response_id: response.data.tech_response_id,
                        update_date: update_date_time[0],
                        update_time: update_date_time[1],
                        update_status: response.data.tech_response_status ? {
                            id: response.data.tech_response_status.id,
                            name: response.data.tech_response_status.name
                        } : {},
                        job_scheduled_date: job_scheduled_date_time.length > 1 ? job_scheduled_date_time[0] : '',
                        job_scheduled_time: job_scheduled_date_time.length > 1 ? job_scheduled_date_time[1] : '',
                        is_chargeable: response.data.is_chargeable == 1 ? true : false,
                        invoice_no: response.data.invoice_no,
                        invoice_value: response.data.invoice_value,
                        remarks: response.data.remarks
                    };
                    var data_array = [];
                    $.each(response.data.tech_response_invoice_details, function(index, value) {
                        data_array.push({
                            index: index,
                            id: value.item ? value.item.id : 0,
                            column: index + 1,
                            code: value.item ? value.item.code : '',
                            name: value.item ? value.item.name : '',
                            unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                            rate: value.rate,
                            quantity: value.quantity,
                            value: value.value,
                            invoice_value: value.invoice_value
                        });
                    });
                    $scope.define_columns();
                    $scope.cleanItemIssueGridData();
                    $scope.itemIssueGridOptions.data = data_array;
                    $scope.itemIssueGridApi.core.refresh();
                    swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover <strong>" + response.data.tech_response_status.name + "</strong> tech response status!",
                            html: true,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#FF0000",
                            confirmButtonText: "Yes, delete it!",
                            closeOnConfirm: false
                        },
                        function() {
                            $http.delete(base_url + '/tech_response/' + $scope.data.id, {
                                params: {
                                    type: $scope.data.type
                                }
                            }).success(function(response_delete) {
                                swal({
                                    title: "Deleted!",
                                    text: response.data.tech_response_status.name + " tech response status has been deleted.",
                                    type: "success",
                                    confirmButtonColor: "#9ACD32"
                                });
                                $scope.resetForm();
                                $scope.main_refresh();
                            });
                        });
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.resetForm = function() {
            $scope.refreshForm();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function() {
            $('#dataForm').submit();
        };

        submitForm = function() {
            if ($scope.data.update_status.id == 13) {
                if ($scope.itemIssueGridOptionsCheck() && number_status) {
                    swal({
                            title: "Are you sure?",
                            text: "Tech Response Total Invoice Value : <strong>" + parseFloat(Math.round($scope.getAggregationTotalValue() * 100) / 100).toFixed(2) + "</strong> !",
                            html: true,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#FF0000",
                            confirmButtonText: "Yes, proceed!",
                            closeOnConfirm: true
                        },
                        function() {
                            $scope.saveForm();
                        });
                }
            } else {
                $scope.saveForm();
            }
        };

        $scope.saveForm = function() {
            $scope.data.tech_response_invoice_details = $scope.itemIssueGridOptions.data;
            $('#save_button').prop('disabled', true);
            if ($scope.data.id == 0) {
                $http.post(base_url + '/tech_response', $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Status',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    if (result.response && $scope.data.update_status.id == 12) {
                        $window.location.href = base_url + '/tech_response/ongoing_tech_response';
                    } else {
                        $scope.resetForm();
                        $scope.main_refresh();
                    }
                });
            } else {
                $http.put(base_url + '/tech_response/' + $scope.data.id, $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Status',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    if (result.response && $scope.data.update_status.id == 12) {
                        $window.location.href = base_url + '/tech_response/ongoing_tech_response';
                    } else {
                        $scope.resetForm();
                        $scope.main_refresh();
                    }
                });
            }
        };

        $scope.main_refresh = function() {
            $('#span_error').show();
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.customer_name + ' Tech Response Status.csv';
            $http({
                method: 'GET',
                url: base_url + '/tech_response/tech_response_status_list',
                params: {
                    tech_response_id: <?php echo $tech_response_id ? $tech_response_id : 0; ?>
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.tech_response_status, function(index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        show_update: value.tech_response_status ? value.tech_response_status.show_update : 0,
                        update_date_time: value.update_date_time,
                        update_status: value.tech_response_status ? value.tech_response_status.name : '',
                        job_scheduled_date_time: value.job_scheduled_date_time,
                        remarks: value.remarks,
                        log_user: value.user ? value.user.first_name : ''
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

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection