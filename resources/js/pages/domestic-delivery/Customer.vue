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
            <el-table-column prop="spb_number" label="Nomor SPB" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="resi_number" label="Nomor Resi" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="destination" label="Tujuan" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_address" label="Alamat Pengiriman" sortable="custom" min-width="170px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_type" label="Jenis Pengiriman" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="service_type" label="Layanan Pengiriman" sortable="custom" min-width="170px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="vehicle_type.name" label="Jenis Armada" sortable="custom" min-width="170px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="quantity" label="Jml Koli" sortable="custom" min-width="150px" header-align="center" align="center"></el-table-column>
            <el-table-column prop="volume" label="Volume" sortable="custom" min-width="100px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.volume | formatNumber }} M<sup>3</sup>
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
            <el-table-column prop="pick_up_date" label="Tanggal Pick Up" sortable="custom" min-width="140px" align="center" header-align="center">
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
            <el-table-column prop="receiver" label="Penerima" sortable="custom" min-width="100px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_cost" label="Biaya Pengiriman" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.delivery_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="delivery_cost_ppn" label="PPN Biaya Pengiriman" sortable="custom" min-width="180px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.delivery_cost_ppn | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost" label="Biaya Packing" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.packing_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost_ppn" label="PPN Biaya Packing" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.packing_cost_ppn | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="total_cost" label="Total Biaya" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.total_cost | formatNumber }}
                </template>
            </el-table-column>

            <el-table-column prop="updated_at" label="Update Terkahir" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="status_note" label="Note" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="right" column-key="status" label="Status" sortable="custom" min-width="150px" :filters="$store.state.deliveryStatusList.map(s => { return { value: s.id, text: s.name } })">
                <template slot-scope="scope">
                    <el-tag :type="$store.state.deliveryStatusList[scope.row.delivery_status_id].type" size="small">{{scope.row.statusName}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <a style="text-decoration:none;" class="el-icon-zoom-in" href="#" @click.prevent="() => { selectedData = scope.row; showDetailDialog = true; }"></a>
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
                        "Tujuan": d.destination,
                        "Alamat Pengiriman": d.delivery_address,
                        "Jenis Pengiriman": d.delivery_type,
                        "Layanan": d.service_type,
                        "Jenis Armada": d.vehicle_type ? d.vehicle_type.name : '',
                        "Jml Koli": d.quantity,
                        "Berat": d.weight,
                        "Berat Volume": d.volume_weight,
                        "Berat Invoice": d.invoice_weight,
                        "Volume": d.volume,
                        "Volume Packing": d.packing_volume,
                        "Tgl Pick Up": d.pick_up_date,
                        "ETD": d.etd,
                        "Tgl Kirim": d.delivery_date,
                        "ETA": d.eta,
                        "Tgl Terima": d.delivered_date,
                        "Penerima": d.receiver,
                        "Biaya Kirim": d.delivery_cost,
                        "PPN Biaya Kirim": d.delivery_cost_ppn,
                        "Biaya Packing": d.packing_cost,
                        "PPN Biaya Packing": d.packing_cost_ppn,
                        "Total Biaya": d.total_cost,
                        "Status": d.statusName,
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
