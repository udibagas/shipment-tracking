<template>
  <el-dialog
    fullscreen
    :before-close="closeForm"
    :visible.sync="visible"
    title="KIRIM REPORT"
    :show-close="false"
    v-loading="loading"
  >
    <el-form label-position="left" label-width="150px">
      <el-row :gutter="30">
        <el-col :span="12">
          <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
            <el-select
              @change="getItems"
              filterable
              default-first-option
              clearable
              v-model="formModel.customer_id"
              placeholder="Customer"
              style="width:100%"
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
            >{{formErrors.customer_id[0]}}</div>
          </el-form-item>
          <el-form-item label="Tanggal" :class="formErrors.dateRange ? 'is-error' : ''">
            <el-date-picker
              @change="getItems"
              type="daterange"
              format="dd-MMM-yyyy"
              value-format="yyyy-MM-dd"
              v-model="formModel.dateRange"
              start-placeholder="Dari Tanggal"
              end-placeholder="Sampai Tanggal"
              style="width:100%"
            ></el-date-picker>
            <div class="el-form-item__error" v-if="formErrors.dateRange">{{formErrors.dateRange[0]}}</div>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item label="Subyek Pesan" :class="formErrors.subject ? 'is-error' : ''">
            <el-input v-model="formModel.subject" placeholder="Subyek Pesan"></el-input>
            <div class="el-form-item__error" v-if="formErrors.subject">{{formErrors.subject[0]}}</div>
          </el-form-item>
          <el-form-item label="Penerima Email" :class="formErrors.email ? 'is-error' : ''">
            <el-input v-model="formModel.email" placeholder="Penerima Email"></el-input>
            <div class="el-form-item__error" v-if="formErrors.email">{{formErrors.email[0]}}</div>
          </el-form-item>
        </el-col>
      </el-row>

      <!-- <el-form-item label="Isi Pesan" :class="formErrors.body ? 'is-error' : ''">
                <el-input v-model="formModel.body" type="textarea" rows="10" placeholder="Isi Pesan"></el-input>
                <div class="el-form-item__error" v-if="formErrors.body">{{formErrors.body[0]}}</div>
      </el-form-item>-->
    </el-form>

    <el-table :data="formModel.items">
      <el-table-column prop="spb_number" label="Nomor SPB" show-overflow-tooltip min-width="100"></el-table-column>
      <el-table-column prop="resi_number" label="Nomor Resi" show-overflow-tooltip min-width="100"></el-table-column>
      <el-table-column prop="destination" label="Tujuan" show-overflow-tooltip min-width="100"></el-table-column>
      <el-table-column prop="delivery_address" label="Alamat" show-overflow-tooltip min-width="100"></el-table-column>
      <el-table-column prop="service_type" label="Layanan" show-overflow-tooltip min-width="100"></el-table-column>
      <el-table-column
        prop="vehicle_type.name"
        label="Armada"
        show-overflow-tooltip
        min-width="100"
      ></el-table-column>
      <el-table-column
        prop="quantity"
        label="Jml Koli"
        min-width="100"
        align="center"
        header-align="center"
      ></el-table-column>
      <el-table-column
        prop="invoice_weight"
        label="Berat"
        min-width="100"
        align="right"
        header-align="right"
      >
        <template slot-scope="scope">{{scope.row.invoice_weight | formatNumber}} KG</template>
      </el-table-column>
      <el-table-column prop="pick_up_date" label="Tgl Pick Up" min-width="100">
        <template slot-scope="scope">{{scope.row.pick_up_date | readableDate}}</template>
      </el-table-column>
      <el-table-column prop="etd" label="ETD" min-width="100">
        <template slot-scope="scope">{{scope.row.etd | readableDate}}</template>
      </el-table-column>
      <el-table-column prop="delivery_date" label="Tgl Kirim" min-width="100">
        <template slot-scope="scope">{{scope.row.delivery_date | readableDate}}</template>
      </el-table-column>
      <el-table-column prop="eta" label="ETA" min-width="100">
        <template slot-scope="scope">{{scope.row.eta | readableDate}}</template>
      </el-table-column>
      <el-table-column prop="delivered_date" label="Tgl Terima" min-width="100">
        <template slot-scope="scope">{{scope.row.delivered_date | readableDate}}</template>
      </el-table-column>
      <el-table-column prop="statusName" label="Status" min-width="100"></el-table-column>
      <el-table-column prop="updated_at" label="Update" show-overflow-tooltip min-width="180">
        <template slot-scope="scope">{{scope.row.etd | readableDateTime}}</template>
      </el-table-column>
    </el-table>

    <span slot="footer">
      <el-button type="primary" @click="send" icon="el-icon-success">KIRIM</el-button>
      <el-button type="info" @click="$emit('close')" icon="el-icon-error">BATAL</el-button>
    </span>
  </el-dialog>
</template>

<script>
export default {
  props: ["visible"],
  watch: {
    "formModel.customer_id"(v, o) {
      if (!!v) {
        this.formModel.email = this.$store.state.customerList.find(
          c => c.id == v
        ).email;
      }
    }
  },
  data() {
    return {
      formModel: { email: "", items: [] },
      formErrors: {},
      loading: false
    };
  },
  methods: {
    closeForm() {
      this.$emit("close");
    },
    send() {
      this.$confirm("Anda yakin?", "Konfirmasi", { type: "warning" })
        .then(() => {
          if (this.formModel.items.length == 0) {
            this.$message({
              message:
                "Tidak ada data untuk customer dan tanggal yang dipilih.",
              type: "error",
              showClose: true,
              duration: 10000
            });
            return;
          }

          this.loading = true;
          axios
            .post("/report/send", this.formModel)
            .then(r => {
              this.formModel = { email: "", items: [] };
              this.$message({
                message: r.data.message,
                type: "success",
                showClose: true
              });
              this.$emit("close");
              this.$emit("submitted");
            })
            .catch(e => {
              if (e.response.status == 500) {
                this.$message({
                  message: e.response.data.message,
                  type: "error",
                  showClose: true,
                  duration: 10000
                });
              }

              if (e.response.status == 422) {
                this.formErrors = e.response.data.errors;
              }
            })
            .finally(() => {
              this.loading = false;
            });
        })
        .catch(e => console.log(e));
    },
    getItems() {
      if (!this.formModel.dateRange || !this.formModel.customer_id) {
        return;
      }

      let params = {
        customer_id: this.formModel.customer_id,
        dateRange: this.formModel.dateRange,
        subject: this.formModel.subject,
        email: this.formModel.email,
        pageSize: 1000000
      };

      axios
        .get("/domesticDelivery", { params: params })
        .then(r => {
          if (r.data.data.length == 0) {
            this.$message({
              message:
                "Tidak ada data untuk customer dan tanggal yang dipilih.",
              type: "error",
              showClose: true,
              duration: 10000
            });
            this.formModel.items = [];
            return;
          }

          this.formModel.items = r.data.data;
        })
        .catch(e => {
          this.$message({
            message: e.response.data.message,
            type: "error",
            showClose: true,
            duration: 10000
          });
        });
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
