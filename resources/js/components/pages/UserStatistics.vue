<template>
    <div>
        <v-card>
            <v-card-title>
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-card-title>
                <v-btn color="primary" @click="onToday">{{ $t('today') }}</v-btn>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="primary" @click="onSevenDay">{{ $t('sevenDay') }}</v-btn>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="primary" @click="onThirtyDay">{{ $t('thirtyDay') }}</v-btn>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="primary" @click="onCurrentSemester">{{ $t('currentSemester') }}</v-btn>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="primary" @click="onReset">{{ $t('clickTotal') }}</v-btn>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="primary" @click="handleDownload">{{ $t('download') }}</v-btn>
            </v-card-title>
            <v-card-title>
                <v-text-field
                    single-line
                    hide-details
                    type="date"
                    v-model="start_date"
                ></v-text-field>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                    single-line
                    hide-details
                    type="date"
                    v-model="end_date"
                ></v-text-field>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-select
                    hide-details
                    single-line
                    v-model="select"
                    :items="filterList"
                    item-text="alias"
                    :item-value="transform"
                    :label="this.$t('category')"
                ></v-select>
                <v-spacer></v-spacer>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="green accent-1" @click="onConfirm">{{ $t('confirm') }}</v-btn>
            </v-card-title>
            <!--            <v-data-table-->
            <!--                :headers="header"-->
            <!--                :items="result"-->
            <!--                :loading="this.$store.state.Status.isLoading"-->
            <!--                loading-text="Loading... Please wait"-->
            <!--                hide-default-footer-->
            <!--            >-->
            <!--                <template v-slot:top>-->
            <!--                    <v-toolbar flat color="white">-->
            <!--                        <v-toolbar-title>{{ $t('member_statistics') }}</v-toolbar-title>-->
            <!--                        <v-divider-->
            <!--                            class="mx-4"-->
            <!--                            inset-->
            <!--                            vertical-->
            <!--                        />-->
            <!--                    </v-toolbar>-->
            <!--                </template>-->
            <!--            </v-data-table>-->

            <v-data-table
                :headers="header"
                :items="result"
                :search="search"
                class="elevation-1"
                :loading="this.$store.state.Status.isLoading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.alias="{ item }">
                    {{ field ? field[0].alias : $t('all') }}
                </template>
            </v-data-table>

        </v-card>
    </div>
</template>

