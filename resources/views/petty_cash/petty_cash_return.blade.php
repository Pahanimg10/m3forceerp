@extends('layouts.main')

@section('title')
<title>M3Force | Petty Cash Return</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Petty Cash</a></li>                    
    <li class="active">Petty Cash Return</li>
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
                    <h3 class="panel-title"><strong>Petty Cash Return</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="add_new()" class="btn btn-primary">Add New</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><a href="{{ asset('home')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Date Range</label>             
                                <div class="col-md-8">                                                
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default pull-left" id="btn_daterange" style="padding: 6px; border-radius: 5px;" class="form-control">
                                            <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                            </span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                        <input type="hidden" ng-model="data.from" id="from" name="from" class="form-control" >
                                        <input type="hidden" ng-model="data.to" id="to" name="to" class="form-control number" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Status</label>
                                <div class="col-md-8"> 
                                    <select name="status" id="status" ng-options="option.name for option in status_array track by option.id" ng-model="data.status" ng-change="main_refresh()" class="form-control" ></select>
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
    function getNumberFormattedDate(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();

        if (month < 10) {
            month = '0' + month;
        }
        if (day < 10) {
            day = '0' + day;
        }

        return year + '-' + month + '-' + day;
    }

    function getStringFormattedDate(date) {
        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();

        var monthNames = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
        ];

        if (day < 10) {
            day = '0' + day;
        }

        return monthNames[month] + ' ' + day + ', ' + year;
    }
    
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
        angular.element(document.querySelector('#main_menu_petty_cash')).addClass('active');
        angular.element(document.querySelector('#sub_menu_petty_cash_return')).addClass('active');
    }); 

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', 'uiGridExporterConstants', '$timeout', '$q', '$window', function ($scope, $http, $rootScope, uiGridConstants, uiGridExporterConstants, $timeout, $q, $window) {  
        $scope.data = [];
            
        $('#btn_daterange').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 2 Weeks': [moment().subtract(13, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        }, function (start, end) {
            $('#btn_daterange span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
            $scope.data.from = start.format('YYYY-MM-DD');
            $scope.data.to = end.format('YYYY-MM-DD');
            $scope.main_refresh();
        });
        
            
        var to = new Date();
        var from = new Date();
        from.setDate(from.getDate() - 29);
        $('#btn_daterange span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.from = getNumberFormattedDate(from);
        $scope.data.to = getNumberFormattedDate(to);
        
        $scope.status_array = [{id:-1, name:'All'}, {id:0, name:'Pending'}, {id:1, name:'Returned'}];
        $scope.data.status = $scope.status_array[1];
        
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
                    field: 'is_posted', 
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
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1 || row.entity.is_posted == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
                },
                {
                    field: 'petty_cash_return_date_time', 
                    displayName: 'Return Date & Time', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'petty_cash_return_from', 
                    displayName: 'Petty Cash Return From', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'petty_cash_return_no', 
                    displayName: 'Petty Cash Return No', 
                    cellClass: 'grid-align',
                    width: '15%', 
                    enableCellEdit: false
                },
                {
                    field: 'petty_cash_return_value', 
                    displayName: 'Petty Cash Return Value', 
                    cellClass: 'grid-align-right',
                    width: '18%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationPettyCashReturnTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'remarks', 
                    displayName: 'Remarks', 
                    width: '40%', 
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
        
        $scope.getAggregationPettyCashReturnTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].petty_cash_return_value);
            }
            return total_value;
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.data.status.name+' Petty Cash Returns From ' + $scope.data.from + ' To ' + $scope.data.to + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/petty_cash_return/petty_cash_return_list',
                params: {
                    type: $scope.data.status.id,
                    from: $scope.data.from,                    
                    to: $scope.data.to
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.petty_cash_returns, function (index, value) {
                    var customer = '';
                    customer = value.petty_cash_issue && value.petty_cash_issue.petty_cash_issue_type_id == 1 && value.petty_cash_issue.job && value.petty_cash_issue.job.inquiry && value.petty_cash_issue.job.inquiry.contact ? value.petty_cash_issue.job.inquiry.contact.name : customer;
                    customer = value.petty_cash_issue && value.petty_cash_issue.petty_cash_issue_type_id == 2 && value.petty_cash_issue.tech_response && value.petty_cash_issue.tech_response.contact ? value.petty_cash_issue.tech_response.contact.name : customer;
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        is_posted: value.is_posted,
                        petty_cash_return_date_time: value.petty_cash_return_date_time,
                        petty_cash_return_from: value.petty_cash_issue ? customer != '' ? customer+' : '+value.petty_cash_issue.issued_to : value.petty_cash_issue.issued_to : '',
                        petty_cash_return_no: value.petty_cash_return_no,
                        petty_cash_return_value: parseFloat(Math.round(value.petty_cash_return_value * 100) / 100).toFixed(2),
                        remarks: value.remarks
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
            $window.location.href = base_url + '/petty_cash_return/add_new';
        };

        $scope.editRecord = function (row) {
            $window.location.href = base_url + '/petty_cash_return/add_new?id='+row.entity.id;
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/petty_cash_return/find_petty_cash_return',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover <strong>" + response.data.petty_cash_return.petty_cash_return_no + "</strong> petty cash issue !",
                    html: true,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#FF0000",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(){
                    $http.delete(base_url + '/petty_cash_return/'+row.entity.id, {params: {type: 0}}).success(function (response_delete) {
                        swal({
                            title: "Deleted!", 
                            text: response.data.petty_cash_return.petty_cash_return_no + " petty cash issue has been deleted.", 
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                        $scope.main_refresh();
                    });
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
    }]);
</script>
@endsection