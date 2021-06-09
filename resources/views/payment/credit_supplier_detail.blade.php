@extends('layouts.main')

@section('title')
<title>M3Force | Credit Supplier Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Payment</a></li>                    
    <li class="active">Credit Supplier Details</li>
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
</style>

<div class="row" ng-controller="mainController">
    <div class="col-md-12">

        <form autocomplete="off" id="dataForm" name="dataForm" class="form-horizontal" j-validate>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Credit Supplier Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new_payment()" class="btn btn-primary">Add New Payment</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('payment/credit_supplier')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <input type="hidden" id="credit_supplier_id" name="credit_supplier_id" ng-model="data.credit_supplier_id" class="form-control" />
                        <div class="col-md-6">
                            <div ui-grid="goodReceiveGridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
                        </div>
                        <div class="col-md-6">
                            <div ui-grid="paymentGridOptions" ui-grid-selection ui-grid-exporter ui-grid-pagination  ui-grid-move-columns ui-grid-resize-columns class="grid"></div>
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
        angular.element(document.querySelector('#main_menu_payment')).addClass('active');
        angular.element(document.querySelector('#sub_menu_credit_supplier')).addClass('active');
    });  

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.credit_supplier_id = <?php echo $credit_supplier_id ? $credit_supplier_id : 0; ?>;
        
        $scope.goodReceiveGridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'good_receive_date_time', 
                    displayName: 'Good Receive Date & Time', 
                    cellClass: 'grid-align',
                    width: '40%', 
                    sort: {direction: 'desc', priority: 0},
                    enableCellEdit: false
                },
                {
                    field: 'good_receive_no', 
                    displayName: 'Good Receive No', 
                    cellClass: 'grid-align',
                    width: '30%',  
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+base_url+'/payment/print_credit_supplier_good_receive?id=<%= row.entity.id %>" target="_blank"><%= row.entity.good_receive_no %></a></div>'
                },
                {
                    field: 'good_receive_value', 
                    displayName: 'Good Receive Value', 
                    cellClass: 'grid-align-right',
                    width: '30%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationGoodReceiveTotalValue() | number:2 %></div>',
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
                $scope.goodReceiveGridApi = gridApi;
            }
        };
        
        $scope.paymentGridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'permission', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'payment_date_time', 
                    displayName: 'Payment Date & Time', 
                    cellClass: 'grid-align',
                    width: '35%', 
                    sort: {direction: 'desc', priority: 0},
                    enableCellEdit: false
                },
                {
                    field: 'payment_no', 
                    displayName: 'Payment No', 
                    cellClass: 'grid-align',
                    width: '30%',  
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+base_url+'/payment/print_credit_supplier_payment?id=<%= row.entity.id %>" target="_blank"><%= row.entity.payment_no %></a></div>'
                },
                {
                    field: 'amount', 
                    displayName: 'Amount', 
                    cellClass: 'grid-align-right',
                    width: '20%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationPaymentTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '15%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>', 
                    visible: $scope.edit_disable ? false : true
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
                $scope.paymentGridApi = gridApi;
            }
        };

        $scope.export = function () {
            var myElement = angular.element(document.querySelectorAll(".custom-csv-link-location"));
            $scope.goodReceiveGridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
            $scope.paymentGridApi.exporter.csvExport(uiGridExporterConstants.ALL, uiGridExporterConstants.VISIBLE, myElement);
        };

        $scope.toggleFiltering = function () {
            $scope.goodReceiveGridOptions.enableFiltering = !$scope.goodReceiveGridOptions.enableFiltering;
            $scope.goodReceiveGridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
            $scope.paymentGridOptions.enableFiltering = !$scope.paymentGridOptions.enableFiltering;
            $scope.paymentGridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };
        
        $scope.getAggregationGoodReceiveTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.goodReceiveGridOptions.data.length; i++){
                total_value += Number($scope.goodReceiveGridOptions.data[i].good_receive_value);
            }
            return total_value;
        };
        
        $scope.getAggregationPaymentTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.paymentGridOptions.data.length; i++){
                total_value += Number($scope.paymentGridOptions.data[i].amount);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/payment/get_credit_supplier_detail',
                params: {
                    id: $scope.credit_supplier_id
                }
            }).then(function successCallback(response) {
                var good_receive_data_array = [];
                var payment_data_array = [];
                $.each(response.data.credit_supplier_good_receives, function (index, value) {
                    if(value.good_receive){
                        good_receive_data_array.push({
                            id: value.id,
                            good_receive_date_time: value.good_receive.good_receive_date_time,
                            good_receive_no: value.good_receive.good_receive_no,
                            good_receive_value: parseFloat(Math.round(value.good_receive.good_receive_value * 100) / 100).toFixed(2)
                        });
                    }
                });   
                $.each(response.data.credit_supplier_payments, function (index, value) { 
                    payment_data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        payment_date_time: value.payment_date_time,
                        payment_no: value.payment_no,
                        amount: parseFloat(Math.round(value.amount * 100) / 100).toFixed(2)
                    });
                });   
                $scope.goodReceiveGridOptions.exporterCsvFilename = response.data.credit_supplier && response.data.credit_supplier.contact ? response.data.credit_supplier.contact.name+' Good Receives.csv' : 'Good Receives.csv';
                $scope.goodReceiveGridOptions.data = good_receive_data_array;
                $scope.paymentGridOptions.exporterCsvFilename = response.data.credit_supplier && response.data.credit_supplier.contact ? response.data.credit_supplier.contact.name+' Payment Details.csv' : 'Payment Details.csv';
                $scope.paymentGridOptions.data = payment_data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();

        $scope.add_new_payment = function () {
            $window.location.href = base_url + '/payment/add_new_payment?credit_supplier_id='+$scope.credit_supplier_id;
        };

        $scope.editRecord = function (row) {
            $window.location.href = base_url + '/payment/add_new_payment?id='+row.entity.id+'&credit_supplier_id='+$scope.credit_supplier_id;
        };

        $scope.deleteRecord = function (row) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover <strong>" + row.entity.payment_no + "</strong> payment !",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                $http.delete(base_url + '/payment/'+row.entity.id, row.entity).success(function (response_delete) {
                    swal({
                        title: "Deleted!", 
                        text: row.entity.payment_no + " payment has been deleted.", 
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.main_refresh();
                });
            });
        };
    }]);
</script>
@endsection