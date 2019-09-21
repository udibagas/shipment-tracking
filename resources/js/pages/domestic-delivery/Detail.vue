<template>
    <div>
        <el-row :gutter="15">
            <el-col :span="8">
                <table style="width:100%">
                    <tbody>
                        <tr><td class="td-label">Customer</td><td class="td-value">{{data.customer}}</td></tr>
                        <tr><td class="td-label">Charge To</td><td class="td-value">{{data.charge_to}}</td></tr>
                        <tr><td class="td-label">Origin</td><td class="td-value">{{data.origin}}</td></tr>
                        <tr><td class="td-label">Destination</td><td class="td-value">{{data.destination}}</td></tr>
                        <tr><td class="td-label">Delivery Address</td><td class="td-value">{{data.delivery_address}}</td></tr>
                        <tr><td class="td-label">P/U Date</td><td class="td-value">{{data.pick_up_date}}</td></tr>
                        <tr><td class="td-label">Receipt Number</td><td class="td-value">{{data.resi_number}}</td></tr>
                        <tr><td class="td-label">SPB Number</td><td class="td-value">{{data.spb_number}}</td></tr>
                        <tr><td class="td-label">Tracking Number</td><td class="td-value">{{data.tracking_number}}</td></tr>
                        <tr><td class="td-label">Delivery Type</td><td class="td-value">{{data.delivery_type}}</td></tr>
                        <tr><td class="td-label">Delivery Service</td><td class="td-value">{{data.service_type}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="8">
                <table style="width:100%">
                    <tbody>
                        <tr><td class="td-label">Volume</td><td class="td-value">{{data.volume}}</td></tr>
                        <tr><td class="td-label">Quantity</td><td class="td-value">{{data.quantity}}</td></tr>
                        <tr><td class="td-label">Dimension</td><td class="td-value">{{data.dimension}}</td></tr>
                        <tr><td class="td-label">Agent</td><td class="td-value">{{data.agent}}</td></tr>
                        <tr><td class="td-label">Ship Name</td><td class="td-value">{{data.ship_name}}</td></tr>
                        <tr><td class="td-label">Vehicle Number</td><td class="td-value">{{data.vehicle_number}}</td></tr>
                        <tr><td class="td-label">Driver Name</td><td class="td-value">{{data.driver_name}}</td></tr>
                        <tr><td class="td-label">Driver Phone</td><td class="td-value">{{data.driver_phone}}</td></tr>
                    </tbody>
                </table>
            </el-col>
            <el-col :span="8">
                <table style="width:100%">
                    <tbody>
                        <tr><td class="td-label">ETD</td><td class="td-value">{{data.etd}}</td></tr>
                        <tr><td class="td-label">Delivery Date</td><td class="td-value">{{data.delivery_date}}</td></tr>
                        <tr><td class="td-label">ETA</td><td class="td-value">{{data.eta}}</td></tr>
                        <tr><td class="td-label">Arrival Date</td><td class="td-value">{{data.delivered_date}}</td></tr>
                        <tr><td class="td-label">Received Date</td><td class="td-value">{{data.received_date}}</td></tr>
                        <tr><td class="td-label">Invoice Date</td><td class="td-value">{{data.invoice_date}}</td></tr>
                        <tr><td class="td-label">Payment Date</td><td class="td-value">{{data.payment_date}}</td></tr>
                        <tr><td class="td-label">Status</td><td class="td-value">{{data.statusName}}</td></tr>
                        <tr><td class="td-label">Note</td><td class="td-value">{{data.status_note}}</td></tr>
                    </tbody>
                </table>
            </el-col>
        </el-row>

        <el-table striped :data="data.items" style="margin-top:10px" height="calc(100vh - 561px)">
            <el-table-column type="index"></el-table-column>
            <el-table-column label="Description" prop="description"> </el-table-column>
            <el-table-column label="Coli" prop="coli"> </el-table-column>
            <el-table-column label="Weight" prop="weight"> </el-table-column>
            <el-table-column label="Item" prop="item"> </el-table-column>
            <el-table-column label="Reference" prop="reference"> </el-table-column>
            <el-table-column label="Remark" prop="remark"> </el-table-column>
        </el-table>
    </div>
</template>

<script>
export default {
    props: ['data']
}
</script>

<style lang="scss" scoped>

</style>
