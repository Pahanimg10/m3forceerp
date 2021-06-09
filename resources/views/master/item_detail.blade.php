@extends('layouts.main')

@section('title')
<title>M3Force | Item Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Master</a></li>                    
    <li class="active">Item Details</li>
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
                    <h3 class="panel-title"><strong>Item Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('item')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="data_value" name="data_value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="data_value_model_no" name="data_value_model_no" ng-model="data.value_model_no" class="form-control" />

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Main Category</label>
                                            <select name="main_category" id="main_category" ng-options="option.name for option in main_category_array track by option.id" ng-model="data.main_category" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Sub Category</label>
                                            <select name="sub_category" id="sub_category" ng-options="option.name for option in sub_category_array track by option.id" ng-model="data.sub_category" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Purchase Type</label>
                                            <select name="purchase_type" id="purchase_type" ng-options="option.name for option in purchase_type_array track by option.id" ng-model="data.purchase_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Code</label>
                                            <input type="text" id="code" name="code" ng-model="data.code" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label class="control-label">Name</label>
                                            <input type="text" id="name" name="name" ng-model="data.name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Model No</label>
                                            <input type="text" id="model_no" name="model_no" ng-model="data.model_no" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Brand</label>
                                            <input type="text" id="brand" name="brand" ng-model="data.brand" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Origin</label>
                                            <input type="text" id="origin" name="origin" ng-model="data.origin" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Unit Type</label>
                                            <select name="unit_type" id="unit_type" ng-options="option.name for option in unit_type_array track by option.id" ng-model="data.unit_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Reorder Level</label>
                                            <input type="text" id="reorder_level" name="reorder_level" ng-model="data.reorder_level" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Rate</label>
                                            <input type="text" id="rate" name="rate" ng-model="data.rate" ng-disabled="data.id" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Serial</label><br/>
                                            <input id="is_serial" bs-switch emit-change="is_serial" ng-model="data.is_serial" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Warranty</label><br/>
                                            <input id="is_warranty" bs-switch emit-change="is_warranty" ng-model="data.is_warranty" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="control-label">Active</label><br/>
                                            <input id="is_active" bs-switch emit-change="is_active" ng-model="data.is_active" switch-active="true" switch-on-text="Yes" switch-off-text="No" type="checkbox" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_master')).addClass('active');
        angular.element(document.querySelector('#sub_menu_item')).addClass('active');
    });   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        main_category: {
                            required: true
                        },
                        sub_category: {
                            required: true
                        },
                        purchase_type: {
                            required: true
                        },
                        name: {
                            required: true,
                            remote: {
                                url: base_url + '/item/validate_item',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    name: function() {
                                      return scope.data.name;
                                    }
                                }
                            }
                        },
                        model_no: {
                            remote: {
                                url: base_url + '/item/validate_model_no',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value_model_no;
                                    },
                                    model_no: function() {
                                      return scope.data.model_no;
                                    }
                                }
                            }
                        },
                        unit_type: {
                            required: true
                        },
                        reorder_level: {
                            number: true
                        },
                        rate: {
                            required: true,
                            number: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        main_category: {
                            required: 'Main category is required'
                        },
                        sub_category: {
                            required: 'Sub category is required'
                        },
                        purchase_type: {
                            required: 'Purchase Type is required'
                        },
                        name: {
                            required: 'Name is required',
                            remote: 'Name already exist'
                        },
                        model_no: {
                            remote: 'Model No already exist'
                        },
                        unit_type: {
                            required: 'Unit Type is required'
                        },
                        reorder_level: {
                            number: 'Invalid number format'
                        },
                        region: {
                            required: 'Region is required'
                        },
                        rate: {
                            required: 'Rate is required',
                            number: 'Invalid number format'
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
        $scope.main_category_array = [];
        $scope.sub_category_array = [];
        $scope.purchase_type_array = [];
        $scope.unit_type_array = [];
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/item/get_data'
            }).then(function successCallback(response) {
                var main_category_array = [];
                main_category_array.push({
                    id: '',
                    name: 'Select Main Category'
                });
                var sub_category_array = [];
                sub_category_array.push({
                    id: '',
                    name: 'Select Sub Category'
                });
                var purchase_type_array = [];
                purchase_type_array.push({
                    id: '',
                    name: 'Select Purchase Type'
                });
                var unit_type_array = [];
                unit_type_array.push({
                    id: '',
                    name: 'Select Unit Type'
                });
                $.each(response.data.main_item_categories, function (index, value) {
                    main_category_array.push({
                        id: value.id,
                        name: value.name,
                        code: value.code
                    });
                }); 
                $.each(response.data.sub_item_categories, function (index, value) {
                    sub_category_array.push({
                        id: value.id,
                        name: value.name,
                        code: value.code
                    });
                }); 
                $.each(response.data.purchase_types, function (index, value) {
                    purchase_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 
                $.each(response.data.unit_types, function (index, value) {
                    unit_type_array.push({
                        id: value.id,
                        name: value.code
                    });
                }); 

                $scope.main_category_array = main_category_array;
                $scope.sub_category_array = sub_category_array;
                $scope.purchase_type_array = purchase_type_array;
                $scope.unit_type_array = unit_type_array;
                $scope.data = {
                    id: 0,
                    value: '',
                    value_model_no: '',
                    main_category: $scope.main_category_array.length > 0 ? $scope.main_category_array[0] : {},
                    sub_category: $scope.sub_category_array.length > 0 ? $scope.sub_category_array[0] : {},
                    purchase_type: $scope.purchase_type_array.length > 0 ? $scope.purchase_type_array[0] : {},
                    code: '',
                    name: '',
                    model_no: '',
                    brand: '',
                    origin: '',
                    unit_type: $scope.unit_type_array.length > 0 ? $scope.unit_type_array[0] : {},
                    reorder_level: 0,
                    rate: 0,
                    is_serial: false,
                    is_warranty: false,
                    is_active: true
                };
                $scope.resetCopy = angular.copy($scope.data);
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();

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
                $http.post(base_url + '/item', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Item Details',
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
                $http.put(base_url + '/item/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Item Details',
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
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/item/find_item',
                params: {
                    id: <?php echo $item_id ? $item_id : 0; ?>
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data = {
                        id: response.data.id,
                        value: response.data.name,
                        value_model_no: response.data.model_no,
                        main_category: response.data.main_item_category ? {id: response.data.main_item_category.id, name: response.data.main_item_category.name, code: response.data.main_item_category.code} : {},
                        sub_category: response.data.sub_item_category ? {id: response.data.sub_item_category.id, name: response.data.sub_item_category.name, code: response.data.sub_item_category.code} : {},
                        purchase_type: response.data.purchase_type ? {id: response.data.purchase_type.id, name: response.data.purchase_type.name} : {},
                        code: response.data.code,
                        name: response.data.name,
                        model_no: response.data.model_no,
                        brand: response.data.brand,
                        origin: response.data.origin,
                        unit_type: response.data.unit_type ? {id: response.data.unit_type.id, name: response.data.unit_type.code} : {},
                        reorder_level: response.data.reorder_level,
                        rate: parseFloat(Math.round(response.data.rate * 100) / 100).toFixed(2),
                        is_serial: response.data.is_serial == 1 ? true : false,
                        is_warranty: response.data.is_warranty == 1 ? true : false,
                        is_active: response.data.is_active == 1 ? true : false
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
    }]);
</script>
@endsection