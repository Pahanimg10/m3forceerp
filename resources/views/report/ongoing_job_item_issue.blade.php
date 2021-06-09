@extends('layouts.main')

@section('title')
<title>M3Force | Ongoing Job Item Issues</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Ongoing Job Item Issues</a></li>  
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
/*    .form-horizontal .form-group{
        margin-left: 0;
        margin-right: 0;
    }
    .control-label{
        padding: 15px 0 5px 0;
    }*/
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Ongoing Job Item Issues</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">                        
                        <div class="col-md-12">
                            <h4 class="col-md-12">Filter Details</h4>
                        </div>                                
                        <div class="col-md-12">
                            <div class="col-md-4 col-md-offset-4">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">To Date</label>
                                    <div class="col-md-8">
                                        <input type="text" id="to_date" name="to_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.to_date" is-open="toDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenToDate()" ng-change="main_refresh()" class="form-control text-center" />
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
        angular.element(document.querySelector('#main_menu_report')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_job_item_issues')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        to_date: {
                            required: true,
                            date: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        to_date: {
                            required: 'To Date is required',
                            date: 'Invalid date format'
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
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.toDatePopup = {
            opened: false
        };        
        $scope.OpenToDate = function () {
            $scope.toDatePopup.opened = !$scope.toDatePopup.opened;
        };
        
        $scope.data.to_date = new Date();
        
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'job_no', 
                    displayName: 'Job No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_name', 
                    displayName: 'Customer Name', 
                    width: '25%', 
                    enableCellEdit: false,
                },
                {
                    field: 'issue_date_time', 
                    displayName: 'Issue Date & Time', 
                    cellClass: 'grid-align',
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
                    cellClass: 'grid-align',
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
                    width: '15%', 
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

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = 'Ongoing Job Item Issues To - ' + $scope.data.to_date + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/report/ongoing_job_item_issue_details',
                params: {                   
                    to: $scope.data.to_date
                }
            }).then(function successCallback(response) {
//                var data_array = [];
//                $.each(response.data, function (index, value) {
//                    if($scope.data.status.id == -1 || $scope.data.status.id == value.update_status_id){
//                        data_array.push({
//                            id: value.id,
//                            inquiry_date_time: value.inquiry_date_time,
//                            customer_name: value.customer_name,
//                            customer_address: value.customer_address,
//                            customer_contact_no: value.customer_contact_no,
//                            mode_of_inquiry: value.mode_of_inquiry,
//                            inquiry_type: value.inquiry_type,
//                            sales_person: value.sales_person,
//                            job_date_time: value.job_date_time,
//                            quoted_price: parseFloat(Math.round(value.quoted_price * 100) / 100).toFixed(2),
//                            update_date_time: value.update_date_time,
//                            update_status: value.update_status,
//                            remarks: value.remarks,
//                            logged_user: value.logged_user
//                        });
//                    }
//                });    
                $scope.gridOptions.data = response.data;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1000, false);
        
    }]);
</script>
@endsection