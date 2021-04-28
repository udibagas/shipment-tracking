<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Http\Requests\CompanyRequest;
use App\User;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Company::when($request->keyword, function ($q) use ($request) {
            return $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('code', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('address', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('status', $request->status);
        })->orderBy($request->sort ?: 'name', $request->order ?: 'asc');

        return $request->paginated ? $data->paginate($request->per_page) : $data->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        Company::create($request->all());
        return ['message' => 'Data telah disimpan'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        // TODO: pindah ke policy
        if (auth()->user()->role == User::ROLE_ADMIN && auth()->user()->company_id != $company->id) {
            return response(['message' => 'Anda tidak berhak mengakses data ini.'], 403);
        }

        return $company;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        // TODO: pindah ke policy
        if (auth()->user()->role == User::ROLE_ADMIN && auth()->user()->company_id != $company->id) {
            return response(['message' => 'Anda tidak berhak mengupdate data ini.'], 403);
        }

        $company->update($request->all());
        return ['message' => 'Data telah diupdate'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return ['message' => 'Data telah dihapus'];
    }

    public function uploadLogo(Request $request)
    {
        $file = $request->file('file');
        $fileName = time() . $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        if (!in_array(strtolower($extension), ['png', 'jpg', 'jpeg'])) {
            return response(['message' => 'File extension not permitted'], 500);
        }

        try {
            $file->move('uploads/', $fileName);
        } catch (\Exception $e) {
            return response(['message' => 'Failed to move file'], 500);
        }

        return ['path' => '/uploads/' . $fileName];
    }
}
