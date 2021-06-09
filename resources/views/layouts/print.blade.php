<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script type="text/javascript" src="{{ asset('js/bootstrap-3.3.0.min.js') }}"></script>
    </head>
    
    <style>
        .panel{
            font-family: Verdana, Geneva, sans-serif; 
        }
        .company-details{
            margin-top: 0;
            margin-bottom: 2px;
            font-size: 10px;
        }
    </style>
    
    <body>
        <div class="container">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                            
                    <div class="row" style="margin-left: 15%;">
                        <div style="width: 42%; float: left;"><img src="{{ asset('assets/images/company/'.$company->company_image) }}" class="img-responsive" width="100%" alt="Image preview..."></div>
                        <div style="width: 2%;"></div>
                        <div style="width: 56%; float: right;">
                            <p class="company-details">{{ $company->address_line_1 }}, {{ $company->address_line_2 }}, {{ $company->address_line_3 }}</p>
                            <p class="company-details">Tel : {{ $company->phone_number }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hotline : {{ $company->hotline_number }}</p>
                            <p class="company-details">Email : {{ $company->email }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website : {{ $company->website }}</p>
                            <p class="company-details">Reg No : {{ $company->reg_number }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SVAT : {{ $company->svat }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;VAT : {{ $company->vat }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <hr style="width: 100%;">
                    </div>

                </div>
                <div class="panel-body">
                    @yield('content')
                </div>
            </div>
            
        </div>
    </body>
</html>
