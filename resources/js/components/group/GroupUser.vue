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
            :items="resources"
            :search="search"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('user_manager') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    />
                    <v-spacer/>
                    <!-- QrCode-->
                    <v-dialog v-model="qrcode.dialog" max-width="350px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mx-2" v-on="on" @click="getQrcode">Qrcode</v-btn>
                        </template>
                        <v-card>
                            <v-card-title class="justify-center">
                                <span class="headline">{{ $t('group.qrcode') }}</span>
                            </v-card-title>
                            <v-card-text class="text-center">
                                <v-container class="justify-center">
                                    <qr-code :text="qrcode.url" :size="278" error-level="L"></qr-code>
                                </v-container>
                                <h3>
                                    {{ `${qrcode.date.min}:${qrcode.date.sec}` }}
                                    <v-icon @click="getQrcode" size="16" v-if="qrcode.date.sec==='00'">mdi-refresh</v-icon>
                                </h3>
                            </v-card-text>
                        </v-card>
                    </v-dialog>
                    <!-- Create user  -->
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mx-2" v-on="on"> +</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-text-field v-model="defaultItem.group_name" :label="$t('channel_name')" disabled/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-select :items="duty" item-text="text" item-value="value" :label="$t('member_duty')" :rules="dutyRules" v-model="editedItem.member_duty" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-text-field v-model="editedItem.habook" :label="$t('team_model_id')" v-if="editedIndex >= 0" disabled/>
                                            <v-textarea v-model="editedItem.habook" :error-messages="error" :label="$t('team_model_id')" v-else/>
                                        </v-col>
                                        <!--                                        <v-col cols="12" sm="6" md="6">-->
                                        <!--                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('status')" :rules="statusRules" v-model="editedItem.member_status" return-object required/>-->
                                        <!--                                        </v-col>-->
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
                </v-toolbar>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)">edit</v-icon>
                <v-icon small @click="deleteItem(item)">delete</v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
            <template v-slot:item.member_duty="{ item }">
                {{ item.member_duty.text }}
            </template>
            <!--            <template v-slot:item.member_status="{ item }">-->
            <!--                {{ item.member_status.text }}-->
            <!--            </template>-->
        </v-data-table>
    </v-card>
</template>

<script>

