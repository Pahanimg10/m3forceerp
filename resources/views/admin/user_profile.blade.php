@extends('layouts.main')

@section('title')
<title>M3Force | User Profile</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Admin</a></li>                    
    <li class="active">User Profile</li>
</ul>
@endsection

@section('content')
<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>User Profile</strong></h3>
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

                                        <input type="hidden" id="user_id" name="user_id" ng-model="user.user_id" class="form-control" />
                                        <input type="hidden" id="user_value" name="user_value" ng-model="user.user_value" class="form-control" />
                                        <input type="hidden" id="types" name="types" ng-model="user.types" class="form-control" />

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">User Image</label><br/>
                                                <img ng-src="<%= image_source %>" width="100%" alt="Image preview...">
                                                <input ng-model="user.image" type="file" accept="image/*" onchange="angular.element(this).scope().uploadedFile(this)" />
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">First Name</label>             
                                                <div class="col-md-8">
                                                    <input type="text" id="first_name" name="first_name" placeholder="John" ng-model="user.first_name" class="form-control" required="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Last Name</label>             
                                                <div class="col-md-8">
                                                    <input type="text" id="last_name" name="last_name" placeholder="Anderson" ng-model="user.last_name" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Contact No.</label>             
                                                <div class="col-md-8">
                                                    <input type="text" id="contact_no" name="contact_no" placeholder="0712345678" ng-model="user.contact_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Email</label>             
                                                <div class="col-md-8">
                                                    <input type="text" id="email" name="email" placeholder="john@example.com" ng-model="user.email" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Job Position</label>             
                                                <div class="col-md-8">
                                                    <select name="job_position" id="job_position" ng-options="option.name for option in user.availableOptions track by option.id" ng-model="user.job_position" class="form-control" required="">
                                                        <option value="">Select Job Position</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Username</label>             
                                                <div class="col-md-8">
                                                    <input type="text" id="username" name="username" placeholder="John" ng-model="user.username" class="form-control" required="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Old Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="old_password" name="old_password" ng-model="user.old_password" class="form-control" required="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">New Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="new_password" name="new_password" ng-model="user.new_password" class="form-control" required="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Confirm Password</label>             
                                                <div class="col-md-8">
                                                    <input type="password" id="con_password" name="con_password" ng-model="user.con_password" class="form-control" required="" />
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
                        first_name: {
                            required: true
                        },
                        email: {
                            email: true
                        },
                        job_position: {
                            required: true
                        },
                        username: {
                            required: true,
                            remote: {
                                url: base_url + '/dashboard/validate_username',
                                type: 'GET',
                                data: {
                                    old_value: function() {
                                      return scope.user.user_value;
                                    },
                                    username: function() {
                                      return scope.user.username;
                                    }
                                }
                            }
                        },
                        old_password: {
                            required: true,
                            remote: {
                                url: base_url + '/dashboard/validate_old_password',
                                type: 'GET',
                                data: {
                                    old_password: function() {
                                      return scope.user.old_password;
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
                        first_name: {
                            required: 'First Name is required',
                        },
                        email: {
                            email: 'Invalid Email'
                        },
                        job_position: {
                            required: 'Job Position is required'
                        },
                        username: {
                            required: 'Username is required',
                            remote: 'Username already exist'
                        },
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

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', '$timeout', '$window', function ($scope, $http, $rootScope, uiGridConstants, $timeout, $window) {         
        $scope.user = {};
	$scope.files = [];
        $scope.image = 'user_login.png';
        $scope.image_source = base_url + '/assets/images/users/' + $scope.image;
        
        $http({
            method: 'GET',
            url: base_url + '/dashboard/job_positions_list'
        }).then(function successCallback(response) {
            var data_array = [];
            $.each(response.data, function (index, value) {
                data_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            $scope.user.availableOptions = data_array;
        }, function errorCallback(response) {
            console.log(response);
        });
        
        submitForm = function () {           
            if($scope.user.user_id){
                $scope.user.image = $scope.files[0];
                $('#save_button').prop('disabled', true);
                $http({
                    method  : 'POST',
                    url     : base_url + '/dashboard/image_upload',
                    processData: false,
                    transformRequest: function (data) {
                        var formData = new FormData();
                        formData.append("image", $scope.user.image);  
                        return formData;  
                    },  
                    data : $scope.user,
                    headers: {
                           'Content-Type': undefined
                    }
                }).success(function(data){
                    $scope.user.image = data.image ? data.image : $scope.image;

                    $http({
                        method: 'POST',
                        url: base_url + '/dashboard/update_user_profile',
                        data: $scope.user
                    }).then(function successCallback(result) {
                        $('#save_button').prop('disabled', false);
                        
                        $(".message_lable").remove();
                        $('.form-control').removeClass("error");
                        $('.form-control').removeClass("valid");
            
                        $.pnotify && $.pnotify({
                            title: 'User Profile',
                            text: result.data.message,
                            type: result.data.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        
                        $("#menuController").load($window.location.href + " #menuController" );

                        $timeout(function () {
                            $window.location.href = base_url+'/home';
                        }, 1000);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                });
            }
        };

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
        
        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/dashboard/find_user',
                params: {
                    id: <?php echo session()->get('users_id'); ?>
                }
            }).then(function successCallback(response) {                
                $scope.user.user_id = response.data.id;          
                $scope.user.user_value = response.data.username;
                $scope.user.first_name = response.data.first_name;
                $scope.user.last_name = response.data.last_name;
                $scope.user.contact_no = response.data.contact_no;
                $scope.user.email = response.data.email;     
                $scope.user.job_position = response.data.job_position ? {id: response.data.job_position.id, name: response.data.job_position.name} : {};
                $scope.user.username = response.data.username;
                
                $scope.user.old_password = null;
                $scope.user.new_password = null;
                $scope.user.con_password = null;
                
                $scope.files = [];
                $scope.image = response.data.user_image;
                $scope.image_source = base_url + '/assets/images/users/' + $scope.image;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1000);
    }]);
</script>
@endsection