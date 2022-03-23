<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources"
            sort-by="calories"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >
                    <v-toolbar-title>{{ $t('division.admin') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog
                        v-model="dialog"
                        max-width="600px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{ $t('division.create') }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-text-field
                                                v-model="editedItem.title"
                                                :label="$t('division.title')"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-select
                                                v-model="selectedReview"
                                                :items="reviewList"
                                                :label="$t('division.review')"
                                                item-value="id"
                                                item-text="name"
                                                multiple
                                            >
                                                <template v-slot:prepend-item>
                                                    <v-list-item
                                                        ripple
                                                        @click="toggleByReview"
                                                    >
                                                        <v-list-item-action>
                                                            <v-icon :color="selectedReview.length > 0 ? 'indigo darken-4' : ''">
                                                                {{ icon }}
                                                            </v-icon>
                                                        </v-list-item-action>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ $t('division.all') }}
                                                            </v-list-item-title>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                    <v-divider class="mt-2"></v-divider>
                                                </template>
                                                <template v-slot:append-item>
                                                    <v-divider class="mb-2"></v-divider>
                                                    <v-list-item disabled>
                                                        <v-list-item-avatar color="grey lighten-3">
                                                            <v-icon>
                                                                mdi-food-apple
                                                            </v-icon>
                                                        </v-list-item-avatar>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ $t('division.review') + $t('division.count') }}
                                                            </v-list-item-title>
                                                            <v-list-item-subtitle>
                                                                {{ selectedReview.length }}
                                                            </v-list-item-subtitle>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                </template>
                                            </v-select>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-select
                                                v-model="selectedLesson"
                                                :items="lessonList"
                                                :label="$t('division.lesson')"
                                                item-value="content_id"
                                                item-text="name"
                                                multiple
                                            >
                                                <template v-slot:prepend-item>
                                                    <v-list-item
                                                        ripple
                                                        @click="toggleByLesson"
                                                    >
                                                        <v-list-item-action>
                                                            <v-icon :color="selectedLesson.length > 0 ? 'indigo darken-4' : ''">
                                                                {{ icon }}
                                                            </v-icon>
                                                        </v-list-item-action>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ $t('division.all') }}
                                                            </v-list-item-title>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                    <v-divider class="mt-2"></v-divider>
                                                </template>
                                                <template v-slot:append-item>
                                                    <v-divider class="mb-2"></v-divider>
                                                    <v-list-item disabled>
                                                        <v-list-item-avatar color="grey lighten-3">
                                                            <v-icon>
                                                                mdi-food-apple
                                                            </v-icon>
                                                        </v-list-item-avatar>
                                                        <v-list-item-content>
                                                            <v-list-item-title>
                                                                {{ $t('division.review') + $t('division.count') }}
                                                            </v-list-item-title>
                                                            <v-list-item-subtitle>
                                                                {{ selectedLesson.length }}
                                                            </v-list-item-subtitle>
                                                        </v-list-item-content>
                                                    </v-list-item>
                                                </template>
                                            </v-select>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('model.cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="save">{{ $t('model.submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.users="{ item }">
                <div v-for="user in item.users" class="long-text">
                    <v-icon>mdi-account</v-icon>
                    {{ user.name }}
                </div>
            </template>

            <template v-slot:item.tbas="{ item }">
                <div v-for="tba in item.tbas" class="long-text">
                    <v-icon>mdi-book-open</v-icon>
                    {{ tba.name }}
                </div>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon
                    small
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn
                    color="primary"
                    @click="initialize"
                >
                    Reset
                </v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>
<script>
import AppNotification from '../app/Notification';

export default {
    data() {
        return {
            dialog        : false,
            dialogDelete  : false,
            headers       : [
                {
                    text    : this.$t('division.title'),
                    align   : 'start',
                    sortable: false,
                    value   : 'title',
                },
                {
                    text    : this.$t('division.review'),
                    align   : 'start',
                    sortable: false,
                    value   : 'users',
                },
                {
                    text    : this.$t('division.lesson'),
                    align   : 'start',
                    sortable: false,
                    value   : 'tbas',
                },
                {text: this.$t('division.operation'), value: 'actions', sortable: false},
            ],
            resources     : [],
            editedIndex   : -1,
            editedItem    : {
                title   : '',
                group_id: 0,
                users   : [],
                tbas    : [],
            },
            defaultItem   : {
                title   : '',
                group_id: 0,
                users   : [],
                tbas    : [],
            },
            reviewList    : [],
            lessonList    : [],
            lessonInit    : [],
            selectedReview: [],
            selectedLesson: [],
        }
    },

    computed: {
        formTitle() {
            // 新增排除以選的名單
            // 編輯顯示各組可選的名單
            if (this.editedIndex === -1) {
                this.lessonList = _.filter(this.lessonInit, (v) => {
                    if (v.division_id === null) {
                        return v;
                    }
                });
            } else {
                this.lessonList = _.filter(this.lessonInit, (v) => {
                    if (v.division_id === this.editedItem.id || v.division_id === null) {
                        return v;
                    }
                });
            }
            return this.editedIndex === -1 ? this.$t('division.create') : this.$t('division.edit')
        },
        likesAllReview() {
            return this.selectedReview.length === this.reviewList.length
        },
        likesSomeReview() {
            return this.selectedReview.length > 0 && !this.likesAllReview
        },
        icon() {
            if (this.likesAllReview) return 'mdi-close-box'
            if (this.likesSomeReview) return 'mdi-minus-box'
            if (this.likesAllLesson) return 'mdi-close-box'
            if (this.likesSomeLesson) return 'mdi-minus-box'
            return 'mdi-checkbox-blank-outline'
        },
        likesAllLesson() {
            return this.selectedLesson.length === this.lessonList.length
        },
        likesSomeLesson() {
            return this.selectedLesson.length > 0 && !this.likesAllLesson
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },

    created() {
        this.$store.dispatch("updateLoading", true);
        this.initialize()
    },

    methods: {
        initialize() {
            let _this = this
            let group_id = this.$store.state.Auth.user.group_id
            let url = `/api/group/division/${group_id}`;
            axios.get(url).then((response) => {
                let result = response.data;
                _this.resources = result.divisions
                _this.reviewList = result.users
                _this.lessonList = result.tbas
                _this.lessonInit = result.tbas
                _this.$store.dispatch("updateLoading", false);
                // console.log(result.tbas)
            }).catch((e) => {
                console.log(e.message)
            });

        },

        editItem(item) {
            this.editedIndex = this.resources.indexOf(item)
            this.editedItem = Object.assign({}, item)
            // 已選取的評審
            _.forEach(item.users, (v) => {
                this.selectedReview.push(v.id);
            });
            // 已選取的課例
            _.forEach(item.tbas, (v) => {
                this.selectedLesson.push(v.content_id);
            });
            this.dialog = true
        },

        deleteItem(item) {
            this.$store.dispatch("updateLoading", true);
            this.editedIndex = this.resources.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm() {
            let _this = this
            this.$store.dispatch("updateLoading", true);
            let url = `/api/group/division/`;
            axios.delete(url + _this.editedItem.id).then((response) => {
                this.initialize();
            }).catch((e) => {
                // console.log(e.message);
            })
            this.$store.dispatch("updateLoading", true);
            // Object.assign(this.resources[this.editedIndex], data)
            this.resources.splice(this.editedIndex, 1);
            this.closeDelete()

        },
        close() {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.lessonList = this.lessonInit;
                this.selectedLesson = [];
                this.selectedReview = [];
                this.editedIndex = -1
            })
        },

        closeDelete() {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            let _this = this
            this.$store.dispatch("updateLoading", true);
            let group_id = this.$store.state.Auth.user.group_id
            let url = `/api/group/division/`;

            // 轉換格式
            const obj = {
                tbas    : this.selectedLesson,
                users   : this.selectedReview,
                group_id: group_id
            };
            // 合併格式
            let data = Object.assign({}, this.editedItem, obj);
            if (this.editedIndex > -1) {
                axios.put(url + _this.editedItem.id, data).then((response) => {
                    this.initialize();
                    this.$store.dispatch("updateLoading", false);
                }).catch((e) => {
                    // console.log(e.message);
                })
                // Object.assign(this.resources[this.editedIndex], data)
            } else {
                axios.post(url, data).then((response) => {
                    this.initialize();
                    this.$store.dispatch("updateLoading", false);
                }).catch((e) => {
                    console.log(e.message);
                })
                // this.resources.push(data)
            }
            this.close()
        },
        toggleByLesson() {
            this.$nextTick(() => {
                if (this.likesAllLesson) {
                    this.selectedLesson = []
                } else {
                    this.selectedLesson = _.map(this.lessonList, 'content_id')
                }
            })
        },
        toggleByReview() {
            this.$nextTick(() => {
                if (this.likesAllReview) {
                    this.selectedReview = []
                } else {
                    this.selectedReview = _.map(this.reviewList, 'id')
                }
            })
        },
    },
}
</script>
<style lang="scss" scoped>
.long-text {
    white-space: nowrap;
    max-width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
