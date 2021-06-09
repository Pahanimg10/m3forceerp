@extends('layouts.main')

@section('title')
<title>M3Force | Petty Cash Issue Details</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Petty Cash</a></li>                    
    <li class="active">Petty Cash Issue Details</li>
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
                    <h3 class="panel-title"><strong>Petty Cash Issue Details</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" ng-disabled="edit_disable" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-show="data.petty_cash_issue_id" ng-disabled="edit_disable || permission || data.is_posted == 1" ng-click="postForm()" class="btn btn-danger" id="post_button">Issue</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><div><a ng-show="data.petty_cash_issue_id && data.is_posted == 1" target="_blank" class="btn btn-default" ng-href="<%=base_url%>/petty_cash_issue/print_petty_cash_issue?id=<%=data.petty_cash_issue_id%>">Print</a></div></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('petty_cash_issue')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="petty_cash_issue_id" name="petty_cash_issue_id" ng-model="data.petty_cash_issue_id" class="form-control" />
                                <input type="hidden" id="item_code" name="item_code" ng-model="data.item_code" class="form-control" />
                                <input type="hidden" id="item_name" name="item_name" ng-model="data.item_name" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Reference Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Issue Type</label>
                                            <select name="petty_cash_issue_type" id="petty_cash_issue_type" ng-options="option.name for option in petty_cash_issue_type_array track by option.id" ng-model="data.petty_cash_issue_type" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.petty_cash_issue_type && data.petty_cash_issue_type.id != 3">
                                        <div class="form-group">
                                            <label class="control-label">Document</label>
                                            <input type="text" id="document" name="document" ui-grid-edit-auto ng-model="data.document" ng-disabled="edit_disable" typeahead="name as document_array.name for document_array in document_array | filter:$viewValue | limitTo:5" typeahead-min-length="1" typeahead-on-select="onDocumentSelect($item, $model, $label)" ng-keyup="get_documents(data.petty_cash_issue_type.id, data.document)" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4" ng-show="data.petty_cash_issue_type && data.petty_cash_issue_type.id != 3">
                                        <div class="form-group">
                                            <label class="control-label">Customer</label>
                                            <input type="text" id="customer" name="customer" ng-model="data.customer" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Issued To</label>
                                            <input type="text" id="issued_to" name="issued_to" ng-model="data.issued_to" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Petty Cash Issue Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Issue No</label>
                                            <input type="text" id="petty_cash_issue_no" name="petty_cash_issue_no" ng-model="data.petty_cash_issue_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Issue Mode</label>
                                            <select name="issue_mode" id="issue_mode" ng-options="option.name for option in issue_mode_array track by option.id" ng-model="data.issue_mode" ng-disabled="edit_disable" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Petty Cash Issue Value</label>
                                            <input type="text" id="petty_cash_issue_value" name="petty_cash_issue_value" ng-model="data.petty_cash_issue_value" ng-disabled="edit_disable" class="form-control text-right" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.issue_mode.id == 1">
                                        <div class="form-group">
                                            <label class="control-label">Cheque No</label>
                                            <input type="text" id="cheque_no" name="cheque_no" ng-model="data.cheque_no" ng-disabled="edit_disable" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-3" ng-show="data.issue_mode.id == 3">
                                        <div class="form-group">
                                            <label class="control-label">Bank</label>
                                            <input type="text" id="bank" name="bank" ng-model="data.bank" ng-disabled="edit_disable" class="form-control" />
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
        angular.element(document.querySelector('#sub_menu_petty_cash_issue')).addClass('active');
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
                        petty_cash_issue_type: {
                            required: true
                        },
                        document: {
                            required: true,
                            remote: {
                                url: base_url + '/petty_cash_issue/validate_document_no',
                                type: 'GET',
                                data: {
                                    petty_cash_issue_type: function() {
                                      return scope.data.petty_cash_issue_type && scope.data.petty_cash_issue_type.id ? scope.data.petty_cash_issue_type.id : '';
                                    },
                                    document: function() {
                                      return scope.data.document && scope.data.document.name ? scope.data.document.name : scope.data.document;
                                    }
                                }
                            }
                        },
                        issued_to: {
                            required: true
                        },
                        issue_mode: {
                            required: true
                        },
                        petty_cash_issue_value: {
                            required: true,
                            number: true,
                            min: 0
                        }, 
                        cheque_no: {
                            required: function(element){
                                return $('#issue_mode').val() == 1;
                            }
                        },
                        bank: {
                            required: function(element){
                                return $('#issue_mode').val() == 3;
                            }
                        },
                        errorClass:'error'
                    },
                    messages: {
                        petty_cash_issue_type: {
                            required: 'Petty Cash Issue Type is required'
                        },
                        document: {
                            required: 'Document is required',
                            remote: 'Invalid Document'
                        },
                        issued_to: {
                            required: 'Issued To is required'
                        },
                        issue_mode: {
                            required: 'Issue Mode is required'
                        },
                        petty_cash_issue_value: {
                            required: 'Petty Cash Issue Value is required',
                            number: 'Invalid number format',
                            min: 'Minimum quantity 0'
                        },
                        cheque_no: {
                            required: 'Cheque No is required'
                        },
                        bank: {
                            required: 'Bank is required'
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
        $scope.petty_cash_issue_type_array = [];
        $scope.issue_mode_array = [];
        $scope.document_array = [];
        
        $scope.data.petty_cash_issue_id = <?php echo $petty_cash_issue_id ? $petty_cash_issue_id : 0; ?>;
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/petty_cash_issue/get_data'
            }).then(function successCallback(response) {
                var petty_cash_issue_type_array = [];
                petty_cash_issue_type_array.push({
                    id: '',
                    name: 'Select Petty Cash Issue Type'
                });
                $.each(response.data.petty_cash_issue_types, function (index, value) {
                    petty_cash_issue_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });   
                var issue_mode_array = [];
                issue_mode_array.push({
                    id: '',
                    name: 'Select Issue Mode'
                });
                $.each(response.data.issue_modes, function (index, value) {
                    issue_mode_array.push({
                        id: value.id,
                        name: value.name
                    });
                }); 

                $scope.petty_cash_issue_type_array = petty_cash_issue_type_array;
                $scope.issue_mode_array = issue_mode_array;
                
                $scope.data = {
                    petty_cash_issue_id: $scope.data.petty_cash_issue_id,
                    petty_cash_issue_type: $scope.petty_cash_issue_type_array.length > 0 ? $scope.petty_cash_issue_type_array[0] : {},
                    document: '',
                    customer: '',
                    issued_to: '',
                    petty_cash_issue_no: '',
                    issue_mode: $scope.issue_mode_array.length > 0 ? $scope.issue_mode_array[0] : {},
                    petty_cash_issue_value: '',
                    cheque_no: '',
                    bank: '',
                    remarks: '',
                    is_posted: 0
                };

                $http({
                    method: 'GET',
                    url: base_url + '/petty_cash_issue/find_petty_cash_issue',
                    params: {
                        id: $scope.data.petty_cash_issue_id
                    }
                }).then(function successCallback(response) {
                    if(response.data.petty_cash_issue){
                        var document = '';
                        var customer = '';
                        if(response.data.petty_cash_issue.petty_cash_issue_type_id == 1){
                            document = response.data.petty_cash_issue.job ? {id:response.data.petty_cash_issue.job.id, name:response.data.petty_cash_issue.job.job_no} : '';
                            customer = response.data.petty_cash_issue.job && response.data.petty_cash_issue.job.inquiry && response.data.petty_cash_issue.job.inquiry.contact ? response.data.petty_cash_issue.job.inquiry.contact.name : '';
                        } else if(response.data.petty_cash_issue.petty_cash_issue_type_id == 2){
                            document = response.data.petty_cash_issue.tech_response ? {id:response.data.petty_cash_issue.tech_response.id, name:response.data.petty_cash_issue.tech_response.tech_response_no} : '';
                            customer = response.data.petty_cash_issue.tech_response && response.data.petty_cash_issue.tech_response.contact ? response.data.petty_cash_issue.tech_response.contact.name : '';
                        }
                        
                        $scope.data.petty_cash_issue_id = response.data.petty_cash_issue.id;
                        $scope.data.petty_cash_issue_type = response.data.petty_cash_issue.item_issue_type ? {id: response.data.petty_cash_issue.item_issue_type.id, name: response.data.petty_cash_issue.item_issue_type.name} : '';
                        $scope.data.document = document;
                        $scope.data.customer = customer;
                        $scope.data.issued_to = response.data.petty_cash_issue.issued_to;
                        $scope.data.petty_cash_issue_no = response.data.petty_cash_issue.petty_cash_issue_no;
                        $scope.data.issue_mode = response.data.petty_cash_issue.issue_mode ? {id: response.data.petty_cash_issue.issue_mode.id, name: response.data.petty_cash_issue.issue_mode.name} : '';
                        $scope.data.petty_cash_issue_value = parseFloat(Math.round(response.data.petty_cash_issue.petty_cash_issue_value * 100) / 100).toFixed(2);
                        $scope.data.cheque_no = response.data.petty_cash_issue.cheque_no;
                        $scope.data.bank = response.data.petty_cash_issue.bank;
                        $scope.data.remarks = response.data.petty_cash_issue.remarks;
                        $scope.data.is_posted = response.data.petty_cash_issue.is_posted;
                        $scope.edit_disable = response.data.petty_cash_issue.is_posted == 1 ? true : false;
                    }
                    $scope.permission = response.data.permission;
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };
        
        $timeout(function () {
            $scope.refreshForm();
        }, 1500, false);

        $scope.get_documents = function(petty_cash_issue_type_id, document_no){  
            if(document_no && document_no.length > 0){
                $scope.document_array = [];
                if(petty_cash_issue_type_id == 1){
                    $http({
                        method: 'GET',
                        url: base_url + '/get_job_nos',
                        params:{
                            job_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];            
                        $.each(response.data, function (index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.job_no,
                                customer: value.inquiry && value.inquiry.contact ? value.inquiry.contact.name : ''
                            });
                        });
                        $scope.find_document(petty_cash_issue_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                } else if(petty_cash_issue_type_id == 2){
                    $http({
                        method: 'GET',
                        url: base_url + '/get_tech_response_nos',
                        params:{
                            tech_response_no: document_no
                        }
                    }).then(function successCallback(response) {
                        $scope.document_array = [];            
                        $.each(response.data, function (index, value) {
                            $scope.document_array.push({
                                id: value.id,
                                name: value.tech_response_no,
                                customer: value.contact ? value.contact.name : ''
                            });
                        });
                        $scope.find_document(petty_cash_issue_type_id, document_no);
                    }, function errorCallback(response) {
                        console.log(response);
                    });
                }
            }
        };

        $scope.find_document = function(petty_cash_issue_type_id, document_no){  
            if(petty_cash_issue_type_id == 1){
                $http({
                    method: 'GET',
                    url: base_url + '/find_job_no',
                    params:{
                        job_no: document_no
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.data.document = {id: response.data.id, name: response.data.job_no};
                        $scope.data.customer = response.data.inquiry && response.data.inquiry.contact ? response.data.inquiry.contact.name : '';
                    } 
                }, function errorCallback(response) {
                    console.log(response);
                });
            } else if(petty_cash_issue_type_id == 2){
                $http({
                    method: 'GET',
                    url: base_url + '/find_tech_response_no',
                    params:{
                        tech_response_no: document_no
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.data.document = {id: response.data.id, name: response.data.tech_response_no};
                        $scope.data.customer = response.data.contact ? response.data.contact.name : '';
                    } 
                }, function errorCallback(response) {
                    console.log(response);
                });
            }
        };
        
        $scope.onDocumentSelect = function ($item, $model, $label) {
            $scope.data.document = {id: $item.id, name: $item.name};
            $scope.data.customer = $item.customer;
            $timeout(function() { 
                $scope.find_document($scope.data.document.name);
                $('#issued_to').focus(); 
            }, 200, false);
        };

        $scope.resetForm = function(){
            $scope.data.petty_cash_issue_id = 0;
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
                text: "Petty Cash Issue Value : <strong>Rs "+parseFloat(Math.round($scope.data.petty_cash_issue_value * 100) / 100).toFixed(2)+"</strong>",
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
                    url: base_url + '/petty_cash_issue/post_petty_cash_issue',
                    params: {
                        id: $scope.data.petty_cash_issue_id
                    }
                }).then(function successCallback(result) {
                    $('#post_button').prop('disabled', false);
                    if(result.data.response){
                        swal({
                            title: "Issued!", 
                            text: "Petty Cash Issue No : "+ $scope.data.petty_cash_issue_no,
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
            if($scope.data.petty_cash_issue_id == 0){
                $http.post(base_url + '/petty_cash_issue', $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Petty Cash Issue Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.petty_cash_issue_id = result.response ? result.data.id : 0;
                    $scope.data.petty_cash_issue_no = result.response ? result.data.petty_cash_issue_no : 0;
                    $scope.data.petty_cash_issue_value = result.response ? parseFloat(Math.round(result.data.petty_cash_issue_value * 100) / 100).toFixed(2) : 0;
                });
            } else{
                $http.put(base_url + '/petty_cash_issue/'+$scope.data.id, $scope.data).success(function (result) {
                    $('#save_button').prop('disabled', false);
                    $.pnotify && $.pnotify({
                        title: 'Petty Cash Issue Details',
                        text: result.message,
                        type: result.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });

                    $scope.data.petty_cash_issue_id = result.response ? result.data.id : 0;
                    $scope.data.petty_cash_issue_no = result.response ? result.data.petty_cash_issue_no : 0;
                    $scope.data.petty_cash_issue_value = result.response ? parseFloat(Math.round(result.data.petty_cash_issue_value * 100) / 100).toFixed(2) : 0;
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