<template>
    <div>
        <el-row :gutter="15">
            <el-col :span="8">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Nomor Resi</td><td class="td-value">{{data.resi_number}}</td></tr>
                        <tr><td class="td-label">Nomor SPB</td><td class="td-value">{{data.spb_number}}</td></tr>
                        <tr><td class="td-label">Customer</td><td class="td-value">{{data.customer}}</td></tr>
                        <!-- <tr><td class="td-label">Charge To</td><td class="td-value">{{data.charge_to}}</td></tr> -->
                        <tr><td class="td-label">Asal</td><td class="td-value">{{data.origin}}</td></tr>
                        <tr><td class="td-label">Tujuan</td><td class="td-value">{{data.destination}}</td></tr>
                        <tr><td class="td-label">Alamat Pengiriman</td><td class="td-value">{{data.delivery_address}}</td></tr>
                        <tr><td class="td-label">Jenis Pengiriman</td><td class="td-value">{{data.delivery_type}}</td></tr>
                        <!-- <tr><td class="td-label">Tracking Number</td><td class="td-value">{{data.tracking_number}}</td></tr> -->
                    </tbody>
                </table>
            </el-col>
            <el-col :span="8">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Layanan Pengiriman</td><td class="td-value">{{data.service_type}}</td></tr>
                        <tr><td class="td-label">Jenis Armada</td><td class="td-value">{{data.vehicle_type ? data.vehicle_type.name : ''}}</td></tr>
                        <tr><td class="td-label">Agent</td><td class="td-value">{{data.agent}}</td></tr>
                        <tr><td class="td-label">Nama Kapal</td><td class="td-value">{{data.ship_name}}</td></tr>
                        <tr><td class="td-label">Plat Nomor Armada</td><td class="td-value">{{data.vehicle_number}}</td></tr>
                        <tr><td class="td-label">Nama Driver</td><td class="td-value">{{data.driver_name}}</td></tr>
                        <tr><td class="td-label">No. HP Driver</td><td class="td-value">{{data.driver_phone}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="8">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Tanggal Pick Up</td><td class="td-value">{{data.pick_up_date | readableDate}}</td></tr>
                        <tr><td class="td-label">ETD</td><td class="td-value">{{data.etd | readableDate}}</td></tr>
                        <tr><td class="td-label">Tanggal Kirim</td><td class="td-value">{{data.delivery_date | readableDate}}</td></tr>
                        <tr><td class="td-label">ETA</td><td class="td-value">{{data.eta | readableDate}}</td></tr>
                        <tr>
                            <td class="td-label">Tanggal Terima</td>
                            <td class="td-value">
                                {{data.delivered_date | readableDate}}
                                {{!!data.receiver ? ' oleh ' + data.receiver : ''}}
                            </td>
                        </tr>
                        <tr><td class="td-label">Tanggal Terima STT</td><td class="td-value">{{data.stt_received_date | readableDate}}</td></tr>
                        <!-- <tr><td class="td-label">Invoice Date</td><td class="td-value">{{data.invoice_date}}</td></tr> -->
                        <!-- <tr><td class="td-label">Payment Date</td><td class="td-value">{{data.payment_date}}</td></tr> -->
                        <tr><td class="td-label">Status</td><td class="td-value">{{data.statusName}}</td></tr>
                        <tr><td class="td-label">Note</td><td class="td-value">{{data.status_note}}</td></tr>
                    </tbody>
                </table>
            </el-col>
        </el-row>

        <el-tabs type="card">
            <el-tab-pane label="ITEM">
                <DetailItem :items="data.items" />
            </el-tab-pane>
            <el-tab-pane label="BIAYA">
                <DetailBiaya :data="data" />
            </el-tab-pane>
            <el-tab-pane lazy label="PROGRESS">
                <DeliveryProgress :id="data.id" :data="data" />
            </el-tab-pane>
        </el-tabs>

    </div>
</template>

<script>
import DeliveryProgress from './DeliveryProgress'
import DetailBiaya from './DetailBiaya'
import DetailItem from './DetailItem'

export default {
    props: ['data'],
    components: { DeliveryProgress, DetailBiaya, DetailItem },
}
</script>

<style lang="scss" scoped>
.big {
    font-size: 20px;
}
</style>
