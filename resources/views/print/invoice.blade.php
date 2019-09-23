@extends('layouts.print')

@section('content')
    <table>
        <tbody>
            <tr>
                <td>
                    <img src="{{asset($data->company->logo)}}" alt="">
                </td>
                <td>
                    <h3>{{$data->company->name}}</h3>
                    {{$data->company->address}} <br>
                    Telp. : {{$data->company->phone}} <br>
                    Email : {{$data->company->email}} <br>
                    Website : {{$data->company->website}} <br>
                </td>
                <td>
                    <h1 style="text-align:right">INVOICE</h1>
                    <br>
                    <br>
                    Tanggal : {{$data->date}} <br>
                    Nomor : {{$data->nomor}} <br>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    Bill To: <br>
                    <h3>{{$data->customer->name}}</h3>
                    {{$data->customer->address}}
                </td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tgl Kirim</th>
                <th>Tgl Terima</th>
                <th>Surat Pengantar</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>Layanan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tarif</th>
                <th>Biaya</th>
                <th>PPN</th>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->items as $item)
            <tr>
                <td>{{$loop->index}}</td>
                <td>{{$item->delivery_date}}</td>
                <td>{{$item->delivered_date}}</td>
                <td>{{$item->spb_number}}</td>
                <td>{{$item->origin}}</td>
                <td>{{$item->destination}}</td>
                <td>{{$item->service_type}}</td>
                <td>{{$item->quantity}}</td>
                <td>{{$item->unit}}</td>
                <td>{{$item->fare}}</td>
                <td>{{$item->price}}</td>
                <td>{{$item->tax}}</td>
                <td>{{$item->total}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10">TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <table>
        <tbody>
            <tr>
                <td>
                    Direktur Utama,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Theuis Yuliati
                </td>
            </tr>
        </tbody>
    </table>
@endsection