<script>
export default {
    props: ['semester', 'filterList', 'startDate'],
    name : "UserStatistics",
    data() {
        return {
            search     : null,
            select     : {
                id   : null,
                alias: this.$t('all'),
                type : null
            },
            field      : null,
            sortBy     : 'habook',
            start_date : this.startDate,
            end_date   : this.$moment().format('YYYY-MM-DD'),
            currentDate: null,
            header     : [
                {text: this.$t('user_name'), value: 'name', sortable: true},
                {text: this.$t('category'), value: 'alias', sortable: false},
                {text: this.$t('public_video'), value: 'public_video', sortable: true},
                {text: this.$t('video_total'), value: 'video_total', sortable: true},
                {text: this.$t('doubleGreenLightTotal'), value: 'double_green_light', sortable: true},//雙綠燈影片
                {text: this.$t('material'), value: 'tbaAnnex.material', sortable: true},
                {text: this.$t('lessonPlan'), value: 'tbaAnnex.lessonPlan', sortable: true},
                {text: this.$t('clicks'), value: 'his_total', sortable: true},
                {text: this.$t('tbaMarkerCount'), value: 'total_mark', sortable: true},//被標記數
                {text: this.$t('publicTbaMarkerCount'), value: 'public_mark', sortable: true},//我的公開標記
                // {text: this.$t('ownerTbaMarkerCount'), value: 'private_mark', sortable: true},//我的本人標記

            ],
            result     : [],
            pagination : {
                current_page: 1,
                last_page   : 5,
            },
        };
    },

    watch: {
        result(v) {
            this.result = v;
        },
    },

    methods: {
        init() {
            let _this = this;
            let group_id = this.$store.state.Auth.user.group_id;
            let url = `/api/group/channel/user/stats/${group_id}`
            _this.$store.dispatch("updateLoading", true);
            let date = _this.start_date !== null && _this.end_date != null ? [_this.start_date, _this.end_date] : null;
            axios.get(url, {
                params: {
                    'field': _this.select,
                    // 'column': _this.sortBy,
                    'date': date,
                }
            })
                .then(response => {
                    // 學科顯示
                    _this.field = _.filter(this.filterList, function (v) {
                        if (v.id === _this.select.id && v.type === _this.select.type) {
                            return v.alias
                        }
                    })
                    _this.result = response.data.data;
                    // _this.pagination.current_page = response.data.meta.current_page;
                    // _this.pagination.last_page = response.data.meta.last_page;
                    _this.$store.dispatch("updateLoading", false);
                }).catch(error => {
                // console.log(error)
            });

            let d = this.$moment();
            // 當前時間
            _this.currentDate = d.format('YYYY-MM-DD');
        },
        onPageChange() {
            this.init();
        },
        onReset() {
            let _this = this;
            let date = this.$moment();
            let currentDate = date.format('YYYY-MM-DD');
            let start_date = this.startDate;
            Object.assign({}, _this.start_date = start_date)
            Object.assign({}, _this.end_date = currentDate)
            Object.assign({}, _this.search = null)
            // Object.assign({}, _this.select = null)
            this.init();
        },

        onSevenDay() {
            let _this = this;
            let date = this.$moment();
            let subtractSevenDay = date.subtract(7, 'days').format('YYYY-MM-DD');
            _this.search = null;
            _this.start_date = subtractSevenDay;
            _this.end_date = _this.currentDate;
            this.init();
        },

        onThirtyDay() {
            let _this = this;
            let date = this.$moment();
            let subtractSevenDay = date.subtract(30, 'days').format('YYYY-MM-DD');
            _this.search = null;
            _this.start_date = subtractSevenDay;
            _this.end_date = _this.currentDate;
            this.init();
        },

        onToday() {
            let _this = this;
            _this.search = null;
            _this.start_date = _this.currentDate;
            _this.end_date = _this.currentDate;
            this.init();
        },

        onConfirm() {
            let _this = this;
            if (_this.search) {
                _this.start_date = null;
                _this.end_date = null;
            }
            this.init();
        },

        onCurrentSemester() {
            let _this = this;
            _this.search = null;
            _this.start_date = this.semester;
            _this.end_date = _this.currentDate;
            this.init();
        },
        // Filter value transform
        transform(v) {
            return v;
        },
        handleDownload() {
            let _this = this;
            _this.$store.dispatch("updateLoading", true);
            import('../../plugins/Export2Excel').then(excel => {
                const tHeader = [
                    this.$t('user_name'),
                    this.$t('category'),
                    this.$t('public_video'),
                    this.$t('video_total'),
                    this.$t('doubleGreenLightTotal'),
                    this.$t('material'),
                    this.$t('lessonPlan'),
                    this.$t('clicks'),
                    this.$t('tbaMarkerCount'),
                    this.$t('publicTbaMarkerCount')
                ]
                const filterVal = [
                    'name',
                    'alias',
                    'public_video',
                    'video_total',
                    'double_green_light',
                    'tbaAnnex.material',
                    'tbaAnnex.lessonPlan',
                    'his_total',
                    'total_mark',
                    'public_mark'
                ]
                const list = this.result
                const data = this.formatJson(filterVal, list)
                excel.export_json_to_excel({
                    header   : tHeader,
                    data,
                    filename : this.$store.state.Auth.user.name,
                    autoWidth: this.autoWidth,
                    bookType : this.bookType
                })
                _this.$store.dispatch("updateLoading", false);
            })
        },
        formatJson(filterVal, jsonData) {
            return jsonData.map(v => filterVal.map(j => {
                if (j === 'timestamp') {
                    return parseTime(v[j])
                } else if (j === 'alias') {
                    return v['alis'] = this.field[0].alias
                } else if (j === 'tbaAnnex.material') {
                    return v['tbaAnnex'].material
                } else if (j === 'tbaAnnex.lessonPlan') {
                    return v['tbaAnnex'].lessonPlan
                } else {
                    return v[j]
                }
            }))
        }
    },
    mounted() {
        this.init();
    }
}
</script>

<style scoped>

</style>
