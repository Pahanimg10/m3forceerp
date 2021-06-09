@extends('layouts.main')

@section('title')
<title>M3Force | Print Handover Documents</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Job</a></li>                    
    <li class="active">Print Handover Documents</li>
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
                    <h3 class="panel-title"><strong>Print Handover Documents</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('job/ongoing_job')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <input type="hidden" id="inquiry_id" name="inquiry_id" ng-model="data.inquiry_id" class="form-control" />
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
        angular.element(document.querySelector('#main_menu_job')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_job')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.data = [];
        $scope.data.inquiry_id = <?php echo $inquiry_id ? $inquiry_id : 0; ?>;
                
        $scope.gridOptions = {
            columnDefs: [
                {
                    field: 'inquiry_id', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'document_type', 
                    type: 'number', 
                    visible: false
                },
                {
                    field: 'document_name', 
                    displayName: 'Document Name', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+ base_url + '/job/print_handover_documents?id=<%= row.entity.inquiry_id %>&type=<%= row.entity.document_type %>" target="_blank"><%= row.entity.document_name %></a></div>'
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
                url: base_url + '/inquiry/find_inquiry',
                params: {
                    id: $scope.data.inquiry_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                data_array.push({
                    inquiry_id: response.data.id,
                    document_type: 1,
                    document_name: 'Company Contact Details'
                }); 
                data_array.push({
                    inquiry_id: response.data.id,
                    document_type: 2,
                    document_name: 'Installation Completion Acknowledgement'
                });   
                data_array.push({
                    inquiry_id: response.data.id,
                    document_type: 3,
                    document_name: 'Customer Feedback Form - Installations'
                }); 
                
                if(response.data.inquiry_type_id == 2 || response.data.inquiry_type_id == 4){
                    data_array.push({
                        inquiry_id: response.data.id,
                        document_type: 4,
                        document_name: 'Customer Detail Schedule - Monitoring'
                    }); 
                    data_array.push({
                        inquiry_id: response.data.id,
                        document_type: 5,
                        document_name: 'Monitoring & Response Agreement'
                    }); 
                }
                
                $scope.gridOptions.data = data_array;
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