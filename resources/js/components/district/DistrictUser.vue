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
            sort-by="habook"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('district_user') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    />
                    <v-spacer/>
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on"> +</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="12" md="12">
                                            <v-text-field v-model="editedItem.habook" :label="$t('team_model_id')" v-if="editedIndex >= 0" disabled/>
                                            <v-text-field v-model="editedItem.habook" :error-messages="error" :success-messages="success" hint="TeamModel ID" :label="$t('team_model_id')" v-else/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="6">
                                            <v-select :items="duty" item-text="text" item-value="value" :label="$t('member_duty')" :rules="dutyRules" v-model="editedItem.member_duty" return-object required/>
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
            <template v-slot:item.member_status="{ item }">
                {{ item.member_status.text }}
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    name: "DistrictUser",
    data() {
        return {
            dialog: false,
            valid: true,
            search: '',
            headers: [],
            duty: [],
            status: [],
            resource: [],
            editedIndex: -1,
            editedItem: {
                abbr: null,
                districts_id: null,
                habook: null,
                member_duty: {text: this.$t('admin'), value: 'Admin'},
                // member_status: {text: this.$t('enable'), value: 1},
                name: null,
                user_id: null
            },
            defaultItem: {
                abbr: null,
                districts_id: null,
                habook: null,
                member_duty: {text: this.$t('admin'), value: 'Admin'},
                // member_status: {text: this.$t('enable'), value: 1},
                name: null,
                user_id: null
            },
            success: [],
            error: [],
            dutyRules: [
                v => !!v || this.$t('validate.duty_required')
            ],
            statusRules: [
                v => !!v || this.$t('validate.status_required')
            ],
            isSuccess: true
        }
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_user') : this.$t('editor_user')
        },
    },
    watch: {
        dialog(val) {
            val || this.close()
        },
        'editedItem.habook'() {
            let url = `/api/district/user/exist`;
            if (this.editedItem.habook != null) {
                axios.get(url, {
                    params: {
                        habook: encodeURI(this.editedItem.habook)
                    }
                }).then(response => {
                    this.error = [];
                    this.success = response.status === 204 ? [this.$t('validate.add_success')] : '';
                    this.validation();
                }).catch(error => {
                    this.success = []
                    this.error = error.response.status === 422 ? [this.$t('validate.user_not_exist')] : '';
                    this.validation();
                });
            }
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
            let districtId = this.$store.state.Auth.user.district_id;
            let url = `/api/district/${districtId}/user`;
            let _this = this;

            _this.$store.dispatch("updateLoading", true);
            axios.get(url).then((response) => {
                // 取回來的資料
                _this.resource = response.data.map(v => {
                    return {
                        name: v.name,
                        abbr: v.abbr,
                        habook: v.habook,
                        member_duty: (v.member_duty === 'Admin')
                            ? {text: this.$t('admin'), value: 'Admin'} : (v.member_duty === 'Expert')
                                ? {text: this.$t('expert'), value: 'Expert'} : {text: this.$t('general'), value: 'General'},
                        // member_status: (v.member_status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        user_id: v.user_id,
                        districts_id: v.districts_id,
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
                {text: this.$t('district_name'), value: 'abbr', align: 'left', sortable: false},
                {text: this.$t('user_name'), value: 'name', sortable: false},
                {text: this.$t('team_model_id'), value: 'habook'},
                {text: this.$t('member_duty'), value: 'member_duty'},
                // {text: this.$t('status'), value: 'member_status'},
                {text: this.$t('operation'), value: 'action', sortable: false}
            ];

            const duty = [
                {text: this.$t('admin'), value: 'Admin'},
                // {text: this.$t('expert'), value: 'Expert'},
                // {text: this.$t('general'), value: 'General'}
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
            this.editedIndex = this.resource.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.isSuccess = false;
            this.dialog = true;
        },
        deleteItem(item) {
            let _this = this;
            let url = `/api/district/user/${item.districts_id}`;
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
            setTimeout(() => {
                this.dialog = false;
                this.isSuccess = true;
                this.success = [];
                this.error = [];
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1
            }, 300)
        },
        save() {
            let _this = this;
            let districtId = this.$store.state.Auth.user.district_id;
            if (this.editedIndex > -1) {
                // 編輯
                let url = `/api/district/user/${districtId}`;
                // 格式轉換
                const obj = {
                    member_duty: this.editedItem.member_duty.value,
                    // member_status: this.editedItem.member_status.value,
                };
                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                _this.$store.dispatch("updateLoading", true);
                axios.put(url, data).then((response) => {
                    _this.$store.dispatch("updateLoading", false);
                    _this.$store.dispatch("updateAlert", true);
                })
                Object.assign(this.resource[this.editedIndex], this.editedItem);
                // console.log('編輯')
                return this.close()
            } else {
                _this.$store.dispatch("updateLoading", true);
                // 新增 格式轉換
                const obj = {
                    member_duty: this.editedItem.member_duty.value,
                    // member_status: this.editedItem.member_status.value,
                    districts_id: districtId
                };
                // 合併修改
                const data = Object.assign({}, this.editedItem, obj);
                let url = `/api/district/user/`;
                axios.post(url, data)
                    .then((response) => {
                        response.status === 201 ? this.initialize() : response.status;
                        _this.$store.dispatch("updateLoading", false);
                    }).catch((error) => {
                    // console.log(error.response)
                });
                // console.log('新增')
                return this.close()
            }
        },
        validation() {
            if (this.error.length === 0 && this.editedItem.habook !== null) {
                this.isSuccess = false
            }
        }

    }
}
</script>

<style scoped>

</style>
