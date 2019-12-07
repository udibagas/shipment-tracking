@component('mail::message')
Dear Customer,

Pengiriman dengan nomor resi {{$shipment->resi_number}} dengan tujuan {{$shipment->destination}} telah sampai tujuan pada {{$shipment->delivered_date}} dan diterima oleh {{$shipment->receiver}}

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Terimakasih,<br>
{{ config('app.name') }}
@endcomponent
