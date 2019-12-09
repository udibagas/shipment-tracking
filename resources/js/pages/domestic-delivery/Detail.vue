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
                <el-table striped :data="data.items" style="margin-top:10px" stripe show-summary :summary-method="getSummaryItem">
                    <el-table-column type="index"></el-table-column>
                    <el-table-column label="Deskripsi" prop="description" show-overflow-tooltip></el-table-column>
                    <el-table-column label="P" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.dimension_p | formatNumber}} CM
                        </template>
                    </el-table-column>
                    <el-table-column label="L" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.dimension_l | formatNumber}} CM
                        </template>
                    </el-table-column>
                    <el-table-column label="T" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.dimension_t | formatNumber}} CM
                        </template>
                    </el-table-column>
                    <el-table-column label="Berat" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.weight | formatNumber}} KG
                        </template>
                    </el-table-column>
                    <el-table-column label="Volume" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                                {{scope.row.volume | formatNumber}} M<sup>3</sup>
                            </template>
                    </el-table-column>
                    <el-table-column label="Berat Volume" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.volume_weight | formatNumber}} KG
                        </template>
                    </el-table-column>
                    <el-table-column label="Berat Invoice" align="center" header-align="center" width="100">
                        <template slot-scope="scope">
                            {{scope.row.invoice_weight | formatNumber}} KG
                        </template>
                    </el-table-column>
                    <el-table-column label="Packing" header-align="center" align="center" width="100">
                        <template slot-scope="scope">
                            <el-checkbox disabled v-model="scope.row.packing"></el-checkbox>
                        </template>
                    </el-table-column>
                    <el-table-column label="Keterangan" prop="remark" show-overflow-tooltip> </el-table-column>
                </el-table>
            </el-tab-pane>
            <el-tab-pane label="BIAYA">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Jenis Biaya</th>
                            <th>Berat/Volume</th>
                            <th>Tarif</th>
                            <th>Biaya</th>
                            <th>PPN</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="td-value"> {{data.service_type}} </td>
                            <td class="td-value text-right">{{data.invoice_weight | formatNumber}} KG</td>
                            <td class="td-value text-right">Rp {{data.delivery_rate | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.delivery_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.delivery_cost_ppn * 0.1 * data.delivery_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.delivery_cost + (data.delivery_cost_ppn * 0.1 * data.delivery_cost) | formatNumber}}</td>
                        </tr>
                        <tr>
                            <td class="td-value">PACKING PETI</td>
                            <td class="td-value text-right">{{data.packing_volume | formatNumber}} M<sup>3</sup></td>
                            <td class="td-value text-right">Rp {{data.packing_rate | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.packing_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.packing_cost_ppn * 0.1 * data.packing_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.packing_cost + (data.packing_cost_ppn * 0.1 * data.packing_cost) | formatNumber}}</td>
                        </tr>
                        <tr>
                            <td class="td-value">BIAYA PENERUS</td>
                            <td></td>
                            <td></td>
                            <td class="td-value text-right">Rp. {{data.forwarder_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.forwarder_cost_ppn * 0.1 * data.forwarder_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.forwarder_cost + (data.forwarder_cost_ppn * 0.1 * data.forwarder_cost) | formatNumber}}</td>
                        </tr>
                        <tr>
                            <td class="td-value">BIAYA LAIN - LAIN</td>
                            <td>{{data.additional_cost_description}}</td>
                            <td></td>
                            <td class="td-value text-right">Rp. {{data.additional_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.additional_cost_ppn * 0.1 * data.additional_cost | formatNumber}}</td>
                            <td class="td-value text-right">Rp. {{data.additional_cost + (data.additional_cost_ppn * 0.1 * data.additional_cost) | formatNumber}}</td>
                        </tr>
                        <tr>
                            <td class="td-value">TOTAL</td>
                            <td class="td-value text-right big" colspan="5">Rp. {{data.total_cost | formatNumber}}</td>
                        </tr>
                    </tbody>
                </table>
            </el-tab-pane>
            <el-tab-pane lazy label="PROGRESS">
                <DeliveryProgress :id="data.id" :data="data" />
            </el-tab-pane>
        </el-tabs>

    </div>
</template>

<script>
import DeliveryProgress from './DeliveryProgress'

export default {
    props: ['data'],
    components: { DeliveryProgress },
    methods: {
        getSummaryItem(param) {
            const { columns, data } = param;
            const sums = []
            columns.forEach((column, index) => {
                // berat
                if (index == 5) {
                    sums[index] = this.data.weight + ' KG'
                }
                // Volume
                if (index == 6) {
                    sums[index] = this.data.volume + ' M3'
                }
                // berat volume
                if (index == 7) {
                    sums[index] = this.data.volume_weight + ' KG'
                }
                // berat invoie
                if (index == 8) {
                    sums[index] = this.data.invoice_weight + ' KG'
                }
                // volume packing
                if (index == 9) {
                    sums[index] = this.data.packing_volume + ' M3'
                }
            })

            return sums
        },
    }
}
</script>

<style lang="scss" scoped>
.big {
    font-size: 20px;
}
</style>
