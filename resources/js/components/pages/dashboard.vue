<template>
    <!--    <info-box></info-box>-->
    <v-container fluid grid-list-lg class="mx-0 pa-0">
        <div class="pb-2">
            <v-btn :color="channelBool?'primary':'blue lighten-5'" @click="displayType('channelBool')" pb-3>{{ $t('channel_statistics') }}</v-btn>
            <v-btn :color="userBool?'primary':'blue lighten-5'" @click="displayType('userBool')" pb-3>{{ $t('member_statistics') }}</v-btn>
            <v-btn :color="storageBool?'primary':'blue lighten-5'" @click="displayType('storageBool')" pb-3>{{ $t('storage.spaceUsage') }}</v-btn>
        </div>
        <div v-if="channelBool">


            <info-box :infoBoxData="infoBox"></info-box>
            <v-layout row wrap>
                <v-flex md12 xs12>
                    <v-card light elevation="4">
                        <semester :chart-data="semesterData.chart"></semester>
                    </v-card>
                </v-flex>
                <v-flex md4 xs12>
                    <v-card light elevation="4">
                        <donut :chartData="ratingData.chart" :options="ratingData.option"></donut>
                    </v-card>
                </v-flex>
                <v-flex md4 xs12>
                    <v-card light elevation="4">
                        <donut :chartData="subjectData.chart" :options="subjectData.option"></donut>
                    </v-card>
                </v-flex>
                <v-flex md4 xs12>
                    <v-card light elevation="4">
                        <donut :chartData="gradeData.chart" :options="gradeData.option"></donut>
                    </v-card>
                </v-flex>
            </v-layout>
        </div>
        <user-statistics v-if="userBool" :semester="semester" :startDate="startDate" :filterList='filterList'></user-statistics>
        <storage v-if="storageBool" :storage="storage"></storage>
        <v-dialog
            v-model="dialog"
            hide-overlay
            persistent
            width="300"
        >
            <v-card
                color="primary"
                dark
            >
                <v-card-text>
                    {{ $t('loading') }}
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import Donut          from "../dashboard/Donut"
import InfoBox        from "./InfoBox"
import UserStatistics from "../pages/UserStatistics";
import Storage        from "../pages/Storage";
import Semester       from "../dashboard/Semester";

