@extends('layouts.main')

@section('title')
<title>M3Force | Ongoing Customer Complain</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Customer Complain</a></li>          
    <li class="active">Ongoing Customer Complain</li>
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
                    <h3 class="panel-title"><strong>Ongoing Customer Complain</strong></h3>
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
        angular.element(document.querySelector('#main_menu_customer_complain')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_customer_complain')).addClass('active');
    });  

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'id', 
                    type: 'number', 
                    sort: {direction: 'desc', priority: 0},
                    visible: false
                },
                {
                    field: 'contact_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '20%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-warning btn-sm grid-btn text-center" ng-click="grid.appScope.updateRecord(row)"><i class="fa fa-share-square-o" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'complain_no', 
                    displayName: 'Complain No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'record_date_time', 
                    displayName: 'Record Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'complain_type', 
                    displayName: 'Complain Type', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'person_responsible', 
                    displayName: 'Person Responsible', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_name', 
                    displayName: 'Customer Name', 
                    width: '20%', 
                    enableCellEdit: false
                },
                {
                    field: 'customer_address', 
                    displayName: 'Address', 
                    width: '30%', 
                    enableCellEdit: false
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
                    width: '35%', 
                    enableCellEdit: false
                },
                {
                    field: 'log_user', 
                    displayName: 'Log User', 
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'pending_days', 
                    displayName: 'Dates Since last Update', 
                    cellClass: 'grid-align',
                    width: '20%', 
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
            exporterCsvFilename: 'Ongoing Customer Complains.csv',
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

        $scope.updateRecord = function (row) {
            $window.location.href = base_url + '/customer_complain/update_customer_complain?id='+row.entity.id;
        };

        $scope.editRecord = function (row) {
            $window.location.href = base_url + '/customer_complain/add_new_customer_complain?customer_complain_id='+row.entity.id+'&contact_id='+row.entity.contact_id;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/customer_complain/ongoing_customer_complain_list'
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data, function (index, value) {
                    var date_time = value.update_date_time.split(' ');
                    var start = new Date(date_time[0]);
                    var end   = new Date();
                    var diff  = new Date(end - start);
                    var days  = Math.floor(diff/1000/60/60/24);
                    
                    data_array.push({
                        id: value.id,
                        contact_id: value.contact_id,
                        complain_no: value.complain_no,
                        record_date_time: value.record_date_time,
                        complain_type: value.complain_type,
                        person_responsible: value.person_responsible,
                        customer_name: value.customer_name,
                        customer_address: value.customer_address,
                        update_date_time: value.update_date_time,
                        update_status: value.update_status,
                        remarks: value.remarks,
                        log_user: value.log_user,
                        pending_days: days
                    });
                });    
                $scope.gridOptions.data = data_array;
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.main_refresh();

        $scope.deleteRecord = function (row) {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover <strong>" + row.entity.complain_no + "</strong> customer complain !",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                $http.delete(base_url + '/customer_complain/'+row.entity.id, {params: {type: 0}}).success(function (response_delete) {
                    swal({
                        title: "Deleted!", 
                        text: row.entity.complain_no + " customer complain has been deleted.", 
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