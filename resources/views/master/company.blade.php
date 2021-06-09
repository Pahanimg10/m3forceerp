@extends('layouts.main')

@section('title')
<title>M3Force | Company</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">Company</li>
</ul>
@endsection

@section('content')
<style>
    .help-block{
        color: red; 
        text-align: right;
    }
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Company</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                    
                            <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Company Image</label><br/>
                                    <img ng-src="<%= image_source %>" width="100%" alt="Image preview...">
                                    <input ng-model="data.image" type="file" accept="image/*" onchange="angular.element(this).scope().uploadedFile(this)" />
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Company Name</label>             
                                            <div class="col-md-10">
                                                <input type="text" id="company_name" name="company_name" placeholder="Forwardair (PVT) Ltd" ng-model="data.company_name" class="form-control" required="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Phone No.</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="phone_number" name="phone_number" placeholder="0114766400" ng-model="data.phone_number" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Fax No.</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="fax_number" name="fax_number" placeholder="0114766402" ng-model="data.fax_number" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Email</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="email" name="email" placeholder="info@forwardair.biz" ng-model="data.email" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Web</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="website" name="website" placeholder="https://www.forwardair.biz" ng-model="data.website" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Address</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="address_line_1" name="address_line_1" placeholder="No. 67/1" ng-model="data.address_line_1" class="form-control" />
                                            </div> 
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label"></label>             
                                            <div class="col-md-8">
                                                <input type="text" id="address_line_2" name="address_line_2" placeholder="Hudson Road" ng-model="data.address_line_2" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label"></label>             
                                            <div class="col-md-8">
                                                <input type="text" id="address_line_3" name="address_line_3" placeholder="Colombo 03" ng-model="data.address_line_3" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Reg No.</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="reg_number" name="reg_number" placeholder="PV 102134" ng-model="data.reg_number" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">SVAT</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="svat" name="svat" placeholder="SVAT007514" ng-model="data.svat" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">VAT</label>             
                                            <div class="col-md-8">
                                                <input type="text" id="vat" name="vat" placeholder="174021341-7000" ng-model="data.vat" class="form-control" />
                                            </div>
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
        'ngAnimate', 
        'ngTouch', 
        'ui.grid', 
        'ui.grid.selection', 
        'ui.grid.exporter',
        'ui.grid.pagination',
        'ui.grid.moveColumns', 
        'ui.grid.cellNav']).config(function ($interpolateProvider) {
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
        angular.element(document.querySelector('#main_menu_master')).addClass('active');
        angular.element(document.querySelector('#sub_menu_company')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        company_name: {
                            required: true
                        },
                        email: {
                            email: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        company_name: {
                            required: 'Company Name is required'
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

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', function ($scope, $http, $rootScope, uiGridConstants) {        
        $scope.regex = '\\d{10}';
	$scope.files = [];
        $scope.image = 'm3force.jpg';
        $scope.image_source = base_url + '/assets/images/company/' + $scope.image;        

        $scope.uploadedFile = function(element) {
            $scope.currentFile = element.files[0];
            var reader = new FileReader();

            reader.onload = function(event) {
              $scope.image_source = event.target.result
              $scope.$apply(function($scope) {
                $scope.files = element.files;
              });
            }
            reader.readAsDataURL(element.files[0]);
        }; 
        
        $scope.submitForm = function(){
            $('#dataForm').submit();
        };
        
        submitForm = function(){
            $scope.data.image = $scope.files[0];
            $('#save_button').prop('disabled', true);
            $http({
                method  : 'POST',
                url     : base_url + '/company/image_upload',
                processData: false,
                transformRequest: function (data) {
                    var formData = new FormData();
                    formData.append("image", $scope.data.image);  
                    return formData;  
                },  
                data : $scope.data,
                headers: {
                       'Content-Type': undefined
                }
            }).success(function(data){
                $scope.data.image = data.image ? data.image : $scope.image;

                $http.put(base_url + '/company/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    
                    $.pnotify && $.pnotify({
                        title: 'Company',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    $(".message_lable").remove();
                    $('.form-control').removeClass("error");
                    $('.form-control').removeClass("valid");
                    $scope.main_refresh();
                });
            });
        };

        $scope.main_refresh = function(args){
            $http({
                method: 'GET',
                url: base_url + '/company/find_company',
                params:{
                    id: 1
                }
            }).then(function successCallback(response) {
                $scope.files = [];
                $scope.image = response.data.company_image;
                $scope.image_source = base_url + '/assets/images/company/' + $scope.image;
                
                $scope.data = {
                    id: response.data.id,
                    company_name: response.data.company_name,
                    phone_number: response.data.phone_number,
                    fax_number: response.data.fax_number,
                    email: response.data.email,
                    website: response.data.website,
                    address_line_1: response.data.address_line_1,
                    address_line_2: response.data.address_line_2,
                    address_line_3: response.data.address_line_3,
                    reg_number: response.data.reg_number,
                    svat: response.data.svat,
                    vat: response.data.vat
                };
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();
    }]);
</script>
@endsection