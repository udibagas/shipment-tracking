<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
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
        $sort = $request->sort ? $request->sort : 'name';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return User::selectRaw('users.*, companies.name AS company, customers.name AS customer, agents.name AS agent')
            ->join('companies', 'companies.id', '=', 'users.company_id', 'LEFT')
            ->join('customers', 'customers.id', '=', 'users.customer_id', 'LEFT')
            ->join('agents', 'agents.id', '=', 'users.agent_id', 'LEFT')
            // user admin hanya boleh manage user dengan level dibawahnya
            ->when($request->user()->role == User::ROLE_ADMIN, function($q) {
                return $q->whereIn('role', [
                    User::ROLE_ADMIN,
                    User::ROLE_CUSTOMER,
                    User::ROLE_AGENT,
                    User::ROLE_OPERATOR,
                ])->where('users.company_id', auth()->user()->company_id);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where('users.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->role, function ($q) use ($request) {
                return $q->whereIn('role', $request->role);
            })->when($request->status, function ($q) use ($request) {
                return $q->whereIn('status', $request->status);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $input = $request->all();

        if ($request->password) {
            $input['password'] = bcrypt($request->password);
        }

        return User::create($input);
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

        if ($request->password) {
            $input['password'] = bcrypt($request->password);
        }

        $user->update($input);

        return $user;
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

    public function getList()
    {
        return User::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getRoleList()
    {
        return User::roleList();
    }
}
