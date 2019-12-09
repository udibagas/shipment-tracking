<template>
    <div>
        <el-page-header @back="$emit('back')" content="COMPANIES"> </el-page-header>
        <el-divider></el-divider>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({role: 0, password: ''})" type="primary">ADD NEW COMPANY</el-button>
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
            <el-table-column fixed="left" type="expand">
                <template slot-scope="scope">
                    <table>
                        <tbody>
                            <tr><td class="td-label">Kode</td><td class="td-value">{{scope.row.code}}</td></tr>
                            <tr><td class="td-label">Nama Perusahaan</td><td class="td-value">{{scope.row.name}}</td></tr>
                            <tr><td class="td-label">Alamat</td><td class="td-value">{{scope.row.address}}</td></tr>
                            <tr><td class="td-label">Phone</td><td class="td-value">{{scope.row.phone}}</td></tr>
                            <tr><td class="td-label">Fax</td><td class="td-value">{{scope.row.fax}}</td></tr>
                            <tr><td class="td-label">Email</td><td class="td-value">{{scope.row.email}}</td></tr>
                            <tr><td class="td-label">Website</td><td class="td-value">{{scope.row.website}}</td></tr>
                            <tr><td class="td-label">Contact Person</td><td class="td-value">{{scope.row.contact_person}}</td></tr>
                            <tr><td class="td-label">Contact Person Email</td><td class="td-value">{{scope.row.contact_person_email}}</td></tr>
                            <tr><td class="td-label">Contact Person Phone</td><td class="td-value">{{scope.row.contact_person_phone}}</td></tr>
                            <tr><td class="td-label">Status</td><td class="td-value">{{scope.row.status ? 'Active' : 'Inactive'}}</td></tr>
                        </tbody>
                    </table>
                </template>
            </el-table-column>
            <el-table-column label="Status" sortable="custom" min-width="100px" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag class="rounded full-width text-center" size="small" effect="dark" :type="scope.row.active ? 'success' : 'info'">{{scope.row.active ? 'AKTIF' : 'NONAKTIF'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="code" label="Kode" sortable="custom" min-width="80px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="name" label="Name" sortable="custom" min-width="200px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="email" label="Email" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="phone" label="Phone" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="address" label="Address" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person" label="Contact Person" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person_phone" label="Contact Person Phone" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="contact_person_email" label="Contact Person Email" sortable="custom" min-width="180px" show-overflow-tooltip></el-table-column>

            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="openForm(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Delete</el-dropdown-item>
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

        <el-dialog
        width="680px"
        :visible.sync="showForm"
        :title="!!formModel.id ? 'EDIT COMPANY' : 'ADD NEW COMPANY'"
        v-loading="loading"
        :close-on-click-modal="false">

            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="160px" label-position="left">
                <el-tabs type="card">
                    <el-tab-pane label="Informasi Perusahaan">
                        <el-form-item label="Kode" :class="formErrors.code ? 'is-error' : ''">
                            <el-input placeholder="Kode" v-model="formModel.code"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.code">{{formErrors.code[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nama Perusahaan" :class="formErrors.name ? 'is-error' : ''">
                            <el-input placeholder="Nama Perusahaan" v-model="formModel.name"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Nama Direktur" :class="formErrors.director_name ? 'is-error' : ''">
                            <el-input placeholder="Nama Direktur" v-model="formModel.director_name"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.director_name">{{formErrors.director_name[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Status" :class="formErrors.active ? 'is-error' : ''">
                            <el-switch
                            :active-value="1"
                            :inactive-value="0"
                            v-model="formModel.active"
                            active-color="#13ce66">
                            </el-switch>
                            <el-tag :type="formModel.active ? 'success' : 'info'" size="small" style="margin-left:10px">{{!!formModel.active ? 'Active' : 'Inactive'}}</el-tag>

                            <div class="el-form-item__error" v-if="formErrors.active">{{formErrors.active[0]}}</div>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="Alamat">
                        <el-form-item label="Alamat" :class="formErrors.address ? 'is-error' : ''">
                            <el-input type="textarea" rows="3" placeholder="Alamat" v-model="formModel.address"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.address">{{formErrors.address[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Phone" :class="formErrors.phone ? 'is-error' : ''">
                            <el-input placeholder="Phone" v-model="formModel.phone"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.phone">{{formErrors.phone[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Fax" :class="formErrors.fax ? 'is-error' : ''">
                            <el-input placeholder="Fax" v-model="formModel.fax"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.fax">{{formErrors.fax[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Email" :class="formErrors.email ? 'is-error' : ''">
                            <el-input placeholder="Email" v-model="formModel.email"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Website" :class="formErrors.website ? 'is-error' : ''">
                            <el-input placeholder="Website" v-model="formModel.website"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.website">{{formErrors.website[0]}}</div>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="Contact Person">
                        <el-form-item label="Contact Person Name" :class="formErrors.contact_person ? 'is-error' : ''">
                            <el-input placeholder="Contact Person Name" v-model="formModel.contact_person"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.contact_person">{{formErrors.contact_person[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Contact Person Email" :class="formErrors.contact_person_email ? 'is-error' : ''">
                            <el-input placeholder="Contact Person Email" v-model="formModel.contact_person_email"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.contact_person_email">{{formErrors.contact_person_email[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Contact Person Phone" :class="formErrors.contact_person_phone ? 'is-error' : ''">
                            <el-input placeholder="Contact Person Phone" v-model="formModel.contact_person_phone"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.contact_person_phone">{{formErrors.contact_person_phone[0]}}</div>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="Email Setting">
                        <el-form-item label="SMTP Host" :class="formErrors.smtp_host ? 'is-error' : ''">
                            <el-input placeholder="SMTP Host" v-model="formModel.smtp_host"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.smtp_host">{{formErrors.smtp_host[0]}}</div>
                        </el-form-item>
                        <el-form-item label="SMTP Port" :class="formErrors.smtp_port ? 'is-error' : ''">
                            <el-input type="number" placeholder="SMTP Port" v-model="formModel.smtp_port"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.smtp_port">{{formErrors.smtp_port[0]}}</div>
                        </el-form-item>
                        <el-form-item label="SMTP Encryption" :class="formErrors.smtp_encryption ? 'is-error' : ''">
                            <el-select clearable v-model="formModel.smtp_encryption" placeholder="SMTP Encryption" style="width:100%">
                                <el-option v-for="(e, i) in ['ssl', 'tls']" :key="i" :label="e" :value="e"></el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.smtp_encryption">{{formErrors.smtp_encryption[0]}}</div>
                        </el-form-item>
                        <el-form-item label="SMTP Username" :class="formErrors.smtp_username ? 'is-error' : ''">
                            <el-input placeholder="SMTP Username" v-model="formModel.smtp_username"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.smtp_username">{{formErrors.smtp_username[0]}}</div>
                        </el-form-item>
                        <el-form-item label="SMTP Password" :class="formErrors.smtp_password ? 'is-error' : ''">
                            <el-input placeholder="SMTP Password" v-model="formModel.smtp_password"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.smtp_password">{{formErrors.smtp_password[0]}}</div>
                        </el-form-item>
                    </el-tab-pane>
                </el-tabs>

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
            axios.post('/company', this.formModel).then(r => {
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
            axios.put('/company/' + this.formModel.id, this.formModel).then(r => {
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
                axios.delete('/company/' + id).then(r => {
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
            axios.get('/company', {params: params}).then(r => {
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

<style scoped>

</style>

