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
            
            @yield('content')
            
        </div>
    </body>
</html>
