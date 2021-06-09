@extends('layouts.main')

@section('title')
<title>M3Force | Customers</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Complain</a></li>                    
    <li class="active">Customers</li>
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
                    <h3 class="panel-title"><strong>Customer Details</strong></h3>
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

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Contact No</label>
                                            <input type="text" id="contact_no" name="contact_no" ng-model="data.contact_no" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name="address" ng-model="data.address" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name="email" ng-model="data.email" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_customer_complain')).addClass('active');
        angular.element(document.querySelector('#sub_menu_new_customer_complain')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        name: {
                            required: true,
                            remote: {
                                url: base_url + '/customer_complain/validate_customer_name',
                                type: 'GET',
                                data: {
                                    name: function() {
                                      return scope.data.name;
                                    }
                                }
                            }
                        },
                        address: {
                            required: true
                        },
                        contact_no: {
                            required: true,
                            validContactNo: true
                        },
                        email: {
                            email: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        name: {
                            required: 'Name is required',
                            remote: 'Name already exist'
                        },
                        address: {
                            required: 'Address is required'
                        },
                        contact_no: {
                            required: 'Contact No is required',
                            validContactNo: 'Invalid number format'
                        },
                        email: {
                            email: 'Invalid email format'
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
        $scope.data = {
            id: 0,
            value: '',
            contact_type: {id:2, name:'Non Monitoring Customer'},
            business_type: {},
            code: '',
            contact_id: '',
            name: '',
            nic: '',
            address: '',
            contact_no: '',
            email: '',
            contact_person_1: '',
            contact_person_no_1: '',
            contact_person_2: '',
            contact_person_no_2: '',
            contact_person_3: '',
            contact_person_no_3: '',
            invoice_name: '',
            invoice_delivering_address: '',
            collection_address: '',
            invoice_email: '',
            vat_no: '',
            svat_no: '',
            region: {},
            collection_manager: {},
            monitoring_fee: '',
            service_mode: {},
            group: {},
            is_group: false,
            is_active: true,
            inv_months_1: [],
            inv_months_2: [],
            inv_months_3: [],
            taxes: [
                {id: 1, selected: true},
                {id: 3, selected: true}
            ]
        };
        $scope.resetCopy = angular.copy($scope.data);

        $scope.resetForm = function(){
            $scope.data = angular.copy($scope.resetCopy);

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            console.log($scope.data);
            if($scope.data.id == 0){
                $http.post(base_url + '/contact', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Contact Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $timeout(function () {
                        $window.location.href = base_url + '/customer_complain/new_customer_complain?type=1';
                    }, 1500, false);  
                });
            } else{
                $http.put(base_url + '/contact/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Contact Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $timeout(function () {
                        $window.location.href = base_url + '/customer_complain/new_customer_complain?type=1';
                    }, 1500, false);
                });
            }
        };
            
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