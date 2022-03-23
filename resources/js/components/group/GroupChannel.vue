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
            :items="result"
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
                        <!--                        <template v-slot:activator="{ on }">-->
                        <!--                            <v-btn color="primary" dark class="mb-2" v-on="on"> +</v-btn>-->
                        <!--                        </template>-->
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItem.name" :rules="nameRules" :label="$t('name')"/>
                                        </v-col>
                                        <v-col cols="6" sm="6" md="6">
                                            <v-text-field item-text="text" v-model="editedItem.teacher" :label="$t('teacher')"/>
                                        </v-col>
                                        <v-col cols="6" sm="6" md="6">
                                            <v-text-field v-model="editedItem.habook" :label="$t('team_model_id')"/>
                                        </v-col>
                                        <v-col cols="6" sm="6" md="6">
                                            <v-text-field v-model="editedItem.course_core" :label="$t('course_core')"/>
                                        </v-col>
                                        <v-col cols="6" sm="6" md="6">
                                            <v-text-field v-model="editedItem.observation_focus" :label="$t('observation_focus')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-textarea rows="10" v-model="editedItem.description" :label="$t('description')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-select :items="subjects" item-text="text" v-model="editedItem.alias" :label="$t('alias')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-select :items="grade" item-text="text" item-value="value" :label="$t('grade')" v-model="editedItem.grade" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <!--                                            <v-select :items="ratings" item-text="text" hide-details single-line v-model="editedItem.rating" :label="$t('rating')"/>-->
                                            <v-select :items="ratings" item-text="text" v-model="editedItem.rating" :label="$t('rating')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <!--                                            <v-file-input  item-text="text" item-value="value" v-model="image" multiple :label="$t('thumbnail')"/>-->
                                            <v-file-input v-model="editedItem.thumbnail" multiple :label="$t('video_thumbnail')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-file-input v-model="editedItem.HiTeachNote" multiple :label="$t('HiTeachNote')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-file-input v-model="editedItem.LessonPlan" multiple :label="$t('LessonPlan')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-file-input v-model="editedItem.Material" multiple :label="$t('Material')"/>
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
                    <v-dialog v-model="dialog2" max-width="350px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ $t('score_editing') }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field disabled v-model="editedItemScore.textbook_practice" :label="$t('textbook_practice')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItemScore.teaching_process" :rules="numberRules" type="number" hint="(0 - 100)" :label="$t('teaching_process')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItemScore.instructional_design" :rules="numberRules" type="number" hint="(0 - 100)" :label="$t('instructional_design')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItemScore.fusion_Innovation" :rules="numberRules" type="number" hint="(0 - 100)" :label="$t('fusion_Innovation')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItemScore.technology_application" :rules="numberRules" type="number" hint="(0 - 100)" :label="$t('technology_application')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-text-field v-model="editedItemScore.teaching_effect" :rules="numberRules" type="number" hint="(0 - 100)" :label="$t('teaching_effect')"/>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="saveCount">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <!-- 編輯影片狀態  -->
                    <v-dialog v-model="statusDialog" max-width="500px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ $t('videoStatus') }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('status')" v-model="editedItem.status" return-object required/>
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
                    <!--影片分享-->
                    <v-dialog v-model="shareDialog" max-width="500px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ $t('shareVideo') }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="12">
                                            <v-select :items="groupList" item-text="text" item-value="value" :label="$t('selectChannel')" v-model="shareGroupId" return-object required/>
                                            <v-checkbox v-model="checkbox" :label="$t('share_description')"></v-checkbox>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="saveShareVideo" :disabled="!checkbox">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.thumbnail="{ item }">
                <v-img :src="item.thum" style="width: 50px; height: 50px" v-if="item.thum"/>
                <v-img src="/storage/default.png" style="width: 50px; height: 50px" v-else/>
            </template>
            <template v-slot:item.alias.text="{ item }">
                <pre>{{ item.alias !== null ? item.alias.text : $t('Other') }}</pre>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
                <v-icon small class="mr-2" @click="editCount(item)">C</v-icon>
                <v-icon small class="mr-2" @click="shareVideo(item)" v-if="item.share_status">share</v-icon>
                <v-icon small class="mr-2" @click="editStatus(item)">mdi-earth</v-icon>
                <v-icon small class="mr-2" @click="getLessonPlayer(item)">slideshow</v-icon>
                <v-icon small class="mr-2" @click="deleteItem(item)">delete</v-icon>
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
    name: "GroupChannel",
    data() {
        return {
            dialog          : false,
            dialog2         : false,
            statusDialog    : false,
            shareDialog     : false,
            valid           : true,
            search          : '',
            headers         : [],
            status          : [],
            stages          : [],
            grade           : [],
            subjects        : [],
            ratings         : [],
            result          : [],
            editedIndex     : -1,
            editedItem      : {
                id               : null,
                name             : null,
                thumbnail        : null,
                HiTeachNote      : null,
                LessonPlan       : null,
                Material         : null,
                date             : null,
                alias            : null,
                rating           : null,
                grade            : null,
                teacher          : null,
                description      : null,
                user_id          : null,
                habook           : null,
                course_core      : null,
                observation_focus: null
            },
            defaultItem     : {
                id               : null,
                name             : null,
                status           : null,
                thumbnail        : null,
                HiTeachNote      : null,
                LessonPlan       : null,
                Material         : null,
                date             : null,
                subject          : null,
                rating           : null,
                grade            : null,
                teacher          : null,
                description      : null,
                user_id          : null,
                habook           : null,
                course_core      : null,
                observation_focus: null
            },
            editedItemScore : {
                tba_id                : null,
                user_id               : null,
                textbook_practice     : 0,
                instructional_design  : 0,
                teaching_process      : 0,
                teaching_effect       : 0,
                technology_application: 0,
                fusion_Innovation     : 0,
            },
            defaultItemScore: {
                textbook_practice     : 0,
                instructional_design  : 0,
                teaching_process      : 0,
                teaching_effect       : 0,
                technology_application: 0,
                fusion_Innovation     : 0,
            },
            nameRules       : [
                v => !!v || this.$t('validate.name_required'),
            ],
            numberRules     : [
                v => Number(v) <= 99 || this.$t('validate.maximum_value'),
            ],
            success         : [],
            error           : [],
            isSuccess       : true,
            groupList       : {},
            shareGroupId    : null,
            checkbox        : false

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

        'editedItemScore.teaching_process'() {
            this.operation();
        },
        'editedItemScore.instructional_design'() {
            this.operation();
        },
        'editedItemScore.fusion_Innovation'() {
            this.operation();
        },
        'editedItemScore.technology_application'() {
            this.operation();
        },
        'editedItemScore.teaching_effect'() {
            this.operation();
        },
    },
    created() {
        this.initialize()
    },
    methods: {
        // 這裡需要傳入頻道ID
        initialize() {
            let groupId = this.$store.state.Auth.user.group_id;
            let groupChannelUrl = `/api/group/channel/content/${groupId}`;
            let _this = this;

            _this.$store.dispatch("updateLoading", true);
            // 無效不顯示  invalid (0, 0) 1
            // 頻道內觀摩  valid (1, 0)  2
            // 全平台分享  share (1, 1) 3
            // 尚待審核中  pending (2, 0) 4
            axios.get(groupChannelUrl).then((response) => {
                // 取回來的資料
                _this.result = response.data.map(v => {
                    return {
                        id               : v.id,
                        name             : v.name,
                        status           : this.statusDisplay(v.status),
                        thum             : (_.isEmpty(v.thumbnail)) ? null : `${this.$store.state.Path.tba}${v.id}/${v.thumbnail}?${_.random(0, 9)}`,
                        date             : v.date,
                        alias            : v.alias,
                        rating           : v.rating,
                        grade            : v.grade,
                        course_core      : v.course_core,
                        observation_focus: v.observation_focus,
                        share_status     : v.share_status,
                        // educational_stage_id: v.educational_stage_id,
                        description   : v.description,
                        teacher       : v.teacher,
                        user_id       : this.$store.state.Auth.user.id,
                        tba_statistics: v.tba_statistics,
                        habook        : v.habook_id
                    }
                });
                _this.$store.dispatch("updateLoading", false);
            }).catch((error) => {
                console.log(error)
            });

            _this.defaultColumns();
        },
        defaultColumns() {
            let groupId = this.$store.state.Auth.user.group_id;
            let subjectUrl = `/api/group/subjects/${groupId}`;
            let ratingUrl = `/api/group/rating/${groupId}`;
            let gradeUrl = '/api/group/grade/lang';

            const harder = [
                {text: this.$t('lesson_id'), value: 'id', align: 'left', sortable: false},
                {text: this.$t('name'), value: 'name', sortable: false},
                {text: this.$t('teacher'), value: 'teacher', sortable: false},
                {text: this.$t('teamModelId'), value: 'habook', sortable: false},
                {text: this.$t('status'), value: 'status', sortable: false},
                {text: this.$t('rating'), value: 'rating.text', sortable: false},
                {text: this.$t('alias'), value: 'alias.text', sortable: false},
                {text: this.$t('grade'), value: 'grade.text', sortable: false},
                {text: this.$t('video_thumbnail'), value: 'thumbnail', sortable: false},
                {text: this.$t('create_at'), value: 'date'},
                {text: this.$t('operation'), value: 'action', sortable: false}
            ];

            const status = [
                // {text: this.$t('invalid'), value: 1},
                {text: this.$t('valid'), value: 2},
                {text: this.$t('share'), value: 3},
                {text: this.$t('pending'), value: 4}
            ];
            // const stages = [
            //     {text: this.$t('Preschool'), value: 1},
            //     {text: this.$t('primary_school'), value: 2},
            //     {text: this.$t('middle_school'), value: 3},
            //     {text: this.$t('high_school'), value: 4},
            //     {text: this.$t('higher_vocational'), value: 5},
            //     {text: this.$t('University'), value: 6}
            // ];
            const grade = [];
            axios.get(gradeUrl).then(response => {
                response.data.grades.map(v => {
                    grade.push({text: v.name, value: v.id})
                });
            });

            const subjects = [];
            axios.get(subjectUrl).then(response => {
                response.data.subjects.map(v => {
                    subjects.push({text: v.alias, value: v.id})
                });
            });
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
            this.editedIndex = this.result.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        editCount(item) {
            this.editedItemScore = Object.assign({}, this.defaultItemScore);
            this.editedItemScore.tba_id = item.id;
            this.editedItemScore.user_id = this.$store.state.Auth.user.id;
            item.tba_statistics.forEach(i => {
                if (i.type === 55) {
                    this.editedItemScore.textbook_practice = i.idx;
                }
                if (i.type === 56) {
                    this.editedItemScore.instructional_design = i.idx * 100;
                }
                if (i.type === 57) {
                    this.editedItemScore.teaching_process = i.idx * 100;
                }
                if (i.type === 58) {
                    this.editedItemScore.teaching_effect = i.idx * 100;
                }
                if (i.type === 59) {
                    this.editedItemScore.technology_application = i.idx * 100;
                }
                if (i.type === 60) {
                    this.editedItemScore.fusion_Innovation = i.idx * 100;
                }
            });
            // this.editedIndex = this.result.indexOf(item);
            // 0.95 +0.95+0.95+0.95+0.93  * 100
            this.dialog2 = true;
        },
        editStatus(item) {
            let _this = this;
            this.editedIndex = this.result.indexOf(item);
            this.editedItem = Object.assign({}, item);
            _this.statusDialog = true;
        },
        saveCount() {
            let url = `/api/group/channel/content/`;
            let _this = this;
            _this.$store.dispatch("updateLoading", true);

            axios.put(url, {data: _this.editedItemScore}).then((response) => {
                setTimeout(() => {
                    this.initialize()
                }, 300)
                this.dialog2 = false;
            });
        },

        deleteItem(item) {
            let _this = this;
            let groupId = this.$store.state.Auth.user.group_id;


            let url = `/api/group/channel/content/${groupId}`;
            const index = _this.result.indexOf(item);

            if (confirm('Are you sure you want to delete this item?')) {
                _this.result.splice(index, 1);
                _this.$store.dispatch("updateLoading", true);
                axios.delete(url, {data: item}).then((response) => {
                    _this.$store.dispatch("updateLoading", false);
                })
            }
        },
        close() {
            this.dialog = false;
            this.dialog2 = false;
            this.statusDialog = false;
            this.shareDialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1
            }, 300)
        },
        save() {
            let groupId = this.$store.state.Auth.user.group_id;
            let _this = this;

            if (this.editedIndex > -1) {
                // 編輯
                let url = `/api/group/channel/content/${groupId}`;
                // 格式轉換
                const obj = {
                    grade: (_this.editedItem.grade === null || _this.editedItem.grade === undefined) ? 'Other' : _this.editedItem.grade.value,
                    // educational_stage_id: _this.editedItem.educational_stage_id.value ? _this.editedItem.educational_stage_id.value : _this.editedItem.educational_stage_id,
                    status: isNaN(_this.editedItem.status) ? _this.editedItem.status.value : _this.editedItem.status,
                    rating: isNaN(_this.editedItem.rating) ? _this.editedItem.rating.value : (_this.editedItem.rating !== null) ? _this.editedItem.rating : _.minBy(this.ratings, (v) => {
                        return v.value;
                    }).value,
                    // subject: _this.editedItem.subject.value ? _this.editedItem.subject.value : _this.editedItem.subject,
                    // 這裡續注意 不論新增或修改學科 只會改 alias
                    subject: isNaN(_this.editedItem.alias) ? _this.editedItem.alias.value : _this.editedItem.alias
                };

                let formData = new FormData();
                if (_this.editedItem.thumbnail) {
                    formData.append('thumbnail', _this.editedItem.thumbnail[0]);
                }
                if (_this.editedItem.HiTeachNote) {
                    formData.append('HiTeachNote', _this.editedItem.HiTeachNote[0]);
                }
                if (_this.editedItem.LessonPlan) {
                    formData.append('LessonPlan', _this.editedItem.LessonPlan[0]);
                }
                if (_this.editedItem.Material) {
                    formData.append('Material', _this.editedItem.Material[0]);
                }
                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                _this.$store.dispatch("updateLoading", true);

                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    _this.initialize();
                    _this.$store.dispatch("updateLoading", false);
                });
                Object.assign(this.result[this.editedIndex], this.editedItem);
                // console.log('編輯')

                return this.close()
            } else {
                // _this.$store.dispatch("updateLoading", true);
                // // 新增 格式轉換
                // const obj = {
                //     grade: this.editedItem.grade.value,
                //     educational_stage_id: this.editedItem.educational_stage_id.value,
                //     status: this.editedItem.status.value,
                // };
                // // 合併修改
                // const data = Object.assign({}, this.editedItem, obj);
                // let url = `/api/group/member`;
                // axios.post(url, data)
                //     .then((response) => {
                //         response.data.status !== 401 ? this.initialize() : response.data.status.message;
                //         _this.$store.dispatch("updateLoading", false);
                //     }).catch((error) => {
                //     console.log(error)
                // });
                // // console.log('新增')
                // return this.close()
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
        operation() {
            this.editedItemScore.textbook_practice = Math.round((
                    Number(this.editedItemScore.teaching_process) +
                    Number(this.editedItemScore.instructional_design) +
                    Number(this.editedItemScore.fusion_Innovation) +
                    Number(this.editedItemScore.technology_application) +
                    Number(this.editedItemScore.teaching_effect)
                )
                * 0.01 / 5 * 100);
        },
        getLessonPlayer(item) {
            let groupId = this.$store.state.Auth.user.group_id;
            let url = `/api/group/lesson/example/${groupId}`

            axios.post(url, {data: item}).then((response) => {
                window.open(response.data.url)
            });
        },
        getUserGroupInfo() {
            let _this = this;
            let userUrl = `/api/group/member/${_this.$store.state.Auth.user.id}`
            axios.get(userUrl).then((response) => {
                _this.groupList = response.data.groups.map((v => {
                    return {'value': v.id, 'text': v.name}
                }));
            })
        },
        statusDisplay(status) {
            switch (status) {
                case 2:
                    return {text: this.$t('valid'), value: 2}
                case 3:
                    return {text: this.$t('share'), value: 3}
                default:
                    return {text: this.$t('pending'), value: 4}
            }
        },
        shareVideo(item) {
            let _this = this;
            this.editedIndex = this.result.indexOf(item);
            this.editedItem = Object.assign({}, item);
            _this.shareDialog = true;
        },
        saveShareVideo() {
            let _this = this;
            _this.$store.dispatch("updateLoading", true);
            let url = `/api/group/share/channel/content/${this.shareGroupId.value}`
            axios.post(url, {params: this.editedItem}).then((response) => {
                if (response.status !== 201) {
                    alert('Sharing failed');
                }
                _this.$store.dispatch("updateLoading", false);
            });

            return this.close()
        }

    },
    mounted() {
        this.getUserGroupInfo();

    }
}
</script>

<style scoped>

</style>
