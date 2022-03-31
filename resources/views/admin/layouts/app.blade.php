<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content="tailwind,tailwindcss,tailwind css,css,starter template,free template,admin templates, admin template, admin dashboard, free tailwind templates, tailwind example">
    <!-- Css -->
    <link rel="stylesheet" href="{{asset('assets/dist/styles.css')}}">
    <link rel="stylesheet" href="{{asset('assets/dist/all.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i,600,600i,700,700i" rel="stylesheet">
    <title>Dashboard | Tailwind Admin</title>
</head>
<body>
    <!--Container -->
    <div class="mx-auto bg-grey-400">
        <!--Screen-->
        <div class="min-h-screen flex flex-col">
            <!--Header Section Starts Here-->
            @include('admin.layouts.header')
            <!--/Header-->
            <div class="flex flex-1">
                <!--Sidebar-->
                @include('admin.layouts.sidebar')
                <!--/Sidebar-->
                <!--Main-->
                @yield('content')
                <!--/Main-->
            </div>
           @include('admin.layouts.footer')
        </div>
    </div>
    <script src="{{asset('assets/js/main.js')}}"></script>
</body>
</html>