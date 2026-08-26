<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DangNhapController;
use App\VLUTE;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class isLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->exists(VLUTE::SESSION_IDTaiKhoan)) {
            $request->session()->put('redirect_after_login', url()->full());
            return redirect()->action([DangNhapController::class, 'dangNhapKeycloak']);
        }


        return $next($request);
    }
}
