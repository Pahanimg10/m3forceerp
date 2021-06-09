@extends('layouts.main')

@section('title')
<title>M3Force | Good Receive Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Stock</a></li>                    
    <li class="active">Good Receive Details</li>
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
    .form-horizontal .form-group{
        margin-left: 0;
        margin-right: 0;
    }
    .control-label{
        padding: 15px 0 5px 0;
    }
    input[type="text"]:read-only{
        color: #1CB09A;
    }
    input[type="text"]:disabled{
        color: #1CB09A;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Good Receive Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.good_receive_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><div><a ng-show="data.good_receive_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/good_receive/print_good_receive?id=<%=data.good_receive_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('good_receive')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="data_value" name="data_value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="good_receive_id" name="good_receive_id" ng-model="data.good_receive_id" class="form-control" />
                                <input type="hidden" id="item_code" name="item_code" ng-model="data.item_code" class="form-control" />
                                <input type="hidden" id="item_name" name="item_name" ng-model="data.item_name" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Purchase Order No</label>
                                            <input type="text" id="purchase_order" name="purchase_order" ui-grid-edit-auto ng-model="data.purchase_order" ng-disabled="edit_disable" typeahead="name as purchase_order_array.name for purchase_order_array in purchase_order_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onPurchaseOrderSelect($item, $model, $label)" ng-keyup="get_purchase_orders(data.purchase_order)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Invoice No</label>
                                            <input type="text" id="invoice_no" name="invoice_no" ng-model="data.invoice_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Supplier</label>
                                            <input type="text" id="supplier" name="supplier" ng-model="data.supplier" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Good Receive Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Receive No</label>
                                            <input type="text" id="good_receive_no" name="good_receive_no" ng-model="data.good_receive_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Receive Date</label>
                                            <input type="text" id="good_receive_date" name="good_receive_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.good_receive_date" ng-disabled="edit_disable" is-open="goodReceiveDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenGoodReceiveDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Receive Time</label>
                                            <input type="text" id="good_receive_time" name="good_receive_time" ng-model="data.good_receive_time" ng-disabled="edit_disable" class="form-control text-center" />
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
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Main Category</label>
                                            <select name="main_category" id="main_category" ng-options="option.name for option in main_category_array track by option.id" ng-model="data.main_category" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Sub Category</label>
                                            <select name="sub_category" id="sub_category" ng-options="option.name for option in sub_category_array track by option.id" ng-model="data.sub_category" ng-disabled="edit_disable" class="form-control" ></select>
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
                                            <input type="text" id="model_no" name="model_no" ng-model="data.model_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Brand</label>
                                            <input type="text" id="brand" name="brand" ng-model="data.brand" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Origin</label>
                                            <input type="text" id="origin" name="origin" ng-model="data.origin" ng-disabled="edit_disable" class="form-control" />
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
                                            <label class="control-label">Rate</label>
                                            <input type="text" id="rate" name="rate" ng-model="data.rate" ng-disabled="edit_disable" class="form-control text-right" />
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
                                            <label class="control-label">Location</label>
                                            <input type="text" id="location" name="location" ng-model="data.location" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2" ng-show="data.item && data.item.is_warranty == 1">
                                        <div class="form-group">
                                            <label class="control-label">Warranty Year</label>
                                            <input type="text" id="warranty" name="warranty" ng-model="data.warranty" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.item && data.item.is_serial == 1">
                                        <div class="form-group">
                                            <label class="control-label">Main</label><br/>
                                            <input id="is_main" bs-switch emit-change="is_main" ng-model="data.is_main" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6" ng-show="data.item && data.item.is_serial == 1">
                                        <div class="form-group">
                                            <label class="control-label">Serial No</label>
                                            <input type="text" id="serial_no" name="serial_no" ng-model="data.serial_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center" ng-show="data.item && data.item.is_serial == 1">
                                        <div class="form-group">
                                            <button type="button" ng-click="addSerialNos()" ng-disabled="edit_disable" class="btn btn-info" style="margin-top: 30px;" >Add</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" ng-show="serialGridOptions.data.length > 0">
                                    <div class="col-md-12" style="margin-top: 20px;">
                                        <div ui-grid="serialGridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
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

    </div> 
</div> 

<script type="text/javascript">
    var submitForm; 
    
    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
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
        angular.element(document.querySelector('#main_menu_stock')).addClass('active');
        angular.element(document.querySelector('#sub_menu_good_receive')).addClass('active');
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
                        
                        $scope.$on(uiGridEditConstants.events.BEGIN_CELL_EDIT, function () {
                            $elm.focus();
                            $scope[$attrs.focusMe] = true;
                            
                            if (uiGridCtrl.grid.api.cellNav) {
                                uiGridCtrl.grid.api.cellNav.on.navigate($scope, function (newRowCol, oldRowCol) {
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
                                if(evt.keyCode === uiGridConstants.keymap.TAB){
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
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        purchase_order: {
                            required: true,
                            remote: {
                                url: base_url + '/good_receive/validate_purchase_order_no',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    purchase_order_no: function() {
                                      return scope.data.purchase_order && scope.data.purchase_order.name ? scope.data.purchase_order.name : scope.data.purchase_order;
                                    }
                                }
                            }
                        },
                        invoice_no: {
                            required: true
                        },
                        good_receive_date: {
                            required: true,
                            date: true
                        },
                        good_receive_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        code: {
                            remote: {
                                url: base_url + '/good_receive/validate_item_code',
                                type: 'GET',
                                data: {
                                    good_receive_id: function() {
                                      return scope.data.good_receive_id;
                                    },
                                    item_code: function() {
                                      return scope.data.item_code;
                                    },
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order && scope.data.purchase_order.id ? scope.data.purchase_order.id : '';
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
                                url: base_url + '/good_receive/validate_item_name',
                                type: 'GET',
                                data: {
                                    good_receive_id: function() {
                                      return scope.data.good_receive_id;
                                    },
                                    item_name: function() {
                                      return scope.data.item_name;
                                    },
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order && scope.data.purchase_order.id ? scope.data.purchase_order.id : '';
                                    },
                                    item_id: function() {
                                      return scope.data.item && scope.data.item.id ? scope.data.item.id : '';
                                    },
                                    name: function() {
                                      return scope.data.item && scope.data.item.name ? scope.data.item.name : scope.data.item;
                                    }
                                }
                            }
                        },
                        rate: {
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            remote: {
                                url: base_url + '/good_receive/validate_item_rate',
                                type: 'GET',
                                data: {
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order && scope.data.purchase_order.id ? scope.data.purchase_order.id : '';
                                    },
                                    item_id: function() {
                                      return scope.data.item && scope.data.item.id ? scope.data.item.id : '';
                                    },
                                    rate: function() {
                                      return scope.data.rate;
                                    }
                                }
                            },
                            number: true,
                            min: 0
                        }, 
                        quantity: {
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            remote: {
                                url: base_url + '/good_receive/validate_item_quantity',
                                type: 'GET',
                                data: {
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order && scope.data.purchase_order.id ? scope.data.purchase_order.id : '';
                                    },
                                    item_id: function() {
                                      return scope.data.item && scope.data.item.id ? scope.data.item.id : '';
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
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        purchase_order: {
                            required: 'Purchase Order No is required',
                            remote: 'Invalid Purchase Order No'
                        },
                        invoice_no: {
                            required: 'Invoice No is required'
                        },
                        good_receive_date: {
                            required: 'Good Receive Date is required',
                            date: 'Invalid date format'
                        },
                        good_receive_time: {
                            required: 'Good Receive Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        code: {
                            remote: 'Invalid Code or Item error'
                        },
                        item: {
                            remote: 'Invalid Item or Item error'
                        },
                        rate: {
                            required: 'Rate is required',
                            remote: 'Rate exceeded',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
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
                    submitHandler: function (form) {
                        submitForm();
                    },
                    invalidHandler: function (event, validator) {
                        //
                    }

                });

                scope.$on('$destroy', function () {
                    // Perform cleanup.
                    // (Not familiar with the plugin so don't know what should to be 
                });
            }
        }
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.base_url = base_url;
        $scope.edit_disable = false;
        $scope.permission = false;
            
        $scope.data = [];
        $scope.purchase_order_array = [];
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.code_array = [];
        $scope.item_array = [];
        
        $scope.data.good_receive_id = <?php echo $good_receive_id ? $good_receive_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.goodReceiveDatePopup = {
            opened: false
        };        
        $scope.OpenGoodReceiveDate = function () {
            $scope.goodReceiveDatePopup.opened = !$scope.goodReceiveDatePopup.opened;
        };
        
        $('#good_receive_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/item/get_data'
            }).then(function successCallback(response) {
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
                $.each(response.data.main_item_categories, function (index, value) {
                    main_category_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                $.each(response.data.sub_item_categories, function (index, value) {
                    sub_category_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.main_category_array = main_category_array;
                $scope.sub_category_array = sub_category_array;
                
                var today = new Date();
                var hh = today.getHours();
                var mm = today.getMinutes();
                if(hh<10){
                    hh='0'+hh;
                }
                if(mm<10){
                    mm='0'+mm;
                }
                
                $scope.data = {
                    type: 1,
                    id: 0,
                    value: '',
                    good_receive_id: $scope.data.good_receive_id,
                    item_code: '',
                    item_name: '',
                    purchase_order: '',
                    invoice_no: '',
                    supplier: '',
                    good_receive_no: '',
                    good_receive_date: new Date(),
                    good_receive_time: hh+':'+mm,
                    remarks: '',
                    is_posted: 0,
                    main_category: $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {},
                    sub_category: $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {},
                    code: '',
                    item: '',
                    model_no: '',
                    brand: '',
                    origin: '',
                    unit_type: '',
                    rate: '',
                    quantity: '',
                    location: '',
                    warranty: 0,
                    is_main: true,
                    serial_no: ''
                };
                $scope.serialGridOptions.data = [];

                $http({
                    method: 'GET',
                    url: base_url + '/good_receive/find_good_receive',
                    params: {
                        id: $scope.data.good_receive_id
                    }
                }).then(function successCallback(response) {
                    if(response.data.good_receive){
                        var good_receive_date_time = response.data.good_receive.good_receive_date_time.split(' ');
                        $scope.data.value = response.data.good_receive.purchase_order ? response.data.good_receive.purchase_order.purchase_order_no : '';
                        $scope.data.good_receive_id = response.data.good_receive.id;
                        $scope.data.purchase_order = response.data.good_receive.purchase_order ? {id: response.data.good_receive.purchase_order.id, name: response.data.good_receive.purchase_order.purchase_order_no} : '';
                        $scope.data.invoice_no = response.data.good_receive.invoice_no;
                        $scope.data.supplier = response.data.good_receive.purchase_order && response.data.good_receive.purchase_order.contact ? response.data.good_receive.purchase_order.contact.name : '';
                        $scope.data.good_receive_no = response.data.good_receive.good_receive_no;
                        $scope.data.good_receive_date = good_receive_date_time[0];
                        $scope.data.good_receive_time = good_receive_date_time[1];
                        $scope.data.remarks = response.data.good_receive.remarks;
                        $scope.data.is_posted = response.data.good_receive.is_posted;
                        $scope.edit_disable = response.data.good_receive.is_posted == 1 ? true : false;
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
        
        $scope.resetItem = function(){
            $scope.data.main_category = $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {};
            $scope.data.sub_category = $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {};
            $scope.data.code = '';
            $scope.data.item = '';
            $scope.data.model_no = '';
            $scope.data.brand = '';
            $scope.data.origin = '';
            $scope.data.unit_type = '';
            $scope.data.rate = '';
            $scope.data.quantity = '';
            $scope.data.location = '';
            $scope.data.warranty = 0;
            $scope.data.is_main = true;
            $scope.data.serial_no = '';
            
            $scope.serialGridOptions.data = [];
        };
        
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
        
        $scope.serialGridOptions = {
            columnDefs: [
                {
                    field: 'index', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'is_main', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'main_id', 
                    displayName: 'Main ID', 
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
            onRegisterApi: function (gridApi) {
                $scope.serialGridApi = gridApi;
            }
        };

        $scope.export = function () {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function () {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
            
            $scope.serialGridOptions.enableFiltering = !$scope.serialGridOptions.enableFiltering;
            $scope.serialGridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };
        
        $scope.getAggregationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.get_purchase_orders = function(purchase_order_no){  
            if(purchase_order_no && purchase_order_no.length > 0){
                $scope.purchase_order_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_purchase_order_nos',
                    params:{
                        purchase_order_no: purchase_order_no
                    }
                }).then(function successCallback(response) {
                    $scope.purchase_order_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.purchase_order_array.push({
                            id: value.id,
                            name: value.purchase_order_no,
                            supplier: value.contact ? value.contact.name : ''
                        });
                    });
                    $scope.find_purchase_order(purchase_order_no);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_purchase_order = function(purchase_order_no){  
            $http({
                method: 'GET',
                url: base_url + '/find_purchase_order_no',
                params:{
                    purchase_order_no: purchase_order_no
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.purchase_order = {id: response.data.id, name: response.data.purchase_order_no};
                    $scope.data.supplier = response.data.contact ? response.data.contact.name : '';
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_codes = function(main_category, sub_category, code){  
            if(code && code.length > 0){
                $scope.code_array = []; 
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_codes',
                    params:{
                        type: 1,
                        code: code,
                        main_category: main_category && main_category.id ? main_category.id : '',
                        sub_category: sub_category && sub_category.id ? sub_category.id : ''
                    }
                }).then(function successCallback(response) {
                    $scope.code_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.code_array.push({
                            id: value.id,
                            name: value.code,
                            item_name: value.name,
                            main_category_id: value.main_category_id,
                            main_category_name: value.main_category_name,
                            sub_category_id: value.sub_category_id,
                            sub_category_name: value.sub_category_name,
                            unit_type: value.unit_type,
                            rate: value.rate,
                            model_no: value.model_no,
                            brand: value.brand,
                            origin: value.origin,
                            is_serial: value.is_serial,
                            is_warranty: value.is_warranty
                        });
                    });
                    $scope.find_code(main_category, sub_category, code);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_code = function(main_category, sub_category, code){  
            $http({
                method: 'GET',
                url: base_url + '/find_item_code',
                params:{
                    type: 1,
                    code: code,
                    main_category: main_category && main_category.id ? main_category.id : '',
                    sub_category: sub_category && sub_category.id ? sub_category.id : ''
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.code = {id: response.data.id, name: response.data.code};
                    $scope.data.item = {id: response.data.id, name: response.data.name, is_serial: response.data.is_serial, is_warranty: response.data.is_warranty};
                    $scope.data.main_category = {id: response.data.main_category_id, name: response.data.main_category_name};
                    $scope.data.sub_category = {id: response.data.sub_category_id, name: response.data.sub_category_name};
                    $scope.data.unit_type = response.data.unit_type;
                    $scope.data.rate = parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2);
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.brand = response.data.brand;
                    $scope.data.origin = response.data.origin;
                }
                $scope.data.quantity = ''; 
                $scope.serialGridOptions.data = [];
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_items = function(main_category, sub_category, name){  
            if(name && name.length > 0){
                $scope.item_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_names',
                    params:{
                        type: 1,
                        name: name,
                        main_category: main_category && main_category.id ? main_category.id : '',
                        sub_category: sub_category && sub_category.id ? sub_category.id : ''
                    }
                }).then(function successCallback(response) {
                    $scope.item_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.item_array.push({
                            id: value.id,
                            name: value.name,
                            code: value.code,
                            main_category_id: value.main_category_id,
                            main_category_name: value.main_category_name,
                            sub_category_id: value.sub_category_id,
                            sub_category_name: value.sub_category_name,
                            unit_type: value.unit_type,
                            rate: value.rate,
                            model_no: value.model_no,
                            brand: value.brand,
                            origin: value.origin,
                            is_serial: value.is_serial,
                            is_warranty: value.is_warranty
                        });
                    });       
                    $scope.find_name(main_category, sub_category, name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_name = function(main_category, sub_category, name){  
            $http({
                method: 'GET',
                url: base_url + '/find_item_name',
                params:{
                    type: 1,
                    name: name,
                    main_category: main_category && main_category.id ? main_category.id : '',
                    sub_category: sub_category && sub_category.id ? sub_category.id : ''
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.code = {id: response.data.id, name: response.data.code};
                    $scope.data.item = {id: response.data.id, name: response.data.name, is_serial: response.data.is_serial, is_warranty: response.data.is_warranty};
                    $scope.data.main_category = {id: response.data.main_category_id, name: response.data.main_category_name};
                    $scope.data.sub_category = {id: response.data.sub_category_id, name: response.data.sub_category_name};
                    $scope.data.unit_type = response.data.unit_type;
                    $scope.data.rate = parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2);
                    $scope.data.model_no = response.data.model_no;
                    $scope.data.brand = response.data.brand;
                    $scope.data.origin = response.data.origin;
                }
                $scope.data.quantity = ''; 
                $scope.serialGridOptions.data = [];
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.onPurchaseOrderSelect = function ($item, $model, $label) {
            $scope.data.purchase_order = {id: $item.id, name: $item.name};
            $scope.data.supplier = $item.supplier;
            $timeout(function() { 
                $scope.find_purchase_order($scope.data.purchase_order.name);
                $('#good_receive_date').focus(); 
            }, 200, false);
        };
        
        $scope.onCodeSelect = function ($item, $model, $label) {
            $scope.data.code = {id: $item.id, name: $item.name};
            $scope.data.item = {id: $item.id, name: $item.item_name, is_serial: $item.is_serial, is_warranty: $item.is_warranty};
            $scope.data.main_category = {id: $item.main_category_id, name: $item.main_category_name};
            $scope.data.sub_category = {id: $item.sub_category_id, name: $item.sub_category_name};
            $scope.data.unit_type = $item.unit_type;
            $scope.data.rate = parseFloat(Math.round($item.rate * 100) / 100).toFixed(2);     
            $scope.data.model_no = $item.model_no;
            $scope.data.brand = $item.brand;
            $scope.data.origin = $item.origin;          
            
            $timeout(function() { 
                $scope.find_code($scope.data.main_category, $scope.data.sub_category, $scope.data.code.name);
                $('#warranty').focus(); 
            }, 200, false);
        };
        
        $scope.onItemSelect = function ($item, $model, $label) {
            $scope.data.code = {id: $item.id, name: $item.code};
            $scope.data.item = {id: $item.id, name: $item.name, is_serial: $item.is_serial, is_warranty: $item.is_warranty};
            $scope.data.main_category = {id: $item.main_category_id, name: $item.main_category_name};
            $scope.data.sub_category = {id: $item.sub_category_id, name: $item.sub_category_name};
            $scope.data.unit_type = $item.unit_type;
            $scope.data.rate = parseFloat(Math.round($item.rate * 100) / 100).toFixed(2);        
            $scope.data.model_no = $item.model_no;
            $scope.data.brand = $item.brand;
            $scope.data.origin = $item.origin;           
            
            $timeout(function() { 
                $scope.find_name($scope.data.main_category, $scope.data.sub_category, $scope.data.item.name);
                $('#warranty').focus(); 
            }, 200, false);
        };

        $scope.editRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/good_receive/find_good_receive_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var good_receive_date_time = response.data.good_receive.good_receive_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.good_receive && response.data.good_receive.purchase_order ? response.data.good_receive.purchase_order.purchase_order_no : '',
                        good_receive_id: response.data.good_receive ? response.data.good_receive.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        purchase_order: response.data.good_receive && response.data.good_receive.purchase_order ? {id:response.data.good_receive.purchase_order.id, name:response.data.good_receive.purchase_order.purchase_order_no} : '',
                        invoice_no: response.data.good_receive ? response.data.good_receive.invoice_no : '',
                        supplier: response.data.good_receive && response.data.good_receive.purchase_order && response.data.good_receive.purchase_order.contact ? response.data.good_receive.purchase_order.contact.name : '',
                        good_receive_no: response.data.good_receive ? response.data.good_receive.good_receive_no : '',
                        good_receive_date: good_receive_date_time[0],
                        good_receive_time: good_receive_date_time[1],
                        remarks: response.data.good_receive ? response.data.good_receive.remarks : '',
                        is_posted: response.data.good_receive ? response.data.good_receive.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name, is_serial:response.data.item.is_serial, is_warranty:response.data.item.is_warranty} : '',
                        model_no: response.data.model_no,
                        brand: response.data.brand,
                        origin: response.data.origin,
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        quantity: response.data.quantity,
                        location: response.data.location,
                        warranty: response.data.warranty,
                        is_main: true,
                        serial_no: ''
                    };
                    
                    var data_array = [];
                    var index = 0;
                    var main_array = [];
                    var main_id = 0;
                    for(var i=0; i<response.data.good_receive_breakdown.length; i++){
                        if(!inArray(response.data.good_receive_breakdown[i].main_id, main_array)){
                            main_id++;
                            for(var j=0; j<response.data.good_receive_breakdown.length; j++){
                                if(response.data.good_receive_breakdown[i].main_id == response.data.good_receive_breakdown[j].main_id){
                                    data_array.push({
                                        index: index,
                                        is_main: response.data.good_receive_breakdown[j].is_main,
                                        main_id: main_id,
                                        serial_no: response.data.good_receive_breakdown[j].serial_no
                                    });
                                    index++;
                                }
                            }
                            main_array.push(response.data.good_receive_breakdown[i].main_id);                            
                        }
                    }    console.log(data_array);
                    $scope.serialGridOptions.data = data_array;
                    $scope.serialGridApi.core.refresh();
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/good_receive/find_good_receive_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var good_receive_date_time = response.data.good_receive.good_receive_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.good_receive && response.data.good_receive.purchase_order ? response.data.good_receive.purchase_order.purchase_order_no : '',
                        good_receive_id: response.data.good_receive ? response.data.good_receive.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        purchase_order: response.data.good_receive && response.data.good_receive.purchase_order ? {id:response.data.good_receive.purchase_order.id, name:response.data.good_receive.purchase_order.purchase_order_no} : '',
                        invoice_no: response.data.good_receive ? response.data.good_receive.invoice_no : '',
                        supplier: response.data.good_receive && response.data.good_receive.purchase_order && response.data.good_receive.purchase_order.contact ? response.data.good_receive.purchase_order.contact.name : '',
                        good_receive_no: response.data.good_receive ? response.data.good_receive.good_receive_no : '',
                        good_receive_date: good_receive_date_time[0],
                        good_receive_time: good_receive_date_time[1],
                        remarks: response.data.good_receive ? response.data.good_receive.remarks : '',
                        is_posted: response.data.good_receive ? response.data.good_receive.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name, is_serial:response.data.item.is_serial, is_warranty:response.data.item.is_warranty} : '',
                        model_no: response.data.model_no,
                        brand: response.data.brand,
                        origin: response.data.origin,
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        quantity: response.data.quantity,
                        location: response.data.location,
                        warranty: response.data.warranty,
                        is_main: true,
                        serial_no: ''
                    };
                    
                    var data_array = [];
                    var index = 0;
                    var main_array = [];
                    var main_id = 0;
                    for(var i=0; i<response.data.good_receive_breakdown.length; i++){
                        if(!inArray(response.data.good_receive_breakdown[i].main_id, main_array)){
                            main_id++;
                            for(var j=0; j<response.data.good_receive_breakdown.length; j++){
                                if(response.data.good_receive_breakdown[i].main_id == response.data.good_receive_breakdown[j].main_id){
                                    data_array.push({
                                        index: index,
                                        is_main: response.data.good_receive_breakdown[j].is_main,
                                        main_id: main_id,
                                        serial_no: response.data.good_receive_breakdown[j].serial_no
                                    });
                                    index++;
                                }
                            }
                            main_array.push(response.data.good_receive_breakdown[i].main_id);                            
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
                    function(){
                        $http.delete(base_url + '/good_receive/'+$scope.data.id, {params: {type: $scope.data.type}}).success(function (response_delete) {
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

        $scope.addSerialNos = function(){
            if($scope.data.serial_no){
                var main_id = 0;
                if($scope.data.is_main){
                    if($scope.serialGridOptions.data.length > 0){
                        var index = $scope.serialGridOptions.data.length-1;
                        main_id = $scope.serialGridOptions.data[index].main_id+1;
                    } else{
                        main_id = 1;
                    }
                } else{
                    if($scope.serialGridOptions.data.length > 0){
                        var index = $scope.serialGridOptions.data.length-1;
                        main_id = $scope.serialGridOptions.data[index].main_id;
                    } else{
                        main_id = 1;
                    }
                }
                
                $timeout(function () {
                    $scope.serialGridOptions.data.push({
                        index: $scope.serialGridOptions.data.length,
                        is_main: $scope.data.is_main ? 1 : 0,
                        main_id: main_id,
                        serial_no: $scope.data.serial_no
                    });

                    $scope.data.serial_no = '';
                    var qunatity = 0;
                    for(var i=0; i<$scope.serialGridOptions.data.length; i++){
                        if($scope.serialGridOptions.data[i].is_main == 1){
                            qunatity++;
                        }
                    }
                    $scope.data.quantity = qunatity;
                    $scope.serialGridApi.core.refresh();
                    $('#serial_no').focus(); 
                }, 100, false);
                
                $(".message_lable").remove();
                $('.form-control').removeClass("error");
                $('.form-control').removeClass("valid");
            }
        };

        $scope.deleteSerialRecord = function (row) {
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
            function(){
                $timeout(function () {
                    $scope.serialGridOptions.data.splice(row.entity.index, 1);
                    $scope.data.quantity = row.entity.is_main == 1 ? Number($scope.data.quantity)-1 : Number($scope.data.quantity)-0;
                    for(var i=0; i<$scope.serialGridOptions.data.length; i++){
                        $scope.serialGridOptions.data[i].index = i;
                    } 
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

        $scope.resetForm = function(){
            $scope.data.good_receive_id = '';
            $scope.refreshForm();
            $scope.main_refresh();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            if($scope.data.item && $scope.data.item.is_serial == 1){
                var qunatity = 0;
                for(var i=0; i<$scope.serialGridOptions.data.length; i++){
                    if($scope.serialGridOptions.data[i].is_main == 1){
                        qunatity++;
                    }
                }
                $scope.data.quantity = qunatity;
            }
            $timeout(function () {
                $('#dataForm').submit();
            }, 100, false);
        };

        $scope.postForm = function(){
            swal({
                title: "Are you sure?",
                text: "Good Receive No : <strong>"+ $scope.data.good_receive_no+"</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, posted it!",
                closeOnConfirm: true
            },
            function(){
                document.getElementById('data_load').style.visibility = "visible";
                $('#post_button').prop('disabled', true);
                $http({
                    method: 'GET',
                    url: base_url + '/good_receive/post_good_receive',
                    params: {
                        id: $scope.data.good_receive_id
                    }
                }).then(function successCallback(response) {
                    $http({
                        method: 'GET',
                        url: base_url + '/report/stock_update'
                    }).then(function successCallback(response) {
                        document.getElementById('data_load').style.visibility = "hidden";
                        $('#post_button').prop('disabled', false);
                        swal({
                            title: "Posted!", 
                            text: "Good Receive No : "+ $scope.data.good_receive_no,
                            html: true,
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                        $scope.refreshForm();
                        $scope.main_refresh();
                    });
                });
            });
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            var valid = true;
            
            if($scope.data.item && $scope.data.item.is_serial == 1 && $scope.serialGridOptions.data.length == 0){
                valid = false;
            }
            
            if(valid){
                $scope.data.serial_details = $scope.serialGridOptions.data;
                if($scope.data.id == 0){
                    $http.post(base_url + '/good_receive', $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Good Receive Details',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });

                        $scope.data.good_receive_id = result.response ? result.data : 0;
                        $scope.refreshForm();
                        $scope.main_refresh();
                    });
                } else{
                    $http.put(base_url + '/good_receive/'+$scope.data.id, $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Good Receive Details',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });

                        $scope.data.good_receive_id = result.response ? result.data : 0;
                        $scope.refreshForm();
                        $scope.main_refresh();
                    });
                }
            } else{
                $('#save_button').prop('disabled', false);
                $.pnotify && $.pnotify({
                    title: 'Good Receive Details',
                    text: 'Serial Nos required',
                    type: 'error',
                    nonblock: true,
                    history: false,
                    delay: 6e3,
                    hide: true
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var supplier_name = $scope.data.supplier;
            var purchase_order_no = $scope.data.purchase_order && $scope.data.purchase_order.name ? $scope.data.purchase_order.name : '';
            $scope.gridOptions.exporterCsvFilename = supplier_name+' : '+purchase_order_no+' Good Receive Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/good_receive/good_receive_detail_list',
                params: {
                    id: $scope.data.good_receive_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.good_receive_details, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_posted: value.good_receive ? value.good_receive.is_posted : 0,
                        item_code: value.item ? value.item.code : '',
                        item_name: value.item ? value.item.name : '',
                        unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                        rate: parseFloat(Math.round(value.rate * 100) / 100).toFixed(2),
                        quantity: value.quantity,
                        value: parseFloat(Math.round(Number(value.rate) * Number(value.quantity) * 100) / 100).toFixed(2)
                    });
                });    
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1500, false);

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection