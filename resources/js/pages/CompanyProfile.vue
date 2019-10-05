<template>
    <el-dialog
    width="680px"
    v-loading="loading" :visible="show" :show-close="false"
    title="EDIT PROFIL PERUSAHAAN"
    :close-on-click-modal="false">

        <el-alert type="error" title="ERROR"
            :description="error.message"
            v-show="error.message"
            style="margin-bottom:15px;">
        </el-alert>

        <el-form label-width="160px" label-position="left">
            <el-tabs type="card">
                <el-tab-pane label="Basic Information">
                    <el-form-item label="Code" :class="formErrors.code ? 'is-error' : ''">
                        <el-input placeholder="Code" v-model="formModel.code"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.code">{{formErrors.code[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Name" :class="formErrors.name ? 'is-error' : ''">
                        <el-input placeholder="Name" v-model="formModel.name"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.name">{{formErrors.name[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Logo">
                        <el-upload
                        action="company/uploadLogo"
                        :headers="{'Authorization': 'bearer ' + $store.state.token, 'Accept': 'application/json, plain/text, */*'}"
                        :limit="1"
                        :on-success="handleSuccess"
                        :on-error="handleError"
                        :on-remove="handleRemove">
                            <el-button size="small" type="primary">Click to upload</el-button>
                            <!-- <div slot="tip" class="el-upload__tip">jpg/png file only</div> -->
                        </el-upload>
                    </el-form-item>

                    <el-form-item label="Status" :class="formErrors.active ? 'is-error' : ''">
                        <el-switch
                        disabled
                        :active-value="1"
                        :inactive-value="0"
                        v-model="formModel.active"
                        active-color="#13ce66">
                        </el-switch>
                        <el-tag :type="formModel.active ? 'success' : 'info'" size="small" style="margin-left:10px">{{!!formModel.active ? 'Active' : 'Inactive'}}</el-tag>

                        <div class="el-form-item__error" v-if="formErrors.active">{{formErrors.active[0]}}</div>
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="Address">
                    <el-form-item label="Address" :class="formErrors.address ? 'is-error' : ''">
                        <el-input type="textarea" rows="3" placeholder="Address" v-model="formModel.address"></el-input>
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
                <el-tab-pane label="Bank Information">
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
            <el-button type="primary" @click="update" icon="el-icon-success">SIMPAN</el-button>
            <el-button type="info" @click="$emit('close')" icon="el-icon-error">TUTUP</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ['show'],
    data() {
        return {
            formModel: {},
            formErrors: {},
            error: {},
            loading: false
        }
    },
    methods: {
        getData() {
            axios.get('/company/' + this.$store.state.user.company_id).then(r => {
                this.formModel = r.data
            }).catch(e => {
                this.$message({
                    message: 'Gagal mengambil data.',
                    type: 'error',
                    showClose: true
                });
            })
        },
        handleSuccess() {

        },
        handleError() {

        },
        handleRemove() {

        },
        update() {
            this.loading = true;
            axios.put('/company/' + this.formModel.id, this.formModel).then(r => {
                this.formModel = r.data
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
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
        }
    },
    mounted() {
        this.getData()
    }
}
</script>

<style lang="scss" scoped>

</style>
