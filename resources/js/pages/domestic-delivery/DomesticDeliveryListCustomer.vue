<template>
    <div>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-date-picker
                @change="requestData"
                type="daterange"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                start-placeholder="Dari"
                end-placeholder="Sampai"
                v-model="dateRange"></el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
            <el-form-item>
                <el-button-group>
                    <el-button type="primary" @click="exportToExcel" icon="el-icon-download" title="EXPORT KE EXCEL"></el-button>
                </el-button-group>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        ref="deliveryList"
        @row-dblclick="(row, column, event) => { selectedData = row; showDetailDialog = true; }"
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @filter-change="(f) => { let c = Object.keys(f)[0]; filters[c] = Object.values(f[c]); page = 1; requestData(); }"
        @sort-change="sortChange">
            <el-table-column
            align="center"
            header-align="center"
            column-key="status"
            label="Status"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.deliveryStatusList.map(s => { return { value: s.id, text: s.name } })">
                <template slot-scope="scope">
                    <el-tag effect="dark" size="small" class="rounded full-width text-center"
                    :type="$store.state.deliveryStatusList[scope.row.delivery_status_id].type">
                        {{scope.row.statusName.toUpperCase()}}
                    </el-tag>
                </template>
            </el-table-column>

            <el-table-column prop="resi_number" label="Nomor Resi" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="spb_number" label="Nomor SPB" sortable="custom" min-width="150px"></el-table-column>

            <el-table-column
            prop="customer"
            label="Customer"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.customerList.map(c => { return { value: c.id, text: c.name } })"
            column-key="customer_id">
            </el-table-column>

            <el-table-column prop="origin" label="Asal" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="destination" label="Tujuan" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="delivery_address" label="Alamat Pengiriman" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="pick_up_date" label="Tanggal Pick Up" sortable="custom" min-width="150px" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.pick_up_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="etd" label="ETD" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.etd | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="eta" label="ETA" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.eta | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="delivery_date" label="Tgl Dikirim" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.delivery_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="delivered_date" label="Tgl Terima" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.delivered_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="stt_received_date" label="Tgl Terima STT" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.stt_received_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="receiver" label="Penerima" sortable="custom" min-width="100px"></el-table-column>
            <!-- <el-table-column prop="received_date" label="Tgl Terima" sortable="custom" min-width="140px" header-align="center" align="center"></el-table-column> -->
            <!-- <el-table-column prop="invoice_date" label="Invoice Date" sortable="custom" min-width="140px"></el-table-column> -->
            <!-- <el-table-column prop="payment_date" label="Payment Date" sortable="custom" min-width="140px"></el-table-column> -->
            <!-- <el-table-column prop="tracking_number" label="Nomor Tracking" sortable="custom" min-width="150px"></el-table-column> -->
            <el-table-column prop="delivery_type" label="Jenis Pengiriman" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="service_type" label="Layanan Pengiriman" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="vehicle_type.name" label="Jenis Armada" sortable="custom" min-width="130px"></el-table-column>
            <el-table-column prop="quantity" label="Jml Koli" sortable="custom" min-width="100px" header-align="center" align="center"></el-table-column>
            <el-table-column prop="volume" label="Volume" sortable="custom" min-width="100px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.volume | formatNumber }} M<sup>3</sup>
                </template>
            </el-table-column>
            <el-table-column prop="packing_volume" label="Volume Packing" sortable="custom" min-width="140px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.packing_volume | formatNumber }} M<sup>3</sup>
                </template>
            </el-table-column>
            <el-table-column prop="weight" label="Berat" sortable="custom" min-width="100px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="volume_weight" label="Berat Volume" sortable="custom" min-width="130px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.volume_weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="invoice_weight" label="Berat Invoice" sortable="custom" min-width="120px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.invoice_weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="delivery_cost" label="Biaya Pengiriman" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.delivery_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="delivery_cost_ppn" label="PPN Biaya Pengiriman" sortable="custom" min-width="180px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.delivery_cost_ppn * 0.1 * scope.row.delivery_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost" label="Biaya Packing" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.packing_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost_ppn" label="PPN Biaya Packing" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.packing_cost_ppn * 0.1 * scope.row.packing_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="forwarder_cost" label="Biaya Penerus" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.forwarder_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="forwarder_cost_ppn" label="PPN Biaya Penerus" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.forwarder_cost_ppn * 0.1 * scope.row.forwarder_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="additional_cost" label="Biaya Lain - Lain" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.additional_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="additional_cost_ppn" label="PPN Biaya Lain - Lain" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.additional_cost_ppn * 0.1 * scope.row.additional_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="total_cost" label="Total Biaya" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.total_cost | formatNumber }}
                </template>
            </el-table-column>

            <el-table-column
            prop="agent"
            label="Agent"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.agentList.map(c => { return { value: c.id, text: c.name } })"
            column-key="agent_id">
            </el-table-column>

            <el-table-column prop="ship_name" label="Nama Kapal" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="vehicle_number" label="No. Plat Armada" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="driver_name" label="Nama Driver" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="driver_phone" label="No. HP Driver" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="user" label="Diupdate Oleh" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="updated_at" label="Update Terkahir" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="status_note" label="Note" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-button icon="el-icon-zoom-in" type="text" @click.native.prevent="() => { selectedData = scope.row; showDetailDialog = true; }"></el-button>
                </template>
            </el-table-column>
        </el-table>

        <br>

        <el-pagination background
        @current-change="(p) => { page = p; requestData(); }"
        @size-change="(s) => { pageSize = s; requestData(); }"
        layout="prev, pager, next, sizes, total"
        :page-size="pageSize"
        :page-sizes="[10, 25, 50, 100]"
        :total="tableData.total">
        </el-pagination>

        <el-dialog title="DETAIL PENGIRIMAN DOMESTIC" :visible.sync="showDetailDialog" fullscreen>
            <DetailCustomer v-if="!!selectedData" :data="selectedData" />
        </el-dialog>

    </div>
