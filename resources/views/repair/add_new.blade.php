@extends('layouts.main')

@section('title')
<title>M3Force | New Repair</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Repair</a></li>
    <li class="active">New Repair</li>
</ul>
@endsection

@section('content')
<style>
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
                    <h3 class="panel-title"><strong>New Inquiry</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button" ng-disabled="edit_disable">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('repair')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Repair No</label>
                                            <input type="text" id="repair_no" name="repair_no" ng-model="data.repair_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Date</label>
                                            <input type="text" id="repair_date" name="repair_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.repair_date" ng-disabled="edit_disable" is-open="repairDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenRepairDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Time</label>
                                            <input type="text" id="repair_time" name="repair_time" ng-model="data.repair_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Repair Type</label>
                                            <select name="repair_type" id="repair_type" ng-options="option.name for option in repair_type_array track by option.id" ng-model="data.repair_type" ng-disabled="edit_disable" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Received From</label>
                                            <input type="text" id="received_from" name="received_from" ng-model="data.received_from" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row" ng-show="data.repair_type.id == 1 || data.repair_type.id == 2">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Customer Details</h4>
                                </div>

                                <div class="row" ng-show="data.repair_type.id == 1 || data.repair_type.id == 2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Document No</label>
                                            <input type="text" id="document_no" name="document_no" ui-grid-edit-auto ng-model="data.document_no" ng-disabled="edit_disable" typeahead="name as document_no_array.name for document_no_array in document_no_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onDocumentNoSelect($item, $model, $label)" ng-keyup="get_document_nos(data.repair_type.id, data.document_no)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" ng-model="data.customer_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>

                                <div class="row" ng-show="data.repair_type.id == 1 || data.repair_type.id == 2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Customer Contact No</label>
                                            <input type="text" id="customer_contact_no" name="customer_contact_no" ng-model="data.customer_contact_no" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Customer Address</label>
                                            <input type="text" id="customer_address" name="customer_address" ng-model="data.customer_address" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Item Details</h4>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ui-grid-edit-auto ng-model="data.code" ng-disabled="edit_disable" typeahead="name as code_array.name for code_array in code_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onCodeSelect($item, $model, $label)" ng-keyup="get_codes(data.code)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Item</label>
                                            <input type="text" id="item" name="item" ui-grid-edit-auto ng-model="data.item" ng-disabled="edit_disable" typeahead="name as item_array.name for item_array in item_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onItemSelect($item, $model, $label)" ng-keyup="get_items(data.item)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Model No</label>
                                            <input type="text" id="model_no" name="model_no" ng-model="data.model_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Brand</label>
                                            <input type="text" id="brand" name="brand" ng-model="data.brand" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Serial No</label>
                                            <input type="text" id="serial_no" name="serial_no" ng-model="data.serial_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<script type="text/javascript">
    var submitForm;

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

    myApp.directive('jValidate', function() {
        return {
            link: function(scope, element, attr) {
                element.validate({
                    rules: {
                        repair_date: {
                            required: true,
                            date: true
                        },
                        repair_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        repair_type: {
                            required: true
                        },
                        received_from: {
                            required: true
                        },
                        document_no: {
                            required: true,
                            remote: {
                                url: base_url + '/item_issue/validate_document_no',
                                type: 'GET',
                                data: {
                                    item_issue_type: function() {
                                        return scope.data.repair_type && scope.data.repair_type.id ? scope.data.repair_type.id : '';
                                    },
                                    document: function() {
                                        return scope.data.document_no && scope.data.document_no.name ? scope.data.document_no.name : scope.data.document_no;
                                    }
                                }
                            }
                        },
                        code: {
                            required: true,
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
                            required: true,
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
                        repair_date: {
                            required: 'Date is required',
                            date: 'Invalid date format'
                        },
                        repair_time: {
                            required: 'Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        repair_type: {
                            required: 'Repair Type is required'
                        },
                        received_from: {
                            required: 'Received From is required'
                        },
                        document_no: {
                            required: 'Document No is required',
                            remote: 'Invalid Document No'
                        },
                        code: {
                            required: 'Code is required',
                            remote: 'Invalid Code'
                        },
                        item: {
                            required: 'Item is required',
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
        $scope.repair_type_array = [];

        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        $scope.repairDatePopup = {
            opened: false
        };
        $scope.OpenRepairDate = function() {
            $scope.repairDatePopup.opened = !$scope.repairDatePopup.opened;
        };

        $('#repair_time').mask('00:00');

        $scope.data.id = <?php echo $repair_id ? $repair_id : 0; ?>;

        $scope.refreshForm = function() {
            $http({
                method: 'GET',
                url: base_url + '/repair/get_data'
            }).then(function successCallback(response) {
                var repair_type_array = [];
                repair_type_array.push({
                    id: '',
                    name: 'Select Repair Type'
                });
                $.each(response.data.repair_types, function(index, value) {
                    repair_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });

                $scope.repair_type_array = repair_type_array;

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
                    type: 0,
                    id: $scope.data.id,
                    repair_no: '',
                    repair_date: new Date(),
                    repair_time: hh + ':' + mm,
                    repair_type: $scope.repair_type_array.length > 0 ? $scope.repair_type_array[0] : {},
                    received_from: '',
                    document_no: '',
                    customer_name: '',
                    customer_contact_no: '',
                    customer_address: '',
                    code: '',
                    item: '',
                    model_no: '',
                    brand: '',
                    serial_no: '',
                    remarks: ''
                };
                $http({
                    method: 'GET',
                    url: base_url + '/repair/find_repair',
                    params: {
                        id: $scope.data.id
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        var date_time = response.data.repair_date_time.split(' ');
                        var document_no = '';
                        var customer_name = '';
                        var customer_contact_no = '';
                        var customer_address = '';
                        if (response.data.repair_type && response.data.repair_type.id == 1 && response.data.job && response.data.job.inquiry && response.data.job.inquiry.contact) {
                            document_no = response.data.job.job_no;
                            customer_name = response.data.job.inquiry.contact.name;
                            customer_contact_no = response.data.job.inquiry.contact.contact_no;
                            customer_address = response.data.job.inquiry.contact.address;
                        } else if (response.data.repair_type && response.data.repair_type.id == 2 && response.data.tech_response && response.data.tech_response.contact) {
                            document_no = response.data.tech_response.tech_response_no;
                            customer_name = response.data.tech_response.contact.name;
                            customer_contact_no = response.data.tech_response.contact.contact_no;
                            customer_address = response.data.tech_response.contact.address;
                        }
                        $scope.data = {
                            type: 0,
                            id: response.data.id,
                            repair_no: response.data.repair_no,
                            repair_date: date_time[0],
                            repair_time: date_time[1],
                            repair_type: response.data.repair_type ? {
                                id: response.data.repair_type.id,
                                name: response.data.repair_type.name
                            } : {},
                            received_from: response.data.received_from,
                            document_no: document_no,
                            customer_name: customer_name,
                            customer_contact_no: customer_contact_no,
                            customer_address: customer_address,
                            code: response.data.item ? {
                                id: response.data.item.id,
                                name: response.data.item.code
                            } : {},
                            item: response.data.item ? {
                                id: response.data.item.id,
                                name: response.data.item.name
                            } : {},
                            model_no: response.data.model_no,
                            brand: response.data.brand,
                            serial_no: response.data.serial_no,
                            remarks: response.data.remarks
                        };
                        $scope.edit_disable = response.data.is_completed == 1 ? true : false;
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function() {
            $scope.refreshForm();
        }, 1500, false);

        $scope.get_document_nos = function(repair_type_id, document_no) {
            if (document_no && document_no.length > 0) {
                $scope.document_array = [];
                if (repair_type_id == 1) {
                    $http({
                        method: 'GET',
                        url: base_url + '/repair/get_job_nos',
                        params: {
                            job_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];
                        $.each(response.data, function(index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.job_no,
                                customer_name: value.inquiry && value.inquiry.contact ? value.inquiry.contact.name : '',
                                customer_contact_no: value.inquiry && value.inquiry.contact ? value.inquiry.contact.contact_no : '',
                                customer_address: value.inquiry && value.inquiry.contact ? value.inquiry.contact.address : ''
                            });
                        });
                        $scope.find_document(repair_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                } else if (repair_type_id == 2) {
                    $http({
                        method: 'GET',
                        url: base_url + '/repair/get_tech_response_nos',
                        params: {
                            tech_response_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];
                        $.each(response.data, function(index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.tech_response_no,
                                customer_name: value.contact ? value.contact.name : '',
                                customer_contact_no: value.contact ? value.contact.contact_no : '',
                                customer_address: value.contact ? value.contact.address : ''
                            });
                        });
                        $scope.find_document(repair_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
            }
        };

        $scope.find_document = function(repair_type_id, document_no) {
            if (repair_type_id == 1) {
                $http({
                    method: 'GET',
                    url: base_url + '/repair/find_job_no',
                    params: {
                        job_no: document_no
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        $scope.data.document_no = {
                            id: response.data.id,
                            name: response.data.job_no
                        };
                        $scope.data.customer_name = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.name : '';
                        $scope.data.customer_contact_no = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.contact_no : '';
                        $scope.data.customer_address = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.address : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            } else if (repair_type_id == 2) {
                $http({
                    method: 'GET',
                    url: base_url + '/repair/find_tech_response_no',
                    params: {
                        tech_response_no: document_no
                    }
                }).then(function successCallback(response) {
                    if (response.data) {
                        $scope.data.document_no = {
                            id: response.data.id,
                            name: response.data.tech_response_no
                        };
                        $scope.data.customer_name = response.data.contact ? response.data.contact.name : '';
                        $scope.data.customer_contact_no = response.data.contact ? response.data.contact.contact_no : '';
                        $scope.data.customer_address = response.data.contact ? response.data.contact.address : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.onDocumentSelect = function($item, $model, $label) {
            $scope.data.document_no = {
                id: $item.id,
                name: $item.name
            };
            $scope.data.customer_name = $item.customer_name;
            $scope.data.customer_contact = $item.customer_contact;
            $scope.data.customer_address = $item.customer_address;
            $timeout(function() {
                $scope.find_document($scope.data.repair_type.id, $scope.data.document.name);
                $('#code').focus();
            }, 200, false);
        };

        $scope.get_codes = function(code) {
            if (code && code.length > 0) {
                $scope.code_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/repair/get_item_codes',
                    params: {
                        code: code
                    }
                }).then(function successCallback(response) {
                    $scope.code_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.code_array.push({
                            id: value.id,
                            name: value.code,
                            item_name: value.name,
                            model_no: value.model_no,
                            brand: value.brand
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
                url: base_url + '/repair/find_item_code',
                params: {
                    code: code
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
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.brand = response.data.brand;
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
                    url: base_url + '/repair/get_item_names',
                    params: {
                        name: name
                    }
                }).then(function successCallback(response) {
                    $scope.item_array = [];
                    $.each(response.data, function(index, value) {
                        $scope.item_array.push({
                            id: value.id,
                            name: value.name,
                            code: value.code,
                            model_no: value.model_no,
                            brand: value.brand
                        });
                    });
                    $scope.find_name(name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_name = function(main_category, sub_category, name) {
            $http({
                method: 'GET',
                url: base_url + '/repair/find_item_name',
                params: {
                    name: name
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
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.brand = response.data.brand;
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
            $scope.data.model_no = $item.model_no;
            $scope.data.brand = $item.brand;

            $timeout(function() {
                $('#model_no').focus();
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
            $scope.data.model_no = $item.model_no;
            $scope.data.brand = $item.brand;

            $timeout(function() {
                $('#model_no').focus();
            }, 200, false);
        };

        $scope.resetForm = function() {
            $scope.data.id = 0;
            $scope.edit_disable = false;

            $scope.refreshForm();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function() {
            $('#dataForm').submit();
        };

        submitForm = function() {
            $('#save_button').prop('disabled', true);
            if ($scope.data.id == 0) {
                $http.post(base_url + '/repair', $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'New Repair',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $timeout(function() {
                        $window.location.href = base_url + '/repair';
                    }, 1500, false);
                });
            } else {
                $http.put(base_url + '/repair/' + $scope.data.id, $scope.data).success(function(result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'New Repair',
                        text: result.message,
                        type: result.response ? 'success' : 'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $timeout(function() {
                        $window.location.href = base_url + '/repair';
                    }, 1500, false);
                });
            }
        };

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection