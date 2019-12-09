<template>
    <div>
        <el-row :gutter="15">
            <el-col :span="12">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr><td class="td-label">Nomor Resi</td><td class="td-value">{{data.resi_number}}</td></tr>
                        <tr><td class="td-label">Nomor SPB</td><td class="td-value">{{data.spb_number}}</td></tr>
                        <tr><td class="td-label">Tujuan</td><td class="td-value">{{data.destination}}</td></tr>
                        <tr><td class="td-label">Alamat Pengiriman</td><td class="td-value">{{data.delivery_address}}</td></tr>
                        <tr><td class="td-label">Jenis Pengiriman</td><td class="td-value">{{data.delivery_type}}</td></tr>
                        <tr><td class="td-label">Layanan Pengiriman</td><td class="td-value">{{data.service_type}}</td></tr>
                        <tr><td class="td-label">Jenis Armada</td><td class="td-value">{{data.vehicle_type ? data.vehicle_type.name : ''}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="12">
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
                        <tr>
                            <td class="td-label">Status</td>
                            <td class="td-value">
                                <el-tag effect="dark" size="small" class="rounded text-center"
                                :type="$store.state.deliveryStatusList[data.delivery_status_id].type">
                                    {{data.statusName.toUpperCase()}}
                                </el-tag>
                            </td>
                        </tr>
                        <tr><td class="td-label">Note</td><td class="td-value">{{data.status_note}}</td></tr>
                    </tbody>
                </table>
            </el-col>
        </el-row>

        <el-tabs type="card">
            <el-tab-pane lazy label="PROGRESS">
                <DeliveryProgressCustomer :id="data.id" :data="data" />
            </el-tab-pane>
            <el-tab-pane label="ITEM">
                <DetailItem :data="data" :items="data.items" />
            </el-tab-pane>
            <el-tab-pane label="BIAYA">
                <DetailBiaya :data="data" />
            </el-tab-pane>
        </el-tabs>

    </div>
</template>

<script>
import DeliveryProgressCustomer from './DeliveryProgressCustomer'
import DetailBiaya from './DetailBiaya'
import DetailItem from './DetailItem'

export default {
    props: ['data'],
    components: { DeliveryProgressCustomer, DetailBiaya, DetailItem },
}
</script>

<style lang="scss" scoped>
.big {
    font-size: 20px;
}
</style>
