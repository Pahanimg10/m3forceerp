@extends('layouts.main')

@section('title')
<title>M3Force | Dashboard</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>                    
    <li class="active">Dashboard</li>
</ul>
@endsection

@section('content')
<style>
    .error-block p{
        color: red; 
        text-align: center;
        font-size: 14px;
    }
    .owl-carousel {
        cursor: pointer;
    }
</style>
<div ng-controller="mainController"> 
    <?php if(is_array(session()->get('user_group')) && !in_array(3, session()->get('user_group')) && !in_array(4, session()->get('user_group')) && !in_array(5, session()->get('user_group'))){ ?>
        <!-- START WIDGETS --> 
        <div class="row">
            <div class="col-md-4">

                <!-- START WIDGET SLIDER -->
                <div class="widget widget-default widget-carousel">
                    <div class="owl-carousel" id="owl-example">
                        <div ng-click="new_inquiry()">                                                                         
                            <div class="widget-title">Inquiry</div>                           
                            <div class="widget-subtitle">Not Attended</div> 
                            <div class="widget-int">{{ $inquiry_not_attended }}</div>
                        </div>
                        <div ng-click="ongoing_inquiry()">                                                                     
                            <div class="widget-title">Inquiry</div>                                                                   
                            <div class="widget-subtitle">Pending</div>       
                            <div class="widget-int">{{ $inquiry_pending }}</div>
                        </div>
                        <div ng-click="ongoing_inquiry()">                                                                   
                            <div class="widget-title">Business</div>                                                                                
                            <div class="widget-subtitle">Pending</div>       
                            <div class="widget-int">{{ $business_pending }}</div>
                        </div>
                        <div ng-click="new_job()">                                                                               
                            <div class="widget-title">Job</div>                                                                            
                            <div class="widget-subtitle">New</div>    
                            <div class="widget-int">{{ $job_new }}</div>
                        </div>
                        <div ng-click="ongoing_job()">                                                                                 
                            <div class="widget-title">Job</div>                                                                      
                            <div class="widget-subtitle">Ongoing</div> 
                            <div class="widget-int">{{ $job_ongoing }}</div>
                        </div>
                    </div>                            
                    <div class="widget-controls">                                
                        <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a>
                    </div>                             
                </div>         
                <!-- END WIDGET SLIDER -->

            </div>
            <div class="col-md-4">

                <!-- START WIDGET SLIDER -->
                <div class="widget widget-default widget-carousel">
                    <div class="owl-carousel" id="owl-example">
                        <div ng-click="ongoing_tech_response()">                          
                            <div class="widget-title">Tech Response</div>                                                                   
                            <div class="widget-subtitle">Not Attended</div>               
                            <div class="widget-int">{{ $tech_response_not_attended }}</div>
                        </div>
                        <div ng-click="ongoing_tech_response()">                     
                            <div class="widget-title">Tech Response</div>                                                                                  
                            <div class="widget-subtitle">Ongoing</div>           
                            <div class="widget-int">{{ $tech_response_ongoing }}</div>
                        </div>
                    </div>                            
                    <div class="widget-controls">                                
                        <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a>
                    </div>                             
                </div>         
                <!-- END WIDGET SLIDER -->

            </div>
            <!-- <div class="col-md-3">

                <div class="widget widget-default widget-carousel">
                    <div class="owl-carousel" id="owl-example">
                        <div ng-click="job_done_customer()">                                                                         
                            <div class="widget-title">Collection</div>                           
                            <div class="widget-subtitle">Job Done Customers</div> 
                            <div class="widget-int">{{ number_format($job_done_customer, 2) }}</div>
                        </div>
                        <div ng-click="monitoring_customer()">                                                                     
                            <div class="widget-title">Collection</div>                                                                   
                            <div class="widget-subtitle">Monitoring Customer</div>       
                            <div class="widget-int">{{ number_format($monitoring_customer, 2) }}</div>
                        </div>
                        <div ng-click="tech_response_customer()">                                                                   
                            <div class="widget-title">Collection</div>                                                                                
                            <div class="widget-subtitle">Tech Response Customer</div>       
                            <div class="widget-int">{{ number_format($tech_response_customer, 2) }}</div>
                        </div>
                        <div ng-click="credit_supplier()">                                                                                 
                            <div class="widget-title">Payment</div>                                                                      
                            <div class="widget-subtitle">Credit Supplier</div> 
                            <div class="widget-int">{{ number_format($credit_suppliers, 2) }}</div>
                        </div>
                    </div>                            
                    <div class="widget-controls">                                
                        <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a>
                    </div>                             
                </div>         

            </div> -->
            <div class="col-md-4">

                <!-- START WIDGET CLOCK -->
                <div class="widget widget-info widget-padding-sm">
                    <div class="widget-big-int plugin-clock" style="padding: 10px;">00:00</div>                            
                    <div class="widget-subtitle plugin-date">Loading...</div>
                    <div class="widget-controls">                                
                        <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="left" title="Remove Widget"><span class="fa fa-times"></span></a>
                    </div>                            
    <!--                <div class="widget-buttons widget-c3">
                        <div class="col">
                            <a href="#"><span class="fa fa-clock-o"></span></a>
                        </div>
                        <div class="col">
                            <a href="#"><span class="fa fa-bell"></span></a>
                        </div>
                        <div class="col">
                            <a href="#"><span class="fa fa-calendar"></span></a>
                        </div>
                    </div>                            -->
                </div>                        
                <!-- END WIDGET CLOCK -->

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <!-- START USERS ACTIVITY BLOCK -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>Sales Summary</h3>
                            <span>Active Team</span>
                        </div>                                    
                        <ul class="panel-controls" style="margin-top: 2px;">
                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                            <li><a ng-click="sales_summary_refresh()" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>                                        
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                    <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                </ul>                                        
                            </li>                                        
                        </ul>  
                    </div>                                
                    <div class="panel-body padding-0">
                        <div class="row" style="margin: 10px;">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="form-group">                                              
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default pull-left" id="btn_daterange_sales_summary" style="padding: 6px; border-radius: 5px;" class="form-control">
                                            <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                            </span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                        <input type="hidden" ng-model="data.sales_summary_from" id="sales_summary_from" name="sales_summary_from" class="form-control" >
                                        <input type="hidden" ng-model="data.sales_summary_to" id="sales_summary_to" name="sales_summary_to" class="form-control" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chart-holder" id="dashboard-bar-1" style="min-height: 275px;"></div>
                    </div>                                    
                </div>
                <!-- END USERS ACTIVITY BLOCK -->
            </div>
            <div class="col-md-6">
                <!-- START VISITORS BLOCK -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>Sales Target</h3>
                            <span>Individual</span>
                        </div>
                        <ul class="panel-controls" style="margin-top: 2px;">
                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                            <li><a ng-click="sales_target_refresh()" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>                                        
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                    <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                </ul>                                        
                            </li>                                        
                        </ul>
                    </div>
                    <div class="panel-body padding-0">
                        <div class="row" style="margin: 10px;">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="form-group">                                              
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default pull-left" id="btn_daterange_sales_target" style="padding: 6px; border-radius: 5px;" class="form-control">
                                            <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                            </span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                        <input type="hidden" ng-model="data.sales_target_from" id="sales_target_from" name="sales_target_from" class="form-control" >
                                        <input type="hidden" ng-model="data.sales_target_to" id="sales_target_to" name="sales_target_to" class="form-control" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-2" style="margin-top: 5px;">
                                <div class="form-group">
                                    <select name="sales_person" id="sales_person" ng-options="option.name for option in sales_person_array track by option.id" ng-model="data.sales_person" ng-change="sales_target_refresh()" class="form-control" ></select>
                                </div>
                            </div>
                        </div>
                        <div class="chart-holder" id="dashboard-donut-1" style="min-height: 235px;"></div>
                    </div>
                </div>
                <!-- END VISITORS BLOCK -->
            </div>
            <!-- <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>Payment Collection</h3>
                            <span>Monthly</span>
                        </div>
                        <ul class="panel-controls" style="margin-top: 2px;">
                            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                            <li><a ng-click="payment_collection_refresh()" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>                                        
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                    <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                </ul>                                        
                            </li>                                        
                        </ul>
                    </div>
                    <div class="panel-body padding-0">
                        <div class="row" style="margin: 10px;">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="form-group">                                              
                                    <div class="input-group">
                                        <button type="button" class="btn btn-default pull-left" id="btn_daterange_payment_collection" style="padding: 6px; border-radius: 5px;" class="form-control">
                                            <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                            </span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                        <input type="hidden" ng-model="data.payment_collection_from" id="payment_collection_from" name="payment_collection_from" class="form-control" >
                                        <input type="hidden" ng-model="data.payment_collection_to" id="payment_collection_to" name="payment_collection_to" class="form-control" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chart-holder" id="dashboard-line-1" style="min-height: 275px;"></div>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- END WIDGETS -->  

        <div id="dataModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-body">

                    <div class="col-md-12">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">Sales Target <strong><%= data.sales_person.name %></strong> From : <%= data.sales_target_from %> To : <%= data.sales_target_to %></h5>
                                <ul class="panel-controls">
                                    <li><a href="#" data-dismiss="modal"><span class="fa fa-times"></span></a></li>
                                </ul>
                            </div>
                            <div class="panel-body"> 
                                <div class="row" style="width: 100%;" ng-bind-html="sales_target_data | unsafe"></div>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    <?php } else {?>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1>Welcome to M3FORCE!</h1>
            </div>
        </div>
    <?php } ?>