</template>

<script>
import DetailCustomer from './DetailCustomer'
import exportFromJSON from 'export-from-json'

export default {
    components: { DetailCustomer },
    computed: {
        totalWeight() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + Number(curr.weight)
            }, 0);
        },
        totalVolume() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + (curr.dimension_p * curr.dimension_l * curr.dimension_t / 1000000)
            }, 0);
        },
        totalVolumePacking() {
            return this.formModel.items.reduce((prev, curr) => {
                let volume = !!curr.packing ? (curr.dimension_p * curr.dimension_l * curr.dimension_t / 1000000) : 0
                return prev + volume
            }, 0);
        },
        totalVolumeWeight() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + (curr.dimension_p * curr.dimension_l * curr.dimension_t / 4000)
            }, 0);
        },
        totalInvoiceWeight() {
            let total =  this.formModel.items.reduce((prev, curr) => {
                let invoiceWeight = curr.dimension_p * curr.dimension_l * curr.dimension_t / 4000
                return curr.weight > invoiceWeight ? prev + Number(curr.weight) : prev + Number(invoiceWeight)
            }, 0);

            // kalau dia reguler
            if (!!this.delivery_rate.minimum) {
                return total >= this.delivery_rate.minimum ? total : this.delivery_rate.minimum;
            }

            return total
        },
        totalQuantity() {
            return this.formModel.items.length
        },
        delivery_cost() {
            return this.formModel.service_type == 'CHARTER'
                ? this.delivery_rate.fare
                : this.delivery_rate.fare * this.totalInvoiceWeight
        },
        delivery_cost_ppn() {
            return this.delivery_rate.ppn ? this.delivery_cost * 10 / 100 : 0
        },
        packing_cost() {
            return this.packing_rate.fare * this.totalVolumePacking
        },
        packing_cost_ppn() {
            return this.packing_rate.ppn ? this.packing_cost * 10 / 100 : 0
        },
        total_cost() {
            return this.delivery_cost + this.delivery_cost_ppn + this.packing_cost + this.packing_cost_ppn + Number(this.formModel.forwarder_cost)
        }
    },
    data() {
        return {
            loading: false,
            tableData: {},
            page: 1,
            pageSize: 10,
            keyword: '',
            sort: 'updated_at',
            order: 'descending',
            selectedData: {},
            showDetailDialog: false,
            filters: {},
            dateRange: '',
        }
    },
    methods: {
        exportToExcel() {
            let params = {
                keyword: this.keyword,
                pageSize: 1000000, // hacky solution, but it works
                sort: this.sort,
                order: this.order,
                dateRange: this.dateRange
            }

            this.loading = true;
            axios.get('/domesticDelivery', { params: Object.assign(params, this.filters) }).then(r => {
                let data = r.data.data.map(d => {
                    return {
                        "Nomor SPB": d.spb_number,
                        "Nomor Resi": d.resi_number,
                        "Customer": d.customer,
                        "Asal": d.origin,
                        "Tujuan": d.destination,
                        "Alamat Pengiriman": d.delivery_address,
                        "Jenis Pengiriman": d.delivery_type,
                        "Layanan": d.service_type,
                        "Jenis Armada": d.vehicle_type ? d.vehicle_type.name : '',
                        "Agent": d.agent,
                        "Nama Kapal": d.ship_name,
                        "No. Plat Armada": d.vehicle_number,
                        "Driver Armada": d.driver_name,
                        "No. HP Driver": d.driver_phone,
                        "Tgl Pick Up": d.pick_up_date,
                        "ETD": d.etd,
                        "Tgl Kirim": d.delivery_date,
                        "ETA": d.eta,
                        "Tgl Terima": d.delivered_date,
                        "Penerima": d.receiver,
                        "Tgl Terima STT": d.stt_received_date,
                        "Jml Koli": d.quantity,
                        "Berat": d.weight,
                        "Berat Volume": d.volume_weight,
                        "Berat Invoice": d.invoice_weight,
                        "Volume": d.volume,
                        "Volume Packing": d.packing_volume,
                        "Biaya Kirim": d.delivery_cost,
                        "PPN Biaya Kirim": d.delivery_cost_ppn * 0.1 * d.delivery_cost,
                        "Biaya Packing": d.packing_cost,
                        "PPN Biaya Packing": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Biaya Penerus": d.packing_cost,
                        "PPN Biaya Penerus": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Biaya Lain - Lain": d.packing_cost,
                        "PPN Biaya Lain - Lain": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Total Biaya": d.total_cost,
                        "Status": d.statusName.toUpperCase(),
                        "Waktu Update": d.updated_at,
                    }
                })

                exportFromJSON({ data: data, fileName: 'domestic-delivery', exportType: 'xls' })
            }).catch(e => {
                console.log(e)
                if (e.response.status == 500) {
                    this.$message({
                        message: e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                }
            }).finally(() => {
                this.loading = false
            })
        },
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        requestData() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
                dateRange: this.dateRange
            }

            this.loading = true;
            axios.get('/domesticDelivery', { params: Object.assign(params, this.filters) }).then(r => {
                    this.tableData = r.data
                    this.$emit('loaded', r.data)
            }).catch(e => {
                if (e.response.status == 500) {
                    this.$message({
                        message: e.response.data.message + '\n' + e.response.data.file + ':' + e.response.data.line,
                        type: 'error',
                        showClose: true
                    });
                }
            }).finally(() => {
                this.loading = false
            })
        },
    },
    mounted() {
        this.requestData();
    }
}
</script>
