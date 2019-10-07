@component('mail::message')
Dear Bapak/Ibu,

Berikut kami sampaikan update laporan pengiriman barang periode tanggal {{ date('d/m/Y', strtotime($data->dateRange[0])) }} sampai {{ date('d/m/Y', strtotime($data->dateRange[1])) }}:

@component('mail::table')
| Nomor SPB/Resi | Tujuan | Layanan | Jml Koli | Berat | Tgl Pick Up | ETD | Tgl Kirim | ETA | Tgl Terima | Status | Waktu Update |
| --- | --- | --- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
@foreach ($data->items as $i)
| {{ $i['spb_number'] }} <br> {{ $i['resi_number'] }} | {{ $i['destination']}} <br> {{ $i['delivery_address'] }} | {{ $i['service_type'] }} <br> {{ $i['vehicle_type']['name'] }} | {{ $i['quantity'] }} | {{ $i['invoice_weight'] }} KG | {{ $i['pick_up_date'] }} | {{ $i['etd'] }} | {{ $i['delivery_date'] }} | {{ $i['eta'] }} | {{ $i['delivered_date'] }} | {{ $i['statusName'] }} | {{ $i['updated_at'] }} |
@endforeach
@endcomponent


Terimakasih,<br>


{{auth()->user()->name}},<br>
<strong>{{auth()->user()->company->name}}</strong><br>
{!! nl2br(auth()->user()->company->address) !!} <br>
Phone: {{ auth()->user()->company->phone }}<br>
Fax: {{ auth()->user()->company->fax }}<br>
Email: {{ auth()->user()->company->email }}<br>
Website: {{ auth()->user()->company->website }}<br>
@endcomponent
