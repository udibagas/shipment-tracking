<template>
    <div>
        <el-page-header @back="$emit('back')" content="CUSTOMER"> </el-page-header>
        <el-divider></el-divider>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({role: 0, password: ''})" type="primary">TAMBAH CUSTOMER</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        @row-dblclick="(row, column, event) => { selectedData = row; showDetail = true; }"
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @sort-change="sortChange">
            <el-table-column fixed="left" prop="code" label="Kode" sortable="custom" min-width="80px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="left" prop="name" label="Nama" sortable="custom" min-width="200px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="email" label="Email" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="phone" label="No. Telp." sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="address" label="ALamat" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person" label="Nama Contact Person" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person_phone" label="No. HP Contact Person" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person_email" label="Email Contact Person" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="status" label="Status" sortable="custom" min-width="100px">
                <template slot-scope="scope">
                    <el-tag size="mini" :type="scope.row.active ? 'success' : 'info'">{{scope.row.active ? 'Aktif' : 'Nonaktif'}}</el-tag>
                </template>
            </el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item icon="el-icon-zoom-in" @click.native.prevent="() => { selectedData = scope.row; showDetail = true; }">Lihat Detail</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-edit-outline" @click.native.prevent="openForm(scope.row)">Edit</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-delete" @click.native.prevent="deleteData(scope.row.id)">Hapus</el-dropdown-item>
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

        <el-dialog title="DETAIL CUSTOMER" center :visible.sync="showDetail">
            <table class="table table-sm table-striped" v-if="!!selectedData.id">
                <tbody>
                    <tr><td class="text-bold" style="width:150px">Kode</td><td>: {{selectedData.code}}</td></tr>
                    <tr><td class="text-bold">Nama</td><td>: {{selectedData.name}}</td></tr>
                    <tr><td class="text-bold">Alamat</td><td>: {{selectedData.address}}</td></tr>
                    <tr><td class="text-bold">No Telp.</td><td>: {{selectedData.phone}}</td></tr>
                    <tr><td class="text-bold">Fax</td><td>: {{selectedData.fax}}</td></tr>
                    <tr><td class="text-bold">Email</td><td>: {{selectedData.email}}</td></tr>
                    <tr><td class="text-bold">Website</td><td>: {{selectedData.website}}</td></tr>
                    <tr><td class="text-bold">Nama Contact Person</td><td>: {{selectedData.contact_person}}</td></tr>
                    <tr><td class="text-bold">Email Contact Person</td><td>: {{selectedData.contact_person_email}}</td></tr>
                    <tr><td class="text-bold">No HP Contact Person</td><td>: {{selectedData.contact_person_phone}}</td></tr>
                    <tr><td class="text-bold">Status</td><td>: {{selectedData.status ? 'Inaktif' : 'Nonaktif'}}</td></tr>
                </tbody>
            </table>
        </el-dialog>

        <el-dialog
        top="60px"
        :visible.sync="showForm"
        :title="!!formModel.id ? 'EDIT CUSTOMER' : 'TAMBAH CUSTOMER'"
        width="550"
        v-loading="loading"
        :close-on-click-modal="false">

            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">
                <el-form-item label="Kode" :class="formErrors.code ? 'is-error' : ''">
                    <el-input placeholder="Kode" v-model="formModel.code"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.code">{{formErrors.code[0]}}</div>
                </el-form-item>

                <el-form-item label="Nama" :class="formErrors.name ? 'is-error' : ''">
                    <el-input placeholder="Nama" v-model="formModel.name"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                </el-form-item>

                <el-form-item label="Status" :class="formErrors.active ? 'is-error' : ''">
                    <el-switch
                    :active-value="1"
                    :inactive-value="0"
                    v-model="formModel.active"
                    active-color="#13ce66">
                    </el-switch>
                    <el-tag :type="formModel.active ? 'success' : 'info'" size="small" style="margin-left:10px">{{!!formModel.active ? 'Aktif' : 'Nonaktif'}}</el-tag>

                    <div class="el-form-item__error" v-if="formErrors.active">{{formErrors.active[0]}}</div>
                </el-form-item>

                <el-form-item label="Alamat" :class="formErrors.address ? 'is-error' : ''">
                    <el-input type="textarea" rows="4" placeholder="Alamat" v-model="formModel.address"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.address">{{formErrors.address[0]}}</div>
                </el-form-item>
                <el-form-item label="Phone" :class="formErrors.phone ? 'is-error' : ''">
                    <el-input placeholder="No. Telpon" v-model="formModel.phone"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.phone">{{formErrors.phone[0]}}</div>
                </el-form-item>

                <el-form-item label="Fax" :class="formErrors.fax ? 'is-error' : ''">
                    <el-input placeholder="Fax" v-model="formModel.fax"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.fax">{{formErrors.fax[0]}}</div>
                </el-form-item>

                <el-form-item label="Website" :class="formErrors.website ? 'is-error' : ''">
                    <el-input placeholder="Website" v-model="formModel.website"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.website">{{formErrors.website[0]}}</div>
                </el-form-item>

                <el-form-item label="Email" :class="formErrors.email ? 'is-error' : ''">
                    <el-input type="textarea" rows="4" placeholder="Email" v-model="formModel.email"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                </el-form-item>
                <el-form-item label="Nama Contact Person" :class="formErrors.contact_person ? 'is-error' : ''">
                    <el-input placeholder="Nama Contact Person" v-model="formModel.contact_person"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.contact_person">{{formErrors.contact_person[0]}}</div>
                </el-form-item>

                <el-form-item label="Email Contact Person" :class="formErrors.contact_person_email ? 'is-error' : ''">
                    <el-input placeholder="Email Contact Person" v-model="formModel.contact_person_email"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.contact_person_email">{{formErrors.contact_person_email[0]}}</div>
                </el-form-item>

                <el-form-item label="No. HP Contact Person" :class="formErrors.contact_person_phone ? 'is-error' : ''">
                    <el-input placeholder="No. HP Contact Person" v-model="formModel.contact_person_phone"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.contact_person_phone">{{formErrors.contact_person_phone[0]}}</div>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button icon="el-icon-success" type="primary" @click="() => !!formModel.id ? update() : store()">SIMPAN</el-button>
                <el-button icon="el-icon-error" type="info" @click="showForm = false">BATAL</el-button>
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
            sort: 'name',
            order: 'ascending',
            loading: false,
            showDetail: false,
            selectedData: {}
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
            this.formModel.company_id = this.$store.state.user.company_id
            axios.post('/customer', this.formModel).then(r => {
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
            axios.put('/customer/' + this.formModel.id, this.formModel).then(r => {
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
                axios.delete('/customer/' + id).then(r => {
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
            axios.get('/customer', {params: params}).then(r => {
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
        }
    },
    mounted() {
        this.requestData();
    }
}
</script>

