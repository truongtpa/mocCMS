<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DangNhapController;
use App\VLUTE;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class isQuyen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->exists(VLUTE::SESSION_IDTaiKhoan)) {
            return redirect()->action([DangNhapController::class, 'dangNhapKeycloak']);
        }

        $id_tai_khoan = $request->session()->get(VLUTE::SESSION_IDTaiKhoan);



        return $next($request);
    }
}