export default {
    data() {
        return {
            dialog     : false,
            valid      : true,
            search     : '',
            headers    : [],
            duty       : [],
            status     : [],
            resources  : [],
            stats      : [],
            qrcode     : {
                url   : '',
                dialog: false,
                date  : {
                    timestamp: 0,
                    min      : 0,
                    sec      : 0,
                }
            },
            editedIndex: -1,
            editedItem : {
                group_name   : null,
                habook       : null,
                member_duty  : {text: this.$t('general'), value: 'General'},
                member_status: {text: this.$t('enable'), value: 1},
                groupId      : null,
                userId       : null
            },
            defaultItem: {
                group_name   : null,
                habook       : null,
                member_duty  : {text: this.$t('general'), value: 'General'},
                member_status: {text: this.$t('enable'), value: 1},
                groupId      : null,
                userId       : null
            },
            success    : [],
            error      : [],
            dutyRules  : [
                v => !!v || this.$t('validate.duty_required')
            ],
            statusRules: [
                v => !!v || this.$t('validate.status_required')
            ],
            isSuccess  : true
        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_user') : this.$t('editor_user')
        },
    },
    watch   : {
        dialog(val) {
            val || this.close()
        },
        'qrcode.dialog'(val) {
            val || this.close()
        },
        'editedItem.habook'() {
            //     let url = `/api/group/user/exist/`;
            //     axios.get(url, {
            //         params: {
            //             group_id: this.editedItem.groupId, habook: this.editedItem.habook
            //         }
            //     }).then(response => {
            //         console.log(response.data);
            //         let result = response.data
            //         // this.error = result.status === 200 ? [] : result.status === 404 ? [result.message + this.$t('validate.user_not_exist')] : [result.message + this.$t('validate.user_exist')];
            //         // this.error = result.message;
            //         this.success = result.message;
            //         // this.success = result.status === 200 ? [result.message + this.$t('validate.add_success')] : '';
            this.validation();
            //     })
        },
        'editedItem.member_status'() {
            this.validation();
        },
        'editedItem.member_duty'() {
            this.validation();
        },
    },
    created() {
        this.initialize()
    },
    methods: {
        // 這裡需要傳入頻道ID
        initialize() {
            let groupId = this.$store.state.Auth.user.group_id;
            let groupUserUrl = `/api/group/${groupId}`;
            let _this = this;

            _this.$store.dispatch("updateLoading", true);
            axios.get(groupUserUrl).then((response) => {
                // 預設值
                _this.defaultItem.group_name = response.data.name;
                _this.defaultItem.groupId = this.$store.state.Auth.user.group_id;
                _this.editedItem.groupId = this.$store.state.Auth.user.group_id;

                // 取回來的資料
                _this.resources = response.data.users.map(v => {
                    return {
                        name       : `${v.name}(${v.habook})`,
                        habook     : v.habook,
                        member_duty: (v.pivot.member_duty === 'Admin')
                                     ? {text: this.$t('admin'), value: 'Admin'} : (v.pivot.member_duty === 'Expert')
                                                                                  ? {text: this.$t('expert'), value: 'Expert'} : {text: this.$t('general'), value: 'General'},
                        // member_status: (v.pivot.member_status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        userId    : v.pivot.user_id,
                        groupId   : response.data.id,
                        created_at: v.created_at,
                    }
                });
                _this.$store.dispatch("updateLoading", false);
            }).catch((error) => {
                console.log(error)
            });

            _this.defaultColumns();
        },
        defaultColumns() {
            const harder = [
                // {text: this.$t('channel_name'), value: 'group_name', align: 'left', sortable: false},
                {text: this.$t('user_name'), value: 'name', sortable: true},
                // {text: this.$t('team_model_id'), value: 'habook', sortable: true},
                {text: this.$t('member_duty'), value: 'member_duty.text', sortable: true},
                {text: this.$t('member.join_at'), value: 'created_at', sortable: true},
                {text: this.$t('operation'), value: 'action', sortable: false}
            ];

            const duty = [
                {text: this.$t('admin'), value: 'Admin'},
                {text: this.$t('expert'), value: 'Expert'},
                {text: this.$t('general'), value: 'General'}
            ];
            const status = [
                {text: this.$t('enable'), value: 1},
                {text: this.$t('disable'), value: 0}
            ];

            this.headers = harder;
            this.duty = duty;
            this.status = status;
        },
        editItem(item) {
            this.editedIndex = this.resources.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.isSuccess = false;
            this.dialog = true;
        },
        deleteItem(item) {
            let _this = this;
            let url = `/api/group/member/${item.userId}`;
            const index = _this.resources.indexOf(item);

            if (confirm('Are you sure you want to delete this item?')) {
                _this.resources.splice(index, 1);
                _this.$store.dispatch("updateLoading", true);
                axios.delete(url, {data: item}).then((response) => {
                    _this.$store.dispatch("updateLoading", false);
                })
            }

        },
        close() {
            this.dialog = false;
            this.isSuccess = true;
            this.error = null;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.qrcode = Object.assign({}, {
                    url   : '',
                    dialog: false,
                    date  : {
                        min      : 0,
                        sec      : 0,
                        timestamp: 0
                    }
                })
                this.editedIndex = -1
            }, 300)
        },
        save() {
            let _this = this;
            if (this.editedIndex > -1) {
                // 編輯
                let url = `/api/group/member/${this.editedItem.userId}`;
                // 格式轉換
                const obj = {
                    member_duty  : this.editedItem.member_duty.value,
                    member_status: 1
                };
                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                _this.$store.dispatch("updateLoading", true);
                axios.put(url, data).then((response) => {
                    if (response.status === 204) {
                        _this.$store.dispatch("updateLoading", false);
                        Object.assign(this.resources[this.editedIndex], this.editedItem);
                        _this.$store.dispatch("updateAlert", true);
                        return this.close()
                    }
                })
            } else {
                _this.$store.dispatch("updateLoading", true);
                // 新增 格式轉換
                const obj = {
                    member_duty  : this.editedItem.member_duty.value,
                    member_status: 1
                };
                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                let url = `/api/group/member`;
                axios.post(url, data)
                    .then((response) => {
                        if (response.status === 201 || response.status === 200) {
                            this.initialize()
                            _this.$store.dispatch("updateLoading", false);
                            return this.close()
                        }
                    }).catch((error) => {
                    _this.error = error.response.data.message;
                    _this.$store.dispatch("updateLoading", false);

                });
            }
        },
        getQrcode() {
            let groupId = this.$store.state.Auth.user.group_id;
            let url = `/api/group/qrcode/${groupId}`;
            let _this = this
            _this.$store.dispatch("updateLoading", true);
            axios.get(url).then((response) => {
                if (response.status === 201 || response.status === 200) {
                    let result = response.data;
                    let timestamp = new URL(result.url).searchParams.get('expires') * 1000;
                    _this.qrcode.url = result.url;
                    _this.qrcode.date.timestamp = timestamp;
                    this.timeCalculation();
                    _this.$store.dispatch("updateLoading", false);
                }
            }).catch((error) => {
                _this.error = error.response.data.message;
                _this.$store.dispatch("updateLoading", false);
            })
        },

        timeCalculation() {
            let _this = this;
            let runInterval = setInterval(function () {  // 設置倒數計時: 結束時間 - 當前時間
                // 取消計時
                if (_this.qrcode.date.timestamp === 0) {
                    clearInterval(runInterval)
                    return
                }
                // 當前時間
                let time = new Date();
                let nowTime = time.getTime(); // 獲取當前毫秒數
                let endTime = time.setTime(_this.qrcode.date.timestamp) //結束時間;
                // // 倒數計時: 差值
                let offsetTime = (endTime - nowTime) / 1000; // ** 以秒為單位
                let sec = parseInt(offsetTime % 60); // 秒
                sec = ("0" + sec).slice(-2);

                let min = parseInt((offsetTime / 60) % 60); // 分 ex: 90秒
                // // let hr = parseInt(offsetTime / 60 / 60); // 時
                _this.qrcode.date.min = min;
                _this.qrcode.date.sec = sec;

                // 取消計時
                if (_this.qrcode.date.min === 0 && _this.qrcode.date.sec === '00') {
                    clearInterval(runInterval)
                    return
                }
            }, 1000)

        },

        validation() {
            if (this.editedItem.member_duty !== null && this.editedItem.member_status !== null && this.editedItem.habook !== null) {
                this.isSuccess = false
            }
        }

    }
}
</script>