</div>   

<?php if(is_array(session()->get('user_group')) && !in_array(3, session()->get('user_group')) && !in_array(4, session()->get('user_group')) && !in_array(5, session()->get('user_group'))){ ?>
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
        'ui.grid.resizeColumns',
        'ui.grid.moveColumns', 
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
        angular.element(document.querySelector('#main_menu_dashboard')).addClass('active');
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
        
    myApp.filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);
        };
    });

    myApp.controller('mainController', ['$scope', '$http', '$rootScope', 'uiGridConstants', '$timeout', '$window', function ($scope, $http, $rootScope, uiGridConstants, $timeout, $window) {     
        $scope.base_url = base_url;
        $scope.data = [];
        $scope.sales_person_array = [];
        
        $http({
            method: 'GET',
            url: base_url + '/get_filter_data'
        }).then(function successCallback(response) {
            var sales_person_array = [];
            $.each(response.data.sales_team, function (index, value) {
                sales_person_array.push({
                    id: value.id,
                    name: value.name
                });
            });
            
            $scope.sales_person_array = sales_person_array;
            $scope.data.sales_person = $scope.sales_person_array.length > 0 ? $scope.sales_person_array[0] : {};
        }, function errorCallback(response) {
            console.log(response);
        });
        
        $('#btn_daterange_sales_summary').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 6 Months': [moment().subtract(5, 'month').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month')
        }, function (start, end) {
            $('#btn_daterange_sales_summary span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
            $scope.data.sales_summary_from = start.format('YYYY-MM-DD');
            $scope.data.sales_summary_to = end.format('YYYY-MM-DD');
            $scope.sales_summary_refresh();
        });
        
        $('#btn_daterange_sales_target').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 6 Months': [moment().subtract(5, 'month').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month')
        }, function (start, end) {
            $('#btn_daterange_sales_target span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
            $scope.data.sales_target_from = start.format('YYYY-MM-DD');
            $scope.data.sales_target_to = end.format('YYYY-MM-DD');
            $scope.sales_target_refresh();
        });
        
        $('#btn_daterange_payment_collection').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 6 Months': [moment().subtract(5, 'month').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            startDate: moment().subtract(5, 'month').startOf('month'),
            endDate: moment().endOf('month')
        }, function (start, end) {
            $('#btn_daterange_payment_collection span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
            $scope.data.payment_collection_from = start.format('YYYY-MM-DD');
            $scope.data.payment_collection_to = end.format('YYYY-MM-DD');
            $scope.payment_collection_refresh();
        });
        
        var date = new Date();
        var from = new Date(date.getFullYear(), date.getMonth(), 1);
        var to = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        $('#btn_daterange_sales_summary span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.sales_summary_from = getNumberFormattedDate(from);
        $scope.data.sales_summary_to = getNumberFormattedDate(to);
        $('#btn_daterange_sales_target span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.sales_target_from = getNumberFormattedDate(from);
        $scope.data.sales_target_to = getNumberFormattedDate(to);
        from.setMonth(date.getMonth() - 5);
        $('#btn_daterange_payment_collection span').html(getStringFormattedDate(from) + ' - ' + getStringFormattedDate(to));
        $scope.data.payment_collection_from = getNumberFormattedDate(from);
        $scope.data.payment_collection_to = getNumberFormattedDate(to);
            
        $scope.sales_summary_refresh = function(){            
            $('#dashboard-bar-1').empty();
            document.getElementById('data_load').style.visibility = "visible";
            $http({
                method: 'GET',
                url: base_url + '/dashboard/get_bar_data',
                params: {
                    from : $scope.data.sales_summary_from,
                    to : $scope.data.sales_summary_to
                }
            }).then(function successCallback(response) { 
                var bar_data = response.data.bar_data;
                if(bar_data.length > 0){
                    Morris.Bar({
                        element: 'dashboard-bar-1',
                        data: bar_data,
                        xkey: 'month',
                        ykeys: response.data.ykeys,
                        labels: response.data.labels,
                        barColors: response.data.barColors,
                        gridTextSize: '10px',
                        hideHover: true,
                        resize: true,
                        gridLineColor: '#E5E5E5'
                    });
                } else{
                    $('#dashboard-bar-1').append('<p class="error-block" style="padding-top: 100px;">Sales Summary Not Available</p>')
                }
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };
            
        $scope.sales_target_refresh = function(){   
            $('#dashboard-donut-1').empty();     
            document.getElementById('data_load').style.visibility = "visible";       
            $http({
                method: 'GET',
                url: base_url + '/dashboard/get_donut_data',
                params: {
                    from : $scope.data.sales_target_from,
                    to : $scope.data.sales_target_to,
                    sales_team_id : $scope.data.sales_person ? $scope.data.sales_person.id : 0
                }
            }).then(function successCallback(response) { 
                var donut_data = [];
                $.each(response.data, function (index, value) {
                    donut_data.push({
                        label: value.label, 
                        value: value.value
                    });
                });
                if(donut_data.length > 0){
                    Morris.Donut({
                        element: 'dashboard-donut-1',
                        data: donut_data,
                        colors: ['#33414E', '#1caf9a'],
                        resize: true
                    }).on('click', function (i, row) {  
                        $scope.get_sales_target_data();
                    });
                } else{
                    $('#dashboard-donut-1').append('<p class="error-block" style="padding-top: 100px;">Sales Target Not Available</p>')
                }
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };  
            
        $scope.get_sales_target_data = function(){ 
            document.getElementById('data_load').style.visibility = "visible";
            $('#dataModal').modal('show'); 
            $scope.sales_target_data = '';      
            $http({
                method: 'GET',
                url: base_url + '/dashboard/get_sales_target_data',
                params: {
                    from : $scope.data.sales_target_from,
                    to : $scope.data.sales_target_to,
                    sales_team_id : $scope.data.sales_person ? $scope.data.sales_person.id : 0
                }
            }).then(function successCallback(response) { 
                $scope.sales_target_data = response.data.view;
                $('#data_table').dataTable({
                    "aaSorting": [[0, 'asc']],
                    "paging": false,
                    "searching": false,
                    "info": false
                });
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };  
            
        $scope.payment_collection_refresh = function(){   
            $('#dashboard-line-1').empty();   
            document.getElementById('data_load').style.visibility = "visible";       
            $http({
                method: 'GET',
                url: base_url + '/dashboard/get_line_data',
                params: {
                    from : $scope.data.payment_collection_from,
                    to : $scope.data.payment_collection_to
                }
            }).then(function successCallback(response) { 
                var line_data = [];
                $.each(response.data, function (index, value) {
                    line_data.push({
                        month: value.month, 
                        collection: value.collection
                    });
                });
                if(line_data.length > 0){
                    Morris.Line({
                      element: 'dashboard-line-1',
                      data: line_data,
                      xkey: 'month',
                      ykeys: ['collection'],
                      labels: ['Collection'],
                      resize: true,
                      hideHover: true,
                      xLabels: 'month',
                      gridTextSize: '10px',
                      lineColors: ['#1caf9a'],
                      gridLineColor: '#E5E5E5'
                    }); 
                } else{
                    $('#dashboard-line-1').append('<p class="error-block" style="padding-top: 65px;">Payment Collection Not Available</p>')
                }
                document.getElementById('data_load').style.visibility = "hidden";
            }, function errorCallback(response) {
                console.log(response);
            });
        };        
            
        $scope.main_refresh = function(){            
            $scope.sales_summary_refresh();        
            $scope.sales_target_refresh();
            $scope.payment_collection_refresh();
        };
        
        $scope.new_inquiry = function(){
            $window.open(base_url + '/inquiry/new_inquiry', '_self');
        };
        
        $scope.ongoing_inquiry = function(){
            $window.open(base_url + '/inquiry/ongoing_inquiry', '_self');
        };
        
        $scope.new_job = function(){
            $window.open(base_url + '/job/new_job', '_self');
        };
        
        $scope.ongoing_job = function(){
            $window.open(base_url + '/job/ongoing_job', '_self');
        };
        
        $scope.ongoing_tech_response = function(){
            $window.open(base_url + '/tech_response/ongoing_tech_response', '_self');
        };
        
        $scope.job_done_customer = function(){
            $window.open(base_url + '/job_done_customer', '_self');
        };
        
        $scope.monitoring_customer = function(){
            $window.open(base_url + '/monitoring_customer', '_self');
        };
        
        $scope.tech_response_customer = function(){
            $window.open(base_url + '/tech_response_customer', '_self');
        };
        
        $scope.credit_supplier = function(){
            $window.open(base_url + '/payment/credit_supplier', '_self');
        };

        $timeout(function () {
            $scope.main_refresh();
        }, 1500); 
    }]);
</script>
<?php } else {?>
<script type="text/javascript"> 
    var myApp = angular.module('myModule', []).config(function ($interpolateProvider) {
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

    myApp.controller('myController',['$http', '$scope', '$window', '$timeout', function ($http, $scope, $window, $timeout) {                        
        // function to submit the form after all validation has occurred
    }]);
</script> 
<?php } ?>

@endsection