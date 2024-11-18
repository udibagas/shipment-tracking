<template>
    <div>
        <el-page-header @back="$emit('back')" content="PROFIL PERUSAHAAN">
        </el-page-header>
        <br />
        <el-alert
            type="error"
            title="ERROR"
            :description="error.message"
            v-show="error.message"
            style="margin-bottom: 15px"
        >
        </el-alert>

        <el-form label-width="160px" label-position="left">
            <el-tabs type="card">
                <el-tab-pane label="Informasi Perusahaan">
                    <el-card>
                        <el-form-item
                            label="Code"
                            :class="formErrors.code ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Code"
                                v-model="formModel.code"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.code"
                            >
                                {{ formErrors.code[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Nama Perusahaan"
                            :class="formErrors.name ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Nama Perusahaan"
                                v-model="formModel.name"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.name"
                            >
                                {{ formErrors.name[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Nama Direktur"
                            :class="formErrors.director_name ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Nama Direktur"
                                v-model="formModel.director_name"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.director_name"
                            >
                                {{ formErrors.director_name[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Status"
                            :class="formErrors.active ? 'is-error' : ''"
                        >
                            <el-switch
                                disabled
                                :active-value="1"
                                :inactive-value="0"
                                v-model="formModel.active"
                                active-color="#13ce66"
                            >
                            </el-switch>
                            <el-tag
                                :type="formModel.active ? 'success' : 'info'"
                                size="small"
                                style="margin-left: 10px"
                                >{{
                                    !!formModel.active ? "Active" : "Inactive"
                                }}</el-tag
                            >

                            <div
                                class="el-form-item__error"
                                v-if="formErrors.active"
                            >
                                {{ formErrors.active[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Logo"
                            :class="formErrors.logo ? 'is-error' : ''"
                        >
                            <el-upload
                                ref="upload"
                                class="avatar-uploader"
                                :action="baseUrl + '/company/uploadLogo'"
                                :headers="{
                                    Accept: 'application/json, plain/text, */*',
                                }"
                                :show-file-list="false"
                                :on-error="handleUploadImageError"
                                :on-success="handleUploadImageSuccess"
                            >
                                <img
                                    v-if="imageUrl"
                                    :src="imageUrl"
                                    class="avatar"
                                />
                                <i
                                    v-else
                                    class="el-icon-plus avatar-uploader-icon"
                                ></i>
                            </el-upload>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.logo"
                            >
                                {{ formErrors.logo[0] }}
                            </div>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
                <el-tab-pane label="Alamat">
                    <el-card>
                        <el-form-item
                            label="Alamat"
                            :class="formErrors.address ? 'is-error' : ''"
                        >
                            <el-input
                                type="textarea"
                                rows="3"
                                placeholder="Alamat"
                                v-model="formModel.address"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.address"
                            >
                                {{ formErrors.address[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="No Telp."
                            :class="formErrors.phone ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="No Telp."
                                v-model="formModel.phone"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.phone"
                            >
                                {{ formErrors.phone[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Fax"
                            :class="formErrors.fax ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Fax"
                                v-model="formModel.fax"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.fax"
                            >
                                {{ formErrors.fax[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Email"
                            :class="formErrors.email ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Email"
                                v-model="formModel.email"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.email"
                            >
                                {{ formErrors.email[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Website"
                            :class="formErrors.website ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Website"
                                v-model="formModel.website"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.website"
                            >
                                {{ formErrors.website[0] }}
                            </div>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
                <el-tab-pane label="Informasi Bank">
                    <el-card>
                        <CompanyBank
                            :company="$store.state.auth.user.company_id"
                        />
                    </el-card>
                </el-tab-pane>
                <el-tab-pane label="Contact Person">
                    <el-card>
                        <el-form-item
                            label="Nama Contact Person"
                            :class="formErrors.contact_person ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="Nama Contact Person"
                                v-model="formModel.contact_person"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.contact_person"
                            >
                                {{ formErrors.contact_person[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="Email Contact Person"
                            :class="
                                formErrors.contact_person_email
                                    ? 'is-error'
                                    : ''
                            "
                        >
                            <el-input
                                placeholder="Email Contact Person"
                                v-model="formModel.contact_person_email"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.contact_person_email"
                            >
                                {{ formErrors.contact_person_email[0] }}
                            </div>
                        </el-form-item>

                        <el-form-item
                            label="No. HP Contact Person"
                            :class="
                                formErrors.contact_person_phone
                                    ? 'is-error'
                                    : ''
                            "
                        >
                            <el-input
                                placeholder="No. HP Contact Person"
                                v-model="formModel.contact_person_phone"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.contact_person_phone"
                            >
                                {{ formErrors.contact_person_phone[0] }}
                            </div>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
                <el-tab-pane label="Email Setting">
                    <el-card>
                        <el-form-item
                            label="SMTP Host"
                            :class="formErrors.smtp_host ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="SMTP Host"
                                v-model="formModel.smtp_host"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.smtp_host"
                            >
                                {{ formErrors.smtp_host[0] }}
                            </div>
                        </el-form-item>
                        <el-form-item
                            label="SMTP Port"
                            :class="formErrors.smtp_port ? 'is-error' : ''"
                        >
                            <el-input
                                type="number"
                                placeholder="SMTP Port"
                                v-model="formModel.smtp_port"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.smtp_port"
                            >
                                {{ formErrors.smtp_port[0] }}
                            </div>
                        </el-form-item>
                        <el-form-item
                            label="SMTP Encryption"
                            :class="
                                formErrors.smtp_encryption ? 'is-error' : ''
                            "
                        >
                            <el-select
                                clearable
                                v-model="formModel.smtp_encryption"
                                placeholder="SMTP Encryption"
                                style="width: 100%"
                            >
                                <el-option
                                    v-for="(e, i) in ['ssl', 'tls']"
                                    :key="i"
                                    :label="e"
                                    :value="e"
                                ></el-option>
                            </el-select>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.smtp_encryption"
                            >
                                {{ formErrors.smtp_encryption[0] }}
                            </div>
                        </el-form-item>
                        <el-form-item
                            label="SMTP Username"
                            :class="formErrors.smtp_username ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="SMTP Username"
                                v-model="formModel.smtp_username"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.smtp_username"
                            >
                                {{ formErrors.smtp_username[0] }}
                            </div>
                        </el-form-item>
                        <el-form-item
                            label="SMTP Password"
                            :class="formErrors.smtp_password ? 'is-error' : ''"
                        >
                            <el-input
                                placeholder="SMTP Password"
                                v-model="formModel.smtp_password"
                            ></el-input>
                            <div
                                class="el-form-item__error"
                                v-if="formErrors.smtp_password"
                            >
                                {{ formErrors.smtp_password[0] }}
                            </div>
                        </el-form-item>
                    </el-card>
                </el-tab-pane>
            </el-tabs>
        </el-form>

        <br />

        <el-button type="primary" @click="update" icon="el-icon-success"
            >SIMPAN</el-button
        >
    </div>
</template>

<script>
import CompanyBank from "../components/CompanyBank";

export default {
    components: { CompanyBank },
    data() {
        return {
            baseUrl: BASE_URL,
            formModel: {},
            formErrors: {},
            error: {},
            loading: false,
            imageUrl: "",
        };
    },
    methods: {
        getData() {
            axios
                .get("/company/" + this.$store.state.auth.user.company_id)
                .then((r) => {
                    this.formModel = r.data;
                    this.imageUrl = r.data.logo;
                    this.$forceUpdate();
                })
                .catch((e) => {
                    this.$message({
                        message: "Gagal mengambil data.",
                        type: "error",
                        showClose: true,
                    });
                });
        },
        handleUploadImageSuccess(res, file, fileList) {
            this.imageUrl = URL.createObjectURL(file.raw);
            this.formModel.logo = res.path;
            this.$forceUpdate();
        },
        handleUploadImageError(err, file, fileList) {
            this.formErrors.logo = [JSON.parse(err.message).message];
            this.$forceUpdate();
            console.log(err);
        },
        update() {
            this.loading = true;
            axios
                .put("/company/" + this.formModel.id, this.formModel)
                .then((r) => {
                    this.formModel = r.data;
                    this.$store.commit("getCompanyByUser");
                    this.$message({
                        message: "Data berhasil disimpan.",
                        type: "success",
                        showClose: true,
                    });
                })
                .catch((e) => {
                    if (e.response.status == 422) {
                        this.error = {};
                        this.formErrors = e.response.data.errors;
                    }

                    if (e.response.status == 500) {
                        this.formErrors = {};
                        this.error = e.response.data;
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
    mounted() {
        this.getData();
    },
};
</script>

<style lang="scss" scoped>
.avatar-uploader {
    border: 1px dashed #d9d9d9;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 150px;
    height: 150px;
}

.avatar-uploader:hover {
    border-color: #409eff;
}

.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 150px;
    height: 150px;
    line-height: 150px;
    text-align: center;
}

.avatar {
    width: 150px;
    height: 150px;
    display: block;
}

img.thumbnail {
    height: 50px;
    border: 1px solid #ddd;
}
</style>
