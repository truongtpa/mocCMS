<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mocCMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--
        Đặt chế độ màu trước khi CSS vẽ, nếu không trang sẽ nháy nền sáng rồi
        mới đổi sang tối lúc Vue mount. Khóa phải khớp với STORAGE_KEY trong
        resources/js/components/layout/useColorMode.js
    --}}
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('moccms.theme') || 'auto'
                var dark = saved === 'dark' || (saved === 'auto' &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches)
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light')
            } catch (e) {}
        })()
    </script>
    @vite('resources/js/main.js')
    @routes
</head>
<body>
    <div id="app"></div>
</body>
</html>
