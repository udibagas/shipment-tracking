<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DelayCause;
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
        $data = DelayCause::when($request->keyword, function ($q) use ($request) {
            return $q->where('code', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('description', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('status', $request->status);
        })->orderBy($request->sort ?: 'code', $request->order ?: 'asc');

        return $request->paginated ? $data->paginate($request->per_page) : $data->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DelayCauseRequest $request)
    {
        DelayCause::create($request->all());
        return ['message' => 'Data telah disimpan'];
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
        return ['message' => 'Data telah diupdate'];
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
}