export default {
    components: {Semester, Donut, InfoBox, UserStatistics, Storage},
    name      : "dashboard",
    data() {
        return {
            dialog         : false,
            infoBox        : [],
            storage        : [],
            semesterData   : {
                chart : {},
                option: {}
            },
            ratingData     : {
                chart : {},
                option: {}
            },
            gradeData      : {
                chart : {},
                option: {}
            },
            subjectData    : {
                chart : {},
                option: {}
            },
            backgroundColor: ["#0288D1", "#0097A7", "#00796B", "#388E3C", "#689F38", '#AFB42B', '#FBC02D', '#FFA000', '#F57C00', '#E64A19', '#5D4037', '#455A64'],
            channelBool    : true,
            userBool       : false,
            storageBool    : false,
            semester       : null,
            startDate      : null,
            filterList     : [],
            skill          : 20,
            knowledge      : 33,
            power          : 78,
        }
    },
    watch   : {
        // statsBool(v) {
        //     !v ? this.get() : null;
        // }
    },
    computed: {},
    mounted() {
        // this.get();
    },

    created() {
        this.get();
    },

    methods: {
        get() {
            let url = `api/dashboard/${this.$store.state.Auth.user.group_id}`;
            let _this = this;
            _this.dialog = true;
            axios.get(url).then((response => {
                let resource = response.data.result;
                // 學科
                if (typeof resource.group_subject_fields !== 'undefined') {
                    _.forEach(resource.group_subject_fields, (v) => {
                        _this.filterList.push({'alias': this.$t('subject_name') + '-' + v.alias, 'id': v.id, 'type': 'subject'});
                    })
                    _this.filterList.unshift({'alias': this.$t('all'), 'id': null, 'type': null})
                }
                // 教研
                if (typeof resource.ratings !== 'undefined') {
                    _.forEach(resource.ratings, (v) => {
                        _this.filterList.push({'alias': this.$t('rating') + '-' + v.name, 'id': v.id, 'type': 'rating'});
                    })
                }
                // 年級
                if (typeof resource.grades !== 'undefined') {
                    _.forEach(resource.grades, (v) => {
                        _this.filterList.push({'alias': this.$t('grade') + '-' + v.name, 'id': v.id, 'type': 'grade'});
                    })
                }

                _this.semester = resource.semester.current_semester;
                _this.startDate = resource.semester.start_semester;
                // 綜合數據
                _this.infoBox = resource.stats;

                let data = [];
                let backgroundColor = [];
                let labels = [];
                // 分類
                if (typeof resource.ratings !== 'undefined') {
                    resource.ratings.forEach(function (v, k) {
                        data.push(v.total)
                        backgroundColor.push(_this.backgroundColor[k])
                        labels.push(`${v.name} (${v.total})`)
                    });
                }
                _this.ratingData.chart = _this.chartData(labels, [{backgroundColor: backgroundColor, data: data}])
                _this.ratingData.option = _this.chartOption(_this.$t('rating_video_total'));

                data = []
                backgroundColor = []
                labels = []
                // 年級
                if (typeof resource.grades !== 'undefined') {
                    resource.grades.forEach(function (v, k) {
                        data.push(v.total)
                        backgroundColor.push(_this.backgroundColor[k])
                        labels.push(`${v.name} (${v.total})`)
                    });
                }
                _this.gradeData.chart = _this.chartData(labels, [{backgroundColor: backgroundColor, data: data}])
                _this.gradeData.option = _this.chartOption(_this.$t('grade_video_total'));

                data = []
                backgroundColor = []
                labels = []
                // 學科
                if (typeof resource.subjects !== 'undefined') {
                    resource.subjects.forEach(function (v, k) {
                        data.push(v.total)
                        backgroundColor.push(_this.backgroundColor[k])
                        labels.push(`${v.name} (${v.total})`)
                    });
                }
                _this.subjectData.chart = _this.chartData(labels, [{backgroundColor: backgroundColor, data: data}])
                _this.subjectData.option = _this.chartOption(_this.$t('subject_video_total'));


                data = []
                let datasets = [];
                labels = []
                // 學期數據 雙綠燈
                if (typeof resource.semester.double_green_light !== 'undefined') {
                    _.orderBy(resource.semester.double_green_light, ['year'], ['asc']).forEach(function (v) {
                        data.push(v.total)
                    });
                }
                datasets.push({
                    label                 : this.$t('double_green_video'),
                    backgroundColor       : "#eb9fc1",
                    data                  : data,
                    showLine              : false,
                    hideInLegendAndTooltip: true,
                    borderColor           : '#e55b83',
                    borderWidth           : 1,
                    categoryPercentage    : 0.95,
                    barPercentage         : 0.95
                },)
                data = []
                // 影片總數
                if (typeof resource.semester.total !== 'undefined') {
                    _.orderBy(resource.semester.total, ['year'], ['asc']).forEach(function (v) {
                        data.push(v.total)
                        labels.push(v.year);
                    });
                }
                datasets.push({
                    label                 : this.$t('video_total'),
                    backgroundColor       : "#fde4a9",
                    data                  : data,
                    showLine              : false,
                    hideInLegendAndTooltip: true,
                    borderColor           : '#facd55',
                    borderWidth           : 1,
                    categoryPercentage    : 0.95,
                    barPercentage         : 0.95
                })
                // 累積數據
                let AccumulateData = []
                let defaultNumber = 0;
                if (typeof data !== 'undefined') {
                    data.forEach((v, k) => {
                        if (k === 0) {
                            defaultNumber = v;
                            AccumulateData.push(v);
                        } else {
                            AccumulateData.push(defaultNumber += v);
                        }
                    });
                }
                datasets.push({
                        label                 : this.$t('cumulative_total'),
                        backgroundColor       : "#9bdddf",
                        data                  : AccumulateData,
                        showLine              : false,
                        hideInLegendAndTooltip: true,
                        borderColor           : '#62c1c0',
                        borderWidth           : 1,
                        categoryPercentage    : 0.95,
                        barPercentage         : 0.95
                    }
                )
                _this.dialog = false;
                _this.semesterData.chart = _this.chartData(labels, datasets)
                // 空間計算
                _this.storage = resource.storage;
            })).catch((e) => {
                console.error(e);
            });
        },


        chartData(labels = [], datasets = {}) {
            return {
                labels  : labels,
                datasets: datasets
                // [
                // {
                //     backgroundColor: backgroundColor,
                //     data           : data,
                // }
                // ]
            }
        },

        chartOption(title) {
            return {
                legend: {
                    position : 'bottom',
                    fullWidth: true,
                    labels   : {
                        fontSize : 16,
                        fontStyle: 'bold'
                    }
                },
                title : {
                    display  : true,
                    text     : title,
                    position : 'top',
                    fontSize : 20,
                    fontStyle: 'bold'
                },

                responsive: true, maintainAspectRatio: false
            }
        },

        displayType(v) {
            switch (v) {
                case 'channelBool':
                    this.channelBool = true;
                    this.userBool = false;
                    this.storageBool = false;
                    break
                case 'userBool':
                    this.channelBool = false;
                    this.userBool = true;
                    this.storageBool = false;
                    break;
                case 'storageBool':
                    this.channelBool = false;
                    this.userBool = false;
                    this.storageBool = true;
                    break;
            }
        }
    }

}
</script>

<style lang="scss" scoped>

</style>
