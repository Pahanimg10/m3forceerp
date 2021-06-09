@extends('layouts.main')

@section('title')
<title>M3Force | Petty Cash Return Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Petty Cash</a></li>                    
    <li class="active">Petty Cash Return Details</li>
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
                    <h3 class="panel-title"><strong>Petty Cash Return Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.petty_cash_return_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Return</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><div><a ng-show="data.petty_cash_return_id && data.is_posted == 1" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/petty_cash_return/print_petty_cash_return?id=<%=data.petty_cash_return_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('petty_cash_return')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="petty_cash_return_id" name="petty_cash_return_id" ng-model="data.petty_cash_return_id" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Issue No</label>
                                            <input type="text" id="petty_cash_issue_no" name="petty_cash_issue_no" ui-grid-edit-auto ng-model="data.petty_cash_issue_no" ng-disabled="edit_disable" typeahead="name as petty_cash_issue_no_array.name for petty_cash_issue_no_array in petty_cash_issue_no_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onPettyCashIssueNoSelect($item, $model, $label)" ng-keyup="get_petty_cash_issue_nos(data.petty_cash_issue_no)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label">Issued To</label>
                                            <input type="text" id="issued_to" name="issued_to" ng-model="data.issued_to" class="form-control" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Petty Cash Return Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Return No</label>
                                            <input type="text" id="petty_cash_return_no" name="petty_cash_return_no" ng-model="data.petty_cash_return_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Return Date</label>
                                            <input type="text" id="petty_cash_return_date" name="petty_cash_return_date" uib-datepicker-popup="<%=dateFormat%>" ng-model="data.petty_cash_return_date" ng-disabled="edit_disable" is-open="pettyCashReturnDatePopup.opened" datepicker-options="availableDateOptions" close-text="Close" ng-click="OpenPettyCashReturnDate()" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Return Time</label>
                                            <input type="text" id="petty_cash_return_time" name="petty_cash_return_time" ng-model="data.petty_cash_return_time" ng-disabled="edit_disable" class="form-control text-center" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Return Value</label>
                                            <input type="text" id="petty_cash_return_value" name="petty_cash_return_value" ng-model="data.petty_cash_return_value" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                            <input type="text" id="remarks" name="remarks" ng-model="data.remarks" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                            </div>
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
        angular.element(document.querySelector('#main_menu_petty_cash')).addClass('active');
        angular.element(document.querySelector('#sub_menu_petty_cash_return')).addClass('active');
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
                        petty_cash_issue_no: {
                            required: true,
                            remote: {
                                url: base_url + '/petty_cash_return/validate_petty_cash_issue_no',
                                type: 'GET',
                                data: {
                                    petty_cash_issue_no: function() {
                                      return scope.data.petty_cash_issue_no && scope.data.petty_cash_issue_no.name ? scope.data.petty_cash_issue_no.name : scope.data.petty_cash_issue_no;
                                    }
                                }
                            }
                        },
                        petty_cash_return_date: {
                            required: true,
                            date: true
                        },
                        petty_cash_return_time: {
                            required: true,
                            minlength: 5,
                            validTime: true
                        },
                        petty_cash_return_value: {
                            required: true,
                            remote: {
                                url: base_url + '/petty_cash_return/validate_petty_cash_return_value',
                                type: 'GET',
                                data: {
                                    petty_cash_issue_no: function() {
                                      return scope.data.petty_cash_issue_no && scope.data.petty_cash_issue_no.name ? scope.data.petty_cash_issue_no.name : scope.data.petty_cash_issue_no;
                                    },
                                    petty_cash_return_value: function() {
                                      return scope.data.petty_cash_return_value;
                                    }
                                }
                            },
                            number: true,
                            min: 0
                        }, 
                        errorClass:'error'
                    },
                    messages: {
                        petty_cash_issue_no: {
                            required: 'Petty Cash Issue No is required',
                            remote: 'Invalid Petty Cash Issue No'
                        },
                        petty_cash_return_date: {
                            required: 'Petty Cash Return Date is required',
                            date: 'Invalid date format'
                        },
                        petty_cash_return_time: {
                            required: 'Petty Cash Return Time is required',
                            minlength: 'Invalid time format',
                            validTime: 'Invalid time'
                        },
                        petty_cash_return_value: {
                            required: 'Petty Cash Return Value is required',
                            remote: 'Invalid value',
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
        $scope.base_url = base_url;
        $scope.edit_disable = false;
        $scope.permission = false;
            
        $scope.data = [];
        $scope.petty_cash_issue_no_array = [];
        
        $scope.data.petty_cash_return_id = <?php echo $petty_cash_return_id ? $petty_cash_return_id : 0; ?>;
        
        $scope.dateFormat = 'yyyy-MM-dd';
        $scope.availableDateOptions = {
            formatYear: 'yy',
            startingDay: 1,
        };
        
        $scope.pettyCashReturnDatePopup = {
            opened: false
        };        
        $scope.OpenPettyCashReturnDate = function () {
            $scope.pettyCashReturnDatePopup.opened = !$scope.pettyCashReturnDatePopup.opened;
        };
        
        $('#petty_cash_return_time').mask('00:00');
        
        $scope.refreshForm = function(){
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
                petty_cash_return_id: $scope.data.petty_cash_return_id,
                petty_cash_issue_no: '',
                issued_to: '',
                petty_cash_return_no: '',
                petty_cash_return_date: new Date(),
                petty_cash_return_time: hh+':'+mm,
                petty_cash_return_value: '',
                remarks: '',
                is_posted: 0
            };

            $http({
                method: 'GET',
                url: base_url + '/petty_cash_return/find_petty_cash_return',
                params: {
                    id: $scope.data.petty_cash_return_id
                }
            }).then(function successCallback(response) {
                if(response.data.petty_cash_return){
                    var petty_cash_return = response.data.petty_cash_return;
                    var issued_to = '';
                    if(petty_cash_return.petty_cash_issue && petty_cash_return.petty_cash_issue.petty_cash_issue_type_id == 1){
                        issued_to = petty_cash_return.petty_cash_issue && petty_cash_return.petty_cash_issue.job && petty_cash_return.petty_cash_issue.job.inquiry && petty_cash_return.petty_cash_issue.job.inquiry.contact ? petty_cash_return.petty_cash_issue.job.inquiry.contact.name+' : '+petty_cash_return.petty_cash_issue.issued_to : '';
                    } else if(petty_cash_return.petty_cash_issue && petty_cash_return.petty_cash_issue.petty_cash_issue_type_id == 2){
                        issued_to = petty_cash_return.petty_cash_issue && petty_cash_return.petty_cash_issue.tech_response && petty_cash_return.petty_cash_issue.tech_response.contact ? petty_cash_return.petty_cash_issue.tech_response.contact.name+' : '+petty_cash_return.petty_cash_issue.issued_to : '';
                    }
                    
                    var petty_cash_return_date_time = petty_cash_return.petty_cash_return_date_time.split(' ');
                    $scope.data.petty_cash_return_id = petty_cash_return.id;
                    $scope.data.petty_cash_issue_no = petty_cash_return.petty_cash_issue ? {id:petty_cash_return.petty_cash_issue.id, name:petty_cash_return.petty_cash_issue.petty_cash_issue_no} : '';
                    $scope.data.issued_to = issued_to;
                    $scope.data.petty_cash_return_no = petty_cash_return.petty_cash_return_no;
                    $scope.data.petty_cash_return_date = petty_cash_return_date_time[0];
                    $scope.data.petty_cash_return_time = petty_cash_return_date_time[1];
                    $scope.data.petty_cash_return_value = parseFloat(Math.round(petty_cash_return.petty_cash_return_value * 100) / 100).toFixed(2);
                    $scope.data.remarks = petty_cash_return.remarks;
                    $scope.data.is_posted = petty_cash_return.is_posted;
                    $scope.edit_disable = petty_cash_return.is_posted == 1 ? true : false;
                }
                $scope.permission = response.data.permission;
            }, function errorCallback(response) {
                console.log(response);
            });

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };
        
        $scope.refreshForm();

        $scope.get_petty_cash_issue_nos = function(petty_cash_issue_no){ 
            $scope.petty_cash_issue_no_array = [];
            $http({
                method: 'GET',
                url: base_url + '/get_petty_cash_issue_nos',
                params:{
                    petty_cash_issue_no: petty_cash_issue_no
                }
            }).then(function successCallback(response) {
                $scope.petty_cash_issue_no_array = [];            
                $.each(response.data, function (index, value) {
                    var customer = '';
                    customer = value.petty_cash_issue_type_id == 1 && value.job && value.job.inquiry && value.job.inquiry.contact ? value.job.inquiry.contact.name : customer;
                    customer = value.petty_cash_issue_type_id == 2 && value.tech_response && value.tech_response.contact ? value.tech_response.contact.name : customer;
                    
                    $scope.petty_cash_issue_no_array.push({
                        id: value.id,
                        name: value.petty_cash_issue_no,
                        issued_to: customer != '' ? customer+' : '+value.issued_to : value.issued_to
                    });
                });
                $scope.find_petty_cash_issue_no(petty_cash_issue_no);
            }, function errorCallback(response) {
                console.log(response);
            });
        };

        $scope.find_petty_cash_issue_no = function(petty_cash_issue_no){  
            $http({
                method: 'GET',
                url: base_url + '/find_petty_cash_issue_no',
                params:{
                    petty_cash_issue_no: petty_cash_issue_no
                }
            }).then(function successCallback(response) {
                if(response.data){
                    var customer = '';
                    customer = response.data.petty_cash_issue_type_id == 1 && response.data.job && response.data.job.inquiry && response.data.job.inquiry.contact ? response.data.job.inquiry.contact.name : customer;
                    customer = response.data.petty_cash_issue_type_id == 2 && response.data.tech_response && response.data.tech_response.contact ? response.data.tech_response.contact.name : customer;
                    
                    $scope.data.petty_cash_issue_no = {id: response.data.id, name: response.data.petty_cash_issue_no};
                    $scope.data.issued_to = customer != '' ? customer+' : '+response.data.issued_to : response.data.issued_to;
                } 
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.onPettyCashIssueNoSelect = function ($item, $model, $label) {
            $scope.data.petty_cash_issue_no = {id: $item.id, name: $item.name};
            $scope.data.issued_to = $item.issued_to;
            $timeout(function() { 
                $scope.find_petty_cash_issue_no($scope.data.petty_cash_issue_no.name);
                $('#petty_cash_return_date').focus(); 
            }, 200, false);
        };

        $scope.resetForm = function(){
            $scope.data.petty_cash_return_id = 0;
            $scope.refreshForm();

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
                text: "Petty Cash Return Value : <strong>Rs "+parseFloat(Math.round($scope.data.petty_cash_return_value * 100) / 100).toFixed(2)+"</strong>",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, issue it!",
                closeOnConfirm: false
            },
            function(){
                $('#post_button').prop('disabled', true);
                $http({
                    method: 'GET',
                    url: base_url + '/petty_cash_return/post_petty_cash_return',
                    params: {
                        id: $scope.data.petty_cash_return_id
                    }
                }).then(function successCallback(result) {
                    $('#post_button').prop('disabled', false);
                    if(result.data.response){
                        swal({
                            title: "Returned!", 
                            text: "Petty Cash Return No : "+ $scope.data.petty_cash_return_no,
                            html: true,
                            type: "success",
                            confirmButtonColor: "#9ACD32"
                        });
                    } else{
                        swal({
                            title: "Issue Failed!", 
                            text: result.data.message,
                            html: true,
                            type: "error",
                            confirmButtonColor: "#FF0000"
                        });
                    }
                    $scope.refreshForm();
                });
            });
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            if($scope.data.petty_cash_return_id == 0){
                $http.post(base_url + '/petty_cash_return', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Petty Cash Return Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.petty_cash_return_id = result.response ? result.data.id : 0;
                    $scope.data.petty_cash_return_no = result.response ? result.data.petty_cash_return_no : 0;
                    $scope.data.petty_cash_return_value = result.response ? parseFloat(Math.round(result.data.petty_cash_return_value * 100) / 100).toFixed(2) : 0;
                });
            } else{
                $http.put(base_url + '/petty_cash_return/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Petty Cash Return Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.petty_cash_return_id = result.response ? result.data.id : 0;
                    $scope.data.petty_cash_return_no = result.response ? result.data.petty_cash_return_no : 0;
                    $scope.data.petty_cash_return_value = result.response ? parseFloat(Math.round(result.data.petty_cash_return_value * 100) / 100).toFixed(2) : 0;
                });
            }
        };

        $.validator.addMethod('validTime', function(value, element, param) {
            var time = value.split(':');
            return time[0] < 24 && time[1] < 60;
        });
    }]);
</script>
@endsection