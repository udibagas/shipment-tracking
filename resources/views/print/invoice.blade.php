@extends('layouts.print')

@section('content')
    <table>
        <tbody>
            <tr>
                <td style="width:100px">
                    <img src="{{asset($data->company->logo)}}" alt="" style="width:80%">
                </td>
                <td>
                    <h2 style="margin:0">{{$data->company->name}}</h2>
                    {!! nl2br($data->company->address) !!} <br>
                    Telp. : {{$data->company->phone}} <br>
                    Email : {{$data->company->email}} <br>
                    Website : {{$data->company->website}} <br>
                </td>
                <td class="text-right">
                    <h1 style="margin:0;">INVOICE</h1>
                    <div style="font-size:16px">
                        Tgl : {{date('d/m/Y', strtotime($data->date))}} <br>
                        No. : {{$data->number}}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <hr> --}}
    <br>

    <table class="bordered">
        <tbody>
            <tr>
                <td width="50%"  style="border-top:1px solid #ddd">
                    Kepada:
                    <h3 style="margin:0;">{{$data->customer->name}}</h3>
                    {!! nl2br($data->customer->address) !!}
                </td>
                <td width="50%" style="border-top:1px solid #ddd">
                    Pembayaran ditransfer ke:  <br>
                    @foreach($data->company->banks()->where('active', 1)->get() as $bank)
                    <strong>
                        - Bank {{$bank->bank_name}} Cab. {{$bank->bank_branch}} No. Rek. {{$bank->account_number}} a/n {{$bank->account_holder}}
                    </strong><br>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>

    <br>

    <table class="bordered">
        <thead style="background-color:#eee;">
            <tr>
                <th>#</th>
                <th>Tgl Kirim</th>
                <th>Tgl Terima</th>
                <th>Surat Pengantar</th>
                {{-- <th>Asal</th> --}}
                <th>Tujuan</th>
                <th>Layanan</th>
                @if ($data->service_type == 'REGULER')
                <th>Koli</th>
                <th>KG/M<sup>3</sup></th>
                @endif
                <th style="width:100px">Tarif</th>
                <th style="width:100px">Biaya</th>
                <th style="width:100px">PPN 10%</th>
                <th style="width:100px">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->groupedItems() as $items)
            @foreach($items as $i => $item)
            <tr>
                @if ($i == 0)
                <td rowspan="{{count($items)}}">{{$loop->index + 1}}</td>
                <td rowspan="{{count($items)}}" class="text-center">{{date('d/m/y', strtotime($item->description['delivery_date']))}}</td>
                <td rowspan="{{count($items)}}" class="text-center">{{date('d/m/y', strtotime($item->description['delivered_date']))}}</td>
                <td rowspan="{{count($items)}}">{{$item->description['spb_number']}}</td>
                {{-- <td rowspan="{{count($item)}}">{{$item->description['origin']}}</td> --}}
                <td style="width:150px" rowspan="{{count($items)}}">{{$item->description['destination']}}</td>
                @endif
                <td style="width:180px">
                    {{$item->description['service_type'] == 'CHARTER' ? 'CHARTER - ' . $item->description['vehicle_type'] : $item->description['service_type']}}
                </td>
                @if ($data->service_type == 'REGULER')
                <td class="text-center">{{$item->description['total_coli']}}</td>
                <td class="text-right">
                    {{number_format($item->quantity, $item->unit == 'M3' ? 3 : 0, ',', '.')}}
                    {!! $item->unit == 'M3' ? 'M<sup>3</sup>' : $item->unit !!}
                </td>
                @endif
                <td class="text-right">Rp. {{number_format($item->fare, 0, ',', '.')}}</td>
                <td class="text-right">Rp. {{number_format($item->price, 0, ',', '.')}}</td>
                <td class="text-right">Rp. {{number_format($item->tax, 0, ',', '.')}}</td>
                <td class="text-right">Rp. {{number_format($item->total, 0, ',', '.')}}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
        <tfoot style="background-color:#eee;">
            <tr>
                <th colspan="{{ $data->service_type == 'REGULER' ? '9' : '7' }}" class="text-right">TOTAL</th>
                <th class="text-right">Rp. {{ number_format(array_reduce($data->items->toArray(), function($prev, $curr) { return $prev + $curr['price']; }, 0), 0, ',', '.') }}</th>
                <th class="text-right">Rp. {{ number_format(array_reduce($data->items->toArray(), function($prev, $curr) { return $prev + $curr['tax']; }, 0), 0, ',', '.') }}</th>
                <th class="text-right">Rp. {{number_format($data->total, 0, ',', '.')}}</th>
            </tr>
            <tr>
                <th colspan="12" class="text-bold text-left">
                    Terbilang : {{$data->total_said}}
                </th>
            </tr>
        </tfoot>
    </table>

    <br><br>

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
                    <br>
                    <br>
                    <br>
                    {{$data->company->director_name}}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
