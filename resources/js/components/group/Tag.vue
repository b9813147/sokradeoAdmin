<template>
    <v-card>
        <v-toolbar>
            <v-toolbar-title>
                {{ $t('tag.admin') }}
            </v-toolbar-title>
            <v-divider
                class="mx-4"
                inset
                vertical
            />
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon
                    large
                    @click="createItem(0)"
                >
                    mdi-plus
                </v-icon>
            </v-btn>
        </v-toolbar>
        <v-card-text>
            <v-treeview
                hoverable
                activatable
                :items="resources"
            >
                <template v-slot:append="{ item }">
                    <v-icon v-if="item.children"
                            small
                            class="mr-2"
                            @click="createItem(item.id)"
                    >
                        mdi-plus
                    </v-icon>
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
                <template v-slot:label="{ item }">
                    <span>{{ item.name }}</span>
                    <br>
                    <span class="blue-grey--text text--lighten-3">
                        {{ item.description }}
                    </span>
                </template>

            </v-treeview>
            <v-dialog v-model="dialog" max-width="900px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                    </v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field v-model="editedItem.name" :error-messages="error.name" :label="$t('tag.name')"/>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field v-if="!editedItem.hasOwnProperty('group_id')" v-model="editedItem.description" :error-messages="error.description" :label="$t('tag.description')"/>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer/>
                        <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                        <v-btn color="blue darken-1" text @click="save">{{ $t('submit') }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    data: () => ({
        dialog                    : false,
        headers                   : [],
        error                     : [],
        resources                 : [],
        editedIndex               : -1,
        editedItem                : {
            id         : null,
            type_id    : null,
            name       : null,
            description: null,
        },
        defaultItem               : {
            id         : null,
            type_id    : null,
            name       : null,
            description: null,
        },
        transformFormat           : {
            id     : null,
            type_id: null,
            content: {
                name       : {
                    cn       : null,
                    en       : null,
                    tw       : null,
                    customize: null
                },
                description: {
                    cn       : null,
                    en       : null,
                    tw       : null,
                    customize: null
                }
            }
        },
        transformFormatDefault    : {
            id     : null,
            type_id: null,
            content: {
                name       : {
                    cn       : null,
                    en       : null,
                    tw       : null,
                    customize: null
                },
                description: {
                    cn       : null,
                    en       : null,
                    tw       : null,
                    customize: null
                }
            }
        },
        typeTransformFormatDefault: {
            id      : null,
            group_id: null,
            content : {
                cn       : null,
                en       : null,
                tw       : null,
                customize: null
            }
        },
        typeTransformFormat       : {
            id      : null,
            group_id: null,
            content : {
                cn       : null,
                en       : null,
                tw       : null,
                customize: null
            }
        }
    }),

    computed: {
        formTitle() {
            if (this.editedItem.hasOwnProperty('group_id')) {
                return this.editedIndex === -1 ? this.$t('tag.create') : this.$t('tag.edit')
            }
            return this.editedIndex === -1 ? this.$t('tag.type.create') : this.$t('tag.type.edit')
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
    },

    created() {
        this.initialize()
    },

    methods: {
        initialize() {
            let _this = this;
            let url = `/api/group/${this.$store.state.Auth.user.group_id}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((resource) => {
                if (resource.status !== 200) return;
                let result = resource.data.tagTypes;
                if (result.length > 0) {
                    _this.resources = this.tagTypeFormat(result);
                }
                _this.$store.dispatch("updateLoading", false);
            });
            this.close()
        },
        editItem(item) {
            this.error = [];
            this.editedIndex = 0
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },
        createItem(item) {
            this.editedIndex = -1
            this.dialog = true
            if (item) {
                return this.editedItem = Object.assign({}, {type_id: item})
            }
            return this.editedItem = Object.assign({}, {group_id: this.$store.state.Auth.user.group_id})

        },
        deleteItem(item) {
            let _this = this;
            let url = `/api/group/tag/${item.id}`
            if (item.hasOwnProperty('children')) {
                url = `/api/group/tag/type/${item.id}`
            }
            if (confirm('Are you sure you want to delete this item?')) {
                axios.delete(url)
                    .then(response => {
                        if (response.status !== 204) return;
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
                this.transformFormat = Object.assign({}, this.transformFormatDefault);
                this.typeTransformFormat = Object.assign({}, this.typeTransformFormatDefault);
                this.editedIndex = -1
            })


            this.error = [];
        },
        save() {
            let _this = this;
            let url = `/api/group/tag`
            // 格式轉換
            _this.transformFormat.content.name.customize = _this.editedItem.name
            _this.transformFormat.content.description.customize = _this.editedItem.description ?? ''
            _this.transformFormat.content = JSON.stringify(_this.transformFormat.content)
            _this.transformFormat.type_id = _this.editedItem.type_id
            _this.transformFormat.id = _this.editedItem.id


            _this.$store.dispatch("updateLoading", true);
            if (this.editedItem.hasOwnProperty('group_id')) {
                return this.saveType();
            }

            let params = _this.transformFormat;
            if (this.editedIndex > -1) {
                axios.put(url + '/' + _this.editedItem.id, params)
                    .then(response => {
                        if (response.status !== 204) return;
                        this.initialize();
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;
                    });

                _this.$store.dispatch("updateLoading", false);
                //
            } else {
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
        },
        saveType() {
            let _this = this;
            let url = `/api/group/tag/type`
            // 格式轉換
            _this.typeTransformFormat.content.customize = _this.editedItem.name
            _this.typeTransformFormat.content = JSON.stringify(_this.typeTransformFormat.content)
            _this.typeTransformFormat.group_id = _this.editedItem.group_id
            _this.typeTransformFormat.id = _this.editedItem.id

            let params = _this.typeTransformFormat;
            if (this.editedIndex > -1) {
                axios.put(url + '/' + _this.editedItem.id, params)
                    .then(response => {
                        if (response.status !== 204) return;
                        this.initialize();
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;
                    });

                _this.$store.dispatch("updateLoading", false);
                //
            } else {
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

        },
        tagTypeFormat(tagTypes) {
            let _this = this;
            return tagTypes.map(function (v) {
                let lang = _.last(_.words(_.toLower(localStorage.getItem('local'))))
                let content = JSON.parse(v.content)// tagType
                if (lang === 'us') lang = 'en'

                if (content.customize === null) {
                    return {
                        id      : v.id,
                        children: _this.tagFormat(v.tags, lang),
                        name    : _.find(content, function (v, k) {
                            if (k === lang) return v
                        }),
                        group_id: v.group_id
                    }
                } else {
                    return {
                        id      : v.id,
                        name    : content.customize,
                        children: _this.tagFormat(v.tags, lang),
                        group_id: v.group_id
                    }
                }

            })
        },
        tagFormat(tags, lang) {
            return tags.map(function (v) {
                let tagContent = JSON.parse(v.content)// tag
                let name = {};
                let description = {};

                // 標籤名稱
                if (tagContent.name.customize === null) {
                    name = {
                        name: _.find(tagContent.name, function (v, k) {
                            if (k === lang) return v
                        })
                    }
                } else {
                    name = {name: tagContent.name.customize}
                }
                // 標籤說明
                if (tagContent.description.customize === null) {
                    description = {
                        description: _.find(tagContent.description, function (v, k) {
                            if (k === lang) return v
                        })
                    }
                } else {
                    description = {description: tagContent.description.customize}
                }
                return _.merge({id: v.id, 'type_id': v.type_id}, name, description)
            })
        },

    },
}
</script>


<style lang="scss" scoped>
</style>
