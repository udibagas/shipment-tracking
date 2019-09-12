<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DelayCause;
use App\Http\Requests\DelayCauseRequest;

class DelayCauseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'code';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return DelayCause::when($request->keyword, function ($q) use ($request) {
                return $q->where('code', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->keyword . '%');
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
    public function store(DelayCauseRequest $request)
    {
        return DelayCause::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DelayCauseRequest $request, DelayCause $delayCause)
    {
        $delayCause->update($request->all());
        return $delayCause;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DelayCause $delayCause)
    {
        $delayCause->delete();
        return ['message' => 'Data telah dihapus'];
    }

    public function getList()
    {
        return DelayCause::select(['id', 'code', 'description'])
            ->orderBy('code', 'asc')
            ->get();
    }
}
