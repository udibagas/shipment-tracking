<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // TODO: jangan pake join, pake whereHas
        $data = User::with(['customer', 'company', 'agent'])
            // user admin hanya boleh manage user dengan level dibawahnya
            ->when($request->user()->role == User::ROLE_ADMIN, function ($q) {
                $q->whereIn('role', [
                    User::ROLE_ADMIN,
                    User::ROLE_CUSTOMER,
                    User::ROLE_AGENT,
                    User::ROLE_OPERATOR,
                ])->whereHas('company', function ($q) {
                    $q->where('id', auth()->user()->company_id);
                });
            })->when($request->keyword, function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->role, function ($q) use ($request) {
                $q->whereIn('role', $request->role);
            })->when($request->status, function ($q) use ($request) {
                $q->whereIn('status', $request->status);
            })->orderBy($request->sort ?: 'name', $request->order ?: 'asc');

        return $request->paginated ? $data->paginate($request->per_page) : $data->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        User::create($request->all());
        return ['message' => 'User telah disimpan'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        if (auth()->user()->role == User::ROLE_ADMIN && $user->role == User::ROLE_ADMIN && $user->id != $request->user()->id) {
            return response(['message' => 'Anda tidak bisa mengedit akun admin.'], 500);
        }

        $input = $request->all();

        $user->update($input);

        return ['message' => 'User telah diupdate'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id == auth()->user()->id) {
            return response(['message' => 'Anda tidak bisa menghapus akun sendiri.'], 500);
        }

        if (auth()->user()->role == User::ROLE_ADMIN && $user->role == User::ROLE_ADMIN) {
            return response(['message' => 'Anda tidak bisa menghapus akun admin.'], 500);
        }

        $user->delete();
        return ['message' => 'User telah dihapus'];
    }

    public function getRoleList()
    {
        $roles = [];

        foreach (User::roleList() as $id => $role) {
            $roles[] = ['id' => $id, 'role' => $role];
        }

        return $roles;
    }
}
