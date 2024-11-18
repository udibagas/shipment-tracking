<template>
    <el-dialog
        fullscreen
        v-loading="loading"
        :visible.sync="show"
        :title="!!formModel.id ? 'EDIT INVOICE' : 'INVOICE BARU'"
        :before-close="closeForm"
    >
        <el-alert
            type="error"
            title="ERROR"
            :description="error.message"
            v-show="error.message"
            style="margin-bottom: 15px"
        ></el-alert>

        <el-form label-width="150px" label-position="left">
            <el-row :gutter="30">
                <el-col :span="8">
                    <el-form-item
                        label="Customer"
                        :class="formErrors.customer_id ? 'is-error' : ''"
                    >
                        <el-select
                            @change="getItems"
                            v-model="formModel.customer_id"
                            placeholder="Customer"
                            style="width: 100%"
                        >
                            <el-option
                                v-for="c in $store.state.customerList"
                                :key="c.id"
                                :value="c.id"
                                :label="c.name"
                            ></el-option>
                        </el-select>
                        <div
                            class="el-form-item__error"
                            v-if="formErrors.customer_id"
                        >
                            {{ formErrors.customer_id[0] }}
                        </div>
                    </el-form-item>
                    <el-form-item
                        label="Tanggal"
                        :class="formErrors.date ? 'is-error' : ''"
                    >
                        <el-date-picker
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            v-model="formModel.date"
                            placeholder="Tanggal"
                            style="width: 100%"
                        ></el-date-picker>
                        <div class="el-form-item__error" v-if="formErrors.date">
                            {{ formErrors.date[0] }}
                        </div>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item
                        label="Nomor Invoice"
                        :class="formErrors.number ? 'is-error' : ''"
                    >
                        <el-input
                            placeholder="Nomor Invoice"
                            v-model="formModel.number"
                        ></el-input>
                        <div
                            class="el-form-item__error"
                            v-if="formErrors.number"
                        >
                            {{ formErrors.number[0] }}
                        </div>
                    </el-form-item>
                    <el-form-item
                        label="Nomor Faktur"
                        :class="formErrors.faktur ? 'is-error' : ''"
                    >
                        <el-input
                            placeholder="Nomor Faktur"
                            v-model="formModel.faktur"
                        ></el-input>
                        <div
                            class="el-form-item__error"
                            v-if="formErrors.faktur"
                        >
                            {{ formErrors.faktur[0] }}
                        </div>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item
                        label="Layanan Pengiriman"
                        :class="formErrors.service_type ? 'is-error' : ''"
                    >
                        <el-select
                            @change="getItems"
                            v-model="formModel.service_type"
                            placeholder="Layanan Pengiriman"
                            style="width: 100%"
                        >
                            <el-option
                                v-for="(l, i) in ['REGULER', 'CHARTER']"
                                :value="l"
                                :label="l"
                                :key="i"
                            ></el-option>
                        </el-select>
                        <div
                            class="el-form-item__error"
                            v-if="formErrors.service_type"
                        >
                            {{ formErrors.service_type[0] }}
                        </div>
                    </el-form-item>
                    <el-form-item
                        label="Total"
                        :class="formErrors.total ? 'is-error' : ''"
                    >
                        <div
                            style="
                                font-size: 20px;
                                height: 40px;
                                line-height: 40px;
                                border: 1px solid #ddd;
                                border-radius: 4px;
                                background-color: yellow;
                                padding-left: 20px;
                            "
                        >
                            Rp {{ total | formatNumber }}
                        </div>
                        <div
                            class="el-form-item__error"
                            v-if="formErrors.total"
                        >
                            {{ formErrors.total[0] }}
                        </div>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-form-item
                label="Terbilang"
                :class="formErrors.total_said ? 'is-error' : ''"
            >
                <div
                    style="
                        height: 40px;
                        line-height: 40px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        background-color: yellow;
                        padding-left: 20px;
                    "
                >
                    {{ terbilang(total) }}
                </div>
                <div class="el-form-item__error" v-if="formErrors.total_said">
                    {{ formErrors.total_said[0] }}
                </div>
            </el-form-item>
        </el-form>

        <br />

        <el-table :data="formModel.items">
            <el-table-column
                label="#"
                type="index"
                width="30px"
            ></el-table-column>
            <el-table-column
                label="Tgl Kirim"
                width="90"
                header-align="center"
                align="center"
            >
                <template slot-scope="scope">{{
                    scope.row.delivery_date | readableDateShort
                }}</template>
            </el-table-column>
            <el-table-column
                label="Tgl Terima"
                width="90"
                header-align="center"
                align="center"
            >
                <template slot-scope="scope">{{
                    scope.row.delivered_date | readableDateShort
                }}</template>
            </el-table-column>
            <el-table-column
                label="Surat Pengantar"
                prop="spb_number"
                min-width="150"
            ></el-table-column>
            <!-- <el-table-column label="Asal" prop="origin" min-width="100"></el-table-column> -->
            <el-table-column
                label="Tujuan"
                prop="destination"
                min-width="100"
            ></el-table-column>
            <el-table-column label="Layanan" min-width="100">
                <template slot-scope="scope">{{
                    scope.row.service_type == "CHARTER"
                        ? "CHARTER " + scope.row.vehicle_type
                        : scope.row.service_type
                }}</template>
            </el-table-column>
            <el-table-column
                v-if="formModel.service_type == 'REGULER'"
                label="Koli"
                width="100"
                align="center"
                header-align="center"
            >
                <template slot-scope="scope">
                    {{ scope.row.total_coli | formatNumber }}
                    <!-- <el-input type="number" v-model="scope.row.total_coli" size="small"></el-input> -->
                </template>
            </el-table-column>
            <el-table-column
                v-if="formModel.service_type == 'REGULER'"
                label="KG/M3"
                width="100"
                align="right"
                header-align="right"
            >
                <template slot-scope="scope">
                    {{ scope.row.quantity | formatNumber }}
                    <!-- <el-input v-model="scope.row.quantity" size="small"></el-input> -->
                </template>
            </el-table-column>
            <el-table-column
                v-if="formModel.service_type == 'REGULER'"
                prop="unit"
                width="40"
                align="right"
                header-align="right"
            ></el-table-column>
            <el-table-column
                label="Tarif"
                width="100"
                align="right"
                header-align="right"
            >
                <template slot-scope="scope">
                    Rp. {{ scope.row.fare | formatNumber }}
                    <!-- <el-input type="number" v-model="scope.row.fare" size="small"></el-input> -->
                </template>
            </el-table-column>
            <el-table-column
                label="Biaya"
                prop="price"
                align="right"
                header-align="right"
                width="100"
            >
                <template slot-scope="scope"
                    >Rp. {{ scope.row.price | formatNumber }}</template
                >
            </el-table-column>
            <!-- <el-table-column label width="40">
        <template slot-scope="scope">
          <el-checkbox v-model="scope.row.taxable"></el-checkbox>
        </template>
      </el-table-column>-->
            <el-table-column
                label="PPN"
                align="right"
                header-align="right"
                width="100"
            >
                <template slot-scope="scope"
                    >Rp. {{ scope.row.tax | formatNumber }}</template
                >
            </el-table-column>
            <el-table-column
                label="Total"
                align="right"
                header-align="right"
                width="100"
            >
                <template slot-scope="scope"
                    >Rp. {{ scope.row.total | formatNumber }}</template
                >
            </el-table-column>
        </el-table>

        <span slot="footer" class="dialog-footer">
            <el-button type="primary" plain @click="save(0)" icon="el-icon-edit"
                >DRAFT</el-button
            >
            <el-button type="primary" @click="save(1)" icon="el-icon-success"
                >SIMPAN</el-button
            >
            <el-button type="info" @click="closeForm" icon="el-icon-error"
                >BATAL</el-button
            >
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["show", "data"],
    computed: {
        formModel() {
            return this.data;
        },
        total() {
            return this.formModel.items.reduce(
                (t, c) => t + c.price + c.tax,
                0
            );
        },
    },
    data() {
        return {
            loading: false,
            formErrors: {},
            error: {},
        };
    },
    methods: {
        closeForm() {
            this.error = {};
            this.formErrors = {};
            this.$emit("close-form");
        },
        save(status) {
            if (this.formModel.items.length == 0) {
                this.$message({
                    message:
                        "Tidak ada pengiriman yang bisa ditagih untuk customer terkait",
                    type: "error",
                    showClose: true,
                });
                return;
            }

            this.formModel.status = status;
            this.formModel.total = this.total;
            this.formModel.total_said = this.terbilang(this.total);

            if (!!status) {
                this.$confirm("Anda yakin?", "Konfirmasi", { type: "warning" })
                    .then(() => {
                        if (!!this.formModel.id) {
                            this.update();
                        } else {
                            this.store();
                        }
                    })
                    .catch((e) => {
                        console.log(e);
                    });
            } else {
                if (!!this.formModel.id) {
                    this.update();
                } else {
                    this.store();
                }
            }
        },
        store() {
            this.loading = true;
            axios
                .post("/invoice", this.formModel)
                .then((r) => {
                    this.$message({
                        message: "Data berhasil disimpan.",
                        type: "success",
                        showClose: true,
                    });
                    this.$emit("close-form");
                    this.$emit("refresh-data");
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
        update() {
            this.loading = true;
            axios
                .put("/invoice/" + this.formModel.id, this.formModel)
                .then((r) => {
                    this.$message({
                        message: "Data berhasil disimpan.",
                        type: "success",
                        showClose: true,
                    });
                    this.$emit("close-form");
                    this.$emit("refresh-data");
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
        terbilang(v) {
            let bilangan =
                "nol,satu,dua,tiga,empat,lima,enam,tujuh,delapan,sembilan".split(
                    ","
                );
            let level_bilangan = ",ribu,juta,milyar,triliun".split(",");
            let input = Math.round(v) + "";
            let tmp = [];

            // pisahin angka 3 digit
            let level = 0;
            while (true) {
                tmp.push({ level: level, angka: input.slice(-3) });
                input = input.slice(0, -3);
                level++;
                if (input == "") {
                    break;
                }
            }

            tmp.reverse();
            let said = "";

            tmp.forEach((i) => {
                if (i.angka.length < 3) {
                    i.angka =
                        i.angka.length == 2 ? "0" + i.angka : "00" + i.angka;
                }

                let array_angka = i.angka.split("").map((i) => Number(i));

                if (array_angka[0] == "1") {
                    said += "seratus ";
                } else {
                    said +=
                        array_angka[0] > 0
                            ? bilangan[array_angka[0]] + " ratus "
                            : "";
                }

                if (array_angka[1] == "1") {
                    if (array_angka[2] == "0") {
                        said += "sepuluh ";
                    } else if (array_angka[2] == "1") {
                        said += "sebelas ";
                    } else {
                        said += bilangan[array_angka[2]] + " belas ";
                    }
                } else {
                    said +=
                        array_angka[1] > 0
                            ? bilangan[array_angka[1]] + " puluh "
                            : "";
                    said +=
                        array_angka[2] > 0
                            ? bilangan[array_angka[2]] + " "
                            : "";
                }

                said += level_bilangan[i.level] + " ";
            });

            return said.replace("satu ribu", "seribu").toUpperCase() + "RUPIAH";
        },
        getItems() {
            if (!this.formModel.customer_id || !this.formModel.service_type) {
                return;
            }

            let params = {
                delivery_status_id: 4, // stt_received
                invoice_status: 0,
                customer_id: this.formModel.customer_id,
                company_id: this.$store.state.auth.user.company_id,
                service_type: this.formModel.service_type,
            };

            this.formModel.items = [];

            axios
                .get("/domesticDelivery/search", { params: params })
                .then((r) => {
                    if (r.data.length == 0) {
                        this.$message({
                            message:
                                "Tidak ada pengiriman yang bisa ditagih untuk customer terkait",
                            type: "error",
                            showClose: true,
                        });
                        return;
                    }

                    r.data.forEach((data) => {
                        // Biaya kirim
                        let d = JSON.parse(JSON.stringify(data));
                        d.description = {
                            delivery_id: d.id,
                            delivery_date: d.delivery_date,
                            delivered_date: d.delivered_date,
                            service_type: d.service_type,
                            origin: d.origin,
                            destination: d.destination,
                            vehicle_type: d.vehicle_type
                                ? d.vehicle_type.name
                                : "",
                            spb_number: d.spb_number,
                            total_coli: d.quantity,
                        };

                        d.quantity = d.invoice_weight;
                        d.unit = "KG";
                        d.fare = d.delivery_rate;
                        d.price = d.delivery_cost;
                        d.tax = d.delivery_cost_ppn * 0.1 * d.price;
                        d.total = d.price + d.tax;
                        d.vehicle_type = d.vehicle_type
                            ? d.vehicle_type.name
                            : "";
                        d.total_coli = d.description.total_coli;
                        this.formModel.items.push(d);

                        // biaya packing
                        if (d.packing_cost > 0) {
                            let d = JSON.parse(JSON.stringify(data));
                            d.description = {
                                delivery_id: d.id,
                                delivery_date: d.delivery_date,
                                delivered_date: d.delivered_date,
                                service_type: "PACKING PETI",
                                origin: d.origin,
                                destination: d.destination,
                                vehicle_type: d.vehicle_type
                                    ? d.vehicle_type.name
                                    : "",
                                spb_number: d.spb_number,
                                total_coli: d.items.filter((i) => i.packing)
                                    .length,
                            };

                            d.service_type = "PACKING PETI";
                            d.quantity = d.packing_volume;
                            d.unit = "M3";
                            d.fare = d.packing_rate;
                            d.price = d.packing_cost;
                            d.tax = d.packing_cost_ppn * 0.1 * d.price;
                            d.total = d.price + d.tax;
                            d.total_coli = d.description.total_coli;
                            this.formModel.items.push(d);
                        }

                        // biaya penerus
                        if (d.forwarder_cost > 0) {
                            let d = JSON.parse(JSON.stringify(data));
                            d.description = {
                                delivery_id: d.id,
                                delivery_date: d.delivery_date,
                                delivered_date: d.delivered_date,
                                service_type: "BIAYA PENERUS",
                                origin: d.origin,
                                destination: d.destination,
                                vehicle_type: d.vehicle_type
                                    ? d.vehicle_type.name
                                    : "",
                                spb_number: d.spb_number,
                                total_coli: 0,
                            };

                            d.service_type = "BIAYA PENERUS";
                            d.quantity = 1;
                            d.unit = "";
                            d.fare = 0;
                            d.price = d.forwarder_cost;
                            d.tax = d.forwarder_cost_ppn * 0.1 * d.price;
                            d.total = d.price + d.tax;
                            d.total_coli = d.description.total_coli;
                            this.formModel.items.push(d);
                        }

                        // biaya lain - lain
                        if (d.additional_cost > 0) {
                            let d = JSON.parse(JSON.stringify(data));
                            d.description = {
                                delivery_id: d.id,
                                delivery_date: d.delivery_date,
                                delivered_date: d.delivered_date,
                                service_type:
                                    "BIAYA LAIN - LAIN : " +
                                    d.additional_cost_description,
                                origin: d.origin,
                                destination: d.destination,
                                vehicle_type: d.vehicle_type
                                    ? d.vehicle_type.name
                                    : "",
                                spb_number: d.spb_number,
                                total_coli: 0,
                            };

                            d.service_type =
                                "BIAYA LAIN - LAIN : " +
                                d.additional_cost_description;
                            d.quantity = 1;
                            d.unit = "";
                            d.fare = 0;
                            d.price = d.additional_cost;
                            d.tax = d.additional_cost_ppn * 0.1 * d.price;
                            d.total = d.price + d.tax;
                            d.total_coli = d.description.total_coli;
                            this.formModel.items.push(d);
                        }
                    });
                })
                .catch((e) => {
                    console.log(e);
                    if (
                        e.response &&
                        (e.response.status == 404 || e.response.status == 500)
                    ) {
                        this.$message({
                            message: e.response.data.message,
                            type: "error",
                            showClose: true,
                        });
                    }
                });
        },
    },
};
</script>

<style lang="scss" scoped></style>
