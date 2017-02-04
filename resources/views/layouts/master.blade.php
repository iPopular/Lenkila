<!DOCTYPE html>
<html>
    <head>        
        <title>Lenkila Stadium</title>        
        @include('layouts.partials.header')            
    </head>
    <body class="fixed-sn blue-skin">
           
        <div id="page-loader">
            <div class="sk-double-bounce">
                <div class="sk-child sk-double-bounce1"></div><!-- End .sk-child -->
                <div class="sk-child sk-double-bounce2"></div><!-- End .sk-child -->
            </div><!-- End .sk-double-bounce -->
        </div><!-- End #page-loader -->
        <header>
            @if (!Auth::guest())
                @include('layouts.partials.sidebar')
            @endif
            @include('layouts.partials.navbar') 
        </header>

            @yield('main')

            @include('layouts.partials.script')              
    </body>
</html>