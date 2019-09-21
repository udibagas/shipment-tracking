@extends('layouts.print')

@section('content')

<table style="width:100%;border:1px solid #000;font-family:monospace" cellspacing="0" cellpadding="5" style="border:1px solid #000">
    <thead>
        <tr>
            <th colspan="6" style="border: 1px solid #000">SURAT PENGANTAR BARANG</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3" style="border: 1px solid #000;border-right:none;">
                <span style="width:100px;display:inline-block;">SPB NO.</span>: {{$data->spb_number}} <br>
                <span style="width:100px;display:inline-block;">SHIP TO.</span>: {{$data->customer->name}} - {{$data->destination}} <br>
                <span style="width:100px;display:inline-block;">ADDRESS</span>: {{nl2br($data->delivery_address)}} <br>
            </td>
            <td colspan="3" style="border: 1px solid #000;border-left:none;">
                <span style="width:100px;display:inline-block;">RESI NO.</span>: {{$data->resi_number}} <br>
                <span style="width:100px;display:inline-block;">CHARGE TO</span>: {{$data->charge_to}} <br>
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;text-align:center;">URAIAN BARANG</td>
            <td style="border: 1px solid #000;text-align:center;">COLI</td>
            <td style="border: 1px solid #000;text-align:center;">WEIGHT</td>
            <td style="border: 1px solid #000;text-align:center;">ITEM</td>
            <td style="border: 1px solid #000;text-align:center;">REFERENCE</td>
            <td style="border: 1px solid #000;text-align:center;">REMARK</td>
        </tr>
        @foreach ($data->items as $item)
        <tr>
            <td style="border: 1px solid #000">{{$item->description}}</td>
            <td style="border: 1px solid #000;text-align:center">{{$item->coli}}</td>
            <td style="border: 1px solid #000;text-align:center">{{$item->weight}} KG</td>
            <td style="border: 1px solid #000;text-align:center">{{$item->item}}</td>
            <td style="border: 1px solid #000">{{$item->reference}}</td>
            <td style="border: 1px solid #000">{{$item->remark}}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
