@extends('layouts.main')

@section('title')
<title>M3Force | Job Attendance Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Job</a></li>                    
    <li class="active">Job Attendance Details</li>
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
    input[type="text"]:read-only{
        color: #1CB09A;
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
                    <h3 class="panel-title"><strong>Job Attendance</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('job_attendance')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Job Type</label>
                                            <select name="job_type" id="job_type" ng-options="option.name for option in job_type_array track by option.id" ng-model="data.job_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Job No</label>
                                            <input type="text" id="job_no" name="job_no" ui-grid-edit-auto ng-model="data.job_no" typeahead="name as job_no_array.name for job_no_array in job_no_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onJobNoSelect($item, $model, $label)" ng-keyup="get_job_nos(data.job_type.id, data.job_no)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" ng-model="data.customer_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Attended Date</label>
                                            <input type="text" id="attended_date" name="attended_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.attended_date" is-open="attendedDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenAttendedDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Technical Team</label>
                                            <input type="text" id="technical_team" name="technical_team" ui-grid-edit-auto ng-model="data.technical_team" typeahead="name as technical_team_array.name for technical_team_array in technical_team_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onTechnicalTeamSelect($item, $model, $label)" ng-keyup="get_technical_teams(data.technical_team)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Mandays</label>
                                            <input type="text" id="mandays" name="mandays" ng-model="data.mandays" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-md-offset-3" style="margin-top: 20px;">
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
        angular.element(document.querySelector('#main_menu_job')).addClass('active');
        angular.element(document.querySelector('#sub_menu_job_attendance')).addClass('active');
    });  
    
    myApp.directive('uiGridEditAuto', ['$timeout', '$document', 'uiGridConstants', 'uiGridEditConstants', function($timeout, $document, uiGridConstants, uiGridEditConstants) {
        return {
            require: ['?^uiGrid', '?^uiGridRenderContainer'],
            scope: true,
            compile: function() {
                return {
                    post: function($scope, $elm, $attrs, controllers) {
                        var uiGridCtrl = controllers[0];
                        var renderContainerCtrl = controllers[1];
                        
                        $scope.$on(uiGridEditConstants.events.BEGIN_CELL_EDIT, function () {
                            $elm.focus();
                            $scope[$attrs.focusMe] = true;
                            
                            if (uiGridCtrl.grid.api.cellNav) {
                                uiGridCtrl.grid.api.cellNav.on.navigate($scope, function (newRowCol, oldRowCol) {
                                    $scope.stopEdit();
                                });
                            } else {

                                angular.element(document.querySelectorAll('.ui-grid-cell-contents')).on('click', onCellClick);
                            }
//                                angular.element(window).on('click', onWindowClick);
                        });

//                            $scope.$on('$destroy', function () {
//                                angular.element(window).off('click', onWindowClick);
//                                $('body > .dropdown-menu, body > div > .dropdown-menu').remove();
//                            });

                        $scope.stopEdit = function(evt) {
                            $scope.$emit(uiGridEditConstants.events.END_CELL_EDIT);
                        };
                        $elm.on('keydown', function(evt) {
                            switch (evt.keyCode) {
                                case uiGridConstants.keymap.ESC:
                                    evt.stopPropagation();
                                    $scope.$emit(uiGridEditConstants.events.CANCEL_CELL_EDIT);
                                    break;
                            }
                            if (uiGridCtrl && uiGridCtrl.grid.api.cellNav) {
                                if(evt.keyCode === uiGridConstants.keymap.TAB){
                                    evt.uiGridTargetRenderContainerId = renderContainerCtrl.containerId;

                                    console.log(evt.key);
                                    if (uiGridCtrl.cellNav.handleKeyDown(evt) !== null) {
                                        $scope.stopEdit(evt);
                                    }

                                }

                            } else {
                                switch (evt.keyCode) {
                                    case uiGridConstants.keymap.ENTER:
                                    case uiGridConstants.keymap.TAB:
                                        evt.stopPropagation();
                                        evt.preventDefault();
                                        $scope.stopEdit(evt);
                                        break;
                                }
                            }
                            return true;
                        });
                    }
                };
            }
        };
    }]);   
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        job_type: {
                            required: true
                        },
                        job_no: {
                            required: true,
                            remote: {
                                url: base_url + '/job_attendance/validate_job_no',
                                type: 'GET',
                                data: {
                                    job_type: function() {
                                      return scope.data.job_type && scope.data.job_type.id ? scope.data.job_type.id : '';
                                    },
                                    job_no: function() {
                                      return scope.data.job_no && scope.data.job_no.name ? scope.data.job_no.name : scope.data.job_no;
                                    }
                                }
                            }
                        },
                        attended_date: {
                            required: true,
                            date: true
                        },
                        technical_team: {
                            remote: {
                                url: base_url + '/validate_technical_team_name',
                                type: 'GET',
                                data: {
                                    name: function() {
                                      return scope.data.technical_team && scope.data.technical_team.name ? scope.data.technical_team.name : scope.data.technical_team;
                                    }
                                }
                            }
                        },
                        mandays: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        job_type: {
                            required: 'Job Type is required'
                        },
                        job_no: {
                            required: 'Job No is required',
                            remote: 'Invalid Job No'
                        },
                        attended_date: {
                            required: 'Attended Date is required',
                            date: 'Invalid date format'
                        },
                        technical_team: {
                            required: 'Technical Team is required',
                            remote: 'Invalid Technical Team'
                        },
                        warranty: {
                            required: 'Warranty is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
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
        $scope.job_no_array = [];
        $scope.technical_team_array = [];
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.attendedDatePopup = {
            opened: false
        };        
        $scope.OpenAttendedDate = function () {
            $scope.attendedDatePopup.opened = !$scope.attendedDatePopup.opened;
        };
            
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
            
        $http({
            method: 'GET',
            url: base_url + '/item_issue/get_data'
        }).then(function successCallback(response) {
            var job_type_array = [];
            job_type_array.push({
                id: '',
                name: 'Select Job Type'
            });
            $.each(response.data.item_issue_types, function (index, value) {
                job_type_array.push({
                    id: value.id,
                    name: value.name
                });
            }); 

            $scope.job_type_array = job_type_array;

            var to = new Date();
            var from = new Date();
            from.setDate(from.getDate() - 29);
            $('#btn_daterange span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        
            $scope.data = {
                from: getNumberFormattedDate(from),
                to: getNumberFormattedDate(to),
                id: 0,
                job_type: $scope.job_type_array.length > 0 ? $scope.job_type_array[0] : {},
                job_no: '',
                customer_name: '',
                attended_date: new Date(),
                technical_team: '',
                mandays: ''
            };
            $scope.resetCopy = angular.copy($scope.data);
        }, function errorCallback(response) {
            console.log(response);
        });
                    
        $scope.refreshForm = function(){
            $scope.data.id = 0;
            $scope.data.job_no = '';
            $scope.data.customer_name = '';
            $scope.data.attended_date = new Date();
            $scope.data.mandays = '';

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };
        
        $scope.gridOptions = {
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
                    field: 'attended_date', 
                    displayName: 'Attended Date', 
                    cellClass: 'grid-align',
                    width: '12%', 
                    sort: {direction: 'desc', priority: 0},
                    enableCellEdit: false
                },
                {
                    field: 'technical_team', 
                    displayName: 'Technical Team', 
                    width: '18%', 
                    enableCellEdit: false
                },
                {
                    field: 'job_type', 
                    displayName: 'Job Type', 
                    width: '10%', 
                    enableCellEdit: false
                },
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
                    enableCellEdit: false
                },
                {
                    field: 'mandays', 
                    displayName: 'Mandays', 
                    cellClass: 'grid-align',
                    width: '10%', 
                    footerCellTemplate: '<div class="ui-grid-cell-contents text-right" ><%= grid.appScope.getAggregationMandaysTotalValue() | number:2 %></div>',
                    enableCellEdit: false
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '10%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-info btn-sm grid-btn text-center" ng-click="grid.appScope.editRecord(row)"><i class="fa fa-pencil" style="margin: 0; width: 12px;"></i></button><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
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
        
        $scope.getAggregationMandaysTotalValue = function(){
            var total_value = 0;
            for(var i=0; i<$scope.gridOptions.data.length; i++){
                total_value += Number($scope.gridOptions.data[i].mandays);
            }
            return total_value;
        };

        $scope.get_job_nos = function(job_type_id, job_no){  
            if(job_no && job_no.length > 0){
                $scope.job_no_array = [];
                if(job_type_id == 1){
                    $http({
                        method: 'GET',
                        url: base_url + '/get_job_nos',
                        params:{
                            job_no: job_no
                        }
                    }).then(function successCallback(response) {
                        $scope.job_no_array = [];            
                        $.each(response.data, function (index, value) {
                            $scope.job_no_array.push({
                                id: value.id,
                                name: value.job_no,
                                customer: value.inquiry && value.inquiry.contact ? value.inquiry.contact.name : ''
                            });
                        });
                        $scope.find_job_no(job_type_id, job_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                } else if(job_type_id == 2){
                    $http({
                        method: 'GET',
                        url: base_url + '/get_tech_response_nos',
                        params:{
                            tech_response_no: job_no
                        }
                    }).then(function successCallback(response) {
                        $scope.job_no_array = [];            
                        $.each(response.data, function (index, value) {
                            $scope.job_no_array.push({
                                id: value.id,
                                name: value.tech_response_no,
                                customer: value.contact ? value.contact.name : ''
                            });
                        });
                        $scope.find_job_no(job_type_id, job_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
            }
        };

        $scope.find_job_no = function(job_type_id, job_no){ 
            if(job_type_id == 1){
                $http({
                    method: 'GET',
                    url: base_url + '/find_job_no',
                    params:{
                        job_no: job_no
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.data.job_no = {id: response.data.id, name: response.data.job_no};
                        $scope.data.customer_name = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.name : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            } else if(job_type_id == 2){
                $http({
                    method: 'GET',
                    url: base_url + '/find_tech_response_no',
                    params:{
                        tech_response_no: job_no
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.data.job_no = {id: response.data.id, name: response.data.tech_response_no};
                        $scope.data.customer = response.data.contact ? response.data.contact.name : '';
                    } 
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.get_technical_teams = function(name){  
            if(name && name.length > 0){
                $scope.technical_team_array = [];  
                $http({
                    method: 'GET',
                    url: base_url + '/get_technical_teams',
                    params:{
                        name: name
                    }
                }).then(function successCallback(response) {
                    $scope.technical_team_array = [];            
                    $.each(response.data, function (index, value) {
                        $scope.technical_team_array.push({
                            id: value.id,
                            name: value.name
                        });
                    });
                    $scope.find_technical_team(name);
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };

        $scope.find_technical_team = function(name){  
            $http({
                method: 'GET',
                url: base_url + '/find_technical_team',
                params:{
                    name: name
                }
            }).then(function successCallback(response) {
                if(response.data){
                    $scope.data.technical_team = {id: response.data.id, name: response.data.name};
                    $timeout(function () {
                        $scope.main_refresh();
                    }, 1500, false);
                } else{
                    $scope.gridOptions.data = [];
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.onJobNoSelect = function ($item, $model, $label) {
            $scope.data.job_no = {id: $item.id, name: $item.name};
            $scope.data.customer_name = $item.customer_name;
            $timeout(function() { 
                $scope.find_job_no($scope.data.job_type.id, $scope.data.job_no.name);
                $('#attended_date').focus(); 
            }, 200, false);
        };
        
        $scope.onTechnicalTeamSelect = function ($item, $model, $label) {
            $scope.data.technical_team = {id: $item.id, name: $item.name};
            $timeout(function() { 
                $scope.find_technical_team($scope.data.technical_team.name);
                $('#mandays').focus(); 
                $scope.main_refresh();
            }, 200, false);
        };

        $scope.editRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/job_attendance/find_job_attendance_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var job_no = '';
                    if(response.data.job_type_id == 1){
                        job_no = response.data.job ? {id:response.data.job.id, name:response.data.job.job_no} : '';
                    } else if(response.data.job_type_id == 2){
                        job_no = response.data.tech_response ? {id:response.data.tech_response.id, name:response.data.tech_response.tech_response_no} : '';
                    }
                    $scope.data = {
                        from: $scope.data.from,
                        to: $scope.data.to,
                        id: response.data.id,
                        job_type: response.data.job_type ? {id:response.data.job_type.id, name:response.data.job_type.name} : {},
                        job_no: job_no,
                        customer_name: response.data.job && response.data.job.inquiry && response.data.job.inquiry.contact ? response.data.job.inquiry.contact.name : '',
                        attended_date: response.data.attended_date,
                        technical_team: response.data.technical_team ? {id:response.data.technical_team.id, name:response.data.technical_team.name} : '',
                        mandays: response.data.mandays,
                    };
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.deleteRecord = function (row) {
            $http({
                method: 'GET',
                url: base_url + '/job_attendance/find_job_attendance_detail',
                params: {
                    id: row.entity.id
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var job_no = '';
                    if(response.data.job_type_id == 1){
                        job_no = response.data.job ? response.data.job.job_no : '';
                    } else if(response.data.job_type_id == 2){
                        job_no = response.data.tech_response ? response.data.tech_response.tech_response_no : '';
                    }
                    $scope.data = {
                        from: $scope.data.from,
                        to: $scope.data.to,
                        id: response.data.id,
                        job_type: response.data.job_type ? {id:response.data.job_type.id, name:response.data.job_type.name} : {},
                        job_no: job_no,
                        customer_name: response.data.job && response.data.job.inquiry && response.data.job.inquiry.contact ? response.data.job.inquiry.contact.name : '',
                        attended_date: response.data.attended_date,
                        technical_team: response.data.technical_team ? {id:response.data.technical_team.id, name:response.data.technical_team.name} : '',
                        mandays: response.data.mandays,
                    };
                    
                    swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover <strong>" + job_no + "</strong> job attendence details!",
                        html: true,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        $http.delete(base_url + '/job_attendance/'+$scope.data.id, $scope.data).success(function (response_delete) {
                            swal({
                                title: "Deleted!", 
                                text: "<strong>" + job_no + "</strong> job attendence details has been deleted.", 
                                html: true,
                                type: "success",
                                confirmButtonColor: "#9ACD32"
                            });
                            $scope.refreshForm();
                            $scope.main_refresh();
                        });
                    });
                }
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.resetForm = function(){
            $scope.data = angular.copy($scope.resetCopy);
            $scope.gridOptions.data = [];

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.id == 0){
                $http.post(base_url + '/job_attendance', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Job Attendance',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            } else{
                $http.put(base_url + '/job_attendance/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Job Attendance',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                    
                    $scope.refreshForm();
                    $scope.main_refresh();
                });
            }
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            var technical_team = $scope.data.technical_team && $scope.data.technical_team.name ? $scope.data.technical_team.name : '';
            $scope.gridOptions.exporterCsvFilename = technical_team+' Job Attendance Details From ' + $scope.data.from + ' To ' + $scope.data.to + '.csv';
            $http({
                method: 'GET',
                url: base_url + '/job_attendance/job_attendance_detail_list',
                params: {
                    from: $scope.data.from,                    
                    to: $scope.data.to,
                    technical_team_id: $scope.data.technical_team && $scope.data.technical_team.id ? $scope.data.technical_team.id : ''
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.job_attendances, function (index, value) {
                    var job_no = '';
                    if(value.job_type_id == 1){
                        job_no = value.job ? {id:value.job.id, name:value.job.job_no} : '';
                    } else if(value.job_type_id == 2){
                        job_no = value.tech_response ? {id:value.tech_response.id, name:value.tech_response.tech_response_no} : '';
                    }
                    data_array.push({
                        id: value.id,
                        permission: value.permission ? 1 : 0,
                        attended_date: value.attended_date,
                        technical_team: value.technical_team ? value.technical_team.name : '',
                        job_type: value.job_type ? value.job_type.name: '',
                        job_no: value.job ? value.job.job_no : '',
                        customer_name: value.job && value.job.inquiry && value.job.inquiry.contact ? value.job.inquiry.contact.name : '',
                        mandays: value.mandays
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
    }]);
</script>
@endsection