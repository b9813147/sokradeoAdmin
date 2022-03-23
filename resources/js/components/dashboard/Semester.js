import {Bar} from "vue-chartjs";

export default {
    extends: Bar,
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
            this.renderChart(this.chartData,
                {
                    title                                : {
                        display  : true,
                        text     : this.$t('school_dashboard'),
                        position : 'bottom',
                        fontSize : 20,
                        fontStyle: 'bold'
                    },
                    legend                               : {
                        position : 'top',
                        fullWidth: true,
                        labels   : {
                            fontSize : 16,
                            fontStyle: 'bold'
                        }
                    },
                    scales                               : {
                        xAxes: [{
                            display: true,
                            offset : true,
                        }],
                        yAxes: [{
                            display: true,
                        }],
                    },
                    responsive: true, maintainAspectRatio: false,
                }
            )
        }
    },

    mounted() {
        this.renderChart(this.chartData,
            {
                title                                : {
                    display  : true,
                    text     : '學校數據統計圖表',
                    position : 'bottom',
                    fontSize : 20,
                    fontStyle: 'bold'
                },
                legend                               : {
                    position : 'top',
                    fullWidth: true,
                    labels   : {
                        fontSize : 16,
                        fontStyle: 'bold'
                    }
                },
                scales                               : {
                    xAxes: [{
                        display: true,
                        offset : true,
                    }],
                    yAxes: [{
                        display: true,
                    }],
                },
                responsive: true, maintainAspectRatio: false,

            }
        );
    }
};
