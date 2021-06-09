<!DOCTYPE html>
<html lang="en">
    <head>        
        <!-- META SECTION -->
        @yield('title')            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        <!-- END META SECTION -->
        
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" >
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">-->
        
        <!-- CSS INCLUDE -->
        <link rel="stylesheet" type="text/css" id="theme" href="{{ asset('css/theme-default.css') }}"/>
        
        <!-- sweey-alert -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert-1.1.3.min.css') }}" />
        
        <!-- latest bootstrap-switch release -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-switch.css') }}">
        
        <!-- START PLUGINS -->       
        <script type="text/javascript" src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/jquery-ui-1.11.0.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bootstrap-3.3.0.min.js') }}"></script>
        <!-- END PLUGINS -->
        
        <!-- latest bootstrap-switch release -->
        <script type="text/javascript" src="{{ asset('js/bootstrap-switch.js') }}"></script>
        
        <!-- EOF CSS INCLUDE -->  
        <script type="text/javascript" src="{{ asset('js/angular-1.5.0.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/angular-touch-1.5.0.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/angular-animate-1.5.0.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/csv.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/pdfmake.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>     
        
        <link rel="stylesheet" type="text/css" href="{{ asset('css/ui-grid-unstable.css') }}">
        <script type="text/javascript" src="{{ asset('js/ui-grid.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/module.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/bsSwitch.js') }}"></script>        
              
        <script type="text/javascript" src="{{ asset('js/checklist-model.js') }}"></script>
              
        <script type="text/javascript" src="{{ asset('js/ui-grid-unstable.js') }}"></script>
        
        <link rel="stylesheet" type="text/css" id="theme" href="https://cdn.rawgit.com/angular-ui/bower-ui-grid/master/ui-grid.min.css"/>
            
        <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.pnotify.default-1.3.1.min.css') }}" />
        
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
        
        <script type='text/javascript' src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
        <script type='text/javascript' src="{{ asset('js/angular-ui-tinymce/src/tinymce.js') }}"></script>
          
        <script type="text/javascript">
            var base_url = '<?php echo URL::to('/'); ?>';  
        </script>
        <style>
            #load{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                background:url("https://www.creditmutuel.fr/cmne/fr/banques/webservices/nswr/images/loading.gif") no-repeat center center rgba(0,0,0,0.25)
            }
            #data_load{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                visibility: hidden;
                background:url("https://www.creditmutuel.fr/cmne/fr/banques/webservices/nswr/images/loading.gif") no-repeat center center rgba(0,0,0,0.25)
            }
        </style>
    </head>
    <body ng-app="myModule">
        <div id="load"></div>
        <div id="data_load"></div>
        <!-- START PAGE CONTAINER -->
        <div class="page-container">
 
            @include('partials.side_bar')

            <!-- PAGE CONTENT -->
            <div class="page-content">

                @include('partials.x_navigation')

                <!-- START BREADCRUMB -->
                @yield('breadcrumb') 
                <!-- END BREADCRUMB -->                       

                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    @yield('content')
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                
            </div>            
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->

        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>                    
                        <p>Press No if you want to continue work. Press Yes to logout current user {{ session()->get('username') }}.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <a href="{{ asset('logout') }}" class="btn btn-success btn-lg">Yes</a>
                            <button class="btn btn-default btn-lg mb-control-close">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MESSAGE BOX-->                  

        <!-- START SCRIPTS -->

        <script type="text/javascript" src="{{ asset('js/ui-bootstrap-tpls-0.14.3.js') }}"></script>
        
        <script type="text/javascript" src="{{ asset('js/jquery.pnotify-1.3.1.js') }}"></script>
        
        <!-- Sweetalert -->
        <script type="text/javascript" src="{{ asset('js/sweetalert-1.1.3.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/sweetalert-dev-1.1.3.min.js') }}"></script>
        
        <!-- START THIS PAGE PLUGINS-->        
        <script type="text/javascript" src="{{ asset('js/icheck-1.0.2.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/plugins/scrolltotop/scrolltopcontrol.js') }}"></script>
        
        <script type="text/javascript" src="{{ asset('js/raphael-2.1.2.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/morris-0.5.0.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/plugins/rickshaw/d3.v3.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/plugins/rickshaw/rickshaw.min.js') }}"></script>
        <script type='text/javascript' src="{{ asset('js/plugins/bootstrap/bootstrap-datepicker.js') }}"></script>   
        <script type='text/javascript' src="{{ asset('js/plugins/bootstrap/bootstrap-file-input.js') }}"></script>  
        <script type='text/javascript' src="{{ asset('js/plugins/bootstrap/bootstrap-select.js') }}"></script>  
        <script type="text/javascript" src="{{ asset('js/plugins/owl/owl.carousel.min.js') }}"></script> 
        
        <script type="text/javascript" src="{{ asset('js/plugins/moment.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/plugins/daterangepicker/daterangepicker.js') }}"></script>
        
        <script type="text/javascript" src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <!-- END THIS PAGE PLUGINS-->
        
        <script type="text/javascript" src="{{ asset('js/plugins.js') }}"></script>        
        <script type="text/javascript" src="{{ asset('js/actions.js') }}"></script>
        
        <script type="text/javascript" src="{{ asset('js/jquery.validate-1.12.0.min.js') }}"></script>
        
        <script type="text/javascript">
            // Prevent the backspace key from navigating back.
            $(document).unbind('keydown').bind('keydown', function (event) {
                if (event.keyCode === 8) {
                    var doPrevent = true;
                    var types = ["text", "password", "file", "search", "email", "number", "date", "color", "datetime", "datetime-local", "month", "range", "search", "tel", "time", "url", "week"];
                    var d = $(event.srcElement || event.target);
                    var disabled = d.prop("readonly") || d.prop("disabled");
                    if (!disabled) {
                        if (d[0].isContentEditable) {
                            doPrevent = false;
                        } else if (d.is("input")) {
                            var type = d.attr("type");
                            if (type) {
                                type = type.toLowerCase();
                            }
                            if (types.indexOf(type) > -1) {
                                doPrevent = false;
                            }
                        } else if (d.is("textarea")) {
                            doPrevent = false;
                        }
                    }
                    if (doPrevent) {
                        event.preventDefault();
                        return false;
                    }
                }
            });
        </script>    
        
        <script type="text/javascript">
            document.onreadystatechange = function () {
                $(".page-sidebar").addClass("scroll").mCustomScrollbar("update");  
                var state = document.readyState;
                if (state == 'complete') {
                    setTimeout(function(){
                        document.getElementById('interactive');
                        document.getElementById('load').style.visibility="hidden";
                    },1000);
                }
              }
        </script>
        <!-- END TEMPLATE -->        
        <!-- END SCRIPTS -->         
    </body>
</html>






