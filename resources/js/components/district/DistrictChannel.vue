<template>
    <v-card>
        <v-card-title>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            />
        </v-card-title>
        <v-data-table
            :headers="headers"
            :items="resource"
            :search="search"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('editor_channel_content') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    />
                    <v-spacer/>
                    <v-dialog v-model="dialog" max-width="500px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-select :items="ratings" item-text="text" v-model="editedItem.rating" :label="$t('rating')"/>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="save" :disabled="isSuccess">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <!-- 教材實踐 計算 -->
                </v-toolbar>
            </template>
            <template v-slot:item.thumbnail="{ item }">
                <v-img :src="item.thum" style="width: 50px; height: 50px" v-if="item.thum"/>
                <v-img src="/storage/error.png" style="width: 50px; height: 50px" v-else/>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
                <!--                <v-icon small class="mr-2" @click="deleteItem(item)">delete</v-icon>-->
            </template>
            <!--            <template v-slot:item.action="{ item }">-->
            <!--                <v-icon small class="mr-2" @click="editCount(item)">edit</v-icon>-->
            <!--            </template>-->
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
            <template v-slot:item.status="{ item }">
                <v-chip :color="getColor(item.status)" dark>{{ item.status.text }}</v-chip>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    name: "DistrictChannel",
    data() {
        return {
            dialog     : false,
            dialog2    : false,
            valid      : true,
            search     : '',
            headers    : [],
            status     : [],
            stages     : [],
            grade      : [],
            subjects   : [],
            ratings    : [],
            resource   : [],
            editedIndex: -1,
            editedItem : {
                id    : null,
                rating: null,
            },
            defaultItem: {
                id    : null,
                rating: null,
            },
            nameRules  : [
                v => !!v || this.$t('validate.name_required'),
            ],
            numberRules: [
                v => Number(v) <= 99 || this.$t('validate.maximum_value'),
            ],
            success    : [],
            error      : [],
            isSuccess  : true
        }
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_user') : this.$t('editor_channel_content')
        },
    },
    watch   : {
        dialog(val) {
            val || this.close()
        },
        'editedItem.name'() {
            this.editedItem.name === '' ? this.isSuccess = true : this.isSuccess = false;
        },
    },
    created() {
        this.initialize()
    },
    methods: {
        // 這裡需要傳入頻道ID
        initialize() {
            let districtId = this.$store.state.Auth.user.district_id;
            let url = `/api/district/channel/content/${districtId}`;
            let _this = this;

            _this.$store.dispatch("updateLoading", true);
            // 無效不顯示  invalid (0, 0) 1
            // 頻道內觀摩  valid (1, 0)  2
            // 全平台分享  share (1, 1) 3
            // 尚待審核中  pending (2, 0) 4
            axios.get(url).then((response) => {
                // 取回來的資料
                _this.resource = response.data.map(v => {
                    return {
                        id  : v.id,
                        name: v.name,
                        // thum: (_.isEmpty(v.thumbnail)) ? null : `${this.$store.state.Path.tba}${v.id}/${v.thumbnail}?${_.random(0, 9)}`,
                        // date: v.date,
                        subject: v.subject,
                        rating : v.rating,
                        grade  : v.grade,
                        // educational_stage_id: v.educational_stage_id,
                        // description: v.description,
                        // teacher: v.teacher,
                        // user_id: this.$store.state.Auth.user.id,
                        // tba_statistics: v.tba_statistics,
                        // habook: v.habook_id
                    }
                });
                _this.$store.dispatch("updateLoading", false);
            }).catch((error) => {
                console.log(error)
            });

            _this.defaultColumns(districtId);
        },
        defaultColumns(districtId) {
            let ratingUrl = `/api/district/rating/${districtId}`;

            const harder = [
                {text: 'ID', value: 'id', align: 'left', sortable: false},
                {text: this.$t('name'), value: 'name', sortable: false},
                // {text: this.$t('teacher'), value: 'teacher', sortable: false},
                // {text: this.$t('status'), value: 'status', sortable: false},
                {text: this.$t('rating'), value: 'rating.text', sortable: false},
                {text: this.$t('district_subject'), value: 'subject.text', sortable: false},
                {text: this.$t('grade'), value: 'grade.text', sortable: false},
                // {text: this.$t('video_thumbnail'), value: 'thumbnail', sortable: false},
                // {text: this.$t('create_at'), value: 'date'},
                {text: this.$t('operation'), value: 'action', sortable: false}
            ];

            const status = [
                {text: this.$t('invalid'), value: 1},
                {text: this.$t('valid'), value: 2},
                {text: this.$t('share'), value: 3},
                {text: this.$t('pending'), value: 4}
            ];
            const grade = [];
            // axios.get(gradeUrl).then(response => {
            //     response.data.grades.map(v => {
            //         grade.push({text: v.name, value: v.id})
            //     });
            // });

            const subjects = [];
            // axios.get(subjectUrl).then(response => {
            //     response.data.subjects.map(v => {
            //         subjects.push({text: v.subject, value: v.id})
            //     });
            // });
            const ratings = [];
            axios.get(ratingUrl).then(response => {
                response.data.map(v => {
                    ratings.push({text: v.name, value: v.id})
                });
            });

            this.headers = harder;
            // this.stages = stages;
            this.grade = grade;
            this.status = status;
            this.subjects = subjects;
            this.ratings = ratings;
        },
        editItem(item) {
            this.editedIndex = this.resource.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },

        deleteItem(item) {
            let _this = this;
            let url = `/api/district/channel/content/${item.id}`;
            const index = _this.resource.indexOf(item);

            if (confirm('Are you sure you want to delete this item?')) {
                _this.resource.splice(index, 1);
                _this.$store.dispatch("updateLoading", true);
                axios.delete(url, {data: item}).then((response) => {
                    _this.$store.dispatch("updateLoading", false);
                })
            }
        },
        close() {
            this.dialog = false;
            this.dialog2 = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1
            }, 300)
        },
        save() {
            let _this = this;
            _this.$store.dispatch("updateLoading", true);
            if (this.editedIndex > -1) {
                // 編輯
                let url = `/api/district/channel/content/${this.editedItem.id}`;
                // 格式轉換
                const obj = {
                    // grade: (_this.editedItem.grade === null || _this.editedItem.grade === undefined) ? 'Other' : _this.editedItem.grade.value,
                    // educational_stage_id: _this.editedItem.educational_stage_id.value ? _this.editedItem.educational_stage_id.value : _this.editedItem.educational_stage_id,
                    // status: isNaN(_this.editedItem.status) ? _this.editedItem.status.value : _this.editedItem.status,
                    rating: isNaN(_this.editedItem.rating) ? _this.editedItem.rating.value : _this.editedItem.rating,
                    // subject: _this.editedItem.subject.value ? _this.editedItem.subject.value : _this.editedItem.subject,
                    // subject: isNaN(_this.editedItem.subject) ? _this.editedItem.subject.value : _this.editedItem.subject
                };

                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                // console.log(data)

                axios.put(url, {params: data}).then((response) => {
                    console.log(response)
                    _this.initialize();
                    _this.$store.dispatch("updateLoading", false);
                });
                Object.assign(this.resource[this.editedIndex], this.editedItem);

                return this.close()
            }
        },
        // 無效不顯示  invalid (0, 0) 1
        // 頻道內觀摩  valid (1, 0)  2
        // 全平台分享  share (1, 1) 3
        // 尚待審核中  pending (2, 0) 4
        getColor(status) {
            if (status.value === 1) return 'red';
            else if (status.value === 4) return 'orange';
            else return 'green';
        },

    }
}
</script>

<style scoped>

</style>
