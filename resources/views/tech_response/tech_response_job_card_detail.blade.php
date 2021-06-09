@extends('layouts.main')

@section('title')
<title>M3Force | Tech Response Job Card Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">Tech Response Job Card Details</li>
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
                    <h3 class="panel-title"><strong>Tech Response Job Card Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.tech_response_job_card_id && data.is_posted == 0" ng-disabled="edit_disable" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-show="data.tech_response_job_card_id && data.is_posted == 1" ng-disabled="permission || data.is_approved == 1" ng-click="authorizeData()" class="btn btn-danger" data-toggle="modal" data-target="#dataModal">Authorize</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><div><a ng-show="data.tech_response_job_card_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/tech_response_job_card/print_tech_response_job_card?id=<%=data.tech_response_job_card_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('tech_response/tech_response_job_card?id='.$tech_response_id)}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="tech_response_id" name="tech_response_id" ng-model="data.tech_response_id" class="form-control" />
                                <input type="hidden" id="tech_response_job_card_id" name="tech_response_job_card_id" ng-model="data.tech_response_job_card_id" class="form-control" />
                                
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
                                    <h4 class="col-md-12" style="padding-top: 15px;">Tech Response Job Card Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Job Card No</label>
                                            <input type="text" id="tech_response_job_card_no" name="tech_response_job_card_no" ng-model="data.tech_response_job_card_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Job Card Date</label>
                                            <input type="text" id="tech_response_job_card_date" name="tech_response_job_card_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.tech_response_job_card_date" ng-disabled="edit_disable" is-open="techResponseJobCardDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenTechResponseJobCardDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Tech Response Job Card Time</label>
                                            <input type="text" id="tech_response_job_card_time" name="tech_response_job_card_time" ng-model="data.tech_response_job_card_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
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
                                            <label class="control-label">Unit Type</label>
                                            <input type="text" id="unit_type" name="unit_type" ng-model="data.unit_type" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Rate</label>
                                            <input type="text" id="rate" name="rate" ng-model="data.rate" class="form-control text-right" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Quantity</label>
                                            <input type="text" id="quantity" name="quantity" ng-model="data.quantity" ng-disabled="edit_disable" ng-keydown="calculate_value()" ng-keyup="calculate_value()" ng-keypress="calculate_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Margin</label>
                                            <input type="text" id="margin" name="margin" ng-model="data.margin" ng-disabled="edit_disable" ng-keydown="calculate_value()" ng-keyup="calculate_value()" ng-keypress="calculate_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Value</label>
                                            <input type="text" id="value" name="value" ng-model="data.value" ng-disabled="edit_disable" ng-keydown="calculate_margin()" ng-keyup="calculate_margin()" ng-keypress="calculate_margin()"  class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Main</label><br/>
                                            <input id="is_main" bs-switch emit-change="is_main" ng-model="data.is_main" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Chargeable</label><br/>
                                            <input id="is_chargeable" bs-switch emit-change="is_chargeable" ng-model="data.is_chargeable" switch-readonly="edit_disable" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
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

        <div id="dataModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-body">

                    <div class="col-md-12">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong>Authorize</strong> Items</h3>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body"> 
                                <div class="row" style="width: 100%;" ng-bind-html="authorize_data | unsafe"></div>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary pull-right" data-dismiss="modal" ng-click="authorizeForm()" id="authorize_button" style="margin-right: 5px;">Authorize</button> 
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
                        tech_response_job_card_date: {
                            required: true,
                            date: true
                        },
                        tech_response_job_card_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
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
                        quantity: {
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            number: true,
                            min: 0
                        }, 
                        margin: {
                            required: function(element){
                                return $('#code').val() != '' || $('#item').val() != '';
                            },
                            number: true,
                            validMargin: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        tech_response_job_card_date: {
                            required: 'Tech Response Job Card Date is required',
                            date: 'Invalid date format'
                        },
                        tech_response_job_card_time: {
                            required: 'Tech Response Job Card Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        code: {
                            remote: 'Invalid Code'
                        },
                        item: {
                            remote: 'Invalid Item'
                        },
                        quantity: {
                            required: 'Qunatity is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        margin: {
                            required: 'Margin is required',
                            number: 'Invalid number format',
                            validMargin: 'Invalid minimum margin'
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
        
    myApp.filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.base_url = base_url;
        var view = <?php echo $view ? $view : 0; ?>;
        $scope.edit_disable = view == 1 ? true : false;
        $scope.permission = false;
            
        $scope.data = [];
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.code_array = [];
        $scope.item_array = [];
        
        $scope.data.tech_response_id = <?php echo $tech_response_id ? $tech_response_id : 0; ?>;
        $scope.data.tech_response_job_card_id = <?php echo $tech_response_job_card_id ? $tech_response_job_card_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.techResponseJobCardDatePopup = {
            opened: false
        };        
        $scope.OpenTechResponseJobCardDate = function () {
            $scope.techResponseJobCardDatePopup.opened = !$scope.techResponseJobCardDatePopup.opened;
        };
        
        $('#tech_response_job_card_time').mask('00:00');
        
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
                    tech_response_id: $scope.data.tech_response_id,
                    tech_response_job_card_id: $scope.data.tech_response_job_card_id,
                    tech_response_job_card_no: '',
                    tech_response_job_card_date: new Date(),
                    tech_response_job_card_time: hh+':'+mm,
                    remarks: '',
                    is_posted: 0,
                    is_approved: 0,
                    main_category: $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {},
                    sub_category: $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {},
                    code: '',
                    item: '',
                    rate: 0,
                    unit_type: '',
                    quantity: 1,
                    margin: 30,
                    value: 0,
                    is_main: true,
                    is_chargeable: true
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/tech_response/find_tech_response',
                    params: {
                        id: $scope.data.tech_response_id
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.customer_name = response.data.contact ? response.data.contact.name : '';
                        $scope.customer_address = response.data.contact ? response.data.contact.address : '';
                        $scope.customer_contact_no = response.data.contact ? response.data.contact.contact_no : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });

                $http({
                    method: 'GET',
                    url: base_url + '/tech_response_job_card/find_tech_response_job_card',
                    params: {
                        id: $scope.data.tech_response_job_card_id
                    }
                }).then(function successCallback(response) {
                    if(response.data.tech_response_job_card){
                        var tech_response_job_card_date_time = response.data.tech_response_job_card.tech_response_job_card_date_time.split(' ');
                        $scope.data.tech_response_job_card_id = response.data.tech_response_job_card.id;
                        $scope.data.tech_response_job_card_no = response.data.tech_response_job_card.tech_response_job_card_no;
                        $scope.data.tech_response_job_card_date = tech_response_job_card_date_time[0];
                        $scope.data.tech_response_job_card_time = tech_response_job_card_date_time[1];
                        $scope.data.remarks = response.data.tech_response_job_card.remarks;
                        $scope.data.is_posted = response.data.tech_response_job_card.is_posted;
                        $scope.data.is_approved = response.data.tech_response_job_card.is_approved;
                        $scope.edit_disable = response.data.tech_response_job_card.is_posted == 1 ? true : false;
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
            $scope.data.margin = 30;
            $scope.data.value = 0;
            $scope.data.is_main = true;
            $scope.data.is_chargeable = true;
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
                    field: 'is_used', 
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-disabled="row.entity.is_used == 1"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_used == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>', 
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
                    field: 'margin', 
                    displayName: 'Margin %', 
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
                    field: 'stock', 
                    displayName: 'Stock', 
                    cellClass: 'grid-align-right',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'is_main', 
                    displayName: 'Main', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'is_chargeable', 
                    displayName: 'Chargeable', 
                    cellClass: 'grid-align',
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
        
        $scope.getAggregationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.get_codes = function(main_category, sub_category, code){  
            if(code && code.length > 0){
                $scope.code_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_item_codes',
                    params:{
                        type: 0,
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
                    type: 0,
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
                $scope.data.margin = 30;    

                if(response.data.stock < 1){
                    swal({
                        title: "Are you sure?",
                        text: "The item <strong>" + response.data.name + "</strong> not in the stock!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, proceed!"
                    },
                    function(isConfirm){
                        if(!isConfirm){
                            $timeout(function() { 
                                $scope.resetItem();
                                $scope.$apply();
                            }, 100, false);
                        }
                    });
                }           
            
                $timeout(function() { 
                    var margin = (Number($scope.data.margin)+100)/100;
                    $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number(margin) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
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
                        type: 0,
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
                    type: 0,
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
                $scope.data.margin = 30;         

                if(response.data.stock < 1){
                    swal({
                        title: "Are you sure?",
                        text: "The item <strong>" + response.data.name + "</strong> not in the stock!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, proceed!"
                    },
                    function(isConfirm){
                        if(!isConfirm){
                            $timeout(function() { 
                                $scope.resetItem();
                                $scope.$apply();
                            }, 100, false);
                        }
                    });
                }      
            
                $timeout(function() { 
                    var margin = (Number($scope.data.margin)+100)/100;
                    $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number(margin) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                }, 100, false);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.calculate_value = function (){
            $timeout(function() { 
                var margin = (Number($scope.data.margin)+100)/100;
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number(margin) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
            }, 100, false);
        };
        
        $scope.calculate_margin = function (){
            $timeout(function() { 
                var margin = Number($scope.data.value)/(Number($scope.data.rate) * Number($scope.data.quantity));
                $scope.data.margin = (Number(margin) * 100) - 100;
            }, 100, false);
        };
        
        $scope.onCodeSelect = function ($item, $model, $label) {
            $scope.data.code = {id: $item.id, name: $item.name};
            $scope.data.item = {id: $item.id, name: $item.item_name};
            $scope.data.main_category = {id: $item.main_category_id, name: $item.main_category_name};
            $scope.data.sub_category = {id: $item.sub_category_id, name: $item.sub_category_name};
            $scope.data.unit_type = $item.unit_type;
            $scope.data.rate = parseFloat(Math.round($item.rate * 100) / 100).toFixed(2);            
            
            $timeout(function() { 
                var margin = (Number($scope.data.margin)+100)/100;
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number(margin) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
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
                var margin = (Number($scope.data.margin)+100)/100;
                $scope.data.value = parseFloat(Math.round(Number($scope.data.rate) * Number(margin) * Number($scope.data.quantity) * 100) / 100).toFixed(2);
                $scope.find_name($scope.data.main_category, $scope.data.sub_category, $scope.data.item.name);
                $('#quantity').focus(); 
            }, 200, false);
        };

        $scope.editRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/tech_response_job_card/find_tech_response_job_card_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var tech_response_job_card_date_time = response.data.tech_response_job_card.tech_response_job_card_date_time.split(' ');
                    var margin = (Number(response.data.margin)+100)/100;
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        tech_response_id: response.data.tech_response_job_card ? response.data.tech_response_job_card.tech_response_id : 0,
                        tech_response_job_card_id: response.data.tech_response_job_card ? response.data.tech_response_job_card.id : 0,
                        tech_response_job_card_no: response.data.tech_response_job_card ? response.data.tech_response_job_card.tech_response_job_card_no : '',
                        tech_response_job_card_date: tech_response_job_card_date_time[0],
                        tech_response_job_card_time: tech_response_job_card_date_time[1],
                        remarks: response.data.tech_response_job_card ? response.data.tech_response_job_card.remarks : '',
                        is_posted: response.data.tech_response_job_card ? response.data.tech_response_job_card.is_posted : 0,
                        is_approved: response.data.tech_response_job_card ? response.data.tech_response_job_card.is_approved : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name} : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        margin: response.data.margin,
                        value: parseFloat(Math.round(Number(response.data.rate) * Number(margin) * Number(response.data.quantity) * 100) / 100).toFixed(2),
                        is_main: response.data.is_main == 1 ? true : false,
                        is_chargeable: response.data.is_chargeable == 1 ? true : false
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/tech_response_job_card/find_tech_response_job_card_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var tech_response_job_card_date_time = response.data.tech_response_job_card.tech_response_job_card_date_time.split(' ');
                    var margin = (Number(response.data.margin)+100)/100;
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        tech_response_id: response.data.tech_response_job_card ? response.data.tech_response_job_card.tech_response_id : 0,
                        tech_response_job_card_id: response.data.tech_response_job_card ? response.data.tech_response_job_card.id : 0,
                        tech_response_job_card_no: response.data.tech_response_job_card ? response.data.tech_response_job_card.tech_response_job_card_no : '',
                        tech_response_job_card_date: tech_response_job_card_date_time[0],
                        tech_response_job_card_time: tech_response_job_card_date_time[1],
                        remarks: response.data.tech_response_job_card ? response.data.tech_response_job_card.remarks : '',
                        is_posted: response.data.tech_response_job_card ? response.data.tech_response_job_card.is_posted : 0,
                        is_approved: response.data.tech_response_job_card ? response.data.tech_response_job_card.is_approved : 0,
                        main_category: response.data.item && response.data.item.main_item_category ? {id:response.data.item.main_item_category.id, name:response.data.item.main_item_category.name} : {},
                        sub_category: response.data.item && response.data.item.sub_item_category ? {id:response.data.item.sub_item_category.id, name:response.data.item.sub_item_category.name} : {},
                        code: response.data.item ? {id:response.data.item.id, name:response.data.item.code} : '',
                        item: response.data.item ? {id:response.data.item.id, name:response.data.item.name} : '',
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        unit_type: response.data.item && response.data.item.unit_type ? response.data.item.unit_type.code : '',
                        quantity: response.data.quantity,
                        margin: response.data.margin,
                        value: parseFloat(Math.round(Number(response.data.rate) * Number(margin) * Number(response.data.quantity) * 100) / 100).toFixed(2),
                        is_main: response.data.is_main == 1 ? true : false,
                        is_chargeable: response.data.is_chargeable == 1 ? true : false
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
                        $http.delete(base_url + '/tech_response_job_card/'+$scope.data.id, {params: {type: $scope.data.type}}).success(function (response_delete) {
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
            $scope.data.tech_response_job_card_id = '';
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
                text: "Tech Response Job Card No : <strong>"+ $scope.data.tech_response_job_card_no+"</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, posted it!",
                closeOnConfirm: false
            },
            function(){
                $('#post_button').prop('disabled', true);
                $http({
                    method: 'GET',
                    url: base_url + '/tech_response_job_card/post_tech_response_job_card',
                    params: {
                        id: $scope.data.tech_response_job_card_id
                    }
                }).then(function successCallback(response) {
                    $('#post_button').prop('disabled', false);
                    swal({
                        title: "Posted!", 
                        text: "Tech Response Job Card No : "+ $scope.data.tech_response_job_card_no,
                        html: true,
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            });
        };

        $scope.authorizeData = function () {
            $scope.authorize_data = '';
            $http({
                method: 'GET',
                url: base_url + '/tech_response_job_card/get_authorize_data',
                params: {
                    id: $scope.data.tech_response_id
                }
            }).then(function successCallback(response) {
                $scope.authorize_data = response.data.view;
                $scope.approved_job_card_ids = response.data.approved_job_card_ids;
                $scope.approved_installation_sheet_ids = response.data.approved_installation_sheet_ids;
                $('#data_table').dataTable({
                    "aaSorting": [[0, 'asc']],
                    "paging": false,
                    "searching": false,
                    "info": false
                });
            });
        };

        $scope.authorizeForm = function(){
            $('#authorize_button').prop('disabled', true);
            $('#dataModal').modal('hide');
            $http({
                method: 'POST',
                url: base_url + '/tech_response_job_card/approve_tech_response_items',
                data: {
                    tech_response_id: $scope.data.tech_response_id,
                    job_card_ids: $scope.approved_job_card_ids,
                    installation_sheet_ids: $scope.approved_installation_sheet_ids
                }
            }).then(function successCallback(response) {
                $('#authorize_button').prop('disabled', false);
                $.pnotify && $.pnotify({
                    title: 'Tech Response Items',
                    text: 'Authorized Successfully',
                    type: 'success',
                    nonblock: true,
                    history: false,
                    delay: 6e3,
                    hide: true
                });
                    
                $scope.refreshForm();
                $scope.main_refresh();
            });
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/tech_response_job_card', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Job Card Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.tech_response_job_card_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/tech_response_job_card/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Tech Response Job Card Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.tech_response_job_card_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.customer_name+' Tech Response Job Card Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/tech_response_job_card/tech_response_job_card_detail_list',
                params: {
                    id: $scope.data.tech_response_job_card_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.tech_response_job_card_details, function (index, value) {
                    var margin = (Number(value.margin)+100)/100;
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_used: response.data.tech_response_job_card ? response.data.tech_response_job_card.is_used == 1 || response.data.tech_response_job_card.is_posted == 1 ? 1 : 0 : 0,
                        item_code: value.item ? value.item.code : '',
                        item_name: value.item ? value.item.name : '',
                        unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                        rate: parseFloat(Math.round(value.rate * 100) / 100).toFixed(2),
                        quantity: value.quantity,
                        margin: value.margin,
                        value: parseFloat(Math.round(Number(value.rate) * Number(margin) * Number(value.quantity) * 100) / 100).toFixed(2),
                        stock: value.item ? value.item.stock : '',
                        is_main: value.is_main == 1 ? 'Yes' : 'No',
                        is_chargeable: value.is_chargeable == 1 ? 'Yes' : 'No'
                    });
                });    
                $scope.gridOptions.data = data_array;
                $scope.edit_disable = response.data.tech_response_job_card ? response.data.tech_response_job_card.is_used == 1 || $scope.edit_disable : $scope.edit_disable; 
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

        $.validator.addMethod('validMargin', function(value, element, param) {
            if($scope.data.main_category && ($scope.data.main_category.id == 2 || $scope.data.main_category.id == 12 || $scope.data.main_category.id == 14)){
                return value >= 10;
            } else{
                return value >= 30;
            }
        });
    }]);
</script>
@endsection