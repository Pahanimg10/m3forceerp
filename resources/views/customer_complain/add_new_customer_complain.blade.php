@extends('layouts.main')

@section('title')
<title>M3Force | New Complain</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Complain</a></li>                    
    <li class="active">New Complain</li>
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
                    <h3 class="panel-title"><strong>New Complain</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><a href="{{ asset('customer_complain/new_customer_complain')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="id" name="id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="value" name="value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="contact_id" name="contact_id" ng-model="data.contact_id" class="form-control" />
                                
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
                                    <h4 class="col-md-12" style="padding-top: 15px;">Complain Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Complain No</label>
                                            <input type="text" id="complain_no" name="complain_no" ng-model="data.complain_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Record Date</label>
                                            <input type="text" id="record_date" name="record_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.record_date" is-open="recordDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenRecordDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Record Time</label>
                                            <input type="text" id="record_time" name="record_time" ng-model="data.record_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Complain Types</label>
                                            <select name="complain_types" id="complain_types" ng-options="option.name for option in complain_types_array track by option.id" ng-model="data.complain_types" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Person Responsible</label>
                                            <select name="person_responsibles" id="person_responsibles" ng-options="option.name for option in person_responsibles_array track by option.id" ng-model="data.person_responsibles" class="form-control" ></select>
                                        </div>
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
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Reported Person</label>
                                            <input type="text" id="reported_person" name="reported_person" ng-model="data.reported_person" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Reported Contact No </label>
                                            <input type="text" id="reported_contact_no" name="reported_contact_no" ng-model="data.reported_contact_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Reported Email</label>
                                            <input type="text" id="reported_email" name="reported_email" ng-model="data.reported_email" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_customer_complain')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_customer_complain')).addClass('active');
    });  
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        record_date: {
                            required: true,
                            date: true
                        },
                        record_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        complain_types: {
                            required: true,
                            remote: {
                                url: base_url + '/customer_complain/validate_complain_type',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    contact_id: function() {
                                      return scope.data.contact_id;
                                    },
                                    complain_type_id: function() {
                                      return scope.data.complain_types.id;
                                    }
                                }
                            }
                        }, 
                        person_responsibles: {
                            required: true
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        record_date: {
                            required: 'Update Date is required',
                            date: 'Invalid date format'
                        },
                        record_time: {
                            required: 'Update Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        complain_types: {
                            required: 'Complain Type is required',
                            remote: 'Customer Complain already recorded'
                        },
                        person_responsibles: {
                            required: 'Person Responsible is required'
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
        $scope.complain_types_array = [];
        $scope.person_responsibles_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.recordDatePopup = {
            opened: false
        };        
        $scope.OpenRecordDate = function () {
            $scope.recordDatePopup.opened = !$scope.recordDatePopup.opened;
        };
        
        $('#record_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/get_complain_data'
            }).then(function successCallback(response) {
                var complain_types_array = [];
                complain_types_array.push({
                    id: '',
                    name: 'Select Complain Types'
                });
                $.each(response.data.complain_types, function (index, value) {
                    complain_types_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  
                var person_responsibles_array = [];
                person_responsibles_array.push({
                    id: '',
                    name: 'Select Person Responsible'
                });
                $.each(response.data.person_responsibles, function (index, value) {
                    person_responsibles_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 

                $scope.complain_types_array = complain_types_array;
                $scope.person_responsibles_array = person_responsibles_array;
                
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
                    type: 0,
                    id: 0,
                    value: '',
                    contact_id: <?php echo $contact_id ? $contact_id : 0; ?>,
                    complain_no: '',
                    record_date: new Date(),
                    record_time: hh+':'+mm,
                    complain_types: $scope.complain_types_array.length > 0 ? $scope.complain_types_array[0] : {},
                    person_responsibles: $scope.person_responsibles_array.length > 0 ? $scope.person_responsibles_array[0] : {},
                    remarks: '',
                    reported_person: '',
                    reported_contact_no: '',
                    reported_email: ''
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/contact/find_contact',
                    params: {
                        id: <?php echo $contact_id ? $contact_id : 0; ?>
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.customer_name = response.data.name;
                        $scope.customer_address = response.data.address;
                        $scope.customer_contact_no = response.data.contact_no;
                        $scope.data.reported_person = response.data.contact_person_1;
                        $scope.data.reported_contact_no = response.data.contact_person_no_1;
                        $scope.data.reported_email = response.data.email;
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();

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
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/customer_complain', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'New Customer Complain',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $timeout(function () {
                        $window.location.href = base_url + '/customer_complain/ongoing_customer_complain';
                    }, 1500, false);
                });
            } else{
                $http.put(base_url + '/customer_complain/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'New Customer Complain',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $timeout(function () {
                        $window.location.href = base_url + '/customer_complain/ongoing_customer_complain';
                    }, 1500, false);
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/find_customer_complain',
                params: {
                    id: <?php echo $customer_complain_id ? $customer_complain_id : 0; ?>
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var record_date_time = response.data.record_date_time.split(' ');
                    $scope.data = {
                        type: 0,
                        id: response.data.id,
                        value: response.data.complain_type ? response.data.complain_type.id : '',
                        contact_id: response.data.contact_id,
                        complain_no: response.data.complain_no,
                        record_date: record_date_time[0],
                        record_time: record_date_time[1],
                        complain_types: response.data.complain_type ? {id:response.data.complain_type.id, name:response.data.complain_type.name} : {},
                        person_responsibles: response.data.person_responsible ? {id:response.data.person_responsible.id, name:response.data.person_responsible.name} : {},
                        remarks: response.data.remarks,
                        reported_person: response.data.reported_person,
                        reported_contact_no: response.data.reported_contact_no,
                        reported_email: response.data.reported_email
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