<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('tai_khoan')) {
            return $next($request);
        }

        return redirect()->guest(route('DangNhapController.dangNhap'));
    }
}
