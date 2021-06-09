@extends('layouts.main')

@section('title')
<title>M3Force | Inventory Return Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inventory</a></li>                    
    <li class="active">Inventory Return Details</li>
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
                    <h3 class="panel-title"><strong>Inventory Return Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.inventory_return_id" ng-disabled="edit_disable || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><div><a ng-show="data.inventory_return_id && data.is_posted == 1" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/inventory_return/print_inventory_return?id=<%=data.inventory_return_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('inventory_return')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="inventory_return_id" name="inventory_return_id" ng-model="data.inventory_return_id" class="form-control" />
                                <input type="hidden" id="inventory_code_value" name="inventory_code_value" ng-model="data.inventory_code_value" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Issue No</label>
                                            <input type="text" id="inventory_issue" name="inventory_issue" ui-grid-edit-auto ng-model="data.inventory_issue" ng-disabled="edit_disable" typeahead="name as inventory_issue_array.name for inventory_issue_array in inventory_issue_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onInventoryIssueSelect($item, $model, $label)" ng-keyup="get_inventory_issues(data.inventory_issue)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Issued To</label>
                                            <input type="text" id="issued_to" name="issued_to" ng-model="data.issued_to" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Inventory Return Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Return No</label>
                                            <input type="text" id="inventory_return_no" name="inventory_return_no" ng-model="data.inventory_return_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Return Date</label>
                                            <input type="text" id="inventory_return_date" name="inventory_return_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.inventory_return_date" ng-disabled="edit_disable" is-open="inventoryReturnDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenInventoryReturnDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Return Time</label>
                                            <input type="text" id="inventory_return_time" name="inventory_return_time" ng-model="data.inventory_return_time" ng-disabled="edit_disable" class="form-control text-center" />
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
                                    <h4 class="col-md-6" style="padding-top: 15px; float: left;">Inventory Details</h4>
                                    <div class="col-md-6" style="padding-top: 15px;">
                                    <button type="button" ng-click="resetInventory()" style="float: right; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Location</label>
                                            <select name="inventory_location" id="inventory_location" ng-options="option.name for option in inventory_location_array track by option.id" ng-model="data.inventory_location" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inventory Type</label>
                                            <select name="inventory_type" id="inventory_type" ng-options="option.name for option in inventory_type_array track by option.id" ng-model="data.inventory_type" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="inventory_code" name="inventory_code" ui-grid-edit-auto ng-model="data.inventory_code" ng-disabled="edit_disable" typeahead="name as inventory_code_array.name for inventory_code_array in inventory_code_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onInventoryCodeSelect($item, $model, $label)" ng-keyup="get_inventory_codes(data.inventory_location, data.inventory_type, data.inventory_code)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="inventory_name" name="inventory_name" ng-model="data.inventory_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Model No</label>             
                                            <input type="text" id="inventory_model_no" name="inventory_model_no" ng-model="data.inventory_model_no" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">IMEI</label>             
                                            <input type="text" id="inventory_imei" name="inventory_imei" ng-model="data.inventory_imei" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Serial No</label>             
                                            <input type="text" id="inventory_serial_no" name="inventory_serial_no" ng-model="data.inventory_serial_no" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Credit Limit</label>             
                                            <input type="text" id="inventory_credit_limit" name="inventory_credit_limit" ng-model="data.inventory_credit_limit" class="form-control text-right" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="inventory_remarks" name="inventory_remarks" ng-model="data.inventory_remarks" ng-disabled="edit_disable" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_inventory')).addClass('active');
        angular.element(document.querySelector('#sub_menu_inventory_return')).addClass('active');
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
                        inventory_issue: {
                            required: true,
                            remote: {
                                url: base_url + '/inventory_return/validate_inventory_issue_no',
                                type: 'GET',
                                data: {
                                    inventory_issue_no: function() {
                                      return scope.data.inventory_issue && scope.data.inventory_issue.name ? scope.data.inventory_issue.name : scope.data.inventory_issue;
                                    }
                                }
                            }
                        },
                        inventory_return_date: {
                            required: true,
                            date: true
                        },
                        inventory_return_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        inventory_code: {
                            remote: {
                                url: base_url + '/inventory_return/validate_inventory_code',
                                type: 'GET',
                                data: {
                                    inventory_issue_id: function() {
                                      return scope.data.inventory_issue && scope.data.inventory_issue.id ? scope.data.inventory_issue.id : '';
                                    },
                                    inventory_code_value: function() {
                                      return scope.data.inventory_code_value;
                                    },
                                    inventory_code: function() {
                                      return scope.data.inventory_code && scope.data.inventory_code.name ? scope.data.inventory_code.name : scope.data.inventory_code;
                                    }
                                }
                            }
                        },
                        errorClass:'error'
                    },
                    messages: {
                        inventory_issue: {
                            required: 'Inventory Issue No is required',
                            remote: 'Invalid Inventory Issue No'
                        },
                        inventory_return_date: {
                            required: 'Inventory Return Date is required',
                            date: 'Invalid date format'
                        },
                        inventory_return_time: {
                            required: 'Inventory Return Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        inventory_code: {
                            remote: 'Invalid Inventory Code or Inventory already returned'
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
            
        $scope.data = [];
        $scope.inventory_issue_array = [];
        $scope.inventory_location_array = [];
        $scope.inventory_type_array = [];
        $scope.inventory_code_array = [];
        
        $scope.data.inventory_return_id = <?php echo $inventory_return_id ? $inventory_return_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.inventoryReturnDatePopup = {
            opened: false
        };        
        $scope.OpenInventoryReturnDate = function () {
            $scope.inventoryReturnDatePopup.opened = !$scope.inventoryReturnDatePopup.opened;
        };
        
        $('#inventory_return_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/get_inventory_data'
            }).then(function successCallback(response) {
                var inventory_location_array = [];
                inventory_location_array.push({
                    id: '',
                    name: 'Inventory Location'
                });
                var inventory_type_array = [];
                inventory_type_array.push({
                    id: '',
                    name: 'Inventory Type'
                });
                $.each(response.data.inventory_locations, function (index, value) {
                    inventory_location_array.push({
                        id: value.id,
                        name: value.name
                    });
                });
                $.each(response.data.inventory_types, function (index, value) {
                    inventory_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.inventory_location_array = inventory_location_array;
                $scope.inventory_type_array = inventory_type_array;
                
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
                    id: 0,
                    inventory_return_id: $scope.data.inventory_return_id,
                    inventory_code_value: '',
                    inventory_issue: '',
                    issued_to: '',
                    inventory_return_no: '',
                    inventory_return_date: new Date(),
                    inventory_return_time: hh+':'+mm,
                    remarks: '',
                    is_posted: 0,
                    inventory_location: $scope.inventory_location_array.length > 0 ? $scope.inventory_location_array[0] : {},
                    inventory_type: $scope.inventory_type_array.length > 0 ? $scope.inventory_type_array[0] : {},
                    inventory_code: '',
                    inventory_name: '',
                    inventory_model_no: '',
                    inventory_imei: '',
                    inventory_serial_no: '',
                    inventory_credit_limit: '',
                    inventory_remarks: ''
                };

                $http({
                    method: 'GET',
                    url: base_url + '/inventory_return/find_inventory_return',
                    params: {
                        id: $scope.data.inventory_return_id
                    }
                }).then(function successCallback(response) {
                    if(response.data){ 
                        var inventory_return_date_time = response.data.inventory_return_date_time.split(' ');
                        $scope.data.inventory_return_id = response.data.id;
                        $scope.data.inventory_issue = response.data.inventory_issue ? {id: response.data.inventory_issue.id, name: response.data.inventory_issue.inventory_issue_no} : '';
                        $scope.data.issued_to = response.data.inventory_issue ? response.data.inventory_issue.issued_to : '';
                        $scope.data.inventory_return_no = response.data.inventory_return_no;
                        $scope.data.inventory_return_date = inventory_return_date_time[0];
                        $scope.data.inventory_return_time = inventory_return_date_time[1];
                        $scope.data.remarks = response.data.remarks;
                        $scope.data.is_posted = response.data.is_posted;
                        $scope.edit_disable = response.data.is_posted == 1 ? true : false;
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
        
        $scope.resetInventory = function(){
            $scope.data.inventory_location = $scope.inventory_location_array.length > 0 ? $scope.inventory_location_array[0] : {};
            $scope.data.inventory_type = $scope.inventory_type_array.length > 0 ? $scope.inventory_type_array[0] : {};
            $scope.data.inventory_code = '';
            $scope.data.inventory_name = '';
            $scope.data.inventory_model_no = '';
            $scope.data.inventory_imei = '';
            $scope.data.inventory_serial_no = '';
            $scope.data.inventory_credit_limit = '';
            $scope.data.inventory_remarks = '';
            
            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)" ng-disabled="row.entity.is_posted == 1"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.is_posted == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'inventory_code', 
                    displayName: 'Code', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_location', 
                    displayName: 'Inventory Location', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_type', 
                    displayName: 'Inventory Type', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_name', 
                    displayName: 'Name', 
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_model_no', 
                    displayName: 'Model No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_imei', 
                    displayName: 'IMEI', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_serial_no', 
                    displayName: 'Serial No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inventory_credit_limit', 
                    displayName: 'Credit Limit', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalCreditLimit() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'inventory_remarks', 
                    displayName: 'Remarks', 
                    width: '30%', 
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
        
        $scope.getAggregationTotalCreditLimit = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].inventory_credit_limit);
            }
            return total_value;
        };

        $scope.get_inventory_issues = function(inventory_issue_no){  
            if(inventory_issue_no && inventory_issue_no.length > 0){
                $scope.inventory_issue_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_inventory_issues',
                    params:{
                        inventory_issue_no: inventory_issue_no
                    }
                }).then(function successCallback(response) {
                    $scope.inventory_issue_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.inventory_issue_array.push({
                            id: value.id,
                            name: value.inventory_issue_no,
                            issued_to: value.issued_to
                        });
                    });
                    $scope.find_inventory_issue(inventory_issue_no);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_inventory_issue = function(inventory_issue_no){  
            $http({
                method: 'GET',
                url: base_url + '/find_inventory_issue',
                params:{
                    inventory_issue_no: inventory_issue_no
                }
            }).then(function successCallback(response) {
                if(response.data){ 
                    $scope.data.inventory_issue = {id: response.data.id, name: response.data.inventory_issue_no};
                    $scope.data.issued_to = response.data.issued_to;
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.get_inventory_codes = function(inventory_location, inventory_type, inventory_code){  
            if(inventory_code && inventory_code.length > 0){
                $scope.inventory_code_array = [];
                $http({
                    method: 'GET',
                    url: base_url + '/get_inventory_codes',
                    params:{
                        code: inventory_code,
                        inventory_location: inventory_location && inventory_location.id ? inventory_location.id : '',
                        inventory_type: inventory_type && inventory_type.id ? inventory_type.id : ''
                    }
                }).then(function successCallback(response) {
                    $scope.inventory_code_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.inventory_code_array.push({
                            id: value.id,
                            name: value.code,
                            inventory_name: value.name,
                            inventory_location_id: value.inventory_location ? value.inventory_location.id : '',
                            inventory_location_name: value.inventory_location ? value.inventory_location.name : '',
                            inventory_type_id: value.inventory_type ? value.inventory_type.id : '',
                            inventory_type_name: value.inventory_type ? value.inventory_type.name : '',
                            model_no: value.model_no,
                            imei: value.imei,
                            serial_no: value.serial_no,
                            credit_limit: value.credit_limit != 0 ? parseFloat(Math.round(value.credit_limit * 100) / 100).toFixed(2) : '',
                            remarks: value.remarks
                        });
                    });
                    $scope.find_inventory_code(inventory_type, inventory_code);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_inventory_code = function(inventory_location, inventory_type, inventory_code){  
            $http({
                method: 'GET',
                url: base_url + '/find_inventory_code',
                params:{
                    code: inventory_code,
                    inventory_location: inventory_location && inventory_location.id ? inventory_location.id : '',
                    inventory_type: inventory_type && inventory_type.id ? inventory_type.id : ''
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.inventory_code = {id: response.data.id, name: response.data.code};
                    $scope.data.inventory_name = response.data.name;
                    $scope.data.inventory_location = response.data.inventory_location ? {id: response.data.inventory_location.id, name: response.data.inventory_location.name} : {};
                    $scope.data.inventory_type = response.data.inventory_type ? {id: response.data.inventory_type.id, name: response.data.inventory_type.name} : {};
                    $scope.data.inventory_model_no = response.data.model_no;
                    $scope.data.inventory_imei = response.data.imei;
                    $scope.data.inventory_serial_no = response.data.serial_no;
                    $scope.data.inventory_credit_limit = response.data.credit_limit != 0 ? parseFloat(Math.round(response.data.credit_limit * 100) / 100).toFixed(2) : '';
                    $scope.data.inventory_remarks = response.data.remarks;
                } 
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.onInventoryIssueSelect = function ($item, $model, $label) {
            $scope.data.inventory_issue = {id: $item.id, name: $item.name};
            $scope.data.issued_to = $item.issued_to;
            $timeout(function() { 
                $scope.find_inventory_issue($scope.data.inventory_issue.name);
                $('#inventory_return_date').focus(); 
            }, 200, false);
        };
        
        $scope.onInventoryCodeSelect = function ($item, $model, $label) {
            $scope.data.inventory_code = {id: $item.id, name: $item.name};
            $scope.data.inventory_name = $item.inventory_name;
            $scope.data.inventory_location = {id: $item.inventory_location_id, name: $item.inventory_location_name};
            $scope.data.inventory_type = {id: $item.inventory_type_id, name: $item.inventory_type_name};
            $scope.data.inventory_model_no = $item.model_no;   
            $scope.data.inventory_imei = $item.imei;    
            $scope.data.inventory_serial_no = $item.serial_no;    
            $scope.data.inventory_credit_limit = $item.credit_limit;    
            $scope.data.inventory_remarks = $item.remarks;       
            
            $timeout(function() { 
                $scope.find_inventory_code($scope.data.inventory_location, $scope.data.inventory_type, $scope.data.inventory_code.name);
                $('#inventory_remarks').focus(); 
            }, 200, false);
        };

        $scope.editRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/inventory_return/find_inventory_return_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){ 
                    var inventory_return_date_time = response.data.inventory_return ? response.data.inventory_return.inventory_return_date_time.split(' ') : null;
                    $scope.data = {
                        id: response.data.id,
                        inventory_return_id: response.data.inventory_return ? response.data.inventory_return.id : 0,
                        inventory_code_value: response.data.inventory_register ? response.data.inventory_register.code : '',
                        inventory_issue: response.data.inventory_return && response.data.inventory_return.inventory_issue ? {id:response.data.inventory_return.inventory_issue.id, name:response.data.inventory_return.inventory_issue.inventory_issue_no} : '',
                        issued_to: response.data.inventory_return && response.data.inventory_return.inventory_issue ? response.data.inventory_return.inventory_issue.issued_to : '',
                        inventory_return_no: response.data.inventory_return ? response.data.inventory_return.inventory_return_no : '',
                        inventory_return_date: response.data.inventory_return ? inventory_return_date_time[0] : '',
                        inventory_return_time: response.data.inventory_return ? inventory_return_date_time[1] : '',
                        remarks: response.data.inventory_return ? response.data.inventory_return.remarks : '',
                        is_posted: response.data.inventory_return ? response.data.inventory_return.is_posted : 0,
                        inventory_location: response.data.inventory_register && response.data.inventory_register.inventory_location ? {id:response.data.inventory_register.inventory_location.id, name:response.data.inventory_register.inventory_location.name} : {},
                        inventory_type: response.data.inventory_register && response.data.inventory_register.inventory_type ? {id:response.data.inventory_register.inventory_type.id, name:response.data.inventory_register.inventory_type.name} : {},
                        inventory_code: response.data.inventory_register ? {id:response.data.inventory_register.id, name:response.data.inventory_register.code} : '',
                        inventory_name: response.data.inventory_register ? response.data.inventory_register.name : '',
                        inventory_model_no: response.data.inventory_register ? response.data.inventory_register.model_no : '',
                        inventory_imei: response.data.inventory_register ? response.data.inventory_register.imei : '',
                        inventory_serial_no: response.data.inventory_register ? response.data.inventory_register.serial_no : '',
                        inventory_credit_limit: response.data.inventory_register && response.data.inventory_register.credit_limit != 0 ? parseFloat(Math.round(response.data.inventory_register.credit_limit * 100) / 100).toFixed(2) : '',
                        inventory_remarks: response.data.inventory_register ? response.data.inventory_register.remarks : ''
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/inventory_return/find_inventory_return_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){ 
                    var inventory_return_date_time = response.data.inventory_return ? response.data.inventory_return.inventory_return_date_time.split(' ') : null;
                    $scope.data = {
                        id: response.data.id,
                        inventory_return_id: response.data.inventory_return ? response.data.inventory_return.id : 0,
                        inventory_code_value: response.data.inventory_register ? response.data.inventory_register.code : '',
                        inventory_issue: response.data.inventory_return && response.data.inventory_return.inventory_issue ? {id:response.data.inventory_return.inventory_issue.id, name:response.data.inventory_return.inventory_issue.inventory_issue_no} : '',
                        issued_to: response.data.inventory_return && response.data.inventory_return.inventory_issue ? response.data.inventory_return.inventory_issue.issued_to : '',
                        inventory_return_no: response.data.inventory_return ? response.data.inventory_return.inventory_return_no : '',
                        inventory_return_date: response.data.inventory_return ? inventory_return_date_time[0] : '',
                        inventory_return_time: response.data.inventory_return ? inventory_return_date_time[1] : '',
                        remarks: response.data.inventory_return ? response.data.inventory_return.remarks : '',
                        is_posted: response.data.inventory_return ? response.data.inventory_return.is_posted : 0,
                        inventory_location: response.data.inventory_register && response.data.inventory_register.inventory_location ? {id:response.data.inventory_register.inventory_location.id, name:response.data.inventory_register.inventory_location.name} : {},
                        inventory_type: response.data.inventory_register && response.data.inventory_register.inventory_type ? {id:response.data.inventory_register.inventory_type.id, name:response.data.inventory_register.inventory_type.name} : {},
                        inventory_code: response.data.inventory_register ? {id:response.data.inventory_register.id, name:response.data.inventory_register.code} : '',
                        inventory_name: response.data.inventory_register ? response.data.inventory_register.name : '',
                        inventory_model_no: response.data.inventory_register ? response.data.inventory_register.model_no : '',
                        inventory_imei: response.data.inventory_register ? response.data.inventory_register.imei : '',
                        inventory_serial_no: response.data.inventory_register ? response.data.inventory_register.serial_no : '',
                        inventory_credit_limit: response.data.inventory_register && response.data.inventory_register.credit_limit != 0 ? parseFloat(Math.round(response.data.inventory_register.credit_limit * 100) / 100).toFixed(2) : '',
                        inventory_remarks: response.data.inventory_register ? response.data.inventory_register.remarks : ''
                    };
                    
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.inventory_register.code + "</strong> inventory details!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/inventory_return/'+$scope.data.id, {params: {type: 1}}).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.inventory_register.code + " inventory details has been deleted.", 
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
            $scope.data.inventory_return_id = '';
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
                text: "Inventory Return No : <strong>"+ $scope.data.inventory_return_no+"</strong>",
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
                    url: base_url + '/inventory_return/post_inventory_return',
                    params: {
                        id: $scope.data.inventory_return_id
                    }
                }).then(function successCallback(result) {
                    $('#post_button').prop('disabled', false);
                    if(result.data.response){
                        swal({
                            title: "Posted!", 
                            text: "Inventory Return No : "+ $scope.data.inventory_return_no,
                            html: true,
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                    } else{
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
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/inventory_return', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Inventory Return Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.inventory_return_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/inventory_return/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Inventory Return Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.inventory_return_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.data.inventory_return_no+' Inventory Return Details.csv';
            $http({
                method: 'GET',
                url: base_url + '/inventory_return/inventory_return_detail_list',
                params: {
                    id: $scope.data.inventory_return_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    data_array.push({
                        id: value.id,
                        is_posted: value.inventory_return ? value.inventory_return.is_posted : 0,
                        inventory_code: value.inventory_register ? value.inventory_register.code : '',
                        inventory_location: value.inventory_register && value.inventory_register.inventory_location ? value.inventory_register.inventory_location.name : '',
                        inventory_type: value.inventory_register && value.inventory_register.inventory_type ? value.inventory_register.inventory_type.name : '',
                        inventory_name: value.inventory_register ? value.inventory_register.name : '',
                        inventory_model_no: value.inventory_register ? value.inventory_register.model_no : '',
                        inventory_imei: value.inventory_register ? value.inventory_register.imei : '',
                        inventory_serial_no: value.inventory_register ? value.inventory_register.serial_no : '',
                        inventory_credit_limit: value.inventory_register && value.inventory_register.credit_limit != 0 ? parseFloat(Math.round(value.inventory_register.credit_limit * 100) / 100).toFixed(2) : '',
                        inventory_remarks: value.inventory_register ? value.inventory_register.remarks : ''
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