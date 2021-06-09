@extends('layouts.main')

@section('title')
<title>M3Force | Manage Users</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Admin</a></li>                    
    <li class="active">Manage Users</li>
</ul>
@endsection

@section('content')
<style>
    .grid {
        width:100%;
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
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" class="form-horizontal">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Manage</strong> Users</h3>
                    <ul class="panel-controls">
                        <li><button ng-click="reset_form()" data-toggle="modal" data-target="#dataModal" class="btn btn-primary">Add</button></li>
                        <li><button  ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button  ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div> 

<div id="dataModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-body">

            <div class="row" ng-controller="registerController">
                <div class="col-md-12">

                    <form autocomplete="off" id="registerForm" name="registerForm" class="form-horizontal" j-validate>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong>User</strong> Profile</h3>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body">                                                                        

                                <div class="row">
                                    
                                    <input type="hidden" id="user_id" name="user_id" ng-model="user.user_id" class="form-control" />
                                    <input type="hidden" id="user_value" name="user_value" ng-model="user.user_value" class="form-control" />

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
                                            <label class="col-md-4 control-label">Password</label>             
                                            <div class="col-md-8">
                                                <input type="password" id="password" name="password" ng-model="user.password" class="form-control" required="" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Confirm Password</label>             
                                            <div class="col-md-8">
                                                <input type="password" id="con_password" name="con_password" ng-model="user.con_password" class="form-control" required="" />
                                            </div>
                                        </div>   
                                        
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">User Group</label>  
                                            <div class="col-md-8">
                                                <label style="display: block;" ng-repeat="type in user.types">
                                                    <span data-toggle="tooltip" style="cursor: pointer;" title="<%= type.permission %>"><input type="checkbox" name="selectedTypes[]" ng-checked="type.selected" ng-model="type.selected" ng-change="validate_user_group()" />&nbsp;<%= type.name %>&nbsp;&nbsp;&nbsp;</span>
                                                </label> 
                                            </div>
                                            <div id="div_user_group_error">
                                                <span class="col-md-12 help-block pull-right" ng-show="user_group_error">User Group is required.</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                                    
                                <button type="submit" class="btn btn-primary pull-right" id="save_button">Save</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div> 

        </div>
    </div>
</div>

<script type="text/javascript">
    var submitForm;
    var valid_user_group = false;
    
    $('[data-toggle="tooltip"]').tooltip();    
    $('#div_user_group_error').hide();
    
    var myApp = angular.module('myModule', [
        'ngAnimate', 
        'ngTouch', 
        'ui.grid', 
        'ui.grid.selection', 
        'ui.grid.exporter',
        'ui.grid.pagination',
        'ui.grid.moveColumns', 
        'ui.grid.resizeColumns',
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
        angular.element(document.querySelector('#main_menu_admin')).addClass('active');
        angular.element(document.querySelector('#sub_menu_manage_users')).addClass('active');
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
                                url: base_url + '/user/validate_username',
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
                        password: {
                            required: true,
                            minlength: 6
                        },
                        con_password: {
                            required: true,
                            equalTo: "#password"
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
                        password: {
                            required: 'Password is required',
                            minlength: 'Password must have at least 6 letters'
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

    myApp.controller('registerController', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
        $scope.regex = '\\d{10}';
        $scope.user_group_error = false;
        
        $scope.user = {};
	$scope.files = [];
        $scope.image = 'user_login.png';
        $scope.image_source = base_url + '/assets/images/users/' + $scope.image;
              
        $http({
            method: 'GET',
            url: base_url + '/user/group_list'
        }).then(function successCallback(response) {
            var data_array = [];
            $.each(response.data, function (index, value) {
                data_array.push({
                    id: value.id,
                    name: value.name,
                    permission: value.permission,
                    selected: false
                });
            });
            $scope.type_array = data_array;
            $scope.user.types = data_array;
        }, function errorCallback(response) {
            console.log(response);
        });
        
        $http({
            method: 'GET',
            url: base_url + '/user/job_positions_list'
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
        
        $scope.validate_user_group = function(){
            valid_user_group = false;
            for(var i=0; i<$scope.user.types.length; i++){
                if($scope.user.types[i].selected == true){
                    valid_user_group = true;
                }
            }
            
            $scope.user_group_error = !valid_user_group; 
        };
        
        submitForm = function () {  
            $scope.validate_user_group();
            if($scope.user.user_id && valid_user_group){
                $scope.user.image = $scope.files[0];
                $('#save_button').prop('disabled', true);
                $http({
                    method  : 'POST',
                    url     : base_url + '/user/image_upload',
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
                    
                    $http.put(base_url + '/user/' + $scope.user.user_id, $scope.user).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'User Profile',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $rootScope.$emit("main_refesh", {});
                        $('#dataModal').modal('hide');
                    });
                });
            } else if(valid_user_group){
                $scope.user.image = $scope.files[0];
                $('#save_button').prop('disabled', true);
                $http({
                    method  : 'POST',
                    url     : base_url + '/user/image_upload',
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
                    $scope.user.image = data.image ? data.image  : $scope.image;

                    $http.post(base_url + '/user', $scope.user).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'User Profile',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $rootScope.$emit("main_refesh", {});
                        $('#dataModal').modal('hide');
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
        
        $rootScope.$on("reset_form", function(event, args){
           $scope.reset_form(args);
        });      
        
        $rootScope.$on("edit_form", function(event, args){
           $scope.edit_form(args);
        });
        
        $rootScope.$on("delete_form", function(event, args){
           $scope.delete_form(args);
        });
        
        $scope.reset_form = function(args) {
            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
                
            $scope.user_group_error = false;
                
            $scope.user = {};
            $scope.files = [];
            $scope.image = 'user_login.png';
            $scope.image_source = base_url + '/assets/images/users/' + $scope.image;

            $http({
                method: 'GET',
                url: base_url + '/user/group_list'
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    data_array.push({
                        id: value.id,
                        name: value.name,
                        permission: value.permission,
                        selected: false
                    });
                });
                $scope.type_array = data_array;
                $scope.user.types = data_array;
            }, function errorCallback(response) {
                console.log(response);
            });

            $http({
                method: 'GET',
                url: base_url + '/user/job_positions_list'
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
        };
        
        $scope.edit_form = function(args) {
            $scope.reset_form();
            $http({
                method: 'GET',
                url: base_url + '/user/find_user',
                params: {
                    id: args
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
                
                var data_array = [];
                if(response.data.user_group_permission){
                    for(var i=0; i<$scope.type_array.length; i++){
                        var selected = false;
                        $.each(response.data.user_group_permission, function (index, value) {
                            if($scope.type_array[i].id == value.user_group_id){
                                selected = true;
                            }
                        });
                        data_array.push({
                            id: $scope.type_array[i].id,
                            name: $scope.type_array[i].name,
                            permission: $scope.type_array[i].permission,
                            selected: selected
                        });
                    }
                }
        
                $scope.user.types = data_array;
                
                $scope.files = [];
                $scope.image = response.data.user_image;
                $scope.image_source = base_url + '/assets/images/users/' + $scope.image;
                
                $scope.registerForm.$setPristine();
                $scope.registerForm.$setUntouched();
                
                $('#dataModal').modal('show');
            }, function errorCallback(response) {
                console.log(response);
            });            
        };
        
        $scope.delete_form = function(args) {
            $scope.reset_form();
            $http({
                method: 'GET',
                url: base_url + '/user/find_user',
                params: {
                    id: args
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
                                
                var data_array = [];
                if(response.data.user_group_permission){
                    for(var i=0; i<$scope.type_array.length; i++){
                        var selected = false;
                        $.each(response.data.user_group_permission, function (index, value) {
                            if($scope.type_array[i].id == value.user_group_id){
                                selected = true;
                            }
                        });
                        data_array.push({
                            id: $scope.type_array[i].id,
                            name: $scope.type_array[i].name,
                            permission: $scope.type_array[i].permission,
                            selected: selected
                        });
                    }
                }
        
                $scope.user.types = data_array;
                
                $scope.files = [];
                $scope.image = response.data.user_image;
                $scope.image_source = base_url + '/assets/images/users/' + $scope.image;
                
                $scope.registerForm.$setPristine();
                $scope.registerForm.$setUntouched();
                
                $('#dataModal').modal('show');
                
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover " + response.data.username + " profile!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $http.delete(base_url + '/user/' + $scope.user.user_id, $scope.user).success(function (result) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.username + " profile has been deleted.", 
                                type: "success",
                                confirmButtonColor: "#9ACD32"
                            },
                            function(){
                                $('#dataModal').modal('hide');
                                $rootScope.$emit("main_refesh", {});
                            });
                        });
                    } else{
                        $('#dataModal').modal('hide');
                    }
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
    }]);

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants) {        
            $scope.gridOptions = {
                columnDefs: [
                    {field: 'id', visible: false},
                    {field: 'name'},
                    {field: 'contact_no'},
                    {field: 'email'},
                    {field: 'job_position'},
                    {
                        field: 'options', 
                        displayName: '', 
                        enableCellEdit: false,
                        enableFiltering: false, 
                        enableSorting: false, 
                        width: '10%', 
                        cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn" ng-click="grid.appScope.editUser(row)" ng-show="row.entity.id == 1 ? grid.appScope.login_id == 1 : true"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn" ng-click="grid.appScope.deleteUser(row)" ng-show="row.entity.id == 1 ? grid.appScope.login_id == 1 : true"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                    }
                ],
                enableCellEditOnFocus: true,
                enableRowSelection: false,
                enableRowHeaderSelection: false,
                paginationPageSizes: [10, 25, 50],
                paginationPageSize: 10,
                enableFiltering: false,
                enableSorting: true,
                enableFiltering: false,
                enableColumnResizing: true,
                exporterLinkLabel: 'get your csv here',
                exporterCsvFilename: 'Users.csv',
                onRegisterApi: function (gridApi) {
                    $scope.gridApi = gridApi;
                }
            };

            var fillter = function () {
                $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
                $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
            };

            $scope.export = function () {
                var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
                $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
            };
            
            $scope.toggleFiltering = function () {
                fillter();
            };
                
            $scope.editUser = function (row) {
                $rootScope.$emit("edit_form", row.entity.id);
            };

            $scope.deleteUser = function (row) {
                $rootScope.$emit("delete_form", row.entity.id);
            };
            
            $rootScope.$on("main_refesh", function(event, args){
                $scope.main_refresh(args);
            });

            $scope.main_refresh = function(args){
                document.getElementById('data_load').style.visibility = "visible";
                $('#div_user_group_error').show();
                $http({
                    method: 'GET',
                    url: base_url + '/user/users_list'
                }).then(function successCallback(response) {
                    $scope.login_id = response.data.login_id;
                    var data_array = [];
                    $.each(response.data.users, function (index, value) {
                        var name = value.first_name;
                        name += value.last_name ? ' ' + value.last_name : '';
                        data_array.push({
                            id: value.id,
                            name: name,
                            contact_no: value.contact_no,
                            email: value.email,
                            job_position: value.job_position ? value.job_position.name : null
                        });
                    });
                    $scope.gridOptions.data = data_array;
                    document.getElementById('data_load').style.visibility = "hidden";
                }, function errorCallback(response) {
                    console.log(response);
                });
            };
            
            $scope.main_refresh();
            
            $scope.reset_form = function(){
                $rootScope.$emit("reset_form", {});
            };
        }]);
</script>
@endsection