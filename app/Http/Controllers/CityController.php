<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\City;

class CityController extends Controller
{
    public function getList() {
        return City::select(['id', 'name'])->orderBy('name', 'asc')->get();
    }
}
