@extends('layouts.main')

@section('title')
<title>M3Force | Cost Sheet Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">Cost Sheet Details</li>
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
                    <h3 class="panel-title"><strong>Cost Sheet Detail</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><div><a ng-show="data.cost_sheet_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/cost_sheet/print_cost_sheet?id=<%=data.cost_sheet_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('inquiry/cost_sheet?id='.$inquiry_id.'&type='.$type)}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="inquiry_id" name="inquiry_id" ng-model="data.inquiry_id" class="form-control" />
                                <input type="hidden" id="cost_sheet_id" name="cost_sheet_id" ng-model="data.cost_sheet_id" class="form-control" />
                                
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
                                    <h4 class="col-md-12" style="padding-top: 15px;">Cost Sheet Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Cost Sheet No</label>
                                            <input type="text" id="cost_sheet_no" name="cost_sheet_no" ng-model="data.cost_sheet_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Cost Sheet Date</label>
                                            <input type="text" id="cost_sheet_date" name="cost_sheet_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.cost_sheet_date" ng-disabled="edit_disable" is-open="costSheetDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenCostSheetDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Cost Sheet Time</label>
                                            <input type="text" id="cost_sheet_time" name="cost_sheet_time" ng-model="data.cost_sheet_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Installation Rate</label>
                                            <select name="installation_rate" id="installation_rate" ng-options="option.name for option in installation_rate_array track by option.id" ng-model="data.installation_rate" ng-disabled="edit_disable" ng-change="calculate_total_value()" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Meters</label>
                                            <input type="text" id="meters" name="meters" ng-model="data.meters" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Excavation Work</label>
                                            <input type="text" id="excavation_work" name="excavation_work" ng-model="data.excavation_work" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Transport</label>
                                            <input type="text" id="transport" name="transport" ng-model="data.transport" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Traveling Mandays</label>
                                            <input type="text" id="traveling_mandays" name="traveling_mandays" ng-model="data.traveling_mandays" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Food</label>
                                            <input type="text" id="food" name="food" ng-model="data.food" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Accommodation</label>
                                            <input type="text" id="accommodation" name="accommodation" ng-model="data.accommodation" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Bata</label>
                                            <input type="text" id="bata" name="bata" ng-model="data.bata" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Other Expenses</label>
                                            <input type="text" id="other_expenses" name="other_expenses" ng-model="data.other_expenses" ng-disabled="edit_disable" ng-keydown="calculate_total_value()" ng-keyup="calculate_total_value()" ng-keypress="calculate_total_value()" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Mandays (1 Person)</label>
                                            <input type="text" id="mandays" name="mandays" ng-model="data.mandays" class="form-control text-right" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Installation Value</label>
                                            <input type="text" id="installation_value" name="installation_value" ng-model="data.installation_value" class="form-control text-right" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Labour Value</label>
                                            <input type="text" id="labour_value" name="labour_value" ng-model="data.labour_value" class="form-control text-right" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Total Value</label>
                                            <input type="text" id="total_value" name="total_value" ng-model="data.total_value" class="form-control text-right" disabled />
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

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div> 
</div> 

<script type="text/javascript">
    var type = <?php echo $type ? $type : 0; ?>;
    
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
        angular.element(document.querySelector(type == 0 ? '#main_menu_inquiry' : '#main_menu_job')).addClass('active');
        angular.element(document.querySelector(type == 0 ? '#sub_menu_ongoing_inquiry' : '#sub_menu_ongoing_job')).addClass('active');
    });  
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        cost_sheet_date: {
                            required: true,
                            date: true
                        },
                        cost_sheet_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
