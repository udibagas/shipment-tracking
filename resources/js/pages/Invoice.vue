<template>
    <div>
        <el-page-header @back="$emit('back')" content="INVOICE"> </el-page-header>
        <el-divider></el-divider>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({items: []})" type="primary">INVOICE BARU</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @sort-change="sortChange">
            <el-table-column prop="customer" label="Customer" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column prop="date" label="Tanggal" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column prop="number" label="Nomor" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column prop="total" label="Total" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column label="Status" prop="status" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.status}}
                </template>
            </el-table-column>
            <el-table-column label="Update Terakhir" prop="updated_at" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.updated_at | readableDateTime}}
                </template>
            </el-table-column>
            <el-table-column label="User" prop="user" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="openForm(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
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

        <el-dialog :visible.sync="showForm" :title="!!formModel.id ? 'EDIT INVOICE' : 'INVOICE BARU'" fullscreen v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">
                <el-row :gutter="30">
                    <el-col :span="12">
                        <el-form-item label="Customer">
                            <el-select @change="getItems" v-model="formModel.customer_id" placeholder="Customer" style="width:100%">
                                <el-option v-for="c in $store.state.customerList" :key="c.id" :value="c.id" :label="c.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Tanggal">
                            <el-date-picker format="dd-MMM-yyyy" value-format="yyyy-MM-dd" v-model="formModel.date" placeholder="Tanggal" style="width:100%"></el-date-picker>
                        </el-form-item>
                        <el-form-item label="Nomor">
                            <el-input placeholder="Nomor" v-model="formModel.number"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Total (Rp)">
                            <el-input disabled v-model="total"></el-input>
                        </el-form-item>
                        <el-form-item label="PPN (Rp)">
                            <el-input disabled v-model="ppn"></el-input>
                        </el-form-item>
                        <el-form-item label="Grand Total (Rp)">
                            <el-input disabled v-model="grand_total"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <el-table :data="formModel.items">
                <el-table-column label="No" type="index" width="55px"></el-table-column>
                <el-table-column label="Tgl Kirim" min-width="120">
                    <template slot-scope="scope">
                        {{scope.row.delivery_date | formattedDate}}
                    </template>
                </el-table-column>
                <el-table-column label="Tgl Terima" min-width="120">
                    <template slot-scope="scope">
                        {{scope.row.delivered_date | formattedDate}}
                    </template>
                </el-table-column>
                <el-table-column label="Surat Pengantar" prop="spb_number" min-width="150"></el-table-column>
                <el-table-column label="Asal" prop="origin" show-overflow-tooltip min-width="150"></el-table-column>
                <el-table-column label="Tujuan" prop="destination" show-overflow-tooltip min-width="150"></el-table-column>
                <el-table-column label="Layanan" prop="service_type" min-width="120"></el-table-column>
                <el-table-column label="Quantity" prop="quantity" min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.quantity | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="Unit" prop="unit" header-align="center" align="center" min-width="80"></el-table-column>
                <el-table-column label="Tarif" prop="fare" align="right" header-align="right" min-width="120">
                    <template slot-scope="scope">
                        Rp. {{scope.row.fare | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="Harga" prop="price" align="right" header-align="right" min-width="120">
                    <template slot-scope="scope">
                        Rp. {{scope.row.price | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="PPN" prop="tax" align="right" header-align="right" min-width="120">
                    <template slot-scope="scope">
                        Rp. {{scope.row.tax | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="Total" prop="total" align="right" header-align="right" min-width="120">
                    <template slot-scope="scope">
                        Rp. {{scope.row.total | formatNumber}}
                    </template>
                </el-table-column>
            </el-table>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()" icon="el-icon-success">SIMPAN</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">BATAL</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    computed: {
        total() {
            return this.formModel.items.reduce((t, c) => t + c.price , 0)
        },
        ppn() {
            return this.formModel.items.reduce((t, c) => t + c.tax , 0)
        },
        grand_total() {
            return this.total + this.ppn;
        }
    },
    data() {
        return {
            showForm: false,
            formErrors: {},
            error: {},
            formModel: { items: []},
            keyword: '',
            page: 1,
            pageSize: 10,
            tableData: {},
            sort: 'name',
            order: 'ascending',
            loading: false
        }
    },
    methods: {
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        openForm(data) {
            this.error = {}
            this.formErrors = {}
            this.formModel = JSON.parse(JSON.stringify(data));
            this.showForm = true
        },
        store() {
            this.loading = true;
            axios.post('/domesticDeliveryInvoice', this.formModel).then(r => {
                this.showForm = false;
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.requestData();
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        update() {
            this.loading = true;
            axios.put('/domesticDeliveryInvoice/' + this.formModel.id, this.formModel).then(r => {
                this.showForm = false
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.requestData()
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        deleteData(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', { type: 'warning' }).then(() => {
                axios.delete('/domesticDeliveryInvoice/' + id).then(r => {
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
            }

            this.loading = true;
            axios.get('/domesticDeliveryInvoice', {params: params}).then(r => {
                    this.tableData = r.data
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
        getItems() {
            let params = {
                delivery_status_id: 3, // received
                invoice_status: 0,
                customer_id: this.formModel.customer_id,
                company_id: this.$store.state.user.company_id
            }

            axios.get('/domesticDelivery/search', { params: params }).then(r => {
                this.formModel.items = r.data.map(d => {
                    d.unit = ' KG'
                    d.price = d.quantity * d.fare
                    d.tax = d.ppn
                    d.total = d.tax + d.tax
                    return d
                })
            }).catch(e => {
                this.$message({
                    message: e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        }
    },
    mounted() {
        this.requestData();
        this.$store.commit('getCustomerList')
    }
}
</script>
