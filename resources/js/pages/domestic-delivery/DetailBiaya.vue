<template>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Jenis Biaya</th>
                <th>Berat/Volume</th>
                <th>Tarif</th>
                <th>Biaya</th>
                <th>PPN</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="td-value"> {{data.service_type}} </td>
                <td class="td-value text-right">{{data.invoice_weight | formatNumber}} KG</td>
                <td class="td-value text-right">Rp {{data.delivery_rate | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.delivery_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.delivery_cost_ppn * 0.1 * data.delivery_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.delivery_cost + (data.delivery_cost_ppn * 0.1 * data.delivery_cost) | formatNumber}}</td>
            </tr>
            <tr>
                <td class="td-value">PACKING PETI</td>
                <td class="td-value text-right">{{data.packing_volume | formatNumber}} M<sup>3</sup></td>
                <td class="td-value text-right">Rp {{data.packing_rate | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.packing_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.packing_cost_ppn * 0.1 * data.packing_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.packing_cost + (data.packing_cost_ppn * 0.1 * data.packing_cost) | formatNumber}}</td>
            </tr>
            <tr>
                <td class="td-value">BIAYA PENERUS</td>
                <td></td>
                <td></td>
                <td class="td-value text-right">Rp. {{data.forwarder_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.forwarder_cost_ppn * 0.1 * data.forwarder_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.forwarder_cost + (data.forwarder_cost_ppn * 0.1 * data.forwarder_cost) | formatNumber}}</td>
            </tr>
            <tr>
                <td class="td-value">BIAYA LAIN - LAIN</td>
                <td>{{data.additional_cost_description}}</td>
                <td></td>
                <td class="td-value text-right">Rp. {{data.additional_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.additional_cost_ppn * 0.1 * data.additional_cost | formatNumber}}</td>
                <td class="td-value text-right">Rp. {{data.additional_cost + (data.additional_cost_ppn * 0.1 * data.additional_cost) | formatNumber}}</td>
            </tr>
            <tr>
                <td class="td-value">TOTAL</td>
                <td class="td-value text-right big" colspan="5">Rp. {{data.total_cost | formatNumber}}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: ['data']
}
</script>