//                        installation_rate: {
//                            required: true
//                        },
                        meters: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        excavation_work: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        transport: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        traveling_mandays: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        food: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        accommodation: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        bata: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        other_expenses: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        cost_sheet_date: {
                            required: 'Cost Sheet Date is required',
                            date: 'Invalid date format'
                        },
                        cost_sheet_time: {
                            required: 'Cost Sheet Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
//                        installation_rate: {
//                            required: 'Installation Rate is required'
//                        },
                        meters: {
                            required: 'Meters is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        excavation_work: {
                            required: 'Excavation Work is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        transport: {
                            required: 'Transport is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        traveling_mandays: {
                            required: 'Traveling Mandays is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        food: {
                            required: 'Food is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        accommodation: {
                            required: 'Accommodation is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        bata: {
                            required: 'Bata is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        other_expenses: {
                            required: 'Other Expenses is required',
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
        var view = <?php echo $view ? $view : 0; ?>;
        $scope.edit_disable = view == 1 ? true : false;
            
        $scope.data = [];
        $scope.installation_rate_array = [];
        $scope.item_array = [];
        $scope.manday_rate = 0;
        
        $scope.data.inquiry_id = <?php echo $inquiry_id ? $inquiry_id : 0; ?>;
        $scope.data.cost_sheet_id = <?php echo $cost_sheet_id ? $cost_sheet_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.costSheetDatePopup = {
            opened: false
        };        
        $scope.OpenCostSheetDate = function () {
            $scope.costSheetDatePopup.opened = !$scope.costSheetDatePopup.opened;
        };
        
        $('#cost_sheet_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/cost_sheet/get_data'
            }).then(function successCallback(response) {
                var installation_rate_array = [];
                installation_rate_array.push({
                    id: '',
                    name: 'Select Installation Rate',
                    installation_cost: 0,
                    labour: 0,
                    rate: 0
                });
                $.each(response.data.installation_rates, function (index, value) {
                    installation_rate_array.push({
                        id: value.id,
                        name: value.name,
                        installation_cost: value.installation_cost,
                        labour: value.labour,
                        rate: value.rate
                    });
                });  

                $scope.manday_rate = response.data.manday_rate ? response.data.manday_rate.value : 0;
                $scope.installation_rate_array = installation_rate_array;
                
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
                    inquiry_id: $scope.data.inquiry_id,
                    cost_sheet_id: $scope.data.cost_sheet_id,
                    cost_sheet_no: '',
                    cost_sheet_date: new Date(),
                    cost_sheet_time: hh+':'+mm,
                    installation_rate: $scope.installation_rate_array.length > 0 ? $scope.installation_rate_array[0] : {},
                    meters: 0,
                    excavation_work: 0,
                    transport: 0,
                    traveling_mandays: 0,
                    food: 0,
                    accommodation: 0,
                    bata: 0,
                    other_expenses: 0,
                    remarks: '',
                    mandays: 0,
                    installation_value: 0,
                    labour_value: 0,
                    total_value: 0
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/inquiry/find_inquiry',
                    params: {
                        id: $scope.data.inquiry_id
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
                    url: base_url + '/cost_sheet/find_cost_sheet',
                    params: {
                        id: $scope.data.cost_sheet_id
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        var cost_sheet_date_time = response.data.cost_sheet_date_time.split(' ');
                        var total_value = response.data.installation_rate ? (Number(response.data.installation_rate.rate) * Number(response.data.meters)) + Number(response.data.excavation_work) + Number(response.data.transport) + (Number(response.data.traveling_mandays)*Number($scope.manday_rate)) + Number(response.data.food) + Number(response.data.accommodation) + Number(response.data.bata) + Number(response.data.other_expenses) : Number(response.data.excavation_work) + Number(response.data.transport) + (Number(response.data.traveling_mandays)*Number($scope.manday_rate)) + Number(response.data.food) + Number(response.data.accommodation) + Number(response.data.bata) + Number(response.data.other_expenses);
                        $scope.data = {
                            inquiry_id: $scope.data.inquiry_id,
                            cost_sheet_id: response.data.id,
                            cost_sheet_no: response.data.cost_sheet_no,
                            cost_sheet_date: cost_sheet_date_time[0],
                            cost_sheet_time: cost_sheet_date_time[1],
                            installation_rate: response.data.installation_rate ? {id:response.data.installation_rate.id, name:response.data.installation_rate.name, installation_cost:response.data.installation_rate.installation_cost, labour:response.data.installation_rate.labour, rate:response.data.installation_rate.rate} : $scope.installation_rate_array.length > 0 ? $scope.installation_rate_array[0] : {},
                            meters: response.data.meters,
                            excavation_work: parseFloat(Math.round(response.data.excavation_work * 100) / 100).toFixed(2),
                            transport: parseFloat(Math.round(response.data.transport * 100) / 100).toFixed(2),
                            traveling_mandays: parseFloat(Math.round(response.data.traveling_mandays * 100) / 100).toFixed(2),
                            food: parseFloat(Math.round(response.data.food * 100) / 100).toFixed(2),
                            accommodation: parseFloat(Math.round(response.data.accommodation * 100) / 100).toFixed(2),
                            bata: parseFloat(Math.round(response.data.bata * 100) / 100).toFixed(2),
                            other_expenses: parseFloat(Math.round(response.data.other_expenses * 100) / 100).toFixed(2),
                            remarks: response.data.remarks,
                            mandays: response.data.mandays,
                            installation_value: parseFloat(Math.round(response.data.installation_value * 100) / 100).toFixed(2),
                            labour_value: parseFloat(Math.round(response.data.mandays * Number($scope.manday_rate) * 100) / 100).toFixed(2),
                            total_value: parseFloat(Math.round(total_value * 100) / 100).toFixed(2)
                        };
                        $scope.edit_disable = $scope.edit_disable || response.data.is_used == 1;
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.resetForm = function(){
            $scope.data.cost_sheet_id = 0;
            $scope.refreshForm();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.cost_sheet_id == 0){
                $http.post(base_url + '/cost_sheet', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Cost Sheet Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.cost_sheet_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                });
            } else{
                $http.put(base_url + '/cost_sheet/'+$scope.data.cost_sheet_id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Cost Sheet Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.cost_sheet_id = result.response ? result.data : 0;
                    $scope.refreshForm();
                });
            }
        };

        $timeout(function () {
            $scope.refreshForm();
        }, 1500, false);
        
        $scope.calculate_total_value = function (){ 
            $timeout(function() { 
                var total_value = $scope.data.installation_rate && $scope.data.installation_rate.rate ? Number($scope.data.installation_rate.rate)*Number($scope.data.meters) : 0;
                total_value += Number($scope.data.excavation_work);
                total_value += Number($scope.data.transport);
                total_value += Number($scope.data.traveling_mandays) * Number($scope.manday_rate);
                total_value += Number($scope.data.food);
                total_value += Number($scope.data.accommodation);
                total_value += Number($scope.data.bata);
                total_value += Number($scope.data.other_expenses);
                $scope.data.total_value = parseFloat(Math.round(total_value * 100) / 100).toFixed(2);
                
                var installation_mandays = $scope.data.installation_rate && $scope.data.installation_rate.labour ? Number(Number($scope.data.installation_rate.labour)*Number($scope.data.meters))/Number($scope.manday_rate) : 0;
                var other_mandays = Number($scope.data.other_expenses)/(Number($scope.manday_rate)*2);
                var installation_value = $scope.data.installation_rate && $scope.data.installation_rate.installation_cost ? Number($scope.data.installation_rate.installation_cost)*Number($scope.data.meters) + Number($scope.data.other_expenses)/2 : 0;
                
                $scope.data.mandays = Number(installation_mandays)+Number($scope.data.traveling_mandays)+Number(other_mandays);
                $scope.data.installation_value = parseFloat(Math.round(installation_value * 100) / 100).toFixed(2);
                $scope.data.labour_value = parseFloat(Math.round($scope.data.mandays * Number($scope.manday_rate) * 100) / 100).toFixed(2);
            }, 100, false);
        };

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection