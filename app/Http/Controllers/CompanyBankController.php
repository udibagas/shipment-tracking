<?php

namespace App\Http\Controllers;

use App\Models\CompanyBank;
use App\Http\Requests\CompanyBankRequest;
use Illuminate\Http\Request;

class CompanyBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CompanyBank::where('company_id', $request->company_id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyBankRequest $request)
    {
        CompanyBank::create($request->all());
        return ['message' => 'Data telah disimpan'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyBankRequest $request, CompanyBank $companyBank)
    {
        $companyBank->update($request->all());
        return ['message' => 'Data telah diupdate'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyBank $companyBank)
    {
        $companyBank->delete();
        return ['message' => 'Data telah dihapus'];
    }
}
