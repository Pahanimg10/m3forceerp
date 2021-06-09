@extends('layouts.main')

@section('title')
<title>M3Force | New Inquiry</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">New Inquiry</li>
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
                    <h3 class="panel-title"><strong>New Inquiry</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="customer_id" name="customer_id" ng-model="data.customer_id" class="form-control" />
                                <input type="hidden" id="contact_no_value" name="contact_no_value" ng-model="data.contact_no_value" class="form-control" />
                                <input type="hidden" id="address_value" name="address_value" ng-model="data.address_value" class="form-control" />
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Inquiry No</label>
                                            <input type="text" id="inquiry_no" name="inquiry_no" ng-model="data.inquiry_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Date</label>
                                            <input type="text" id="inquiry_date" name="inquiry_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.inquiry_date" is-open="inquiryDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenInquiryDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Time</label>
                                            <input type="text" id="inquiry_time" name="inquiry_time" ng-model="data.inquiry_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Mode of Inquiry</label>
                                            <select name="mode_of_inquiry" id="mode_of_inquiry" ng-options="option.name for option in mode_of_inquiry_array track by option.id" ng-model="data.mode_of_inquiry" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Contact Of</label>
                                            <input type="text" id="contact_of" name="contact_of" ng-model="data.contact_of" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Inquiry Type</label>
                                            <select name="inquiry_type" id="inquiry_type" ng-options="option.name for option in inquiry_type_array track by option.id" ng-model="data.inquiry_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Sales Person</label>
                                            <select name="sales_person" id="sales_person" ng-options="option.name for option in sales_person_array track by option.id" ng-model="data.sales_person" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Customer Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ui-grid-edit-auto ng-model="data.name" typeahead="name as name_array.name for name_array in name_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onNameSelect($item, $model, $label)" ng-keyup="get_names(data.name)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Contact No</label>
                                            <input type="text" id="contact_no" name="contact_no" ng-model="data.contact_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name="email" ng-model="data.email" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name="address" ng-model="data.address" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <h4 class="col-md-12" style="padding-top: 15px;">Filter Details</h4>
                        </div>
                                
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Inquiry Type</label>
                                    <select name="filter_inquiry_type" id="filter_inquiry_type" ng-options="option.name for option in filter_inquiry_type_array track by option.id" ng-model="data.filter_inquiry_type" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Sales Person</label>
                                    <select name="filter_sales_person" id="filter_sales_person" ng-options="option.name for option in filter_sales_person_array track by option.id" ng-model="data.filter_sales_person" ng-change="main_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 text-right" style="margin-top: 10px;">
                            <button type="button" class="btn btn-warning btn-sm grid-btn text-center"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Status&nbsp;<button type="button" class="btn btn-info btn-sm grid-btn text-center"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button>&nbsp;Update&nbsp;<button type="button" class="btn btn-danger btn-sm grid-btn text-center"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button>&nbsp;Delete&nbsp;
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
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
        'ui.grid.cellNav'
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
        angular.element(document.querySelector('#main_menu_inquiry')).addClass('active');
        angular.element(document.querySelector('#sub_menu_new_inquiry')).addClass('active');
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
                        inquiry_date: {
                            required: true,
                            date: true
                        },
                        inquiry_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        mode_of_inquiry: {
                            required: true
                        },
                        inquiry_type: {
                            required: true
                        }, 
                        sales_person: {
                            required: true
                        }, 
                        name: {
                            required: true
                        }, 
                        contact_no: {
                            required: true,
                            validContactNo: true
                        }, 
                        email: {
                            email: true
                        },
                        address: {
                            required: true
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        inquiry_date: {
                            required: 'Date is required',
                            date: 'Invalid date format'
                        },
                        inquiry_time: {
                            required: 'Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        mode_of_inquiry: {
                            required: 'Mode of Inquiry is required'
                        },
                        inquiry_type: {
                            required: 'Inquiry Type is required'
                        },
                        sales_person: {
                            required: 'Sales Person is required'
                        },
                        name: {
                            required: 'Name is required'
                        },
                        contact_no: {
                            required: 'Contact No is required',
                            validContactNo: 'Invalid number format'
                        },
                        email: {
                            email: 'Invalid email format'
                        },
                        address: {
                            required: 'Address is required'
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
        $scope.data = [];
        $scope.mode_of_inquiry_array = [];
        $scope.inquiry_type_array = [];
        $scope.sales_person_array = [];
        $scope.name_array = []; 
        
        $scope.filter_inquiry_type_array = [];
        $scope.filter_sales_person_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        $scope.inquiryDatePopup = {
            opened: false
        };        
        $scope.OpenInquiryDate = function () {
            $scope.inquiryDatePopup.opened = !$scope.inquiryDatePopup.opened;
        };
        
        $('#inquiry_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/inquiry/get_data'
            }).then(function successCallback(response) {
                var mode_of_inquiry_array = [];
                mode_of_inquiry_array.push({
                    id: '',
                    name: 'Select Mode of Inquiry'
                });
                $.each(response.data.mode_of_inquries, function (index, value) {
                    mode_of_inquiry_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                var inquiry_type_array = [];
                inquiry_type_array.push({
                    id: '',
                    name: 'Select Inquiry Type'
                });
                $.each(response.data.inquiry_types, function (index, value) {
                    inquiry_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                var sales_person_array = [];
                sales_person_array.push({
                    id: '',
                    name: 'Select Sales Person'
                });
                $.each(response.data.sales_team, function (index, value) {
                    sales_person_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                
                var filter_inquiry_type_array = [];
                filter_inquiry_type_array.push({
                    id: -1,
                    name: 'All'
                });
                $.each(response.data.inquiry_types, function (index, value) {
                    filter_inquiry_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                var filter_sales_person_array = [];
                filter_sales_person_array.push({
                    id: -1,
                    name: 'All'
                });
                $.each(response.data.sales_team, function (index, value) {
                    filter_sales_person_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 

                $scope.mode_of_inquiry_array = mode_of_inquiry_array;
                $scope.inquiry_type_array = inquiry_type_array;
                $scope.sales_person_array = sales_person_array;
                
                $scope.filter_inquiry_type_array = filter_inquiry_type_array;
                $scope.filter_sales_person_array = filter_sales_person_array;
                
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
                    id: <?php echo $inquiry_id ? $inquiry_id : 0; ?>,
                    customer_id: '',
                    contact_no_value: '',
                    address_value: '',
                    inquiry_no: '',
                    inquiry_date: new Date(),
                    inquiry_time: hh+':'+mm,
                    mode_of_inquiry: $scope.mode_of_inquiry_array.length > 0 ? $scope.mode_of_inquiry_array[0] : {},
                    contact_of: '',
                    inquiry_type: $scope.inquiry_type_array.length > 0 ? $scope.inquiry_type_array[0] : {},
                    sales_person: $scope.sales_person_array.length > 0 ? $scope.sales_person_array[0] : {},
                    remarks: '',
                    name: '',
                    contact_no: '',
                    email: '',
                    address: '',
                    filter_inquiry_type: $scope.filter_inquiry_type_array.length > 0 ? $scope.filter_inquiry_type_array[0] : {},
                    filter_sales_person: $scope.filter_sales_person_array.length > 0 ? $scope.filter_sales_person_array[0] : {}
                };
            }, function errorCallback(response) {
                console.log(response);
            });
            $http({
                method: 'GET',
                url: base_url + '/inquiry/find_inquiry',
                params: {
                    id: <?php echo $inquiry_id ? $inquiry_id : 0; ?>
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var date_time = response.data.inquiry_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        customer_id: response.data.contact ? response.data.contact.id : '',
                        contact_no_value: response.data.contact ? response.data.contact.contact_no : '',
                        address_value: response.data.contact ? response.data.contact.address : '',
                        inquiry_no: response.data.inquiry_no,
                        inquiry_date: date_time[0],
                        inquiry_time: date_time[1],
                        mode_of_inquiry: response.data.i_mode_of_inquiry ? {id:response.data.i_mode_of_inquiry.id, name:response.data.i_mode_of_inquiry.name} : {},
                        contact_of: response.data.contact_of,
                        inquiry_type: response.data.i_inquiry_type ? {id:response.data.i_inquiry_type.id, name:response.data.i_inquiry_type.name} : {},
                        sales_person: response.data.sales_team ? {id:response.data.sales_team.id, name:response.data.sales_team.name} : {},
                        remarks: response.data.remarks,
                        name: response.data.contact ? {id:response.data.contact.id, name:response.data.contact.name} : {},
                        contact_no: response.data.contact ? response.data.contact.contact_no : '',
                        email: response.data.contact ? response.data.contact.email : '',
                        address: response.data.contact ? response.data.contact.address : '',
                        filter_inquiry_type: $scope.filter_inquiry_type_array.length > 0 ? $scope.filter_inquiry_type_array[0] : {},
                        filter_sales_person: $scope.filter_sales_person_array.length > 0 ? $scope.filter_sales_person_array[0] : {}
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();

        $scope.get_names = function(name){  
            if(name && name.length > 0){
                $scope.name_array = [];  
                $http({
                    method: 'POST',
                    url: base_url + '/get_customers',
                    data:{
                        type: [1,2],
                        name: name
                    }
                }).then(function successCallback(response) {
                    $scope.name_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.name_array.push({
                            id: value.id,
                            name: value.name,
                            contact_no: value.contact_no,
                            email: value.email,
                            address: value.address
                        });
                    });       
                    $scope.find_name(name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_name = function(name){ 
            $http({
                method: 'POST',
                url: base_url + '/find_customer',
                data:{
                    type: [1,2],
                    name: name
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.customer_id = response.data.id;
                    $scope.data.name = {id: response.data.id, name: response.data.name};
                    $scope.data.contact_no = response.data.contact_no;
                    $scope.data.email = response.data.email;
                    $scope.data.address = response.data.address;
                    $scope.data.contact_no_value = response.data.contact_no;
                    $scope.data.address_value = response.data.address;
                } 
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.onNameSelect = function ($item, $model, $label) {
            $scope.data.customer_id = $item.id;
            $scope.data.name = {id: $item.id, name: $item.name};
            $scope.data.contact_no = $item.contact_no;
            $scope.data.email = $item.email;
            $scope.data.address = $item.address;
            $scope.data.contact_no_value = $item.contact_no;
            $scope.data.address_value = $item.address;
            $timeout(function() { 
                $scope.find_name($scope.data.name.name);
                $('#contact_no').focus(); 
            }, 200, false);
        };
                
        $('#contact_no').on('change', function() {
            if(this.value != $scope.data.contact_no_value){
                $scope.data.customer_id = '';
            }
        });
                
        $('#address').on('change', function() {
            if(this.value != $scope.data.address_value){
                $scope.data.customer_id = '';
            }
        });
        
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
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '12%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-warning btn-sm grid-btn text-center" ng-click="grid.appScope.updateRecord(row)"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'inquiry_no', 
                    displayName: 'Inquiry No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inquiry_date_time', 
                    displayName: 'Inquiry Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'inquiry_type', 
                    displayName: 'Inquiry Type', 
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_name', 
                    displayName: 'Customer Name', 
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_address', 
                    displayName: 'Address', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_contact_no', 
                    displayName: 'Contact No', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'client_type', 
                    displayName: 'Client Type', 
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'business_type', 
                    displayName: 'Business Type', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'sales_person', 
                    displayName: 'Sales Person', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'pending_days', 
                    displayName: 'Pending Days', 
                    cellClass: 'grid-align',
                    width: '10%', 
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

        $scope.editRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/inquiry/find_inquiry',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var date_time = response.data.inquiry_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        customer_id: response.data.contact ? response.data.contact.id : '',
                        contact_no_value: response.data.contact ? response.data.contact.contact_no : '',
                        address_value: response.data.contact ? response.data.contact.address : '',
                        inquiry_no: response.data.inquiry_no,
                        inquiry_date: date_time[0],
                        inquiry_time: date_time[1],
                        mode_of_inquiry: response.data.i_mode_of_inquiry ? {id:response.data.i_mode_of_inquiry.id, name:response.data.i_mode_of_inquiry.name} : {},
                        contact_of: response.data.contact_of,
                        inquiry_type: response.data.i_inquiry_type ? {id:response.data.i_inquiry_type.id, name:response.data.i_inquiry_type.name} : {},
                        sales_person: response.data.sales_team ? {id:response.data.sales_team.id, name:response.data.sales_team.name} : {},
                        remarks: response.data.remarks,
                        name: response.data.contact ? {id:response.data.contact.id, name:response.data.contact.name} : {},
                        contact_no: response.data.contact ? response.data.contact.contact_no : '',
                        email: response.data.contact ? response.data.contact.email : '',
                        address: response.data.contact ? response.data.contact.address : '',
                        filter_inquiry_type: $scope.filter_inquiry_type_array.length > 0 ? $scope.filter_inquiry_type_array[0] : {},
                        filter_sales_person: $scope.filter_sales_person_array.length > 0 ? $scope.filter_sales_person_array[0] : {}
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/inquiry/find_inquiry',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var date_time = response.data.inquiry_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        customer_id: response.data.contact ? response.data.contact.id : '',
                        contact_no_value: response.data.contact ? response.data.contact.contact_no : '',
                        address_value: response.data.contact ? response.data.contact.address : '',
                        inquiry_no: response.data.inquiry_no,
                        inquiry_date: date_time[0],
                        inquiry_time: date_time[1],
                        mode_of_inquiry: response.data.i_mode_of_inquiry ? {id:response.data.i_mode_of_inquiry.id, name:response.data.i_mode_of_inquiry.name} : {},
                        contact_of: response.data.contact_of,
                        inquiry_type: response.data.i_inquiry_type ? {id:response.data.i_inquiry_type.id, name:response.data.i_inquiry_type.name} : {},
                        sales_person: response.data.sales_team ? {id:response.data.sales_team.id, name:response.data.sales_team.name} : {},
                        remarks: response.data.remarks,
                        name: response.data.contact ? {id:response.data.contact.id, name:response.data.contact.name} : {},
                        contact_no: response.data.contact ? response.data.contact.contact_no : '',
                        email: response.data.contact ? response.data.contact.email : '',
                        address: response.data.contact ? response.data.contact.address : '',
                        filter_inquiry_type: $scope.filter_inquiry_type_array.length > 0 ? $scope.filter_inquiry_type_array[0] : {},
                        filter_sales_person: $scope.filter_sales_person_array.length > 0 ? $scope.filter_sales_person_array[0] : {}
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.inquiry_no + "</strong> inquiry!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/inquiry/'+$scope.data.id, {params: {type: $scope.data.type}}).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.inquiry_no + " inquiry has been deleted.", 
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

        $scope.updateRecord = function (row) {
            $window.open(base_url + '/inquiry/update_inquiry?id='+row.entity.id, '_blank');
        };

        $scope.resetForm = function(){
            $scope.refreshForm();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            if(!$scope.data.customer_id || $scope.data.customer_id == ''){
                swal({
                    title: "Are you sure?",
                    text: "Inquiry will save as <strong>New Customer</strong> !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, proceed it!",
                    closeOnConfirm: true
                },
                function(){        
                    $('#save_button').prop('disabled', true);
                    if($scope.data.id == 0){
                        $http.post(base_url + '/inquiry', $scope.data).success(function (result) {
                            $('#save_button').prop('disabled', false);
                            $.pnotify && $.pnotify({
                                title: 'New Inquiry',
                                text: result.message,
                                type: result.response ? 'success' :'error',
                                nonblock: true,
                                history: false,
                                delay: 6e3,
                                hide: true
                            });
                            $scope.resetForm();
                            $scope.main_refresh();
                        });
                    } else{
                        $http.put(base_url + '/inquiry/'+$scope.data.id, $scope.data).success(function (result) {
                            $('#save_button').prop('disabled', false);
                            $.pnotify && $.pnotify({
                                title: 'New Inquiry',
                                text: result.message,
                                type: result.response ? 'success' :'error',
                                nonblock: true,
                                history: false,
                                delay: 6e3,
                                hide: true
                            });
                            $scope.resetForm();
                            $scope.main_refresh();
                        });
                    }
                });
            } else{            
                $('#save_button').prop('disabled', true);
                if($scope.data.id == 0){
                    $http.post(base_url + '/inquiry', $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'New Inquiry',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                } else{
                    $http.put(base_url + '/inquiry/'+$scope.data.id, $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'New Inquiry',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                }
            }
        };

        $scope.main_refresh = function(){  
            document.getElementById('data_load').style.visibility = "visible";
            var filter_inquiry_type = $scope.data.filter_inquiry_type ? $scope.data.filter_inquiry_type.name : 'All';
            var filter_sales_person = $scope.data.filter_sales_person ? $scope.data.filter_sales_person.name : 'All';
            $scope.gridOptions.exporterCsvFilename = 'New Inquiries ('+filter_inquiry_type+'-'+filter_sales_person+').csv';
            $http({
                method: 'GET',
                url: base_url + '/inquiry/new_inquiry_list',
                params: {
                    inquiry_type_id: $scope.data.filter_inquiry_type ? $scope.data.filter_inquiry_type.id : -1,
                    sales_team_id: $scope.data.filter_sales_person ? $scope.data.filter_sales_person.id : -1
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.inquiries, function (index, value) {
                    var date_time = value.inquiry_date_time.split(' ');
                    var start = new Date(date_time[0]);
                    var end   = new Date();
                    var diff  = new Date(end - start);
                    var days  = Math.floor(diff/1000/60/60/24);
                    
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        inquiry_no: value.inquiry_no,
                        inquiry_date_time: value.inquiry_date_time,
                        inquiry_type: value.i_inquiry_type ? value.i_inquiry_type.name : '',
                        customer_name: value.contact ? value.contact.name : '',
                        customer_address: value.contact ? value.contact.address : '',
                        customer_contact_no: value.contact ? value.contact.contact_no : '',
                        client_type: value.contact && value.contact.i_client_type ? value.contact.i_client_type.name : '',
                        business_type: value.contact && value.contact.i_business_type ? value.contact.i_business_type.name : '',
                        sales_person: value.sales_team ? value.sales_team.name : '',
                        pending_days: days,
                        log_user: value.user ? value.user.first_name : ''
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
            
        $.validator.addMethod('validContactNo', function (value) {                
            if(value != ''){
                return value.match(/^[\s\+0-9]+$/); 
            } else{
                return true;
            }
        });
    }]);
</script>
@endsection