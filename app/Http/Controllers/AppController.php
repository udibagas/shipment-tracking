<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    protected $navs = [
        ['label' => 'Dashboard', 'icon' => 'el-icon-menu', 'path' => '/', 'roles' => [11, 21, 31] ],
        ['label' => 'Pengiriman Domestik', 'icon' => 'el-icon-truck', 'path' => '/domestic-delivery', 'roles' => [11, 21, 31] ],
        ['label' => 'Pengiriman International', 'icon' => 'el-icon-ship', 'path' => '/international-delivery', 'roles' => [11, 21, 31] ],
        ['label' => 'Invoice', 'icon' => 'el-icon-money', 'path' => '/invoice', 'roles' => [11, 21, 31] ],
        ['label' => 'Laporan', 'icon' => 'el-icon-data-analysis', 'path' => '/report', 'roles' => [11, 21, 31] ],
        ['label' => 'Companies', 'icon' => 'el-icon-office-building', 'path' => '/company', 'roles' => [11] ],
        ['label' => 'Customer', 'icon' => 'el-icon-connection', 'path' => '/customer', 'roles' => [11, 21] ],
        ['label' => 'Master Tarif', 'icon' => 'el-icon-s-operation', 'path' => '/master-fare', 'roles' => [11, 21, 31] ],
        ['label' => 'Agent', 'icon' => 'el-icon-s-custom', 'path' => '/agent', 'roles' => [11, 21] ],
        ['label' => 'User', 'icon' => 'el-icon-user', 'path' => '/user', 'roles' => [11, 21] ],
        ['label' => 'Setting', 'icon' => 'el-icon-set-up', 'path' => '/setting', 'roles' => [11] ]
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
