@extends('layouts.main')

@section('title')
<title>M3Force | Monitoring Customer Payment</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Collection</a></li>                    
    <li class="active">Monitoring Customer Payment</li>
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
                    <h3 class="panel-title"><strong>Payment Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><div><a ng-show="data.id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/monitoring_customer/print_monitoring_customer_payment?id=<%=data.id%>">Print</a></div></li>
                        <li><a href="{{ asset('monitoring_customer/monitoring_customer_detail?id='.$monitoring_customer_id)}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="monitoring_customer_id" name="monitoring_customer_id" ng-model="data.monitoring_customer_id" class="form-control" />
                                
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
                                    <h4 class="col-md-12" style="padding-top: 15px;">Payment Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Payment Mode</label>
                                            <select name="payment_mode" id="payment_mode" ng-options="option.name for option in payment_mode_array track by option.id" ng-model="data.payment_mode" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.payment_mode.id == 1 || data.payment_mode.id == 2">
                                        <div class="form-group">
                                            <label class="control-label">Collection Person</label>
                                            <select name="collection_person" id="collection_person" ng-options="option.name for option in collection_person_array track by option.id" ng-model="data.collection_person" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Receipt No</label>
                                            <input type="text" id="receipt_no" name="receipt_no" ng-model="data.receipt_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Receipt Date</label>
                                            <input type="text" id="receipt_date" name="receipt_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.receipt_date" is-open="receiptDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenReceiptDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Receipt Time</label>
                                            <input type="text" id="receipt_time" name="receipt_time" ng-model="data.receipt_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Amount</label>
                                            <input type="text" id="amount" name="amount" ng-model="data.amount" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.payment_mode.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Cheque No</label>
                                            <input type="text" id="cheque_no" name="cheque_no" ng-model="data.cheque_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.payment_mode.id == 3">
                                        <div class="form-group">
                                            <label class="control-label">Bank</label>
                                            <input type="text" id="bank" name="bank" ng-model="data.bank" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2" ng-show="data.payment_mode.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Realize Date</label>
                                            <input type="text" id="realize_date" name="realize_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.realize_date" is-open="realizeDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenRealizeDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_collection')).addClass('active');
        angular.element(document.querySelector('#sub_menu_monitoring_customer')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        payment_mode: {
                            required: true
                        },
                        collection_person: {
                            required: true
                        },
                        receipt_date: {
                            required: true,
                            date: true
                        },
                        receipt_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        amount: {
                            required: true,
                            number: true
                        },
                        cheque_no: {
                            required: function(element){
                                return $('#payment_mode').val() == 1;
                            }
                        },
                        bank: {
                            required: function(element){
                                return $('#payment_mode').val() == 3;
                            }
                        },
                        realize_date: {
                            date: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        payment_mode: {
                            required: 'Payment Mode is required'
                        },
                        collection_person: {
                            required: 'Collection Person is required'
                        },
                        receipt_date: {
                            required: 'Receipt Date is required',
                            date: 'Invalid date format'
                        },
                        receipt_time: {
                            required: 'Receipt Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        amount: {
                            required: 'Amount is required',
                            number: 'Invalid number format'
                        },
                        cheque_no: {
                            required: 'Cheque No is required'
                        },
                        bank: {
                            required: 'Bank is required'
                        },
                        realize_date: {
                            date: 'Invalid date format'
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
        $scope.data = [];
        $scope.payment_mode_array = [];
        $scope.collection_person_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.receiptDatePopup = {
            opened: false
        };        
        $scope.OpenReceiptDate = function () {
            $scope.receiptDatePopup.opened = !$scope.receiptDatePopup.opened;
        };
        
        $scope.realizeDatePopup = {
            opened: false
        };        
        $scope.OpenRealizeDate = function () {
            $scope.realizeDatePopup.opened = !$scope.realizeDatePopup.opened;
        };
        
        $http({
            method: 'GET',
            url: base_url + '/monitoring_customer/get_data'
        }).then(function successCallback(response) {
            var payment_mode_array = [];
            payment_mode_array.push({
                id: '',
                name: 'Select Payment Mode'
            });
            $.each(response.data.payment_modes, function (index, value) {
                payment_mode_array.push({
                    id: value.id,
                    name: value.name
                });
            }); 
            var collection_person_array = [];
            collection_person_array.push({
                id: '',
                name: 'Select Collection Person'
            });
            $.each(response.data.collection_persons, function (index, value) {
                collection_person_array.push({
                    id: value.id,
                    name: value.name
                });
            }); 

            $scope.payment_mode_array = payment_mode_array;
            $scope.collection_person_array = collection_person_array;
                
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
                id: <?php echo $monitoring_customer_payment_id ? $monitoring_customer_payment_id : 0; ?>,
                monitoring_customer_id: <?php echo $monitoring_customer_id ? $monitoring_customer_id : 0; ?>,
                payment_mode: $scope.payment_mode_array.length > 0 ? $scope.payment_mode_array[0] : {},
                collection_person: $scope.collection_person_array.length > 0 ? $scope.collection_person_array[0] : {},
                receipt_no: '',
                receipt_date: new Date(),
                receipt_time: hh+':'+mm,
                amount: '',
                cheque_no: '',
                bank: '',
                realize_date: '',
                remarks: ''
            };
            
            $http({
                method: 'GET',
                url: base_url + '/monitoring_customer/get_monitoring_customer_detail',
                params: {
                    id: $scope.data.monitoring_customer_id
                }
            }).then(function successCallback(response) {
                if(response.data.monitoring_customer){
                    $scope.customer_name = response.data.monitoring_customer.contact ? response.data.monitoring_customer.contact.name : '';
                    $scope.customer_address = response.data.monitoring_customer.contact ? response.data.monitoring_customer.contact.address : '';
                    $scope.customer_contact_no = response.data.monitoring_customer.contact ? response.data.monitoring_customer.contact.contact_no : '';
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        }, function errorCallback(response) {
            console.log(response);
        });

        $scope.resetForm = function(){   
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
                monitoring_customer_id: <?php echo $monitoring_customer_id ? $monitoring_customer_id : 0; ?>,
                payment_mode: $scope.payment_mode_array.length > 0 ? $scope.payment_mode_array[0] : {},
                collection_person: $scope.collection_person_array.length > 0 ? $scope.collection_person_array[0] : {},
                receipt_no: '',
                receipt_date: new Date(),
                receipt_time: hh+':'+mm,
                amount: '',
                cheque_no: '',
                bank: '',
                realize_date: '',
                remarks: ''
            };

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/monitoring_customer', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Monitoring Customer Payments',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.id = result.response ? result.data : 0;

                    $(".message_lable").remove();
                    $('.form-control').removeClass("error");
                    $('.form-control').removeClass("valid");
            
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/monitoring_customer/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Monitoring Customer Payments',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.id = result.response ? result.data : 0;

                    $(".message_lable").remove();
                    $('.form-control').removeClass("error");
                    $('.form-control').removeClass("valid");
            
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/monitoring_customer/find_monitoring_customer_payment',
                params: {
                    id: $scope.data.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var receipt_date_time = response.data.receipt_date_time.split(' ');
                    $scope.data = {
                        id: response.data.id,
                        monitoring_customer_id: response.data.monitoring_customer_id,
                        payment_mode: response.data.payment_mode ? {id: response.data.payment_mode.id, name: response.data.payment_mode.name} : {},
                        collection_person: response.data.collection_person ? {id: response.data.collection_person.id, name: response.data.collection_person.name} : {},
                        receipt_no: response.data.receipt_no,
                        receipt_date: receipt_date_time[0],
                        receipt_time: receipt_date_time[1],
                        amount: parseFloat(Math.round(response.data.amount * 100) / 100).toFixed(2),
                        cheque_no: response.data.cheque_no,
                        bank: response.data.bank,
                        realize_date: response.data.realize_date,
                        remarks: response.data.remarks
                    };   
                }
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