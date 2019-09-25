<template>
    <div>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({})" type="primary">TAMBAH TARIF REGULER</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 345px)"
        v-loading="loading"
        @filter-change="(f) => { let c = Object.keys(f)[0]; filters[c] = Object.values(f[c]); page = 1; requestData(); }"
        @sort-change="sortChange">
            <el-table-column v-if="$store.state.user.role == 11" prop="company" label="Company" sortable="custom" show-overflow-tooltip></el-table-column>

            <el-table-column
            show-overflow-tooltip
            :filters="$store.state.customerList.map(c => { return { value: c.id, text: c.name } })"
            column-key="customer_id"
            prop="customer"
            label="Customer"
            sortable="custom">
            </el-table-column>

            <el-table-column prop="destination" label="Tujuan" sortable="custom" show-overflow-tooltip></el-table-column>
            <el-table-column prop="fare" label="Tarif" sortable="custom" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{scope.row.fare | formatNumber}}
                </template>
            </el-table-column>
            <el-table-column prop="lead_time" label="Lead Time" sortable="custom" align="center" header-align="center"></el-table-column>
            <el-table-column prop="minimum" label="Min. Charge" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.minimum}} KG
                </template>
            </el-table-column>
            <el-table-column prop="ppn" label="PPN" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.ppn ? 'Ya' : 'Tidak'}}
                </template>
            </el-table-column>
            <el-table-column prop="updated_at" label="Update" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.updated_at | readableDate}}
                </template>
            </el-table-column>
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

        <el-dialog :visible.sync="showForm" :title="!!formModel.id ? 'EDIT TARIF REGULER' : 'TAMBAH TARIF REGULER'" width="500px" v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">

                <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
                    <el-select v-model="formModel.customer_id" placeholder="Customer" filterable default-first-option style="width:100%">
                        <el-option v-for="(t, i) in $store.state.customerList"
                        :value="t.id"
                        :label="t.code + ' - ' + t.name"
                        :key="i">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.customer_id">{{formErrors.customer_id[0]}}</div>
                </el-form-item>

                <el-form-item label="Tujuan" :class="formErrors.destination ? 'is-error' : ''">
                    <el-select v-model="formModel.destination" placeholder="Tujuan" filterable default-first-option style="width:100%">
                        <el-option v-for="(t, i) in $store.state.cityList"
                        :value="t.name"
                        :label="t.name"
                        :key="i">
                        </el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.destination">{{formErrors.destination[0]}}</div>
                </el-form-item>

                <el-form-item label="Tarif (Rp)" :class="formErrors.fare ? 'is-error' : ''">
                    <el-input type="number" placeholder="Tarif (Rp)" v-model="formModel.fare"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.fare">{{formErrors.fare[0]}}</div>
                </el-form-item>

                <el-form-item label="Lead Time" :class="formErrors.lead_time ? 'is-error' : ''">
                    <el-input placeholder="Lead Time" v-model="formModel.lead_time"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.lead_time">{{formErrors.lead_time[0]}}</div>
                </el-form-item>

                <el-form-item label="Minimum Charge (KG)" :class="formErrors.minimum ? 'is-error' : ''">
                    <el-input type="number" placeholder="Minimum Charge (KG)" v-model="formModel.minimum"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.minimum">{{formErrors.minimum[0]}}</div>
                </el-form-item>

                <el-form-item label="PPN" :class="formErrors.ppn ? 'is-error' : ''">
                    <el-select placeholder="PPN" v-model="formModel.ppn" style="width:100%">
                        <el-option v-for="(label, index) in ['Tidak', 'Ya']" :key="index" :value="index" :label="label"></el-option>
                    </el-select>
                    <div class="el-form-item__error" v-if="formErrors.ppn">{{formErrors.ppn[0]}}</div>
                </el-form-item>

            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()" icon="el-icon-success">SIMPAN</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">BATAL</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showForm: false,
            formErrors: {},
            error: {},
            formModel: {},
            keyword: '',
            page: 1,
            pageSize: 10,
            tableData: {},
            sort: 'customer',
            order: 'ascending',
            loading: false,
            filters: {},
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
            axios.post('/masterFare', this.formModel).then(r => {
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
            axios.put('/masterFare/' + this.formModel.id, this.formModel).then(r => {
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
                axios.delete('/masterFare/' + id).then(r => {
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
            axios.get('/masterFare', { params: Object.assign(params, this.filters) }).then(r => {
                    this.tableData = r.data
            }).catch(e => {
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
        }
    },
    mounted() {
        this.requestData();
    }
}
</script>
