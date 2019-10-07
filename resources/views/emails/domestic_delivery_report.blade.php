@component('mail::message')
Dear Bapak/Ibu,

Berikut kami sampaikan update laporan pengiriman barang periode tanggal {{ date('d/m/Y', strtotime($data->dateRange[0])) }} sampai {{ date('d/m/Y', strtotime($data->dateRange[1])) }}:

<table class="table">
    <thead>
        <tr>
            <th>Nomor SPB</th>
            <th>Nomor Resi</th>
            <th>Tujuan</th>
            <th>Layanan</th>
            <th>Jml Koli</th>
            <th>Berat</th>
            <th>Tgl Pick Up</th>
            <th>ETD</th>
            <th>Tgl Kirim</th>
            <th>ETA</th>
            <th>Tgl Terima</th>
            <th>Status</th>
            <th>Waktu Update</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->items as $i)
        <tr>
            <td>{{ $i['spb_number'] }}</td>
            <td>{{ $i['resi_number'] }}</td>
            <td>{{ $i['destination']}} <br> {!! nl2br($i['delivery_address']) !!}</td>
            <td>{{ $i['service_type'] }} <br> {{ $i['vehicle_type']['name'] }}</td>
            <td class="text-center">{{ $i['quantity'] }}</td>
            <td class="text-right">{{ number_format($i['invoice_weight'], 0, ',', '.') }} KG</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['pick_up_date'])) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['etd'])) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['delivery_date'])) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['eta'])) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['delivered_date'])) }}</td>
            <td class="text-center">{{ $i['statusName'] }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($i['updated_at'])) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br><br>

Terimakasih,
<br><br>

{{auth()->user()->name}}<br>
<strong>{{auth()->user()->company->name}}</strong><br>
{!! nl2br(auth()->user()->company->address) !!} <br>
Phone: {{ auth()->user()->company->phone }}<br>
Fax: {{ auth()->user()->company->fax }}<br>
Email: {{ auth()->user()->company->email }}<br>
Website: {{ auth()->user()->company->website }}<br>
@endcomponent
