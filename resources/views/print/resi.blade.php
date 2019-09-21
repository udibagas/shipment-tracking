@extends('layouts.print')

@section('content')
<div style="margin-top:4cm;font-family: monospace">
    <div style="padding:0.2cm 0 0.2cm 4cm">{{$data->quantity}}</div>
    <div style="padding:0.2cm 0 0.2cm 4cm">{{$data->weight}} KG</div>
    <div style="padding:0.2cm 0 0.2cm 4cm">{{$data->customer->name}} - {{$data->destination}}</div>
    <div style="padding:0.2cm 0 0.2cm 4cm">{{$data->delivery_address}}</div>
    <div style="padding:0.2cm 0 0.2cm 4cm"></div>
    <div style="padding:0.2cm 0 0.2cm 4cm">{{$data->customer->name}} - {{$data->origin}}</div>
</div>
@endsection
