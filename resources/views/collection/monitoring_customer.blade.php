@extends('layouts.main')

@section('title')
<title>M3Force | Monitoring Customer</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Collection</a></li>                    
    <li class="active">Monitoring Customer</li>
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
                    <h3 class="panel-title"><strong>Monitoring Customer</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
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
        angular.element(document.querySelector('#main_menu_collection')).addClass('active');
        angular.element(document.querySelector('#sub_menu_monitoring_customer')).addClass('active');
    });  

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'name', 
                    displayName: 'Name', 
                    sort: {direction: 'asc', priority: 0},
                    width: '20%', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+base_url+'/monitoring_customer/monitoring_customer_detail?id=<%= row.entity.id %>"><%= row.entity.name %></a></div>'
                },
                {
                    field: 'address', 
                    displayName: 'Address', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'contact_no', 
                    displayName: 'Contact No', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'email', 
                    displayName: 'Email', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'pending_amount', 
                    displayName: 'Pending Amount', 
                    cellClass: 'grid-align-right',
                    width: '15%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationPendingAmountTotal() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'pending_dates', 
                    displayName: 'Pending Dates', 
                    cellClass: 'grid-align',
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
            exporterCsvFilename: 'Monitoring Customers.csv',
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
        
        $scope.getAggregationPendingAmountTotal = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].pending_amount);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/monitoring_customer/monitoring_customer_list'
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    var start = new Date(value.update_date);
                    var end = new Date();
                    var diff = new Date(end - start);
                    var pending_dates = Math.floor(diff/1000/60/60/24);
                    
                    var name = value.is_group == 0 && value.contact ? value.contact.name : '';
                    name = value.is_group == 1 && value.c_group ? value.c_group.name : name;
                    var address = value.is_group == 0 && value.contact ? value.contact.address : '';
                    address = value.is_group == 1 && value.c_group ? value.c_group.address : address;
                    var contact_no = value.is_group == 0 && value.contact ? value.contact.contact_no : '';
                    contact_no = value.is_group == 1 && value.c_group ? value.c_group.contact_no : contact_no;
                    var email = value.is_group == 0 && value.contact ? value.contact.email : '';
                    email = value.is_group == 1 && value.c_group ? value.c_group.email : email;
                    
                    data_array.push({
                        id: value.id,
                        name: name,
                        address: address,
                        contact_no: contact_no,
                        email: email,
                        pending_amount: parseFloat(Math.round(value.pending_amount * 100) / 100).toFixed(2),
                        pending_dates: pending_dates
                    });
                });    
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();
    }]);
</script>
@endsection