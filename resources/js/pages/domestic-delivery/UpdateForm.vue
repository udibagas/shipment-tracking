<template>
    <el-dialog
    center
    top="60px"
    width="500px"
    title="UPDATE STATUS"
    :visible.sync="visible"
    :show-close="false"
    :destroy-on-close="true"
    :close-on-click-modal="false"
    :close-on-press-escape="false">
        <el-form label-width="150px" label-position="left">
            <el-form-item label="Status" :class="formErrors.status ? 'is-error' : ''">
                <el-select v-model="formModel.status" placeholder="Status" filterable default-first-option style="width:100%">
                    <el-option
                    v-for="(t, i) in $store.state.deliveryStatusList.filter(s => s.id >= data.delivery_status_id)"
                    :value="t.id" :label="t.name" :key="i">
                    </el-option>
                </el-select>
                <div class="el-form-item__error" v-if="formErrors.status">{{formErrors.status[0]}}</div>
            </el-form-item>

            <!-- status = Ready For Delivery -->
            <el-form-item v-show="formModel.status == 1" label="ETD" :class="formErrors.etd ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="ETD"
                v-model="formModel.etd">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.etd">{{formErrors.etd[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <!-- <el-form-item v-show="formModel.status == 1" label="Tracking Number" :class="formErrors.tracking_number ? 'is-error' : ''">
                <el-input placeholder="Tracking Number" v-model="formModel.tracking_number"></el-input>
                <div class="el-form-item__error" v-if="formErrors.tracking_number">{{formErrors.tracking_number[0]}}</div>
            </el-form-item> -->

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 1" label="Agent" :class="formErrors.agent_id ? 'is-error' : ''">
                <el-select v-model="formModel.agent_id" placeholder="Agent" filterable default-first-option style="width:100%">
                    <el-option v-for="(t, i) in $store.state.agentList"
                    :value="t.id"
                    :label="t.code + ' - ' + t.name"
                    :key="i">
                    </el-option>
                </el-select>
                <div class="el-form-item__error" v-if="formErrors.agent_id">{{formErrors.agent_id[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="Tanggal Pengiriman" :class="formErrors.delivery_date ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="Tanggal Pengiriman"
                v-model="formModel.delivery_date">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.delivery_date">{{formErrors.delivery_date[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="ETA" :class="formErrors.eta ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="ETA"
                v-model="formModel.eta">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.eta">{{formErrors.eta[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="Nama Kapal" :class="formErrors.ship_name ? 'is-error' : ''">
                <el-input placeholder="Nama Kapal" v-model="formModel.ship_name"></el-input>
                <div class="el-form-item__error" v-if="formErrors.ship_name">{{formErrors.ship_name[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="Plat Nomor Armada" :class="formErrors.vehicle_number ? 'is-error' : ''">
                <el-input placeholder="Plat Nomor Armada" v-model="formModel.vehicle_number"></el-input>
                <div class="el-form-item__error" v-if="formErrors.vehicle_number">{{formErrors.vehicle_number[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="Nama Driver" :class="formErrors.driver_name ? 'is-error' : ''">
                <el-input placeholder="Nama Driver" v-model="formModel.driver_name"></el-input>
                <div class="el-form-item__error" v-if="formErrors.driver_name">{{formErrors.driver_name[0]}}</div>
            </el-form-item>

            <!-- status = On Delivery -->
            <el-form-item v-show="formModel.status == 2" label="No. HP Driver" :class="formErrors.driver_phone ? 'is-error' : ''">
                <el-input placeholder="No. HP Driver" v-model="formModel.driver_phone"></el-input>
                <div class="el-form-item__error" v-if="formErrors.driver_phone">{{formErrors.driver_phone[0]}}</div>
            </el-form-item>

            <!-- status = Delivered -->
            <el-form-item v-show="formModel.status == 3" label="Tangal Terima" :class="formErrors.delivered_date ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="Tangal Terima"
                v-model="formModel.delivered_date">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.delivered_date">{{formErrors.delivered_date[0]}}</div>
            </el-form-item>

            <!-- status = Received -->
            <el-form-item v-show="formModel.status == 4" label="Received Date" :class="formErrors.received_date ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="Received Date"
                v-model="formModel.received_date">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.received_date">{{formErrors.received_date[0]}}</div>
            </el-form-item>

            <!-- status = Received -->
            <el-form-item v-show="formModel.status == 3 || formModel.status == 4" label="Receiver" :class="formErrors.receiver ? 'is-error' : ''">
                <el-input placeholder="Receiver" v-model="formModel.receiver"></el-input>
                <div class="el-form-item__error" v-if="formErrors.receiver">{{formErrors.receiver[0]}}</div>
            </el-form-item>

            <!-- status = Invoice Sent -->
            <el-form-item v-show="formModel.status == 5" label="Invoice Date" :class="formErrors.invoice_date ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="Invoice Date"
                v-model="formModel.invoice_date">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.invoice_date">{{formErrors.invoice_date[0]}}</div>
            </el-form-item>

            <!-- status = Invoice Paid -->
            <el-form-item v-show="formModel.status == 6" label="Payment Date" :class="formErrors.payment_date ? 'is-error' : ''">
                <el-date-picker
                style="width:100%"
                type="date"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                placeholder="Payment Date"
                v-model="formModel.payment_date">
                </el-date-picker>
                <div class="el-form-item__error" v-if="formErrors.payment_date">{{formErrors.payment_date[0]}}</div>
            </el-form-item>

            <el-form-item label="Note" :class="formErrors.note ? 'is-error' : ''">
                <el-input type="textarea" rows="4" placeholder="Note" v-model="formModel.note"></el-input>
                <div class="el-form-item__error" v-if="formErrors.note">{{formErrors.note[0]}}</div>
            </el-form-item>
        </el-form>
        <div slot="footer">
            <el-button type="primary" @click="submit" icon="el-icon-success">SAVE</el-button>
            <el-button type="info" @click="$emit('close')" icon="el-icon-error">CANCEL</el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ['data', 'visible'],
    data() {
        return {
            formModel: {},
            formErrors: {}
        }
    },
    methods: {
        submit() {
            this.$confirm('Anda yakin?', 'Warning', { type: 'warning'}).then(() => {
                this.formModel.delivery_id = this.data.id
                axios.post('deliveryProgress', this.formModel).then(r => {
                    this.$message({
                        message: 'Data berhasil disimpan',
                        type: 'success',
                        showClose: true
                    });
                    this.formModel = {}
                    this.$emit('close')
                    this.$emit('submitted')
                }).catch(e => {
                    if (e.response.status == 422) {
                        this.formErrors = e.response.data.errors;
                    } else {
                        this.$message({
                            message: e.response.data.message,
                            type: 'error',
                            showClose: true
                        });
                    }
                })
            }).catch(e => console.log(e))
        }
    }

}
</script>

<style lang="scss" scoped>

</style>
