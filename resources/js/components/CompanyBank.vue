<template>
    <div>
        <el-table :data="banks" stripe>
            <el-table-column label="Status" sortable="custom" width="100px" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag class="rounded full-width text-center" size="small" effect="dark" :type="scope.row.active ? 'success' : 'info'">{{scope.row.active ? 'AKTIF' : 'NONAKTIF'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Nama Bank" prop="bank_name"> </el-table-column>
            <el-table-column label="Cabang" prop="bank_branch"> </el-table-column>
            <el-table-column label="Nomor Rekening" prop="account_number"> </el-table-column>
            <el-table-column label="Pemegang Rekening" prop="account_holder"> </el-table-column>

            <el-table-column fixed="right" width="70px" align="center" header-align="center">
                <template slot-scope="scope" slot="header">
                    <el-button icon="el-icon-plus" type="primary" size="small" @click="() => { formModel = {}; showForm = true;}"></el-button>
                </template>
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item icon="el-icon-edit-outline" @click.native.prevent="() => { formModel = JSON.parse(JSON.stringify(scope.row)); showForm = true; }">Edit</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-delete" @click.native.prevent="deleteData(scope.row.id)">Hapus</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog :visible.sync="showForm" :title="!!formModel.id ? 'EDIT BANK' : 'TAMBAH BANK'" width="500px">
            <el-form label-position="left" label-width="150px">
                <el-form-item label="Nama Bank" :class="formErrors.bank_name ? 'is-error' : ''">
                    <el-input placeholder="Nama Bank" v-model="formModel.bank_name"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.bank_name">{{formErrors.bank_name[0]}}</div>
                </el-form-item>

                <el-form-item label="Nama Cabang Bank" :class="formErrors.bank_branch ? 'is-error' : ''">
                    <el-input placeholder="Nama Cabang Bank" v-model="formModel.bank_branch"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.bank_branch">{{formErrors.bank_branch[0]}}</div>
                </el-form-item>

                <el-form-item label="Nomor Rekening" :class="formErrors.account_number ? 'is-error' : ''">
                    <el-input placeholder="Nomor Rekening" v-model="formModel.account_number"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.account_number">{{formErrors.account_number[0]}}</div>
                </el-form-item>

                <el-form-item label="Rekening Atas Nama" :class="formErrors.account_holder ? 'is-error' : ''">
                    <el-input placeholder="Rekening Atas Nama" v-model="formModel.account_holder"></el-input>
                    <div class="el-form-item__error" v-if="formErrors.account_holder">{{formErrors.account_holder[0]}}</div>
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
            </el-form>

            <div slot="footer">
                <el-button type="primary" @click="() => { !!formModel.id ? update() : store() }" icon="el-icon-success">SIMPAN</el-button>
                <el-button type="info" @click="() => { formModel = {}; showForm = false; }" icon="el-icon-error">TUTUP</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    props: ['company'],
    data() {
        return {
            formModel: {},
            formErrors: {},
            showForm: false,
            banks: []
        }
    },
    methods: {
        store() {
            this.loading = true;
            this.formModel.company_id = this.company
            axios.post('/companyBank', this.formModel).then(r => {
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
            axios.put('/companyBank/' + this.formModel.id, this.formModel).then(r => {
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
            this.$confirm('Anda yakin akan menghapus data ini?', 'Perhatian', { type: 'warning' }).then(() => {
                axios.delete('/companyBank/' + id).then(r => {
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
            const params = { company_id: this.company }
            axios.get('/companyBank', { params }).then(r => {
                this.banks = r.data
            }).catch(e => {
                this.$message({
                    message: 'Gagal mengambil data bank',
                    type: 'error',
                    showClose: true
                });
            })
        },
    },
    mounted() {
        this.requestData()
    }
}
</script>

<style lang="scss" scoped>

</style>
