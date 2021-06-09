@extends('layouts.main')

@section('title')
<title>M3Force | Change Password</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Admin</a></li>                    
    <li class="active">Change Password</li>
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
                    <h3 class="panel-title"><strong>Change Password</strong></h3>
                    <ul class="panel-controls">
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                    
                            <div class="panel panel-default">
                                <div class="panel-body">                                                                        

                                    <div class="row">

                                        <div class="col-md-6 col-md-offset-3">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Old Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="old_password" name="old_password" ng-model="data.old_password" class="form-control" required="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-md-offset-3" style="margin-top: 15px;">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">New Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="new_password" name="new_password" ng-model="data.new_password" class="form-control" required="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-md-offset-3" style="margin-top: 15px;">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Confirm Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="con_password" name="con_password" ng-model="data.con_password" class="form-control" required="" />
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="panel-footer">                                  
                                    <button type="submit" class="btn btn-primary pull-right" id="save_button">Save</button>
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
        angular.element(document.querySelector('#main_menu_dashboard')).addClass('active');
    });
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        old_password: {
                            required: true,
                            remote: {
                                url: base_url + '/dashboard/validate_old_password',
                                type: 'GET',
                                data: {
                                    old_password: function() {
                                      return scope.data.old_password;
                                    }
                                }
                            }
                        },
                        new_password: {
                            required: true,
                            minlength: 6
                        },
                        con_password: {
                            required: true,
                            equalTo: "#new_password"
                        },
                        errorClass:'error'
                    },
                    messages: {
                        old_password: {
                            required: 'Old Password is required',
                            remote: 'Invalid Old Password'
                        },
                        new_password: {
                            required: 'New Password is required',
                            minlength: 'New Password must have at least 6 letters'
                        },
                        con_password: {
                            required: 'Confirm Password is required',
                            equalTo: 'Confirm Password does not match'
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
            $('#save_button').prop('disabled', true);
        submitForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/dashboard/update_new_password',
                params: $scope.data
            }).then(function successCallback(result) {
                $('#save_button').prop('disabled', false);
                $.pnotify && $.pnotify({
                    title: 'Change Password',
                    text: result.data.message,
                    type: result.data.response ? 'success' :'error',
                    nonblock: true,
                    history: false,
                    delay: 6e3,
                    hide: true
                });
                setTimeout(function(){ 
                    window.location.href = base_url+'/home';
                }, 1000);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
    }]);
</script>
@endsection