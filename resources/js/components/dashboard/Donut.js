import {Doughnut} from "vue-chartjs";

export default {
    extends: Doughnut,
    props  : {
        chartData: {
            type   : Object,
            default: null
        },
        options  : {
            type   : Object,
            default: null
        }
    },
    watch  : {
        chartData() {
            this.renderChart(this.chartData, this.options)
        },
    },
    mounted() {
        this.renderChart(this.chartData, this.options)
    }
}
