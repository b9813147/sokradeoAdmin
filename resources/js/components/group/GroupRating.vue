<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources.ratings"
            sort-by="area"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('rating_editor') }}</v-toolbar-title>
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
                                            <v-text-field v-model="editedItem.name" :label="$t('rating_name')" :error-messages="error.name"></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
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
                    :component-data="getComponentData()"
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
                        <td>{{ item.type }}</td>
                        <td>{{ item.name }}</td>
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
                <v-icon
                    small
                    v-if="item.total <= 0"
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import draggable                                                                               from 'vuedraggable'
import {apiGetRatings, apiUpdateRating, apiUpdateSortRating, apiDeleteRating, apiCreateRating} from '../../apis/rating';

export default {
    components: {
        draggable,
    },
    data      : () => ({
        dialog     : false,
        headers    : [],
        error      : [],
        resources  : {
            ratings: []
        },
        maxOrder   : 1,
        editedIndex: -1,
        editedItem : {
            id  : null,
            type: '',
            name: '',

        },
        defaultItem: {
            id  : null,
            type: '',
            name: '',
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('rating_create') : this.$t('rating_editor')
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
            _this.$store.dispatch("updateLoading", true);
            apiGetRatings(this.$store.state.Auth.user.group_id).then((resource) => {
                let result = resource.data.filter((v) => {
                    return v.type > 0;
                });
                _this.maxOrder = _.maxBy(result, 'type').type;
                _this.resources.ratings = result;
            });
            _this.$store.dispatch("updateLoading", false);
            this.close()
        },

        defaultColumns() {
            let _this = this;
            const header = [
                {text: '#', value: 'type', sortable: false},
                {text: this.$t('rating_order'), value: 'key'},
                {text: this.$t('rating_name'), align: 'start', sortable: false, value: 'name'},
                {text: this.$t('using'), value: 'total'},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ];

            _this.headers = header;
        },

        editItem(item) {
            this.error = [];
            this.editedIndex = this.resources.ratings.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            let _this = this;
            if (confirm('Are you sure you want to delete this item?')) {
                apiDeleteRating(item.id)
                    .then(response => {
                        _this.initialize();
                    })
                    .catch(error => {
                        if (error.response.status === 422) alert(error.response.data)
                    });
            }
        },

        close() {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
            this.error = [];
        },

        save() {
            let _this = this;
            _this.$store.dispatch("updateLoading", true);
            let groupId = this.$store.state.Auth.user.group_id;
            let params = {
                name     : _this.editedItem.name,
                type     : _this.editedIndex === -1 ? _this.maxOrder + 1 : parseInt(_this.editedItem.type),
                groups_id: groupId,
            }

            if (this.editedIndex > -1) {
                apiUpdateRating(this.editedItem.id, params)
                    .then(response => {
                        this.initialize();

                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;

                    });
                _this.$store.dispatch("updateLoading", false);

            } else {
                _this.dialog = true;
                apiCreateRating(params)
                    .then(response => {
                        _this.initialize();
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;
                    })
                _this.$store.dispatch("updateLoading", false);
            }
        },
        sort(e) {
            let _this = this;
            let params = {
                type: e.moved.newIndex + 1,
            }
            apiUpdateSortRating(e.moved.element.id, params)
                .then(response => {
                    _this.initialize();
                })
                .catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;
                });
        },
        getComponentData() {
            return {
                on   : {
                    change: (e) => {
                        // console.log(e)
                    },
                    end   : (e) => {
                        // console.log(e)
                    },
                },
                attrs: {
                    wrap: true
                },
                props: {
                    value: this.activeNames
                }
            };
        }

    },
}
</script>


<style scoped>

</style>
