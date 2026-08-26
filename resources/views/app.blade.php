<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <title>Hệ thống quản lý văn bản</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('themes/images/logo.png') }}" sizes="16x16" type="image/png">
    <link rel="icon" href="{{ asset('themes/images/logo.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('themes/images/logo.png') }}" sizes="48x48" type="image/png">
    <link rel="icon" href="{{ asset('themes/images/logo.png') }}" sizes="62x62" type="image/png">
    <link rel="stylesheet" href="{{ asset('themes/css/lte.min.css') }}">
    <link href="{{ asset('themes/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('themes/css/awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/css/global.css') }}">
    <script src="{{ asset('themes/js/jquery.min.js') }}"></script>
    <script src="{{ asset('themes/js/lte.min.js') }}"></script>
    <script src="{{ asset('themes/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('themes/js/chart.js') }}"></script>

    <script src="{{ asset('themes/js/select2.full.min.js') }}"></script>
    <link href="{{ asset('themes/css/select2.min.css') }}" rel="stylesheet" />
    @vite(['resources/js/main.js'])
    @routes
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<script>
    const __tttk = JSON.parse('{!! json_encode($data) !!}');
    const __show_views = JSON.parse('{!! json_encode($show_views ?? []) !!}');
    localStorage.setItem('show_views', JSON.stringify(__show_views));
</script>

<div id="app"></div>
</body>
</html>
