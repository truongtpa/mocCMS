<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DangNhapController;
use App\Response as AppResponse;
use App\VLUTE;
use Closure;
use Illuminate\Http\Request;
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
            if ($request->expectsJson() || $request->ajax()) {
                return AppResponse::Error('Chưa đăng nhập', 'Vui lòng đăng nhập để tiếp tục!');
            }
            return redirect()->action([DangNhapController::class, 'dangNhapKeycloak']);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;

        if ($routeName) {
            $hasPermission = VLUTE::checkPermission($routeName);
            if (!$hasPermission) {
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson() || str_contains($request->header('Accept', ''), 'application/json')) {
                    return AppResponse::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
                }
                return redirect()->action([DangNhapController::class, 'redirectKhongCoQuyen']);
            }
        }

        if ($request->isMethod('GET')) {
            $request->session()->save();
        }

        return $next($request);
    }
}
