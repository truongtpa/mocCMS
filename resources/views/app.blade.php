<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mocCMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/main.js')
    @routes
</head>
<body>
    <div id="app"></div>
</body>
</html>
