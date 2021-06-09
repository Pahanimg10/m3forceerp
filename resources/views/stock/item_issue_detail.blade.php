@extends('layouts.main')

@section('title')
<title>M3Force | Item Issue Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Stock</a></li>
    <li class="active">Item Issue Details</li>
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

    input[type="text"]:read-only {
        color: #1CB09A;
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
                    <h3 class="panel-title"><strong>Item Issue Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.item_issue_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li>
                            <div><a ng-show="data.item_issue_id && data.is_posted == 1" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/item_issue/print_item_issue?id=<%=data.item_issue_id%>">Print</a></div>
                        </li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('item_issue')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="item_issue_id" name="item_issue_id" ng-model="data.item_issue_id" class="form-control" />
                                <input type="hidden" id="item_code" name="item_code" ng-model="data.item_code" class="form-control" />
                                <input type="hidden" id="item_name" name="item_name" ng-model="data.item_name" class="form-control" />

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Item Issue Type</label>
                                            <select name="item_issue_type" id="item_issue_type" ng-options="option.name for option in item_issue_type_array track by option.id" ng-model="data.item_issue_type" ng-disabled="edit_disable" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.item_issue_type && (data.item_issue_type.id == 1 || data.item_issue_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Document</label>
                                            <input type="text" id="document" name="document" ui-grid-edit-auto ng-model="data.document" ng-disabled="data.item_issue_id || edit_disable" typeahead="name as document_array.name for document_array in document_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onDocumentSelect($item, $model, $label)" ng-keyup="get_documents(data.item_issue_type.id, data.document)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4" ng-show="data.item_issue_type && (data.item_issue_type.id == 1 || data.item_issue_type.id == 2)">
                                        <div class="form-group">
                                            <label class="control-label">Customer</label>
                                            <input type="text" id="customer" name="customer" ng-model="data.customer" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Issued To</label>
                                            <input type="text" id="issued_to" name="issued_to" ng-model="data.issued_to" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Item Issue Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Item Issue No</label>
                                            <input type="text" id="item_issue_no" name="item_issue_no" ng-model="data.item_issue_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Item Issue Date</label>
                                            <input type="text" id="item_issue_date" name="item_issue_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.item_issue_date" ng-disabled="edit_disable" is-open="itemIssueDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenItemIssueDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Item Issue Time</label>
                                            <input type="text" id="item_issue_time" name="item_issue_time" ng-model="data.item_issue_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="col-md-6" style="padding-top: 15px; float: left;">Item Details</h4>
                                    <div class="col-md-6" style="padding-top: 15px;">
                                        <button type="button" ng-click="resetItem()" style="float: right; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button>
                                        <button type="button" ng-show="(data.item_issue_type.id == 1 || data.item_issue_type.id == 2) && data.document.id && data.issued_to != ''" ng-click="findBalanceItems()" ng-disabled="edit_disable" style="float: right;" class="btn btn-primary" data-toggle="modal" data-target="#dataModal">Find</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Main Category</label>
                                            <select name="main_category" id="main_category" ng-options="option.name for option in main_category_array track by option.id" ng-model="data.main_category" ng-disabled="edit_disable" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Sub Category</label>
                                            <select name="sub_category" id="sub_category" ng-options="option.name for option in sub_category_array track by option.id" ng-model="data.sub_category" ng-disabled="edit_disable" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ui-grid-edit-auto ng-model="data.code" ng-disabled="edit_disable" typeahead="name as code_array.name for code_array in code_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onCodeSelect($item, $model, $label)" ng-keyup="get_codes(data.main_category, data.sub_category, data.code)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Item</label>
                                            <input type="text" id="item" name="item" ui-grid-edit-auto ng-model="data.item" ng-disabled="edit_disable" typeahead="name as item_array.name for item_array in item_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onItemSelect($item, $model, $label)" ng-keyup="get_items(data.main_category, data.sub_category, data.item)" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Model No</label>
                                            <input type="text" id="model_no" name="model_no" ng-model="data.model_no" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Unit Type</label>
                                            <input type="text" id="unit_type" name="unit_type" ng-model="data.unit_type" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Quantity</label>
                                            <input type="text" id="quantity" name="quantity" ng-model="data.quantity" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Warranty (Years)</label>
                                            <input type="text" id="warranty" name="warranty" ng-model="data.warranty" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.item && data.item.is_serial == 1">
                                        <div class="form-group">
                                            <label class="control-label">Serial No</label>
                                            <input type="text" id="serial_no" name="serial_no" ui-grid-edit-auto ng-model="data.serial_no" ng-disabled="edit_disable" typeahead="name as serial_no_array.name for serial_no_array in serial_no_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onSerialNoSelect($item, $model, $label)" ng-keyup="get_serial_nos(data.serial_no)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-center" ng-show="data.item && data.item.is_serial == 1">
                                        <div class="form-group">
                                            <button id="add_button" type="button" ng-click="addSerialNos()" ng-disabled="edit_disable" class="btn btn-info" style="margin-top: 30px;">Add</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" ng-show="serialGridOptions.data.length > 0">
                                    <div class="col-md-12" style="margin-top: 20px;">
                                        <div ui-grid="serialGridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination ui-grid-move-columns class="grid"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination ui-grid-move-columns class="grid"></div>
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
                                <h3 class="panel-title"><strong>Pending</strong> Items</h3>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body">
                                <form autocomplete="off" id="bulkDataForm" name="bulkDataForm" class="form-horizontal" j-validate>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <span id="span_error" class="message-block-error" ng-show="error"><%= error_message %></span>
                                        </div>
                                        <div class="col-md-12 text-right" style="margin-top: 10px;">
                                            UT - Unit Type&nbsp;&nbsp;&nbsp;BQ - Balance Quantity&nbsp;&nbsp;&nbsp;IQ - Issue Quantity&nbsp;&nbsp;&nbsp;IW - Issue Warranty
                                        </div>
                                        <div class="col-md-12" style="margin-top: 10px;">
                                            <div ui-grid="bulkGridOptions" ui-grid-selection ui-grid-exporter ui-grid-row-edit ui-grid-edit ui-grid-cellnav ui-grid-pagination ui-grid-move-columns class="grid"></div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary pull-right" ng-click="bulkSave()" id="bulk_save_button" style="margin-right: 5px;">Save</button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    var submitForm;
    var number_status = true;

    $('#span_error').hide();

    // $('#dataForm').on('click', function() {
    //     $("#dataForm").valid();
    // });

    function inArray(needle, haystack) {
        var length = haystack.length;
        for (var i = 0; i < length; i++) {
            if (haystack[i] == needle) return true;
        }
        return false;
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
        angular.element(document.querySelector('#main_menu_stock')).addClass('active');
        angular.element(document.querySelector('#sub_menu_item_issue')).addClass('active');
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
                        item_issue_type: {
                            required: true
                        },
                        document: {
                            required: true,
                            remote: {
                                url: base_url + '/item_issue/validate_document_no',
                                type: 'GET',
                                data: {
                                    item_issue_type: function() {
                                        return scope.data.item_issue_type && scope.data.item_issue_type.id ? scope.data.item_issue_type.id : '';
                                    },
                                    document: function() {
                                        return scope.data.document && scope.data.document.name ? scope.data.document.name : scope.data.document;
                                    }
                                }
                            }
                        },
                        issued_to: {
                            required: true
                        },
                        item_issue_date: {
                            required: true,
                            date: true
                        },
                        item_issue_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        code: {
                            remote: {
                                url: base_url + '/item_issue/validate_item_code',
                                type: 'GET',
                                data: {
                                    item_issue_id: function() {
                                        return scope.data.item_issue_id;
                                    },
                                    item_code: function() {
                                        return scope.data.item_code;
                                    },
                                    item_issue_type: function() {
                                        return scope.data.item_issue_type && scope.data.item_issue_type.id ? scope.data.item_issue_type.id : '';
                                    },
                                    document_id: function() {
                                        return scope.data.document && scope.data.document.id ? scope.data.document.id : '';
                                    },
                                    item_id: function() {
                                        return scope.data.code && scope.data.code.id ? scope.data.code.id : '';
                                    },
                                    code: function() {
                                        return scope.data.code && scope.data.code.name ? scope.data.code.name : scope.data.code;
                                    }
                                }
                            }
                        },
                        item: {
                            remote: {
                                url: base_url + '/item_issue/validate_item_name',
                                type: 'GET',
                                data: {
                                    item_issue_id: function() {
                                        return scope.data.item_issue_id;
                                    },
                                    item_name: function() {
                                        return scope.data.item_name;
                                    },
                                    item_issue_type: function() {
                                        return scope.data.item_issue_type && scope.data.item_issue_type.id ? scope.data.item_issue_type.id : '';
                                    },
                                    document_id: function() {
                                        return scope.data.document && scope.data.document.id ? scope.data.document.id : '';
                                    },
                                    item_id: function() {
                                        return scope.data.code && scope.data.code.id ? scope.data.code.id : '';
                                    },
                                    name: function() {
                                        return scope.data.item && scope.data.item.name ? scope.data.item.name : scope.data.item;
                                    }
                                }
                            }
                        },
                        quantity: {
                            required: function(element) {
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            remote: {
                                url: base_url + '/item_issue/validate_item_quantity',
                                type: 'GET',
                                data: {
                                    item_issue_id: function() {
                                        return scope.data.item_issue_id;
                                    },
                                    item_issue_type: function() {
                                        return scope.data.item_issue_type && scope.data.item_issue_type.id ? scope.data.item_issue_type.id : '';
                                    },
                                    document_id: function() {
                                        return scope.data.document && scope.data.document.id ? scope.data.document.id : '';
                                    },
                                    item_id: function() {
                                        return scope.data.code && scope.data.code.id ? scope.data.code.id : '';
                                    },
                                    quantity: function() {
                                        return scope.data.quantity;
                                    }
                                }
                            },
                            number: true,
                            min: 0
                        },
                        warranty: {
                            required: function(element) {
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            number: true,
                            min: 0
                        },
                        serial_no: {
                            remote: {
                                url: base_url + '/item_issue/validate_serial_no',
                                type: 'GET',
                                data: {
                                    serial_no: function() {
                                        return scope.data.serial_no && scope.data.serial_no.name ? scope.data.serial_no.name : scope.data.serial_no;
                                    }
                                }
                            },
                            serialNoExist: true
                        },
                        errorClass: 'error'
                    },
                    messages: {
                        item_issue_type: {
                            required: 'Item Issue Type is required'
                        },
                        document: {
                            required: 'Document is required',
                            remote: 'Invalid Document'
                        },
                        issued_to: {
                            required: 'Issued To is required'
                        },
                        item_issue_date: {
                            required: 'Item Issue Date is required',
                            date: 'Invalid date format'
                        },
                        item_issue_time: {
                            required: 'Item Issue Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        code: {
                            remote: 'Invalid Code or Item error'
                        },
                        item: {
                            remote: 'Invalid Item or Item error'
                        },
                        quantity: {
                            required: 'Qunatity is required',
                            remote: 'Qunatity exceeded',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        warranty: {
                            required: 'Warranty is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        serial_no: {
                            remote: 'Invalid Serial No',
                            serialNoExist: 'Serial No already exist'
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
        $scope.base_url = base_url;
        $scope.edit_disable = false;
        $scope.permission = false;

        $scope.data = [];
        $scope.item_issue_type_array = [];
        $scope.document_array = [];
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.code_array = [];
        $scope.item_array = [];
        $scope.serial_no_array = [];

        $scope.data.item_issue_id = <?php echo $item_issue_id ? $item_issue_id : 0; ?>;

        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };

        $scope.itemIssueDatePopup = {
            opened: false
        };
        $scope.OpenItemIssueDate = function() {
            $scope.itemIssueDatePopup.opened = !$scope.itemIssueDatePopup.opened;
        };

        $('#item_issue_time').mask('00:00');

        $scope.refreshForm = function() {
            $http({
                method: 'GET',
                url: base_url + '/item_issue/get_data'
            }).then(function successCallback(response) {
                var item_issue_type_array = [];
                item_issue_type_array.push({
                    id: '',
                    name: 'Select Item Issue Type'
                });
                var main_category_array = [];
                main_category_array.push({
                    id: '',
                    name: 'Main Category'
                });
                var sub_category_array = [];
                sub_category_array.push({
                    id: '',
                    name: 'Sub Category'
                });
                $.each(response.data.item_issue_types, function(index, value) {
                    item_issue_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });
                $.each(response.data.main_item_categories, function(index, value) {
                    main_category_array.push({
                        id: value.id,
                        name: value.name
                    });
                });
                $.each(response.data.sub_item_categories, function(index, value) {
                    sub_category_array.push({
                        id: value.id,
                        name: value.name
                    });
                });

                $scope.item_issue_type_array = item_issue_type_array;
                $scope.main_category_array = main_category_array;
                $scope.sub_category_array = sub_category_array;

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
                    item_issue_id: $scope.data.item_issue_id,
                    item_code: '',
                    item_name: '',
                    item_issue_type: $scope.item_issue_type_array.length > 0 ? $scope.item_issue_type_array[0] : {},
                    document: '',
                    customer: '',
                    issued_to: '',
                    item_issue_no: '',
                    item_issue_date: new Date(),
                    item_issue_time: hh + ':' + mm,
                    remarks: '',
                    is_posted: 0,
                    main_category: $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {},
                    sub_category: $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {},
                    code: '',
                    item: '',
                    model_no: '',
                    unit_type: '',
                    quantity: '',
                    warranty: 0,
                    serial_no: ''
                };
                $scope.serialGridOptions.data = [];

                $http({
                    method: 'GET',
                    url: base_url + '/item_issue/find_item_issue',
                    params: {
                        id: $scope.data.item_issue_id
                    }
                }).then(function successCallback(response) {
                    if (response.data.item_issue) {
                        var document = '';
                        var customer = '';
                        if (response.data.item_issue.item_issue_type_id == 1) {
                            document = response.data.item_issue.job ? {
                                id: response.data.item_issue.job.id,
                                name: response.data.item_issue.job.job_no
                            } : '';
                            customer = response.data.item_issue.job && response.data.item_issue.job.inquiry && response.data.item_issue.job.inquiry.contact ? response.data.item_issue.job.inquiry.contact.name : '';
                        } else if (response.data.item_issue.item_issue_type_id == 2) {
                            document = response.data.item_issue.tech_response ? {
                                id: response.data.item_issue.tech_response.id,
                                name: response.data.item_issue.tech_response.tech_response_no
                            } : '';
                            customer = response.data.item_issue.tech_response && response.data.item_issue.tech_response.contact ? response.data.item_issue.tech_response.contact.name : '';
                        }

                        var item_issue_date_time = response.data.item_issue.item_issue_date_time.split(' ');
                        $scope.data.item_issue_id = response.data.item_issue.id;
                        $scope.data.item_issue_type = response.data.item_issue.item_issue_type ? {
                            id: response.data.item_issue.item_issue_type.id,
                            name: response.data.item_issue.item_issue_type.name
                        } : '';
                        $scope.data.document = document;
                        $scope.data.customer = customer;
                        $scope.data.issued_to = response.data.item_issue.issued_to;
                        $scope.data.item_issue_no = response.data.item_issue.item_issue_no;
                        $scope.data.item_issue_date = item_issue_date_time[0];
                        $scope.data.item_issue_time = item_issue_date_time[1];
                        $scope.data.remarks = response.data.item_issue.remarks;
                        $scope.data.is_posted = response.data.item_issue.is_posted;
                        $scope.edit_disable = response.data.item_issue.is_posted == 1 ? true : false;
                    }
                    $scope.permission = response.data.permission;
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

        $scope.resetItem = function() {
            $scope.data.main_category = $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {};
            $scope.data.sub_category = $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {};
            $scope.data.code = '';
            $scope.data.item = '';
            $scope.data.model_no = '';
            $scope.data.unit_type = '';
            $scope.data.quantity = '';
            $scope.data.warranty = 0;
            $scope.data.serial_no = '';

            $scope.serialGridOptions.data = [];
        };

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
                    field: 'is_posted',
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_posted == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>',
                    visible: $scope.edit_disable ? false : true
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
                    field: 'warranty',
                    displayName: 'Warranty',
                    cellClass: 'grid-align-right',
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

        $scope.serialGridOptions = {
            columnDefs: [{
                    field: 'index',
                    type: 'number',
                    sort: {
                        direction: 'desc',
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
                    field: 'count',
                    displayName: 'No#',
                    cellClass: 'grid-align',
                    width: 150,
                    enableCellEdit: false
                },
                {
                    field: 'serial_no',
                    displayName: 'Serail No',
                    width: 835,
                    enableCellEdit: false
                },
                {
                    field: 'options',
                    displayName: '',
                    enableFiltering: false,
                    enableSorting: false,
                    enableCellEdit: false,
                    width: 50,
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteSerialRecord(row)" ng-disabled="grid.appScope.edit_disable"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>',
                    visible: $scope.edit_disable ? false : true
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
            onRegisterApi: function(gridApi) {
                $scope.serialGridApi = gridApi;
            }
        };

        $scope.bulkGridOptions = {
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
            $scope.bulkGridOptions.columnDefs = [{
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
                    field: 'is_serial',
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
                    width: '410',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'unit_type',
                    displayName: 'UT',
                    cellClass: 'grid-align',
                    width: '50',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'balance_quantity',
                    type: 'number',
                    displayName: 'BQ',
                    cellClass: 'grid-align-right',
                    width: '50',
                    allowCellFocus: false,
                    enableCellEdit: false
                },
                {
                    field: 'issue_quantity',
                    type: 'number',
                    displayName: 'IQ',
                    cellClass: 'grid-align-right',
                    width: '50',
                    editableCellTemplate: '<div class="ui-grid-cell-contents" ng-class="{ \'has-error\' : grid.appScope.bulkDataForm.issue_quantity.$invalid && grid.appScope.bulkDataForm.issue_quantity.$dirty }"><input type="text" class="grid-align-right" ng-class="\'colt\' + col.uid" ui-grid-editor ng-model="MODEL_COL_FIELD" ng-keyup="grid.appScope.number_check(row.entity)" name="issue_quantity" ng-validate="issue_quantity" focus-me="grid.appScope.focus_issue_quantity" style="text-align: right;"></div>',
                    enableCellEdit: true
                },
                {
                    field: 'issue_warranty',
                    type: 'number',
                    displayName: 'IW',
                    cellClass: 'grid-align-right',
                    width: '50',
                    editableCellTemplate: '<div class="ui-grid-cell-contents" ng-class="{ \'has-error\' : grid.appScope.bulkDataForm.issue_warranty.$invalid && grid.appScope.bulkDataForm.issue_warranty.$dirty }"><input type="text" class="grid-align-right" ng-class="\'colt\' + col.uid" ui-grid-editor ng-model="MODEL_COL_FIELD" ng-keyup="grid.appScope.warranty_check(row.entity)" name="issue_warranty" ng-validate="issue_warranty" focus-me="grid.appScope.focus_issue_warranty" style="text-align: right;"></div>',
                    enableCellEdit: true
                }
            ];
        };

        $scope.findBalanceItems = function() {
            $scope.error = false;
            $scope.error_message = '';
            $scope.bulkGridOptions.data = [];

            $http({
                method: 'GET',
                url: base_url + '/item_issue/find_balance_items',
                params: {
                    document_id: $scope.data.document ? $scope.data.document.id : 0,
                    type: $scope.data.item_issue_type ? $scope.data.item_issue_type.id : 0,
                    item_issue_id: $scope.data.item_issue_id
                }
            }).then(function successCallback(response) {
                $scope.define_columns();
                $scope.cleanBulkGridData();
                $scope.bulkGridOptions.data = response.data;
                $scope.bulkGridApi.core.refresh();
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.number_check = function(value) {
            if (value.issue_quantity) {
                var num_check = /^[0-9]+$/.test(value.issue_quantity);
                var value_check = value.issue_quantity >= 0 ? true : false;
                var quantity_check = value.balance_quantity >= value.issue_quantity ? true : false;

                number_status = num_check && value_check && quantity_check;

                if ($scope.bulkDataForm.issue_quantity) {
                    $scope.bulkDataForm.issue_quantity.$setValidity('validate', number_status);
                }

                if (!num_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[value.index].column + ' : Invalid Quantity input.';
                } else if (!value_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[value.index].column + ' : Quantity should be grater than or equal 0.';
                } else if (!quantity_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[value.index].column + ' : Issue Quantity can not exceed Balance Quantity';
                } else {
                    $scope.error = false;
                }
            } else {
                number_status = true;
            }
        };

        $scope.warranty_check = function(value) {
            if (value.issue_warranty) {
                var num_check = /^[0-9]+$/.test(value.issue_warranty);
                var value_check = value.issue_warranty >= 0 ? true : false;

                number_status = num_check && value_check;

                if ($scope.bulkDataForm.issue_warranty) {
                    $scope.bulkDataForm.issue_warranty.$setValidity('validate', number_status);
                }

                if (!num_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[value.index].column + ' : Invalid Warranty input.';
                } else if (!value_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[value.index].column + ' : Warranty should be grater than or equal 0.';
                } else {
                    $scope.error = false;
                }
            } else {
                number_status = true;
            }
        };

        $scope.cleanBulkGridData = function() {
            if ($scope.bulkGridOptions.data) {
                var gridRows = $scope.bulkGridApi.rowEdit.getDirtyRows();
                var dataRows = gridRows.map(function(gridRow) {
                    return gridRow.entity;
                });
                $scope.bulkGridApi.rowEdit.setRowsClean(dataRows);
            }
        };

        $scope.bulkGridOptionsCheck = function() {
            for (var i = 0; i < $scope.bulkGridOptions.data.length; i++) {
                var num_check = /^[0-9]+$/.test($scope.bulkGridOptions.data[i].issue_quantity);
                var value_check = $scope.bulkGridOptions.data[i].issue_quantity >= 0 ? true : false;
                var quantity_check = $scope.bulkGridOptions.data[i].balance_quantity >= $scope.bulkGridOptions.data[i].issue_quantity ? true : false;

                number_status = num_check && value_check && quantity_check;

                if (!num_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[i].column + ' : Invalid Quantity input.';
                } else if (!value_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[i].column + ' : Quantity should be grater than or equal 0.';
                } else if (!quantity_check) {
                    $scope.error = true;
                    $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[i].column + ' : Issue Quantity can not exceed Balance Quantity';
                } else {
                    var num_check = /^[0-9]+$/.test($scope.bulkGridOptions.data[i].issue_warranty);
                    var value_check = $scope.bulkGridOptions.data[i].issue_warranty >= 0 ? true : false;

                    number_status = num_check && value_check;

                    if (!num_check) {
                        $scope.error = true;
                        $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[i].column + ' : Invalid Warranty input.';
                    } else if (!value_check) {
                        $scope.error = true;
                        $scope.error_message = 'No# ' + $scope.bulkGridOptions.data[i].column + ' : Warranty should be grater than or equal 0.';
                    } else {
                        $scope.error = false;
                    }
                }

                if (!number_status) {
                    break;
                }
            }
            return number_status;
        };

        $scope.bulkSave = function() {
            if ($scope.bulkGridOptionsCheck() && number_status) {
                $scope.data.bulk_list = $scope.bulkGridOptions.data;

                $scope.error = true;
                $scope.error_message = 'Please Wait.';
                $('#bulk_save_button').prop('disabled', true);

                console.log($scope.data);

                $http({
                    method: 'POST',
                    url: base_url + '/item_issue/bulk_item_issue',
                    data: $scope.data
                }).then(function successCallback(result) {
                    $('#dataModal').hide();
                    $('#bulk_save_button').prop('disabled', false);
                    $scope.error = false;

                    $.pnotify && $.pnotify({
                        title: 'Item Issue Details',
                        text: result.data.message,
                        type: result.data.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.item_issue_id = result.data.response ? result.data.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.bulkGridOptions.onRegisterApi = function(bulkGridApi) {
            //set bulkGridApi on scope focus_location
            $scope.bulkGridApi = bulkGridApi;

            bulkGridApi.edit.on.beginCellEdit($scope, function(rowEntity, colDef) {
                if (colDef.name == 'issue_quantity') {
                    $scope.number_check(rowEntity);
                    $scope.focus_issue_quantity = true;
                }
                if (colDef.name == 'issue_warranty') {
                    $scope.warranty_check(rowEntity);
                    $scope.focus_issue_warranty = true;
                }
            });

            bulkGridApi.edit.on.afterCellEdit($scope, function(rowEntity, colDef, newValue, oldValue) {
                if (colDef.name == 'issue_quantity') {
                    $scope.number_check(rowEntity);
                }

                if (colDef.name == 'issue_quantity' && !number_status) {
                    $scope.bulkGridOptions.data[rowEntity.index].issue_quantity = oldValue;
                }

                if (colDef.name == 'issue_warranty') {
                    $scope.warranty_check(rowEntity);
                }

                if (colDef.name == 'issue_warranty' && !number_status) {
                    $scope.bulkGridOptions.data[rowEntity.index].issue_warranty = oldValue;
                }

                if (!$scope.$$phase) {
                    $scope.$apply();
                }
            });

            bulkGridApi.rowEdit.on.saveRow($scope, function(rowEntity) {
                var promise = $q.defer();
                $scope.bulkGridApi.rowEdit.setSavePromise(rowEntity, promise.promise);
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

            $scope.serialGridOptions.enableFiltering = !$scope.serialGridOptions.enableFiltering;
            $scope.serialGridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.getAggregationTotalValue = function() {
            var total_value = 0;
            for (var i = 0; i < $scope.gridOptions.data.length; i++) {
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.get_documents = function(item_issue_type_id, document_no) {
            if (document_no && document_no.length > 0) {
                $scope.document_array = [];
                if (item_issue_type_id == 1) {
                    $http({
                        method: 'GET',
                        url: base_url + '/get_job_nos',
                        params: {
                            job_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];
                        $.each(response.data, function(index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.job_no,
                                customer: value.inquiry && value.inquiry.contact ? value.inquiry.contact.name : ''
                            });
                        });
                        $scope.find_document(item_issue_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                } else if (item_issue_type_id == 2) {
                    $http({
                        method: 'GET',
                        url: base_url + '/get_tech_response_nos',
                        params: {
                            tech_response_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];
                        $.each(response.data, function(index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.tech_response_no,
                                customer: value.contact ? value.contact.name : ''
                            });
                        });
                        $scope.find_document(item_issue_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
            }
        };

        $scope.find_document = function(item_issue_type_id, document_no) {
            if (item_issue_type_id == 1) {
                $http({
                    method: 'GET',
                    url: base_url + '/find_job_no',
                    params: {
                        job_no: document_no
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        $scope.data.document = {
                            id: response.data.id,
                            name: response.data.job_no
                        };
                        $scope.data.customer = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.name : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            } else if (item_issue_type_id == 2) {
                $http({
                    method: 'GET',
                    url: base_url + '/find_tech_response_no',
                    params: {
                        tech_response_no: document_no
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        $scope.data.document = {
                            id: response.data.id,
                            name: response.data.tech_response_no
                        };
                        $scope.data.customer = response.data.contact ? response.data.contact.name : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.get_codes = function(main_category, sub_category, code) {
            if (code && code.length > 0) {
                $scope.code_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_codes',
                    params: {
                        type: 1,
                        code: code,
                        main_category: main_category && main_category.id ? main_category.id : '',
                        sub_category: sub_category && sub_category.id ? sub_category.id : ''
                    }
                }).then(function successCallback(response) {
                    $scope.code_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.code_array.push({
                            id: value.id,
                            name: value.code,
                            item_name: value.name,
                            main_category_id: value.main_category_id,
                            main_category_name: value.main_category_name,
                            sub_category_id: value.sub_category_id,
                            sub_category_name: value.sub_category_name,
                            model_no: value.model_no,
                            unit_type: value.unit_type,
                            is_serial: value.is_serial
                        });
                    });
                    $scope.find_code(main_category, sub_category, code);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_code = function(main_category, sub_category, code) {
            $http({
                method: 'GET',
                url: base_url + '/find_item_code',
                params: {
                    type: 1,
                    code: code,
                    main_category: main_category && main_category.id ? main_category.id : '',
                    sub_category: sub_category && sub_category.id ? sub_category.id : ''
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.data.code = {
                        id: response.data.id,
                        name: response.data.code
                    };
                    $scope.data.item = {
                        id: response.data.id,
                        name: response.data.name,
                        is_serial: response.data.is_serial,
                        is_warranty: response.data.is_warranty
                    };
                    $scope.data.main_category = {
                        id: response.data.main_category_id,
                        name: response.data.main_category_name
                    };
                    $scope.data.sub_category = {
                        id: response.data.sub_category_id,
                        name: response.data.sub_category_name
                    };
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.unit_type = response.data.unit_type;
                }
                $scope.data.quantity = '';
                $scope.serialGridOptions.data = [];
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_items = function(main_category, sub_category, name) {
            if (name && name.length > 0) {
                $scope.item_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_names',
                    params: {
                        type: 1,
                        name: name,
                        main_category: main_category && main_category.id ? main_category.id : '',
                        sub_category: sub_category && sub_category.id ? sub_category.id : ''
                    }
                }).then(function successCallback(response) {
                    $scope.item_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.item_array.push({
                            id: value.id,
                            name: value.name,
                            code: value.code,
                            main_category_id: value.main_category_id,
                            main_category_name: value.main_category_name,
                            sub_category_id: value.sub_category_id,
                            sub_category_name: value.sub_category_name,
                            model_no: value.model_no,
                            unit_type: value.unit_type,
                            is_serial: value.is_serial
                        });
                    });
                    $scope.find_name(main_category, sub_category, name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_name = function(main_category, sub_category, name) {
            $http({
                method: 'GET',
                url: base_url + '/find_item_name',
                params: {
                    type: 1,
                    name: name,
                    main_category: main_category && main_category.id ? main_category.id : '',
                    sub_category: sub_category && sub_category.id ? sub_category.id : ''
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.data.code = {
                        id: response.data.id,
                        name: response.data.code
                    };
                    $scope.data.item = {
                        id: response.data.id,
                        name: response.data.name,
                        is_serial: response.data.is_serial,
                        is_warranty: response.data.is_warranty
                    };
                    $scope.data.main_category = {
                        id: response.data.main_category_id,
                        name: response.data.main_category_name
                    };
                    $scope.data.sub_category = {
                        id: response.data.sub_category_id,
                        name: response.data.sub_category_name
                    };
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.unit_type = response.data.unit_type;
                }
                $scope.data.quantity = '';
                $scope.serialGridOptions.data = [];
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_serial_nos = function(serial_no) {
            if (serial_no && serial_no.length > 0) {
                $scope.serial_no_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/item_issue/get_serial_nos',
                    params: {
                        serial_no: serial_no
                    }
                }).then(function successCallback(response) {
                    $scope.serial_no_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.serial_no_array.push({
                            id: value.id,
                            name: value.serial_no
                        });
                    });
                    $scope.find_serial_no(serial_no);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_serial_no = function(serial_no) {
            $http({
                method: 'GET',
                url: base_url + '/item_issue/find_serial_no',
                params: {
                    serial_no: serial_no
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.data.serial_no = {
                        id: response.data.id,
                        name: response.data.serial_no
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.onDocumentSelect = function($item, $model, $label) {
            $scope.data.document = {
                id: $item.id,
                name: $item.name
            };
            $scope.data.customer = $item.customer;
            $timeout(function() {
                $scope.find_document($scope.data.document.name);
                $('#item_issue_date').focus();
            }, 200, false);
        };

        $scope.onCodeSelect = function($item, $model, $label) {
            $scope.data.code = {
                id: $item.id,
                name: $item.name
            };
            $scope.data.item = {
                id: $item.id,
                name: $item.item_name,
                is_serial: $item.is_serial
            };
            $scope.data.main_category = {
                id: $item.main_category_id,
                name: $item.main_category_name
            };
            $scope.data.sub_category = {
                id: $item.sub_category_id,
                name: $item.sub_category_name
            };
            $scope.data.unit_type = $item.unit_type;
            $scope.data.model_no = $item.model_no;

            $timeout(function() {
                $scope.find_code($scope.data.main_category, $scope.data.sub_category, $scope.data.code.name);
                $('#quantity').focus();
            }, 200, false);
        };

        $scope.onItemSelect = function($item, $model, $label) {
            $scope.data.code = {
                id: $item.id,
                name: $item.code
            };
            $scope.data.item = {
                id: $item.id,
                name: $item.name,
                is_serial: $item.is_serial
            };
            $scope.data.main_category = {
                id: $item.main_category_id,
                name: $item.main_category_name
            };
            $scope.data.sub_category = {
                id: $item.sub_category_id,
                name: $item.sub_category_name
            };
            $scope.data.unit_type = $item.unit_type;
            $scope.data.model_no = $item.model_no;

            $timeout(function() {
                $scope.find_name($scope.data.main_category, $scope.data.sub_category, $scope.data.item.name);
                $('#quantity').focus();
            }, 200, false);
        };

        $scope.onSerialNoSelect = function($item, $model, $label) {
            $scope.data.serial_no = {
                id: $item.id,
                name: $item.name
            };
            $timeout(function() {
                $scope.find_serial_no($scope.data.serial_no.name);
                $('#add_button').focus();
            }, 200, false);
        };

        $scope.editRecord = function(row) {
            $http({
                method: 'GET',
                url: base_url + '/item_issue/find_item_issue_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    var document = '';
                    var customer = '';
                    if (response.data.item_issue.item_issue_type_id == 1) {
                        document = response.data.item_issue.job ? {
                            id: response.data.item_issue.job.id,
                            name: response.data.item_issue.job.job_no
                        } : '';
                        customer = response.data.item_issue.job && response.data.item_issue.job.inquiry && response.data.item_issue.job.inquiry.contact ? response.data.item_issue.job.inquiry.contact.name : '';
                    } else if (response.data.item_issue.item_issue_type_id == 2) {
                        document = response.data.item_issue.tech_response ? {
                            id: response.data.item_issue.tech_response.id,
                            name: response.data.item_issue.tech_response.tech_response_no
                        } : '';
                        customer = response.data.item_issue.tech_response && response.data.item_issue.tech_response.contact ? response.data.item_issue.tech_response.contact.name : '';
                    }

                    var item_issue_date_time = response.data.item_issue.item_issue_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        item_issue_id: response.data.item_issue ? response.data.item_issue.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        item_issue_type: response.data.item_issue && response.data.item_issue.item_issue_type ? {
                            id: response.data.item_issue.item_issue_type.id,
                            name: response.data.item_issue.item_issue_type.name
                        } : '',
                        document: document,
                        customer: customer,
                        issued_to: response.data.item_issue ? response.data.item_issue.issued_to : 0,
                        item_issue_no: response.data.item_issue ? response.data.item_issue.item_issue_no : '',
                        item_issue_date: item_issue_date_time[0],
                        item_issue_time: item_issue_date_time[1],
                        remarks: response.data.item_issue ? response.data.item_issue.remarks : '',
                        is_posted: response.data.item_issue ? response.data.item_issue.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {
                            id: response.data.item.main_item_category.id,
                            name: response.data.item.main_item_category.name
                        } : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {
                            id: response.data.item.sub_item_category.id,
                            name: response.data.item.sub_item_category.name
                        } : {},
                        code: response.data.item ? {
                            id: response.data.item.id,
                            name: response.data.item.code
                        } : '',
                        item: response.data.item ? {
                            id: response.data.item.id,
                            name: response.data.item.name,
                            is_serial: response.data.item.is_serial
                        } : '',
                        model_no: response.data.item ? response.data.item.model_no : '',
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        warranty: response.data.warranty,
                        serial_no: ''
                    };

                    var data_array = [];
                    for (var i = 0; i < response.data.item_issue_breakdown.length; i++) {
                        if (response.data.item_issue_breakdown[i].type == 1) {
                            data_array.push({
                                index: i,
                                id: response.data.item_issue_breakdown[i].good_receive_breakdown ? response.data.item_issue_breakdown[i].good_receive_breakdown.id : '',
                                count: i + 1,
                                serial_no: response.data.item_issue_breakdown[i].good_receive_breakdown ? response.data.item_issue_breakdown[i].good_receive_breakdown.serial_no : ''
                            });
                        }
                    }
                    $scope.serialGridOptions.data = data_array;
                    $scope.serialGridApi.core.refresh();
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function(row) {
            $http({
                method: 'GET',
                url: base_url + '/item_issue/find_item_issue_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    var document = '';
                    var customer = '';
                    if (response.data.item_issue.item_issue_type_id == 1) {
                        document = response.data.item_issue.job ? {
                            id: response.data.item_issue.job.id,
                            name: response.data.item_issue.job.job_no
                        } : '';
                        customer = response.data.item_issue.job && response.data.item_issue.job.inquiry && response.data.item_issue.job.inquiry.contact ? response.data.item_issue.job.inquiry.contact.name : '';
                    } else if (response.data.item_issue.item_issue_type_id == 2) {
                        document = response.data.item_issue.tech_response ? {
                            id: response.data.item_issue.tech_response.id,
                            name: response.data.item_issue.tech_response.tech_response_no
                        } : '';
                        customer = response.data.item_issue.tech_response && response.data.item_issue.tech_response.contact ? response.data.item_issue.tech_response.contact.name : '';
                    }

                    var item_issue_date_time = response.data.item_issue.item_issue_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        item_issue_id: response.data.item_issue ? response.data.item_issue.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        item_issue_type: response.data.item_issue && response.data.item_issue.item_issue_type ? {
                            id: response.data.item_issue.item_issue_type.id,
                            name: response.data.item_issue.item_issue_type.name
                        } : '',
                        document: document,
                        customer: customer,
                        issued_to: response.data.item_issue ? response.data.item_issue.issued_to : 0,
                        item_issue_no: response.data.item_issue ? response.data.item_issue.item_issue_no : '',
                        item_issue_date: item_issue_date_time[0],
                        item_issue_time: item_issue_date_time[1],
                        remarks: response.data.item_issue ? response.data.item_issue.remarks : '',
                        is_posted: response.data.item_issue ? response.data.item_issue.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {
                            id: response.data.item.main_item_category.id,
                            name: response.data.item.main_item_category.name
                        } : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {
                            id: response.data.item.sub_item_category.id,
                            name: response.data.item.sub_item_category.name
                        } : {},
                        code: response.data.item ? {
                            id: response.data.item.id,
                            name: response.data.item.code
                        } : '',
                        item: response.data.item ? {
                            id: response.data.item.id,
                            name: response.data.item.name,
                            is_serial: response.data.item.is_serial
                        } : '',
                        model_no: response.data.item ? response.data.item.model_no : '',
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        warranty: response.data.warranty,
                        serial_no: ''
                    };

                    var data_array = [];
                    for (var i = 0; i < response.data.item_issue_breakdown.length; i++) {
                        if (response.data.item_issue_breakdown[i].type == 1) {
                            data_array.push({
                                index: i,
                                id: response.data.item_issue_breakdown[i].good_receive_breakdown ? response.data.item_issue_breakdown[i].good_receive_breakdown.id : '',
                                count: i + 1,
                                serial_no: response.data.item_issue_breakdown[i].good_receive_breakdown ? response.data.item_issue_breakdown[i].good_receive_breakdown.serial_no : ''
                            });
                        }
                    }
                    $scope.serialGridOptions.data = data_array;
                    $scope.serialGridApi.core.refresh();

                    swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover <strong>" + response.data.item.name + "</strong> item details!",
                            html: true,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#FF0000",
                            confirmButtonText: "Yes, delete it!",
                            closeOnConfirm: false
                        },
                        function() {
                            $http.delete(base_url + '/item_issue/' + $scope.data.id, {
                                params: {
                                    type: $scope.data.type
                                }
                            }).success(function(response_delete) {
                                swal({
                                    title: "Deleted!",
                                    text: response.data.item.name + " item details has been deleted.",
                                    type: "success",
                                    confirmButtonColor: "#9ACD32"
                                });
                                $scope.refreshForm();
                                $scope.main_refresh();
                            });
                        });
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.addSerialNos = function() {
            if ($scope.data.serial_no && $scope.data.serial_no.id) {
                $timeout(function() {
                    $scope.serialGridOptions.data.push({
                        index: $scope.serialGridOptions.data.length,
                        id: $scope.data.serial_no ? $scope.data.serial_no.id : '',
                        count: $scope.serialGridOptions.data.length + 1,
                        serial_no: $scope.data.serial_no ? $scope.data.serial_no.name : ''
                    });

                    $scope.data.serial_no = '';
                    $scope.data.quantity = $scope.serialGridOptions.data.length;
                    $scope.serialGridApi.core.refresh();
                    $('#serial_no').focus();
                }, 100, false);

                $(".message_lable").remove();
                $('.form-control').removeClass("error");
                $('.form-control').removeClass("valid");
            }
        };

        $scope.deleteSerialRecord = function(row) {
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + row.entity.serial_no + "</strong> serial no!",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function() {
                    $timeout(function() {
                        $scope.serialGridOptions.data.splice(row.entity.index, 1);
                        $scope.data.quantity = row.entity.is_main == 1 ? Number($scope.data.quantity) - 1 : Number($scope.data.quantity) - 0;
                        for (var i = 0; i < $scope.serialGridOptions.data.length; i++) {
                            $scope.serialGridOptions.data[i].index = i;
                        }
                        $scope.data.quantity = $scope.serialGridOptions.data.length;
                        $scope.serialGridApi.core.refresh();
                    }, 100, false);
                    swal({
                        title: "Deleted!",
                        text: row.entity.serial_no + " serial no has been deleted.",
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                });
        };

        $scope.resetForm = function() {
            $scope.data.item_issue_id = '';
            $scope.refreshForm();
            $scope.main_refresh();

            $scope.error = false;
            $scope.cleanBulkGridData();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function() {
            if ($scope.data.item && $scope.data.item.is_serial == 1) {
                $scope.data.quantity = $scope.serialGridOptions.data.length;
            }
            $timeout(function() {
                $('#dataForm').submit();
            }, 100, false);
        };

        $scope.postForm = function() {
            swal({
                    title: "Are you sure?",
                    text: "Item Issue No : <strong>" + $scope.data.item_issue_no + "</strong>",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, posted it!",
                    closeOnConfirm: true
                },
                function() {
                    document.getElementById('data_load').style.visibility = "visible";
                    $('#post_button').prop('disabled', true);
                    $http({
                        method: 'GET',
                        url: base_url + '/item_issue/post_item_issue',
                        params: {
                            id: $scope.data.item_issue_id
                        }
                    }).then(function successCallback(result) {
                        $http({
                            method: 'GET',
                            url: base_url + '/report/stock_update'
                        }).then(function successCallback(response) {
                            document.getElementById('data_load').style.visibility = "hidden";
                            $('#post_button').prop('disabled', false);
                            if (result.data.response) {
                                swal({
                                    title: "Posted!",
                                    text: "Item Issue No : " + $scope.data.item_issue_no,
                                    html: true,
                                    type: "success",
                                    confirmButtonColor: "#9ACD32"
                                });
                            } else {
                                swal({
                                    title: "Post Failed!",
                                    text: result.data.message,
                                    html: true,
                                    type: "error",
                                    confirmButtonColor: "#FF0000"
                                });
                            }
                            $scope.refreshForm();
                            $scope.main_refresh();
                        });
                    });
                });
        };

        submitForm = function() {
            $('#save_button').prop('disabled', true);
            var valid = true;

            if ($scope.data.item && $scope.data.item.is_serial == 1 && $scope.serialGridOptions.data.length == 0) {
                valid = false;
            }

            if (valid) {
                $scope.data.serial_details = $scope.serialGridOptions.data;
                if ($scope.data.id == 0) {
                    $http.post(base_url + '/item_issue', $scope.data).success(function(result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Item Issue Details',
                            text: result.message,
                            type: result.response ? 'success' : 'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });

                        $scope.data.item_issue_id = result.response ? result.data : 0;
                        $scope.refreshForm();
                        $scope.main_refresh();
                    });
                } else {
                    $http.put(base_url + '/item_issue/' + $scope.data.id, $scope.data).success(function(result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Item Issue Details',
                            text: result.message,
                            type: result.response ? 'success' : 'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });

                        $scope.data.item_issue_id = result.response ? result.data : 0;
                        $scope.refreshForm();
                        $scope.main_refresh();
                    });
                }
            } else {
                $('#save_button').prop('disabled', false);
                $.pnotify && $.pnotify({
                    title: 'Item Issue Details',
                    text: 'Serial Nos required',
                    type: 'error',
                    nonblock: true,
                    history: false,
                    delay: 6e3,
                    hide: true
                });
            }
        };

        $scope.main_refresh = function() {
            $('#span_error').show();
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.data.item_issue_no + ' Item Issue Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/item_issue/item_issue_detail_list',
                params: {
                    id: $scope.data.item_issue_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.item_issue_details, function(index, value) {
                    var total_value = 0;
                    for (var i = 0; i < value.item_issue_breakdown.length; i++) {
                        total_value += value.item_issue_breakdown[i].type == 1 ? Number(value.item_issue_breakdown[i].good_receive_breakdown.good_receive_details.rate) * Number(value.item_issue_breakdown[i].quantity) : Number(value.item_issue_breakdown[i].good_receive_details.rate) * Number(value.item_issue_breakdown[i].quantity);
                    }
                    var rate = Number(total_value) / Number(value.quantity);
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_posted: value.item_issue ? value.item_issue.is_posted : 0,
                        item_code: value.item ? value.item.code : '',
                        item_name: value.item ? value.item.name : '',
                        unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                        rate: parseFloat(Math.round(rate * 100) / 100).toFixed(2),
                        quantity: value.quantity,
                        value: parseFloat(Math.round(total_value * 100) / 100).toFixed(2),
                        warranty: value.warranty
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

        $.validator.addMethod('serialNoExist', function(value, element, param) {
            var exist = false;
            for (var i = 0; i < $scope.serialGridOptions.data.length; i++) {
                if ($scope.serialGridOptions.data[i].serial_no == value) {
                    exist = true;
                }
            }
            return !exist;
        });
    }]);
</script>
@endsection