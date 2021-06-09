@extends('layouts.main')

@section('title')
<title>M3Force | Contact List</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Complain</a></li>                    
    <li class="active">Contact List</li>
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
                    <h3 class="panel-title"><strong>Contact List</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new()" class="btn btn-primary">Add New Customer</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Type</label>             
                                <div class="col-md-8">
                                    <select name="contact_type" id="contact_type" ng-options="option.name for option in contact_type_array track by option.id" ng-model="data.contact_type" ng-change="main_refresh()" class="form-control" ></select>
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
        angular.element(document.querySelector('#main_menu_customer_complain')).addClass('active');
        angular.element(document.querySelector('#sub_menu_new_customer_complain')).addClass('active');
    });  

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.data = [];
        $scope.contact_type_array = [];
        $http({
            method: 'GET',
            url: base_url + '/contact_contact_types'
        }).then(function successCallback(response) {
            var data_array = [{
                    id: -1,
                    name: 'All'
            }];
            $.each(response.data, function (index, value) {
                data_array.push({
                    id: value.id,
                    name: value.name
                });
            });    
            $scope.contact_type_array = data_array;
            
            <?php if($type && $type == 1){ ?>
                $scope.data.contact_type = {id:2, name:'Non Monitoring Customer'};
            <?php } else{ ?>
                $scope.data.contact_type = $scope.contact_type_array.length > 1 ? $scope.contact_type_array[1] : $scope.contact_type_array[0];
            <?php } ?>
        }, function errorCallback(response) {
            console.log(response);
        });

        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'customer_id', 
                    displayName: 'Customer ID', 
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'code', 
                    displayName: 'Code', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    enableCellEdit: false
                },
                {
                    field: 'name', 
                    displayName: 'Name', 
                    sort: {direction: 'asc', priority: 0},
                    width: '20%', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+base_url+'/customer_complain/add_new_customer_complain?contact_id=<%= row.entity.id %>" target="_self"><%= row.entity.name %></a></div>'
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
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'email', 
                    displayName: 'Email', 
                    width: '15%', 
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
            onRegisterApi: function (gridApi) {
                $scope.gridApi = gridApi;
            }
        };

        $scope.toggleFiltering = function () {
            $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
            $scope.gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/contact/contact_list',
                params: {
                    contact_type: $scope.data.contact_type.id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.contacts, function (index, value) {
                    data_array.push({
                        id: value.id,
                        customer_id: value.contact_id,
                        code: value.code,
                        name: value.name,
                        address: value.address,
                        contact_no: value.contact_no,
                        email: value.email
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

        $scope.add_new = function () {
            $window.location.href = base_url + '/customer_complain/add_new_contact';
        };
    }]);
</script>
@endsection