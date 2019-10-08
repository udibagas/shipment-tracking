<template>
    <el-timeline style="margin-top:20px">
        <el-timeline-item
        size="large"
        v-for="p in progress"
        :key="p.id"
        :timestamp="p.time + ' by ' + p.user"
        :type="p.type"
        placement="top">
            <el-card>
                <div slot="header" style="font-size:20px;">{{p.status_name}}</div>
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr v-if="p.status == 0"><td class="td-label">Nomor Resi</td><td class="td-value">{{data.resi_number}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Nomor SPB</td><td class="td-value">{{data.spb_number}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Customer</td><td class="td-value">{{data.customer}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Asal</td><td class="td-value">{{data.origin}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Tujuan</td><td class="td-value">{{data.destination}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Alamat Pengiriman</td><td class="td-value">{{data.delivery_address}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Jenis Pengiriman</td><td class="td-value">{{data.delivery_type}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Layanan Pengiriman</td><td class="td-value">{{data.service_type}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Jenis Armada</td><td class="td-value">{{data.vehicle_type ? data.vehicle_type.name : ''}}</td></tr>
                        <tr v-if="p.status == 0"><td class="td-label">Tanggal Pick Up</td><td class="td-value">{{data.pick_up_date | readableDate}}</td></tr>
                        <tr v-if="p.status == 1"><td class="td-label">ETD</td><td class="td-value">{{data.etd | readableDate}}</td></tr>
                        <tr v-if="p.status == 2"><td class="td-label">Tanggal Kirim</td><td class="td-value">{{data.delivery_date | readableDate}}</td></tr>
                        <tr v-if="p.status == 2"><td class="td-label">ETA</td><td class="td-value">{{data.eta | readableDate}}</td></tr>
                        <tr v-if="p.status == 1 || p.status == 2"><td class="td-label">Agent</td><td class="td-value">{{data.agent}}</td></tr>
                        <tr v-if="p.status == 2 && !!data.ship_name"><td class="td-label">Nama Kapal</td><td class="td-value">{{data.ship_name}}</td></tr>
                        <tr v-if="p.status == 2 && !!data.vehicle_number"><td class="td-label">Plat Nomor Armada</td><td class="td-value">{{data.vehicle_number}}</td></tr>
                        <tr v-if="p.status == 2 && !!data.driver_name"><td class="td-label">Nama Driver</td><td class="td-value">{{data.driver_name}}</td></tr>
                        <tr v-if="p.status == 2 && !!data.driver_phone"><td class="td-label">No. HP Driver</td><td class="td-value">{{data.driver_phone}}</td></tr>
                        <tr v-if="p.status == 3"><td class="td-label">Tanggal Terima</td><td class="td-value">{{data.delivered_date | readableDate}}</td></tr>
                        <tr v-if="p.status == 3"><td class="td-label">Penerima</td><td class="td-value">{{data.receiver}}</td></tr>
                        <tr><td class="td-label">Note</td><td class="td-value">{{p.note}}</td></tr>
                    </tbody>
                </table>
            </el-card>
        </el-timeline-item>
    </el-timeline>
</template>

<script>
export default {
    props: ['id', 'data'],
    watch: {
        id(v) { this.requestData() }
    },
    data() {
        return {
            progress: []
        }
    },
    methods: {
        requestData() {
            let params = { delivery_id: this.id }
            axios.get('/deliveryProgress', { params: params }).then(r => {
                this.progress = r.data.map(d => {
                    d.status_name = this.$store.state.deliveryStatusList[d.status].name
                    d.time = moment(d.created_at).format('DD-MMM-YYYY HH:mm')

                    if (d.status == 0) {
                        d.type = 'info'
                    }

                    if (d.status == 1) {
                        d.type = 'warning'
                    }

                    if (d.status == 2) {
                        d.type = 'primary'
                    }

                    if (d.status == 3) {
                        d.type = 'success'
                    }

                    return d;
                })
            }).catch(e => console.log(e))
        }
    },
    mounted() {
        this.requestData()
    }
}
</script>
