<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!--================ CSRF Token ==============================================-->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!--================ CSRF Token ==============================================-->

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
        <meta name="description" content="NXM Assessment Project">
        <meta name="description" content="">
        <meta name="author" content="Oluwatosin Amokeodo">

        <!--================ application favico ======================================-->
        <link rel="icon" href="">
        <!--================ application favicon =====================================-->

        <!--================ application title =======================================-->
        <title>{{ config('app.name', 'NXM Assessment') }}</title>
        <!--================ application title =======================================-->

        <!--================ Bootstrap ===============================================-->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <!--================ Bootstrap ===============================================-->

        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">

        <!--==================== JQuery ==============================================-->
        <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js')}}"></script>
        <Script type="text/javascript" src="{{asset('js/jquery-ui.min.js')}}"></script>
        <!--==================== JQuery ==============================================-->
    </head>
    <body>
        <main role="main" class="container starter-template">
            <div class="row">
                <!--=============== this will load in content of the application ===========-->
                @yield('content')
                <!--=============== this will load in content of the application ===========-->
            </div>
        </main>
        <!--====== Javascripts & Jquery ======-->
        <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    </body>
</html>



