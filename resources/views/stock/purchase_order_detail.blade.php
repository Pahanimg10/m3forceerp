@extends('layouts.main')

@section('title')
<title>M3Force | Purchase Order Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">Purchase Order Details</li>
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
    input[type="text"]:disabled{
        color: #1CB09A;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Purchase Order Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.purchase_order_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><div><a ng-show="data.purchase_order_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/purchase_order/print_purchase_order?id=<%=data.purchase_order_id%>">Print</a></div></li>
                        <li><div><a ng-show="data.purchase_order_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/purchase_order/print_purchase_order_stock?id=<%=data.purchase_order_id%>">Print Stock</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('purchase_order')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="purchase_order_id" name="purchase_order_id" ng-model="data.purchase_order_id" class="form-control" />
                                <input type="hidden" id="item_code" name="item_code" ng-model="data.item_code" class="form-control" />
                                <input type="hidden" id="item_name" name="item_name" ng-model="data.item_name" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Supplier</label>
                                            <input type="text" id="supplier" name="supplier" ui-grid-edit-auto ng-model="data.supplier" ng-disabled="edit_disable" typeahead="name as supplier_array.name for supplier_array in supplier_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onSupplierSelect($item, $model, $label)" ng-keyup="get_suppliers(data.supplier)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Good Request No</label>
                                            <input type="text" id="good_request" name="good_request" ui-grid-edit-auto ng-model="data.good_request" ng-disabled="edit_disable" typeahead="name as good_request_array.name for good_request_array in good_request_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onGoodRequestSelect($item, $model, $label)" ng-keyup="get_good_requests(data.good_request)" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Purchase Order Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Purchase Order No</label>
                                            <input type="text" id="purchase_order_no" name="purchase_order_no" ng-model="data.purchase_order_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Purchase Order Date</label>
                                            <input type="text" id="purchase_order_date" name="purchase_order_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.purchase_order_date" ng-disabled="edit_disable" is-open="purchaseOrderDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenPurchaseOrderDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Purchase Order Time</label>
                                            <input type="text" id="purchase_order_time" name="purchase_order_time" ng-model="data.purchase_order_time" ng-disabled="edit_disable" class="form-control text-center" />
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Unit Type</label>
                                            <input type="text" id="unit_type" name="unit_type" ng-model="data.unit_type" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Rate</label>
                                            <input type="text" id="rate" name="rate" ng-model="data.rate" ng-disabled="edit_disable" ng-keydown="calculate_value()" ng-keyup="calculate_value()" ng-keypress="calculate_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Quantity</label>
                                            <input type="text" id="quantity" name="quantity" ng-model="data.quantity" ng-disabled="edit_disable" ng-keydown="calculate_value()" ng-keyup="calculate_value()" ng-keypress="calculate_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Value</label>
                                            <input type="text" id="value" name="value" ng-model="data.value" class="form-control text-right" disabled />
                                        </div>
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
        angular.element(document.querySelector('#sub_menu_purchase_order')).addClass('active');
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
                        supplier: {
                            required: true,
                            remote: {
                                url: base_url + '/validate_contact_name',
                                type: 'GET',
                                data: {
                                    name: function() {
                                      return scope.data.supplier && scope.data.supplier.name ? scope.data.supplier.name : scope.data.supplier;
                                    }
                                }
                            }
                        },
                        good_request: {
                            remote: {
                                url: base_url + '/validate_good_request_no',
                                type: 'GET',
                                data: {
                                    good_request_no: function() {
                                      return scope.data.good_request && scope.data.good_request.name ? scope.data.good_request.name : scope.data.good_request;
                                    }
                                }
                            }
                        },
                        purchase_order_date: {
                            required: true,
                            date: true
                        },
                        purchase_order_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        code: {
                            remote: {
                                url: base_url + '/purchase_order/validate_item_code',
                                type: 'GET',
                                data: {
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order_id;
                                    },
                                    item_code: function() {
                                      return scope.data.item_code;
                                    },
                                    good_request_id: function() {
                                      return scope.data.good_request && scope.data.good_request.id ? scope.data.good_request.id : '';
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
                                url: base_url + '/purchase_order/validate_item_name',
                                type: 'GET',
                                data: {
                                    purchase_order_id: function() {
                                      return scope.data.purchase_order_id;
                                    },
                                    item_name: function() {
                                      return scope.data.item_name;
                                    },
                                    good_request_id: function() {
                                      return scope.data.good_request && scope.data.good_request.id ? scope.data.good_request.id : '';
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
                            number: true,
                            min: 0
                        },
                        quantity: {
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        supplier: {
                            required: 'Supplier is required',
                            remote: 'Invalid Supplier'
                        },
                        good_request: {
                            required: 'Good Request No is required',
                            remote: 'Invalid Good Request No'
                        },
                        purchase_order_date: {
                            required: 'Purchase Order Date is required',
                            date: 'Invalid date format'
                        },
                        purchase_order_time: {
                            required: 'Purchase Order Time is required',
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
                            number: 'Invalid number format',
                            min: 'Minimum rate 0'
                        },
                        quantity: {
                            required: 'Qunatity is required',
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
        $scope.supplier_array = [];
        $scope.good_request_array = [];
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.code_array = [];
        $scope.item_array = [];
        
        $scope.data.purchase_order_id = <?php echo $purchase_order_id ? $purchase_order_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.purchaseOrderDatePopup = {
            opened: false
        };        
        $scope.OpenPurchaseOrderDate = function () {
            $scope.purchaseOrderDatePopup.opened = !$scope.purchaseOrderDatePopup.opened;
        };
        
        $('#purchase_order_time').mask('00:00');
        
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
                    purchase_order_id: $scope.data.purchase_order_id,
                    item_code: '',
                    item_name: '',
                    supplier: '',
                    good_request: '',
                    purchase_order_no: '',
                    purchase_order_date: new Date(),
                    purchase_order_time: hh+':'+mm,
                    remarks: '',
                    is_posted: 0,
                    main_category: $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {},
                    sub_category: $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {},
                    code: '',
                    item: '',
                    rate: 0,
                    unit_type: '',
                    quantity: 1,
                    value: 0
                };

                $http({
                    method: 'GET',
                    url: base_url + '/purchase_order/find_purchase_order',
                    params: {
                        id: $scope.data.purchase_order_id
                    }
                }).then(function successCallback(response) {
                    if(response.data.purchase_order){
                        var purchase_order_date_time = response.data.purchase_order.purchase_order_date_time.split(' ');
                        $scope.data.purchase_order_id = response.data.purchase_order.id;
                        $scope.data.supplier = response.data.purchase_order.contact ? {id: response.data.purchase_order.contact.id, name: response.data.purchase_order.contact.name} : '';
                        $scope.data.good_request = response.data.purchase_order.good_request ? {id: response.data.purchase_order.good_request.id, name: response.data.purchase_order.good_request.good_request_no} : '';
                        $scope.data.purchase_order_no = response.data.purchase_order.purchase_order_no;
                        $scope.data.purchase_order_date = purchase_order_date_time[0];
                        $scope.data.purchase_order_time = purchase_order_date_time[1];
                        $scope.data.remarks = response.data.purchase_order.remarks;
                        $scope.data.is_posted = response.data.purchase_order.is_posted;
                        $scope.edit_disable = response.data.purchase_order.is_posted == 1 ? true : false;
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
            $scope.data.rate = 0;
            $scope.data.unit_type = '';
            $scope.data.quantity = 1;
            $scope.data.value = 0;
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-disabled="row.entity.is_posted == 1"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_posted == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>', 
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

        $scope.export = function () {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function () {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };
        
        $scope.getAggregationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.get_suppliers = function(name){  
            if(name && name.length > 0){
                $scope.supplier_array = [];
                $http({
                    method: 'POST',
                    url: base_url + '/get_customers',
                    data:{
                        type: [3],
                        name: name
                    }
                }).then(function successCallback(response) {
                    $scope.supplier_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.supplier_array.push({
                            id: value.id,
                            name: value.name
                        });
                    });       
                    $scope.find_supplier(name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_supplier = function(name){ 
            $http({
                method: 'POST',
                url: base_url + '/find_customer',
                data:{
                    type: [3],
                    name: name
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.supplier = {id: response.data.id, name: response.data.name};
                } 
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_good_requests = function(good_request_no){  
            if(good_request_no && good_request_no.length > 0){
                $scope.good_request_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_good_request_nos',
                    params:{
                        good_request_no: good_request_no
                    }
                }).then(function successCallback(response) {
                    $scope.good_request_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.good_request_array.push({
                            id: value.id,
                            name: value.good_request_no
                        });
                    });
                    $scope.find_good_request(good_request_no);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_good_request = function(good_request_no){  
            $http({
                method: 'GET',
                url: base_url + '/find_good_request_no',
                params:{
                    good_request_no: good_request_no
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.good_request = {id: response.data.id, name: response.data.good_request_no};
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
                            rate: value.rate
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
                    $scope.data.item = {id: response.data.id, name: response.data.name};
                    $scope.data.main_category = {id: response.data.main_category_id, name: response.data.main_category_name};
                    $scope.data.sub_category = {id: response.data.sub_category_id, name: response.data.sub_category_name};
                    $scope.data.unit_type = response.data.unit_type;
                    $scope.data.rate = parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2);
                }
                $scope.data.quantity = 1;        
            
                $timeout(function() { 
                    $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                }, 100, false);
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
                            rate: value.rate
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
                    $scope.data.item = {id: response.data.id, name: response.data.name};
                    $scope.data.main_category = {id: response.data.main_category_id, name: response.data.main_category_name};
                    $scope.data.sub_category = {id: response.data.sub_category_id, name: response.data.sub_category_name};
                    $scope.data.unit_type = response.data.unit_type;
                    $scope.data.rate = parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2);
                }
                $scope.data.quantity = 1;          
            
                $timeout(function() { 
                    $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                }, 100, false);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.calculate_value = function (){
            $timeout(function() { 
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
            }, 100, false);
        };
        
        $scope.onSupplierSelect = function ($item, $model, $label) {
            $scope.data.supplier = {id: $item.id, name: $item.name};
            $timeout(function() { 
                $scope.find_supplier($scope.data.supplier.name);
                $('#good_request').focus(); 
            }, 200, false);
        };
        
        $scope.onGoodRequestSelect = function ($item, $model, $label) {
            $scope.data.good_request = {id: $item.id, name: $item.name};
            $timeout(function() { 
                $scope.find_good_request($scope.data.good_request.name);
                $('#purchase_order_date').focus(); 
            }, 200, false);
        };
        
        $scope.onCodeSelect = function ($item, $model, $label) {
            $scope.data.code = {id: $item.id, name: $item.name};
            $scope.data.item = {id: $item.id, name: $item.item_name};
            $scope.data.main_category = {id: $item.main_category_id, name: $item.main_category_name};
            $scope.data.sub_category = {id: $item.sub_category_id, name: $item.sub_category_name};
            $scope.data.unit_type = $item.unit_type;
            $scope.data.rate = parseFloat(Math.round($item.rate * 100) / 100).toFixed(2);            
            
            $timeout(function() { 
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                $scope.find_code($scope.data.main_category, $scope.data.sub_category, $scope.data.code.name);
                $('#quantity').focus(); 
            }, 200, false);
        };
        
        $scope.onItemSelect = function ($item, $model, $label) {
            $scope.data.code = {id: $item.id, name: $item.code};
            $scope.data.item = {id: $item.id, name: $item.name};
            $scope.data.main_category = {id: $item.main_category_id, name: $item.main_category_name};
            $scope.data.sub_category = {id: $item.sub_category_id, name: $item.sub_category_name};
            $scope.data.unit_type = $item.unit_type;
            $scope.data.rate = parseFloat(Math.round($item.rate * 100) / 100).toFixed(2);            
            
            $timeout(function() { 
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                $scope.find_name($scope.data.main_category, $scope.data.sub_category, $scope.data.item.name);
                $('#quantity').focus(); 
            }, 200, false);
        };

        $scope.editRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/purchase_order/find_purchase_order_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var purchase_order_date_time = response.data.purchase_order.purchase_order_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        purchase_order_id: response.data.purchase_order ? response.data.purchase_order.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        supplier: response.data.purchase_order && response.data.purchase_order.contact ? {id:response.data.purchase_order.contact.id, name:response.data.purchase_order.contact.name} : '',
                        good_request: response.data.purchase_order && response.data.purchase_order.good_request ? {id:response.data.purchase_order.good_request.id, name:response.data.purchase_order.good_request.good_request_no} : '',
                        purchase_order_no: response.data.purchase_order ? response.data.purchase_order.purchase_order_no : '',
                        purchase_order_date: purchase_order_date_time[0],
                        purchase_order_time: purchase_order_date_time[1],
                        remarks: response.data.purchase_order ? response.data.purchase_order.remarks : '',
                        is_posted: response.data.purchase_order ? response.data.purchase_order.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name} : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        value: parseFloat(Math.round(Number(response.data.rate) * Number(response.data.quantity) * 100) / 100).toFixed(2)
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/purchase_order/find_purchase_order_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var purchase_order_date_time = response.data.purchase_order.purchase_order_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        purchase_order_id: response.data.purchase_order ? response.data.purchase_order.id : 0,
                        item_code: response.data.item ? response.data.item.code : '',
                        item_name: response.data.item ? response.data.item.name : '',
                        supplier: response.data.purchase_order && response.data.purchase_order.contact ? {id:response.data.purchase_order.contact.id, name:response.data.purchase_order.contact.name} : '',
                        good_request: response.data.purchase_order && response.data.purchase_order.good_request ? {id:response.data.purchase_order.good_request.id, name:response.data.purchase_order.good_request.good_request_no} : '',
                        purchase_order_no: response.data.purchase_order ? response.data.purchase_order.purchase_order_no : '',
                        purchase_order_date: purchase_order_date_time[0],
                        purchase_order_time: purchase_order_date_time[1],
                        remarks: response.data.purchase_order ? response.data.purchase_order.remarks : '',
                        is_posted: response.data.purchase_order ? response.data.purchase_order.is_posted : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name} : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        value: parseFloat(Math.round(Number(response.data.rate) * Number(response.data.quantity) * 100) / 100).toFixed(2)
                    };
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
                        $http.delete(base_url + '/purchase_order/'+$scope.data.id, {params: {type: $scope.data.type}}).success(function (response_delete) {
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

        $scope.resetForm = function(){
            $scope.data.purchase_order_id = '';
            $scope.refreshForm();
            $scope.main_refresh();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        $scope.postForm = function(){
            swal({
                title: "Are you sure?",
                text: "Purchase Order No : <strong>"+ $scope.data.purchase_order_no+"</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, posted it!",
                closeOnConfirm: false
            },
            function(){
                $http({
                    method: 'GET',
                    url: base_url + '/purchase_order/post_purchase_order',
                    params: {
                        id: $scope.data.purchase_order_id
                    }
                }).then(function successCallback(response) {
                    swal({
                        title: "Posted!", 
                        text: "Purchase Order No : "+ $scope.data.purchase_order_no,
                        html: true,
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            });
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/purchase_order', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Purchase Order Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.purchase_order_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/purchase_order/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Purchase Order Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.purchase_order_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var supplier_name = $scope.data.supplier && $scope.data.supplier.name ? $scope.data.supplier.name : '';
            var good_request_no = $scope.data.good_request && $scope.data.good_request.name ? $scope.data.good_request.name : '';
            $scope.gridOptions.exporterCsvFilename = supplier_name+' : '+good_request_no+' Purchase Order Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/purchase_order/purchase_order_detail_list',
                params: {
                    id: $scope.data.purchase_order_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.purchase_order_details, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_posted: value.purchase_order ? value.purchase_order.is_posted : 0,
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