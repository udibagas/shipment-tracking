<template>
    <div>
        <el-page-header @back="$emit('back')" content="INVOICE"> </el-page-header>
        <br>
        <el-form inline class="text-right" @submit.native.prevent="() => { return }">
            <el-form-item class="margin-bottom-10" v-if="$store.state.user.role == 21 || $store.state.user.role == 31">
                <el-button size="small" icon="el-icon-plus" @click="openForm({items: []})" type="primary">INVOICE BARU</el-button>
            </el-form-item>
            <el-form-item class="margin-bottom-10">
                <el-date-picker
                size="small"
                @change="requestData"
                start-placeholder="Dari"
                end-placeholder="Sampai"
                type="daterange"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                v-model="dateRange"></el-date-picker>
            </el-form-item>
            <el-form-item class="margin-bottom-10">
                <el-input
                size="small"
                v-model="keyword"
                placeholder="Cari"
                prefix-icon="el-icon-search"
                clearable
                @change="(v) => { keyword = v; requestData(); }">
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        @row-dblclick="(row, column, event) => { selectedData = row; showDetail = true; } "
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 245px)"
        v-loading="loading"
        @sort-change="sortChange">
            <el-table-column label="Status" sortable="custom" width="100px" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag class="full-width text-center" size="small" effect="dark" :type="scope.row.status ? 'success' : 'info'">{{scope.row.status ? 'FINAL' : 'DRAFT'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="date" label="Tanggal" sortable="custom" width="120" header-align="center" align="center">
                <template slot-scope="scope">
                    {{scope.row.date | readableDate}}
                </template>
            </el-table-column>
            <el-table-column prop="number" label="Nomor Invoice" sortable="custom" min-width="150"></el-table-column>
            <el-table-column prop="faktur" label="Nomor Faktur" sortable="custom" min-width="150"></el-table-column>
            <el-table-column prop="customer" label="Customer" sortable="custom" min-width="120"></el-table-column>
            <el-table-column prop="service_type" label="Layanan" sortable="custom" min-width="100"></el-table-column>
            <el-table-column prop="total" label="Total" sortable="custom" align="right" header-align="right" min-width="120">
                <template slot-scope="scope">
                    <el-tag size="small" effect="dark" type="primary">Rp {{scope.row.total | formatNumber}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Update Terakhir" prop="updated_at" min-width="150" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.updated_at | readableDateTime}}
                </template>
            </el-table-column>
            <el-table-column label="User" prop="user" sortable="custom" min-width="120"></el-table-column>
            <el-table-column fixed="right" width="40px" align="center" header-align="center">
                <template slot="header">
                    <el-button type="text" class="text-white" @click="() => { page = 1; keyword = ''; requestData(); }" icon="el-icon-refresh"></el-button>
                </template>
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item icon="el-icon-zoom-in" @click.native.prevent="() => { showDetail = true; selectedData = scope.row; }">Lihat Detail</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-printer" v-if="!!scope.row.status" @click.native.prevent="print(scope.row.id)">Print Invoice</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-edit-outline" divided @click.native.prevent="openForm(scope.row)">Edit</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-delete" v-if="!scope.row.status" @click.native.prevent="deleteData(scope.row.id)">Hapus</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <br>

        <el-pagination background
        @current-change="(p) => { page = p; requestData(); }"
        @size-change="(s) => { pageSize = s; requestData(); }"
        layout="total, sizes, prev, next"
        :page-size="pageSize"
        :page-sizes="[10, 25, 50, 100]"
        :total="tableData.total">
        </el-pagination>

        <InvoiceForm v-if="!!formModel" :show="showForm" @close-form="showForm = false" @refresh-data="requestData" :data="formModel" />

        <el-dialog :visible.sync="showDetail" :title="selectedData.number" fullscreen center>
            <iframe v-if="!!selectedData.id" :src="baseUrl + '/invoice/print/' + selectedData.id + '?token= ' + $store.state.token" frameborder="0" style="height:calc(100vh - 150px);width:100%;"></iframe>
        </el-dialog>
    </div>
</template>

<script>
import InvoiceForm from './InvoiceForm'

export default {
    components: { InvoiceForm },
    data() {
        return {
            baseUrl: BASE_URL,
            showForm: false,
            keyword: '',
            page: 1,
            pageSize: 10,
            tableData: {},
            sort: 'date',
            order: 'ascending',
            loading: false,
            showDetail: false,
            selectedData: {},
            dateRange: [],
            formModel: null
        }
    },
    methods: {
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        print(id) {
            window.open(BASE_URL + '/invoice/print/' + id + '?print=1&token=' + this.$store.state.token, '_blank')
        },
        openForm(data) {
            this.formModel = JSON.parse(JSON.stringify(data));

            if (!!this.formModel.id) {
                this.formModel.items.map(i => {
                    i.delivery_date = i.description.delivery_date
                    i.delivered_date = i.description.delivered_date
                    i.origin = i.description.origin
                    i.destination = i.description.destination
                    i.service_type = i.description.service_type
                    i.spb_number = i.description.spb_number
                    i.vehicle_type = i.description.vehicle_type
                    return i
                })
            }

            this.showForm = true
        },
        deleteData(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Konfirmasi', { type: 'warning' }).then(() => {
                axios.delete('/invoice/' + id).then(r => {
                    this.requestData();
                    this.$message({
                        message: r.data.message,
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.$message({
                        message: e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                })
            }).catch(() => console.log(e));
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
            axios.get('/invoice', { params })
            .then(r => this.tableData = r.data)
            .catch(e => {
                if (e.response.status == 500) {
                    this.$message({
                        message: e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                }
            })
            .finally(() => this.loading = false)
        },
    },
    mounted() {
        this.requestData();
        this.$store.commit('getCustomerList')
    }
}
</script>
