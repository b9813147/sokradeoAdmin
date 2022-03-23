<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources.subjects"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('subject_editor') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
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
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.subject" :label="editedIndex === -1 ? $t('alias') : $t('subject')" :error-messages="error.subject" :disabled="editedIndex !== -1"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4" v-if="editedIndex !== -1">
                                            <v-text-field v-model="editedItem.alias" :error-messages="error.alias" :label="$t('alias')"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <!-- todo  待修改 -->
                                            <v-select :items="resources.area" item-text="text" item-value="editedItem.area" :error-messages="error.area" :label="$t('area')" v-model="editedItem.area" return-object required/>
                                            <!--                                        <pre>{{ resources.area }}</pre>-->
                                        </v-col>
                                    </v-row>
                                </v-container>
                                <!--                            <pre>{{ resources.area }}</pre>-->
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="save">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:body="props">
                <draggable
                    :list="props.items"
                    @change="sort"
                    tag="tbody"
                >
                    <tr
                        v-for="(item, index) in props.items"
                        :key="index"
                    >
                        <td>
                            <v-icon
                                small
                                class="page__grab-icon"
                            >
                                mdi-arrow-all
                            </v-icon>
                        </td>
                        <td>{{ item.order }}</td>
                        <td>{{ item.area }}</td>
                        <td>{{ item.subject }}</td>
                        <td>{{ item.alias }}</td>
                        <td>{{ item.total }}</td>
                        <td>
                            <v-icon
                                small
                                class="mr-2"
                                @click="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                v-if="item.total <= 0"
                                @click="deleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </td>
                    </tr>
                </draggable>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
                >
                    mdi-pencil
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    subject   : "Subject",
    data      : () => ({
        dialog     : false,
        headers    : [],
        error      : [],
        maxOrder   : 1,
        resources  : {
            area    : [],
            subjects: []
        },
        editedIndex: -1,
        editedItem : {
            id               : 0,
            area             : '',
            subject          : '',
            alias            : '',
            subject_fields_id: 0,

        },
        defaultItem: {
            id               : 0,
            area             : '',
            subject          : '',
            alias            : '',
            subject_fields_id: 0,
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('subject_create') : this.$t('subject_editor')
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
    },

    created() {
        this.initialize()
        this.defaultColumns();
    },

    methods: {
        initialize() {
            let _this = this;
            let url = `/api/group/subjects/${this.$store.state.Auth.user.group_id}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((resource) => {
                if (resource.status !== 200) return;
                let result = resource.data;
                _this.maxOrder = _.maxBy(result.subjects, 'order').order;
                _this.resources.subjects = result.subjects;

                _this.resources.area = result.area.map(function (v) {
                    return {text: v.type, value: v.id}
                })

                // 領域ID轉換中文
                _this.resources.subjects.map((v) => {
                    let area = _.find(result.area.filter(function (a) {
                        return a.id === v.subject_fields_id;
                    }))
                    v.area = area ? area.type : null;
                })
                _this.$store.dispatch("updateLoading", false);
            });

        },

        defaultColumns() {
            let _this = this;
            const header = [
                {text: '#', value: 'type', sortable: false},
                {text: this.$t('rating_order'), value: 'type', sortable: false},
                {text: this.$t('area'), value: 'area'},
                {text: this.$t('subject'), align: 'start', sortable: false, value: 'subject',},
                {text: this.$t('alias'), align: 'start', sortable: false, value: 'alias',},
                {text: this.$t('using'), value: 'total'},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ];

            this.headers = header;
        },

        editItem(item) {
            this.error = [];
            this.editedIndex = this.resources.subjects.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            let _this = this;
            let url = `/api/group/subjects/${item.id}`
            if (confirm('Are you sure you want to delete this item?')) {
                axios.delete(url)
                    .then(response => {
                        if (response.status === 204) {
                            _this.initialize();
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            alert(error.response.data)
                        }
                    });
            }
        },

        close() {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            let _this = this;
            let url = `/api/group/subjects/${this.editedItem.id}`
            let groupId = this.$store.state.Auth.user.group_id;

            _this.$store.dispatch("updateLoading", true);

            // 這裡傳得值是 subject_fields_id 轉換過得參數 area =
            let params = {
                subject_fields_id: _this.editedItem.area.value ? _this.editedItem.area.value : _this.editedItem.subject_fields_id,
                order            : _this.editedIndex === -1 ? _this.maxOrder + 1 : parseInt(_this.editedItem.order),
                alias            : _this.editedIndex === -1 ? _this.editedItem.subject ? _this.editedItem.subject.replace(/\ +/g, "") : _this.editedItem.alias : _this.editedItem.alias,
                groups_id        : groupId,
                subject          : _this.editedItem.subject.replace(/\ +/g, ""),

            }
            if (this.editedIndex > -1) {
                axios.put(url, params).then((response => {
                    if (response.status !== 204) return;
                    _this.initialize();

                })).catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;
                });
                _this.$store.dispatch("updateLoading", false);
            } else {
                let url = '/api/group/subjects/'
                _this.dialog = true;
                axios.post(url, params)
                    .then(response => {
                        if (response.status !== 204) return;
                        _this.initialize();
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;
                    })
                _this.$store.dispatch("updateLoading", false);
            }
            this.close()
        },

        sort(e) {
            let _this = this;
            let url = `/api/group/subjects/sort/${e.moved.element.id}`
            let params = {
                order: e.moved.newIndex + 1,
            }

            axios.put(url, params)
                .then(response => {
                    if (response.status !== 204) return;
                    _this.initialize();
                })
                .catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;
                });
        },
    },
}
</script>


<style scoped>

</style>
