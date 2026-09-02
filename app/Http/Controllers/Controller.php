<?php

namespace App\Http\Controllers;

use App\Response;
use App\VLUTE;

abstract class Controller
{
    public function callAction($method, $parameters)
    {
        $controllerName = class_basename(get_class($this));
        $permissionKey = "{$controllerName}.{$method}";

        if ($this->shouldBypassPermissionCheck($controllerName, $method)) {
            return $this->executeAction($method, $parameters);
        }

        $userId = session()->get(VLUTE::SESSION_IDTaiKhoan);
        if (!$userId) {
            return Response::Error('Chưa đăng nhập', 'Vui lòng đăng nhập để tiếp tục!');
        }

        if (!VLUTE::checkPermission($permissionKey, $userId)) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        return $this->executeAction($method, $parameters);
    }

    protected function executeAction($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...array_values($parameters));
        }
        return Response::Error('Lỗi', 'Phương thức không tồn tại!');
    }

    protected function shouldBypassPermissionCheck($controllerName, $method)
    {
        if (in_array($controllerName, ['DangNhapController', 'DynamicObjectController', 'PhanQuyenController'])) {
            return true;
        }

        return false;
    }
}
