@extends('layouts.main')

@section('title')
<title>M3Force | Good Request Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Stock</a></li>                    
    <li class="active">Good Request Details</li>
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
                    <h3 class="panel-title"><strong>Good Request Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.good_request_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Post</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><div><a ng-show="data.good_request_id" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/good_request/print_good_request?id=<%=data.good_request_id%>">Print</a></div></li>
                        <li><a href="{{ asset('good_request')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="good_request_id" name="good_request_id" ng-model="data.good_request_id" class="form-control" />
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Request No</label>
                                            <input type="text" id="good_request_no" name="good_request_no" ng-model="data.good_request_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Request Date</label>
                                            <input type="text" id="good_request_date" name="good_request_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.good_request_date" ng-disabled="edit_disable" is-open="goodRequestDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenGoodRequestDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Good Request Time</label>
                                            <input type="text" id="good_request_time" name="good_request_time" ng-model="data.good_request_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" ng-disabled="edit_disable" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_stock')).addClass('active');
        angular.element(document.querySelector('#sub_menu_good_request')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        good_request_date: {
                            required: true,
                            date: true
                        },
                        good_request_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        good_request_date: {
                            required: 'Good Request Date is required',
                            date: 'Invalid date format'
                        },
                        good_request_time: {
                            required: 'Good Request Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
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
        $scope.edit_disable = false;
        $scope.permission = false;
            
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
            good_request_id: <?php echo $good_request_id ? $good_request_id : 0; ?>,
            good_request_no: '',
            good_request_date: new Date(),
            good_request_time: hh+':'+mm,
            remarks: '',
            is_posted: 0
        };
        $scope.resetCopy = angular.copy($scope.data);
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.goodRequestDatePopup = {
            opened: false
        };        
        $scope.OpenGoodRequestDate = function () {
            $scope.goodRequestDatePopup.opened = !$scope.goodRequestDatePopup.opened;
        };
        
        $('#good_request_time').mask('00:00');
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'document_no', 
                    displayName: 'Document No', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'item_code', 
                    displayName: 'Item Code', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'item_name', 
                    displayName: 'Item Name', 
                    width: '40%', 
                    enableCellEdit: false
                },
                {
                    field: 'unit_type', 
                    displayName: 'Unit Type', 
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'rate', 
                    displayName: 'Rate', 
                    cellClass: 'grid-align-right',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'quantity', 
                    displayName: 'Quantity', 
                    cellClass: 'grid-align-right',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'value', 
                    displayName: 'Value', 
                    cellClass: 'grid-align-right',
                    width: '10%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationTotalValue() | number:2 %></div>',
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
        
        $scope.getAggregationTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].value);
            }
            return total_value;
        };

        $scope.resetForm = function(){
            $scope.data = angular.copy($scope.resetCopy);
            $scope.main_refresh();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        $scope.postForm = function(){
            swal({
                title: "Are you sure?",
                text: "Good Request No : <strong>"+ $scope.data.good_request_no+"</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, posted it!",
                closeOnConfirm: false
            },
            function(){
                $scope.data.details = $scope.gridOptions.data; 
                $http.put(base_url + '/good_request/'+$scope.data.good_request_id, $scope.data).success(function (result) {
                    $http({
                        method: 'GET',
                        url: base_url + '/good_request/post_good_request',
                        params: {
                            id: $scope.data.good_request_id
                        }
                    }).then(function successCallback(response) {
                        swal({
                            title: "Posted!", 
                            text: "Good Request No : "+ $scope.data.good_request_no,
                            html: true,
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                        $scope.main_refresh();
                    });
                });
            });
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            $scope.data.details = $scope.gridOptions.data; 
            if($scope.data.good_request_id == 0 && $scope.data.details.length > 0){
                $http.post(base_url + '/good_request', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Good Request Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.good_request_id = result.response ? result.data.id : 0;
                    $scope.data.good_request_no = result.response ? result.data.good_request_no : 0;
                    $scope.main_refresh();
                });
            } else if($scope.data.details.length > 0){
                $http.put(base_url + '/good_request/'+$scope.data.good_request_id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Good Request Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.data.good_request_id = result.response ? result.data.id : 0;
                    $scope.data.good_request_no = result.response ? result.data.good_request_no : 0;
                    $scope.main_refresh();
                });
            } else{
                $('#save_button').prop('disabled', false);
                $.pnotify && $.pnotify({
                    title: 'Good Request Details',
                    text: 'Details are required',
                    type: 'error',
                    nonblock: true,
                    history: false,
                    delay: 6e3,
                    hide: true
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var data_array = [];
            var document_array = [];
            $http({
                method: 'GET',
                url: base_url + '/good_request/find_good_request',
                params: {
                    id: $scope.data.good_request_id
                }
            }).then(function successCallback(response) {
                if(response.data.good_request){
                    var good_request_date_time = response.data.good_request.good_request_date_time.split(' ');
                    $scope.data.good_request_no = response.data.good_request.good_request_no;
                    $scope.data.good_request_date = good_request_date_time[0];
                    $scope.data.good_request_time = good_request_date_time[1];
                    $scope.data.remarks = response.data.good_request.remarks;
                    $scope.data.is_posted = response.data.good_request.is_posted;
                    $scope.edit_disable = response.data.good_request.is_posted == 1 ? true : false;
                    
                    $.each(response.data.good_request.good_request_details, function (index, value) {
                        data_array.push({
                            id: value.id,
                            type: value.type,
                            detail_id: value.detail_id,
                            document_no: value.document_no,
                            item_id: value.item ? value.item.id : 0,
                            item_code: value.item ? value.item.code : '',
                            item_name: value.item ? value.item.name : '',
                            unit_type: value.item && value.item.unit_type ? value.item.unit_type.code : '',
                            rate: parseFloat(Math.round(value.rate * 100) / 100).toFixed(2),
                            quantity: value.quantity,
                            value: parseFloat(Math.round(Number(value.rate) * Number(value.quantity) * 100) / 100).toFixed(2)
                        });
                    });   
                    $.each(response.data.good_request.good_request_document, function (index, value) {
                        document_array.push({
                            type: value.type,
                            document_id: value.document_id
                        });
                    });
                }
                $scope.permission = response.data.permission;
                
                if(!$scope.edit_disable){
                    $http({
                        method: 'GET',
                        url: base_url + '/good_request/get_details'
                    }).then(function successCallback(response) {
                        if(response.data){  
                            for(var i=0; i<response.data.documents.length; i++){
                                var push = true;
                                for(var j=0; j<document_array.length; j++){
                                    if(response.data.documents[i].type == document_array[j].type && response.data.documents[i].document_id == document_array[j].document_id){
                                        push = false;
                                    }
                                }
                                if(push){
                                    document_array.push(response.data.documents[i]);
                                }
                            }

                            for(var i=0; i<response.data.details.length; i++){
                                var push = true;
                                for(var j=0; j<data_array.length; j++){
                                    if(response.data.details[i].type == data_array[j].type && response.data.details[i].detail_id == data_array[j].detail_id){
                                        push = false;
                                    }
                                }
                                if(push){
                                    data_array.push(response.data.details[i]);
                                }
                            }
                        }
                
                        $scope.gridOptions.data = data_array;
                        $scope.data.documents = document_array;
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
                
                $scope.gridOptions.data = data_array;
                $scope.data.documents = document_array;
                $scope.gridOptions.exporterCsvFilename = $scope.data.good_request_no+' Good Request Details.csv';
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();
        
        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection