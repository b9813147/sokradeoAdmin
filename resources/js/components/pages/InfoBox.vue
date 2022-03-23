<template>
    <v-layout row wrap>
        <v-flex
            md3
            sm6
            xs12
            v-for="(stat, index) in stats"
            v-bind:key="index"
        >
            <v-card :class="stat.bgColor" dark elevation="4">
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title v-text="stat.title"></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-content class="text-right">
                        <v-list-item-title class="text-size-25" v-text="stat.data"></v-list-item-title>
                    </v-list-item-content>


                    <!--                    <v-list-item-action>-->
                    <!--                        <v-btn icon>-->
                    <!--                            <v-icon color="grey lighten-3">mdi-information</v-icon>-->
                    <!--                        </v-btn>-->
                    <!--                    </v-list-item-action>-->
                </v-list-item>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    name : 'InfoBox',
    props: ['infoBoxData'],
    data() {
        return {
            stats: [
                {
                    bgColor: "orange darken-2",
                    icon   : "mdi-calendar-month-outline",
                    title  : this.$t('today'),
                    data   : 0,
                },
                {
                    bgColor: "orange darken-2",
                    icon   : "mdi-calendar-month-outline",
                    title  : this.$t('sevenDay'),
                    data   : 0,
                },
                {
                    bgColor: "orange darken-2",
                    icon   : "mdi-calendar-month-outline",
                    title  : this.$t('thirtyDay'),
                    data   : 0,
                },
                {
                    bgColor: "orange darken-2",
                    icon   : "mdi-calendar-month-outline",
                    title  : this.$t('currentSemester'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-earth",
                    title  : this.$t('public_video'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-filmstrip",
                    title  : this.$t('video_total'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-circle-slice-8",
                    title  : this.$t('double_green_video'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-notebook-multiple",
                    title  : this.$t('material'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-send",
                    title  : this.$t('lessonPlan'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-eye-outline",
                    title  : this.$t('clicks'),
                    data   : 0,
                },
                {
                    bgColor: "blue-grey darken-1",
                    icon   : "mdi-bookmark",
                    title  : this.$t('tbaMarkerCount'),
                    data   : 0,
                },
            ]
        }
    },
    watch  : {
        infoBoxData(v) {
            this.stats = this.dataMerge(this.stats, v);
        }
    },
    methods: {
        // Solution
        dataMerge(mainData, apiData) {
            // 2 data sets must have the same length
            if (mainData.length !== Object.keys(apiData).length) return;
            // Update data (The datasets must be in the same order)
            let mainDataMerged = mainData;
            let i = 0;
            Object.values(apiData).forEach(function (value) {
                // Numerical thousandth sign ( Comma )
                mainDataMerged[i].data = value.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")
                i++;
            });
            return mainDataMerged
        },

    },
}
</script>
<style lang="scss" scoped>

.text-size-25 {
    font-size: 25px;
}
</style>
