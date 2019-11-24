<template>
    <el-card>
        <h1 style="text-align:center;font-size:20px;margin-bottom:10px">REPORT LEAD TIME PENGIRIMAN DOMESTIC</h1>
        <el-form inline style="text-align:center">
            <el-form-item>
                <el-select v-model="year" placeholder="Pilih Tahun" @change="requestData">
                    <el-option v-for="y in $store.state.filterYearList.map(y => y.year).filter(y => !!y)" :key="y" :value="y" :label="y"></el-option>
                </el-select>
            </el-form-item>
            <!-- kalau customer ga liat ini -->
            <el-form-item v-if="$store.state.user.role != 41">
                <el-select v-model="customer_id" placeholder="Pilih Customer" @change="requestData">
                    <el-option v-for="c in $store.state.customerList" :key="c.id" :value="c.id" :label="c.name"></el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <v-chart style="height: 300px;width:100%" :options="chart"></v-chart>
        <br>
        <el-table stripe :data="summaries">
            <el-table-column prop="month" label="Bulan" header-align="center"> </el-table-column>
            <el-table-column label="Ontime" header-align="center">
                <el-table-column prop="ontime" label="Total" header-align="center" align="center"></el-table-column>
                <el-table-column label="%" align="center" header-align="center">
                    <template slot-scope="scope">
                        {{(scope.row.ontime / (scope.row.total) * 100).toFixed(2)}}%
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="Delay" header-align="center">
                <el-table-column prop="delay" label="Total" header-align="center" align="center"></el-table-column>
                <el-table-column label="%" align="center" header-align="center">
                    <template slot-scope="scope">
                        {{(scope.row.delay / (scope.row.total) * 100).toFixed(2)}}%
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="Total Pengiriman" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.total}}
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script>
import Vue from 'vue'
import ECharts from 'vue-echarts'
import 'echarts/lib/chart/bar'
import 'echarts/lib/component/title'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'
import moment from 'moment'

export default {
    components: {
        'v-chart': ECharts
    },
    data() {
        return {
            year: moment().format('YYYY'),
            monthsShort: moment.monthsShort(),
            months: moment.months(),
            customer_id: '',
            summaries: [],
            chart: {
                legend: {},
                tooltip: {},
                grid: {
                    left: '0%',
                    right: '0%',
                    bottom: '0%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: true,
                    data: []
                },
                yAxis: { type: 'value'},
                series: [{
                    name: 'On Time',
                    type: 'bar',
                    color: '#0070c0',
                    barGap: '0%',
                    label: {
                        normal: {
                            show: true,
                            position: 'top',
                            formatter: (v) => {
                                return (v.value / this.summaries.find(s => s.month == v.name).total * 100).toFixed(2) + '%'
                            }
                        }
                    },
                    data: [],
                }, {
                    name: 'Delay',
                    type: 'bar',
                    color: '#c00000',
                    barGap: '0%',
                    label: {
                        normal: {
                            show: true,
                            position: 'top',
                            formatter: (v) => {
                                return (v.value / this.summaries.find(s => s.month == v.name).total * 100).toFixed(2) + '%'
                            }
                        }
                    },
                    data: [],
                }]
            }
        }
    },
    methods: {
        requestData() {
            let params = {
                type: 'domestic',
                year: this.year,
                customer_id: this.customer_id
            }

            axios.get('/report/leadTime', { params: params }).then(r => {
                this.chart.xAxis.data = r.data.map(d => { return this.monthsShort[d.month - 1] })
                this.chart.series[0].data = r.data.map(d => { return d.ontime })
                this.chart.series[1].data = r.data.map(d => { return d.delay })
                this.summaries = r.data.map(d => {
                    d.month = this.monthsShort[d.month - 1]
                    return d
                })
            }).catch(e => console.log(e))
        }
    },
    mounted() {
        this.$store.commit('getCustomerList')
        this.$store.commit('getFilterYear')

        if (this.$store.state.user.role == 41) {
            this.customer_id = this.$store.state.user.customer_id
        }

        this.requestData()
    }
}
</script>
