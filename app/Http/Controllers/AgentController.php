<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Agent;
use App\User;
use App\Http\Requests\AgentRequest;

class AgentController extends Controller
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

        return Agent::when($request->keyword, function ($q) use ($request) {
                return $q->where(function($qq) use ($request) {
                    return $qq->where('name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('code', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('address', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
                });
            })->when($request->user()->role == User::ROLE_ADMIN, function($q) {
                return $q->where('company_id', auth()->user()->company_id);
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
    public function store(AgentRequest $request)
    {
        return Agent::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Agent $agent)
    {
        return $agent;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgentRequest $request, Agent $agent)
    {
        $agent->update($request->all());
        return $agent;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();
        return ['message' => 'Data telah dihapus'];
    }

    public function getList()
    {
        return Agent::select(['id', 'code', 'name', 'company_id'])
            ->when(auth()->user()->company_id, function($q) {
                return $q->where('company_id', auth()->user()->company_id);
            })->orderBy('code', 'asc')->get();
    }
}
