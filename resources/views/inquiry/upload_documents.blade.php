@extends('layouts.main')

@section('title')
<title>M3Force | Upload Documents</title>
@endsection

@section('breadcrumb')
<ul class="breadcrumb">
    <li><a href="#">Inquiry</a></li>                    
    <li class="active">Upload Documents</li>
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
                    <h3 class="panel-title"><strong>Upload Documents</strong></h3>
                    <ul class="panel-controls">
                        <li><button type="button" ng-click="submitForm()" class="btn btn-primary" id="save_button">Save</button></li>
                        <li><button type="button" ng-click="resetForm()" class="btn btn-info">Reset</button></li>
                        <li><button type="button" ng-click="export()" class="btn btn-warning">Export CSV</button></li>
                        <li><button type="button" ng-click="toggleFiltering()" class="btn btn-success">Filter</button></li>
                        <li><button type="button" ng-click="refreshForm()" style="display: block; float: left; width: 30px; height: 30px; text-align: center; line-height: 28px; color: #22262e; border: 1px solid #BBB; border-radius: 20%; margin-left: 3px; transition: all 200ms ease;"><span class="fa fa-refresh" style="margin: 0;"></span></button></li>
                        <li><a href="{{ asset('inquiry/ongoing_inquiry')}}"><span class="fa fa-times"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">       
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">

                                <input type="hidden" id="data_id" name="data_id" ng-model="data.id" class="form-control" />
                                <input type="hidden" id="value" name="value" ng-model="data.value" class="form-control" />
                                <input type="hidden" id="inquiry_id" name="inquiry_id" ng-model="data.inquiry_id" class="form-control" />
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Customer Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Customer Name</label>
                                            <input type="text" id="customer_name" name="customer_name" ng-model="customer_name" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Customer Address</label>
                                            <input type="text" id="customer_address" name="customer_address" ng-model="customer_address" class="form-control" disabled />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Customer Contact No</label>
                                            <input type="text" id="customer_contact_no" name="customer_contact_no" ng-model="customer_contact_no" class="form-control text-center" disabled />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <h4 class="col-md-12" style="padding-top: 15px;">Document Details</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Document Type</label>
                                            <select name="document_type" id="document_type" ng-options="option.name for option in document_type_array track by option.id" ng-model="data.document_type" class="form-control" ></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Document Name</label>
                                            <input type="text" id="document_name" name="document_name" ng-model="data.document_name" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Document Upload</label>
                                            <input ng-model="data.upload_document" id="upload_document" name="upload_document" type="file" onchange="angular.element(this).scope().uploadedFile(this)" />
                                        </div>
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
        angular.element(document.querySelector('#main_menu_inquiry')).addClass('active');
        angular.element(document.querySelector('#sub_menu_ongoing_inquiry')).addClass('active');
    }); 
    
    myApp.directive('jValidate', function () {
        return {
            link: function (scope, element, attr) {
                element.validate({
                    rules: {
                        document_type: {
                            required: true
                        },
                        document_name: {
                            required: true,
                            remote: {
                                url: base_url + '/inquiry/validate_document_upload',
                                type: 'GET',
                                data: {
                                    value: function() {
                                      return scope.data.value;
                                    },
                                    inquiry_id: function() {
                                      return scope.data.inquiry_id;
                                    },
                                    document_name: function() {
                                      return scope.data.document_name;
                                    }
                                }
                            }
                        },
                        upload_document: {
                            required: true
                        },
                        errorClass:'error'
                    },
                    messages: {
                        document_type: {
                            required: 'Document Type is required'
                        },
                        document_name: {
                            required: 'Document Name is required',
                            remote: 'Document Name already exist'
                        },
                        upload_document: {
                            required: 'Document Upload is required'
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
        $scope.files = [];
        $scope.file_source = '';
        $scope.data = [];
        $scope.document_type_array = [];
        
        $scope.refreshForm = function(){
            $http({
                method: 'GET',
                url: base_url + '/get_document_types'
            }).then(function successCallback(response) {
                var document_type_array = [];
                document_type_array.push({
                    id: '',
                    name: 'Select Document Type'
                });
                $.each(response.data, function (index, value) {
                    document_type_array.push({
                        id: value.id,
                        name: value.name
                    });
                });  

                $scope.document_type_array = document_type_array;
                
                $scope.files = [];
                $scope.file_source = '';
                $('#upload_document').val('');
                $scope.data = {
                    type: 2,
                    id: 0,
                    value: '',
                    inquiry_id: <?php echo $inquiry_id ? $inquiry_id : 0; ?>,
                    document_type: $scope.document_type_array.length > 0 ? $scope.document_type_array[0] : {},
                    document_name: '',
                    upload_document: ''
                };
                
                $http({
                    method: 'GET',
                    url: base_url + '/inquiry/find_inquiry',
                    params: {
                        id: <?php echo $inquiry_id ? $inquiry_id : 0; ?>
                    }
                }).then(function successCallback(response) {
                    if(response.data){
                        $scope.customer_name = response.data.contact ? response.data.contact.name : '';
                        $scope.customer_address = response.data.contact ? response.data.contact.address : '';
                        $scope.customer_contact_no = response.data.contact ? response.data.contact.contact_no : '';
                    }
                }, function errorCallback(response) {
                    console.log(response);
                });
            }, function errorCallback(response) {
                console.log(response);
            });
        };
        
        $scope.refreshForm();
        
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
                    field: 'document_type', 
                    displayName: 'Document Type', 
                    width: '30%', 
                    enableCellEdit: false
                },
                {
                    field: 'document_name', 
                    displayName: 'Document Name', 
                    width: '60%', 
                    enableCellEdit: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><a ng-href="'+ base_url + '/assets/uploads/documents/<%= row.entity.upload_document %>" target="_blank"><%= row.entity.document_name %></a></div>'
                },
                {
                    field: 'options', 
                    displayName: '', 
                    enableFiltering: false, 
                    enableSorting: false, 
                    enableCellEdit: false,
                    width: '10%', 
                    cellTemplate: '<div class="text-center"><button type="button" class="btn btn-danger btn-sm grid-btn text-center" ng-click="grid.appScope.deleteRecord(row)" ng-disabled="row.entity.permission == 1"><i class="fa fa-trash-o" style="margin: 0; width: 12px;"></i></button></div>'
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

        $scope.uploadedFile = function(element) {
            $scope.currentFile = element.files[0];
            var reader = new FileReader();

            reader.onload = function(event) {
              $scope.file_source = event.target.result
              $scope.$apply(function($scope) {
                $scope.files = element.files;
              });
            }
            reader.readAsDataURL(element.files[0]);
        };  

        $scope.deleteRecord = function (row) {
            $scope.resetForm();
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover <strong>" + row.entity.document_name + "</strong> upload document!",
                html: true,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#FF0000",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                $http.delete(base_url + '/inquiry/'+row.entity.id, {params: {type: 2}}).success(function (response_delete) {
                    swal({
                        title: "Deleted!", 
                        text: row.entity.document_name + " upload document has been deleted.", 
                        type: "success",
                        confirmButtonColor: "#9ACD32"
                    });
                    $scope.resetForm();
                    $scope.main_refresh();
                });
            });
        };

        $scope.resetForm = function(){
            $scope.refreshForm();

            $(".message_lable").remove();
            $('.form-control').removeClass("error");
            $('.form-control').removeClass("valid");
        };

        $scope.submitForm = function(){
            $('#dataForm').submit();
        };

        submitForm = function(){
            $('#save_button').prop('disabled', true);
            $scope.data.upload_document = $scope.files[0];
            $http({
                method  : 'POST',
                url     : base_url + '/inquiry/file_upload',
                processData: false,
                transformRequest: function (data) {
                    var formData = new FormData();
                    formData.append("file", $scope.data.upload_document);  
                    return formData;  
                },  
                data : $scope.data,
                headers: {
                       'Content-Type': undefined
                }
            }).success(function(data){
                $('#save_button').prop('disabled', !data.response);
                $scope.data.upload_document = data.response ? data.file : '';
                $scope.data.doc_name = data.response ? data.name : '';
                
                if(data.response && $scope.data.id == 0){
                    $http.post(base_url + '/inquiry', $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Document Uploads',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                } else if(data.response){
                    $http.put(base_url + '/inquiry/'+$scope.data.id, $scope.data).success(function (result) {
                        $('#save_button').prop('disabled', false);
                        $.pnotify && $.pnotify({
                            title: 'Document Uploads',
                            text: result.message,
                            type: result.response ? 'success' :'error',
                            nonblock: true,
                            history: false,
                            delay: 6e3,
                            hide: true
                        });
                        $scope.resetForm();
                        $scope.main_refresh();
                    });
                } else{
                    $.pnotify && $.pnotify({
                        title: 'Document Uploads',
                        text: data.message,
                        type: data.response ? 'success' :'error',
                        nonblock: true,
                        history: false,
                        delay: 6e3,
                        hide: true
                    });
                }
            });
        };

        $scope.main_refresh = function(){
            document.getElementById('data_load').style.visibility = "visible";
            $scope.gridOptions.exporterCsvFilename = $scope.customer_name+' Upload Documents.csv';
            $http({
                method: 'GET',
                url: base_url + '/inquiry/upload_document_list',
                params: {
                    inquiry_id: $scope.data.inquiry_id
                }
            }).then(function successCallback(response) {
                var data_array = [];
                $.each(response.data.upload_documents, function (index, value) {
                    data_array.push({
                        id: value.id,
                        permission: response.data.permission ? 1 : 0,
                        document_type: value.document_type ? value.document_type.name : '',
                        document_name: value.document_name,
                        upload_document: value.upload_document
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
        }, 2000, false);
    }]);
</script>
@endsection