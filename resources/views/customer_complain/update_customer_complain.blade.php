@extends('layouts.main')

@section('title')
<title>M3Force | Customer Complain Status</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Complain</a></li>                    
    <li class="active">Customer Complain Status</li>
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
                    <h3 class="panel-title"><strong>Customer Complain Status</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('customer_complain/ongoing_customer_complain')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="value" name="value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="customer_complain_id" name="customer_complain_id" ng-model="data.customer_complain_id" class="form-control" />
                                
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
                                    <h4 class="col-md-12" style="padding-top: 15px;">Update Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Update Date</label>
                                            <input type="text" id="update_date" name="update_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.update_date" is-open="updateDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenUpdateDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Update Time</label>
                                            <input type="text" id="update_time" name="update_time" ng-model="data.update_time" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Update Status</label>
                                            <select name="update_status" id="update_status" ng-options="option.name for option in update_status_array track by option.id" ng-model="data.update_status" class="form-control" ></select>
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

                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div ui-grid="gridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
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
        angular.element(document.querySelector('#main_menu_tech_response')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_tech_response')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        update_date: {
                            required: true,
                            date: true
                        },
                        update_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        update_status: {
                            required: true,
                            remote: {
                                url: base_url + '/customer_complain/validate_customer_complain_status',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    customer_complain_id: function() {
                                      return scope.data.customer_complain_id;
                                    },
                                    update_status: function() {
                                      return scope.data.update_status.id;
                                    }
                                }
                            }
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        update_date: {
                            required: 'Update Date is required',
                            date: 'Invalid date format'
                        },
                        update_time: {
                            required: 'Update Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        update_status: {
                            required: 'Update Status is required',
                            remote: 'Update Status already exist'
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
        $scope.update_status_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.updateDatePopup = {
            opened: false
        };        
        $scope.OpenUpdateDate = function () {
            $scope.updateDatePopup.opened = !$scope.updateDatePopup.opened;
        };
        
        $('#update_time').mask('00:00');
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/get_data'
            }).then(function successCallback(response) {
                var update_status_array = [];
                update_status_array.push({
                    id: '',
                    name: 'Select Update Status'
                });
                $.each(response.data, function (index, value) {
                    update_status_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.update_status_array = update_status_array;
                
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
                    type: 1,
                    id: 0,
                    value: '',
                    customer_complain_id: <?php echo $customer_complain_id ? $customer_complain_id : 0; ?>,
                    update_date: new Date(),
                    update_time: hh+':'+mm,
                    update_status: $scope.update_status_array.length > 0 ? $scope.update_status_array[0] : {},
                    remarks: ''
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/customer_complain/find_customer_complain',
                    params: {
                        id: <?php echo $customer_complain_id ? $customer_complain_id : 0; ?>
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
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'permission', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'show_update', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '10%', 
                    cellTemplate: '<div class="text-center" ng-show="row.entity.show_update == 1"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'update_date_time', 
                    displayName: 'Update Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'update_status', 
                    displayName: 'Update Status', 
                    width: '25%', 
                    enableCellEdit: false
                },
                {
                    field: 'remarks', 
                    displayName: 'Remarks', 
                    width: '40%', 
                    enableCellEdit: false
                },
                {
                    field: 'log_user', 
                    displayName: 'Log User', 
                    width: '10%', 
                    enableCellEdit: false
                }
            ],
            showColumnFooter: true,
            enableCellEditOnFocus: true,
            enableRowSelection: false,
            enableRowHeaderSelection: false,
            paginationPageSizes: [10, 25, 50],
            paginationPageSize: 10,
            enableFiltering: false,
            enableSorting: true,
            enableCellEdit: false,
            enableColumnResizing: true,
            exporterLinkLabel: 'get your csv here',
            onRegisterApi: function (gridApi) {
                $scope.gridApi = gridApi;
            }
        };

        $scope.export = function () {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.gridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function () {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.editRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/find_customer_complain_status',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var update_date_time = response.data.update_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.customer_complain_status ? response.data.customer_complain_status.id : '',
                        customer_complain_id: response.data.customer_complain_id,
                        update_date: update_date_time[0],
                        update_time: update_date_time[1],
                        update_status: response.data.customer_complain_status ? {id:response.data.customer_complain_status.id, name:response.data.customer_complain_status.name} : {},
                        remarks: response.data.remarks
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $scope.resetForm();
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/find_customer_complain_status',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var update_date_time = response.data.update_date_time.split(' ');
                    $scope.data = {
                        type: 1,
                        id: response.data.id,
                        value: response.data.customer_complain_status ? response.data.customer_complain_status.id : '',
                        customer_complain_id: response.data.customer_complain_id,
                        update_date: update_date_time[0],
                        update_time: update_date_time[1],
                        update_status: response.data.customer_complain_status ? {id:response.data.customer_complain_status.id, name:response.data.customer_complain_status.name} : {},
                        remarks: response.data.remarks
                    };
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + response.data.customer_complain_status.name + "</strong> customer complain status!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/customer_complain/'+$scope.data.id, {params: {type: $scope.data.type}}).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: response.data.customer_complain_status.name + " customer complain status has been deleted.", 
                                type: "success",
                                confirmButtonColor: "#9ACD32"
                            });
                            $scope.resetForm();
                            $scope.main_refresh();
                        });
                    });
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

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
            if($scope.data.update_status.id == 4){
                swal({
                    title: "Are you sure?",
                    text: "This will <strong>close</strong> the complain !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, proceed!",
                    closeOnConfirm: true
                },
                function(){
                    $scope.saveForm();
                });
            } else{
                $scope.saveForm();
            }
        };

        $scope.saveForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/customer_complain', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Customer Complain Status',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    if(result.response && $scope.data.update_status.id == 4){
                        $window.location.href = base_url + '/customer_complain/ongoing_customer_complain';
                    } else{
                        $scope.resetForm();
                        $scope.main_refresh();
                    }
                });
            } else{
                $http.put(base_url + '/customer_complain/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Customer Complain Status',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    if(result.response && $scope.data.update_status.id == 4){
                        $window.location.href = base_url + '/customer_complain/ongoing_customer_complain';
                    } else{
                        $scope.resetForm();
                        $scope.main_refresh();
                    }
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.customer_name+' Customer Complain Status.csv';
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/customer_complain_status_list',
                params: {
                    customer_complain_id: <?php echo $customer_complain_id ? $customer_complain_id : 0; ?>
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.customer_complain_status, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        show_update: value.customer_complain_status ? value.customer_complain_status.show_update : 0,
                        update_date_time: value.update_date_time,
                        update_status: value.customer_complain_status ? value.customer_complain_status.name : '',
                        remarks: value.remarks,
                        log_user: value.user ? value.user.first_name : ''
                    });
                });    
                $scope.gridOptions.data = data_array;
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