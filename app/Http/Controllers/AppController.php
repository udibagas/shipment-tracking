<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    protected $navs = [
        ['label' => 'Dashboard', 'icon' => 'el-icon-menu', 'path' => '/', 'roles' => [11, 21, 31] ],
        ['label' => 'Domestic Deliveries', 'icon' => 'el-icon-truck', 'path' => '/domestic-delivery', 'roles' => [11, 21, 31] ],
        ['label' => 'International Deliveries', 'icon' => 'el-icon-ship', 'path' => '/international-delivery', 'roles' => [11, 21, 31] ],
        ['label' => 'Invoices', 'icon' => 'el-icon-money', 'path' => '/invoice', 'roles' => [11, 21, 31] ],
        ['label' => 'Reports', 'icon' => 'el-icon-data-analysis', 'path' => '/report', 'roles' => [11, 21, 31] ],
        ['label' => 'Companies', 'icon' => 'el-icon-office-building', 'path' => '/company', 'roles' => [11] ],
        ['label' => 'Customers', 'icon' => 'el-icon-connection', 'path' => '/customer', 'roles' => [11, 21] ],
        ['label' => 'Agents', 'icon' => 'el-icon-s-custom', 'path' => '/agent', 'roles' => [11, 21] ],
        ['label' => 'Users', 'icon' => 'el-icon-user', 'path' => '/user', 'roles' => [11, 21] ],
        ['label' => 'Settings', 'icon' => 'el-icon-set-up', 'path' => '/setting', 'roles' => [11] ]
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('layouts.app');
    }

    public function checkAuth(Request $request)
    {
        $navs = array_filter($this->navs, function($nav) use ($request) {
            return $nav['path'] == $request->route && in_array(auth()->user()->role, $nav['roles']);
        });

        if (count($navs) == 0) {
            return response(['message' => 'Anda tidak berhak mengakses halaman ini.'], 403);
        }

        return ['message' => 'ok'];
    }

    public function getNavigation()
    {
        return array_filter($this->navs, function($nav) {
            return in_array(auth()->user()->role, $nav['roles']);
        });
    }
}
