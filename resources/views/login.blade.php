<!DOCTYPE html>
<html lang="en" class="body-full-height" ng-app="myModule">
    <head>        
        <!-- META SECTION -->
        <title>M3Force | Welcome</title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico')}}" />
        <!-- END META SECTION -->

        <!-- CSS INCLUDE -->
        <link rel="stylesheet" type="text/css" id="theme" href="{{ asset('css/theme-default.css') }}"/>
        
        <!-- EOF CSS INCLUDE --> 
        <style>
            .help-block{
                color: red; 
                text-align: right;
            }
        </style>                               
    </head>
    <body ng-controller="myController">
        <div id="load"></div>
        <div class="login-container">

            <div class="login-box animated fadeInDown">
                <div class="login-logo"></div>
                <div class="login-body">
                    <div class="login-title">
                        <p class="text-center"><strong>Welcome To ERP System</strong></p>
                        <p>Please Login</p>
                    </div>
                    
                    <form autocomplete="off" id="loginForm" name="loginForm" class="form-horizontal" j-validate>
                        <div class="form-group" ng-show="show_username">
                            <div class="col-md-12">
                                <input type="text" id="username" name="username" placeholder="Username" ng-model="user.username" focus-me="focus_username" class="form-control" required="" />
                            </div>
                        </div>
                        <div id="div_user" class="form-group" ng-show="!show_username">
                            <div class="col-md-5">
                                <img ng-src="{{ asset('assets/images/users/<%= user_image %>') }}" alt="<%= name %>" class="img-circle" style="width: 50%; float: right;" />
                            </div>
                            <div class="col-md-7" style="margin-top: 10px; margin-left: 0; padding-left: 0;">
                                <p class=" login-title text-center" style="margin-bottom: 0; padding-bottom: 0; font-size: 16px;"><strong><%= name %></strong></p>
                                <p class=" login-title text-center" style="margin-top: 0; padding-top: 0; font-size: 12px;"><%= job_position %></p>
                            </div>
                        </div>
                        <div class="form-group" ng-show="show_password">
                            <div class="col-md-12">
                                <input type="password" id="password" name="password" placeholder="Password" ng-model="user.password" focus-me="focus_password" class="form-control" required="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <a ng-show="!show_username" ng-click="reset_username()" class="btn btn-link btn-block">Use Different Login Details</a>
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="button_next" ng-click="check_username()" ng-show="show_username" class="btn btn-info btn-block">Next</button>
                                <button type="button" id="button_login" ng-click="submitForm()" ng-show="!show_username" class="btn btn-info btn-block">Login</button>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="login-footer">
                    <div class="text-center">
                        &copy; 2018 M3Force (PVT) Ltd
                    </div>
                </div>
            </div>

        </div>

        <!-- JavaScripts -->
        <script type="text/javascript" src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/angular-1.5.0.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/jquery.validate-1.12.0.min.js') }}"></script>
        
        <script type="text/javascript">
            var base_url = '<?php echo URL::to('/'); ?>';
        </script>
        
        <script type="text/javascript">
            var submitForm;
            
            var myApp = angular.module('myModule', []).config(function ($interpolateProvider) {
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
        
            myApp.directive('jValidate', function () {
                return {
                    link: function (scope, element, attr) {
                        element.validate({
                            rules: {
                                username: {
                                    required: true,
                                    remote: {
                                        url: base_url + '/validateUsername',
                                        type: 'GET',
                                        data: {
                                            username: function() {
                                              return scope.user.username;
                                            }
                                        }
                                    }
                                },
                                password: {
                                    required: true,
                                    remote: {
                                        url: base_url + '/validateLogin',
                                        type: 'GET',
                                        data: {
                                            username: function() {
                                              return scope.user.username;
                                            },
                                            password: function() {
                                              return scope.user.password;
                                            }
                                        }
                                    }
                                },
                                errorClass:'error'
                            },
                            messages: {
                                username: {
                                    required: 'Username is required',
                                    remote: 'Username does not exist'
                                },
                                password: {
                                    required: 'Password is required',
                                    remote: 'Invalid Password'
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

            myApp.directive('focusMe', function($timeout) {
              return {
                link: function(scope, element, attrs) {
                  scope.$watch(attrs.focusMe, function(value) {
                    if(value === true) { 
                      //$timeout(function() {
                        element[0].focus();
                        scope[attrs.focusMe] = false;
                      //});
                    }
                  });
                }
              };
            });
            
            myApp.controller('myController',['$http', '$scope', '$window', '$timeout', function ($http, $scope, $window, $timeout) {                        
                // function to submit the form after all validation has occurred
                
                $('#div_user').hide();
                $scope.user = {};
                $scope.show_username = true;
                $scope.show_password = false;
                
                $timeout(function() { 
                    $('#username').focus(); 
                }, 100, false);
                
                $('#username').keyup(function(event){
                    if(event.keyCode == 13){
                        $('#button_next').click();
                    }
                });
                
                $('#password').keyup(function(event){
                    if(event.keyCode == 13){
                        $('#button_login').click();
                    }
                });
                
                $scope.check_username = function(){
                    if($('#loginForm').valid()){
                        $http({
                            method: 'GET',
                            url: base_url + '/checkUsername',
                            params: {
                                username: $scope.user.username
                            }
                        }).then(function successCallback(response) {
                            if(response.data){
                                $('#div_user').show();
                                $scope.show_username = false;
                                $scope.show_password = true;
                
                                $scope.user_image = response.data.user_image;
                                $scope.name = response.data.first_name+' '+response.data.last_name;
                                $scope.job_position = response.data.job_position ? response.data.job_position.name : null;
                
                                $timeout(function() { 
                                    $('#password').focus(); 
                                }, 100, false);
                            }
                        }, function errorCallback(response) {
                            console.log(response);
                        });
                    }
                };
                
                $scope.reset_username = function(){
                    $('#div_user').hide();
                    $scope.user.username = null;
                    $scope.user.password = null;
                    $scope.show_username = true;
                    $scope.show_password = false;
                    $scope.user_image = null;
                    $scope.name = null;
                    
                    $timeout(function() { 
                        $('#username').focus(); 
                    }, 100, false);
                };

                $scope.submitForm = function(){
                    $('#loginForm').submit();
                };
            
                submitForm = function () {
                    if($('#loginForm').valid()){
                        $window.location.href = 'home?type=1';
                    }
                };
            }]);
        </script>

    </body>
</html>






