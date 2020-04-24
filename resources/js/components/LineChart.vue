<template>
    <div id="stats"></div>
</template>

<script>
    import Vue from 'vue'
    import moment from 'moment'
    import {Chart} from 'frappe-charts/dist/frappe-charts.esm.js'

    Vue.prototype.moment = moment

    export default {
        name: 'line-chart',

        components: {
            Chart
        },

        props: {
            views: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.createChart();
        },

        methods: {
            createChart() {
                return new Chart('#stats', {
                    data: {
                        labels: Object.keys(this.views),
                        datasets: [{
                            values: Object.values(this.views)
                        }]
                    },
                    title: this.trans.stats.cards.views.title,
                    type: 'line',
                    axisOptions: {
                        xIsSeries: true,
                    },
                    lineOptions: {
                        regionFill: 1,
                        hideDots: 1,
                    },
                    height: 250,
                    colors: ['#03a87c'],
                    tooltipOptions: {
                        formatTooltipX: d => moment(d, 'YYYY-MM-DD').format('dddd, MMMM Do'),
                        formatTooltipY: d => d + this.plural(this.trans.stats.chart.view, d),
                    }
                });
            }
        }
    }
</script>

<style scoped>
    @import '~frappe-charts/dist/frappe-charts.min.css';
</style>
