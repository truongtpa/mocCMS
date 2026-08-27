<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    /**
     * Intercept and execute an action on the controller with automated RBAC validation.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $controllerClass = get_class($this);
        $controllerName = class_basename($controllerClass);
        $permissionKey = "{$controllerName}.{$method}"; // e.g. "TaiKhoanController.danhSach"

        // 1. Bypass check for authentication controllers
        if ($this->shouldBypassPermissionCheck($controllerName, $method)) {
            return $this->executeAction($method, $parameters);
        }

        // 2. Retrieve authenticated user info from session
        $userId = session()->get(\App\VLUTE::SESSION_IDTaiKhoan);
        $email = session()->get(\App\VLUTE::SESSION_Email);

        if (!$userId || !$email) {
            abort(401, 'Unauthenticated');
        }

        // 3. Determine user type (student or lecturer)
        $maDoiTuong = explode('@', $email)[0];
        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
        $userType = $isStudent ? 'sinh_vien' : 'giang_vien';

        // 4. Perform RBAC validation
        if (!$this->hasPermission($userId, $userType, $permissionKey)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        return $this->executeAction($method, $parameters);
    }

    /**
     * Execute the controller method dynamically.
     */
    protected function executeAction($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...array_values($parameters));
        }
        abort(404);
    }

    /**
     * Check if the specific controller action is public and should bypass permissions.
     */
    protected function shouldBypassPermissionCheck($controllerName, $method)
    {
        // Publicly accessible controllers or system admin controllers
        if (in_array($controllerName, ['DangNhapController', 'DynamicObjectController', 'PhanQuyenController'])) {
            return true;
        }

        return false;
    }

    /**
     * Perform database query validation for the RBAC permissions.
     */
    protected function hasPermission($userId, $userType, $permissionKey)
    {
        try {
            // 1. Check if the user is assigned to the "admin" role (admin has full access to all features)
            $isAdmin = DB::table('vai_tro_nguoi_dung')
                ->join('vai_tro', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro.id')
                ->where('vai_tro_nguoi_dung.user_id', $userId)
                ->where('vai_tro_nguoi_dung.user_type', $userType)
                ->where('vai_tro.ma_vai_tro', 'admin')
                ->exists();

            if ($isAdmin) {
                return true;
            }

            // 2. Query fine-grained permissions for specific features (Controller.function)
            $hasPermission = DB::table('vai_tro_nguoi_dung')
                ->join('vai_tro', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro.id')
                ->join('vai_tro_quyen', 'vai_tro_quyen.vai_tro_id', '=', 'vai_tro.id')
                ->join('quyen_han', 'vai_tro_quyen.quyen_id', '=', 'quyen_han.id')
                ->where('vai_tro_nguoi_dung.user_id', $userId)
                ->where('vai_tro_nguoi_dung.user_type', $userType)
                ->where('quyen_han.ma_quyen', $permissionKey)
                ->exists();

            return $hasPermission;

        } catch (\Exception $e) {
            Log::error("RBAC Validation Error (User: {$userId}, Type: {$userType}, Key: {$permissionKey}): " . $e->getMessage());
        }

        return false;
    }
}
