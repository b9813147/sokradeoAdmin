<template>
    <validation-observer
        ref="observer"
        v-slot="{ invalid }"
    >
        <form @submit.prevent="submit">
            <validation-provider
                v-slot="{ errors }"
                name="title"
                rules="required|max:300"
            >
                <v-text-field
                    v-model="editedItem.title"
                    :counter="300"
                    :error-messages="errors"
                    :label="$t('notification.title')"
                    required
                ></v-text-field>
            </validation-provider>
            <v-textarea
                clear-icon="mdi-close-circle"
                :label="$t('notification.content')"
                :value="defaultItem.content"
                v-model="editedItem.content"
                required
            ></v-textarea>
            <validation-provider
                v-slot="{ errors }"
                name="channel_id"
                rules="required|numeric"
            >
                <v-text-field
                    v-model="editedItem.channel_id"
                    :error-messages="errors"
                    :label="$t('notification.channel_id')"
                    required
                ></v-text-field>
            </validation-provider>
            <validation-provider
                v-slot="{ errors }"
                name="channel_ids"
            >
                <v-text-field
                    v-model="editedItem.channel_ids"
                    :error-messages="errors"
                    :label="$t('notification.channel')"

                ></v-text-field>
            </validation-provider>
            <validation-provider
                v-slot="{ errors }"
                name="district_ids"
            >
                <v-text-field
                    v-model="editedItem.district_ids"
                    :error-messages="errors"
                    :label="$t('notification.district')"

                ></v-text-field>
            </validation-provider>
            <validation-provider
                v-slot="{ errors }"
                name="teamModel_ids"
            >
                <v-text-field
                    v-model="editedItem.teamModel_ids"
                    :error-messages="errors"
                    :label="$t('notification.team_model_id')"

                ></v-text-field>
            </validation-provider>
            <validation-provider
                v-slot="{ errors }"
                name="status"
                rules="numeric"
            >
                <v-text-field
                    v-model="editedItem.status"
                    :error-messages="errors"
                    :label="$t('notification.status')"
                ></v-text-field>
            </validation-provider>
            <v-text-field
                v-model="editedItem.url"
                label="url">
            </v-text-field>

            <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="editedItem.validity"
                        label="Date"
                        hint="MM/DD/YYYY"
                        persistent-hint
                        prepend-icon="mdi-calendar"
                        v-bind="attrs"
                        @blur="date = parseDate(dateFormatted)"
                        v-on="on"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="editedItem.validity"
                    no-title
                    @input="menu = false"
                ></v-date-picker>
            </v-menu>
            <v-row>
                <v-checkbox
                    v-model="editedItem.top"
                    :label="$t('notification.top')"
                ></v-checkbox>
                <v-checkbox
                    v-model="editedItem.isOperating"
                    :label="$t('notification.isOperating')"
                ></v-checkbox>
                <v-checkbox
                    v-model="editedItem.isReview"
                    :label="$t('notification.isReview')"
                ></v-checkbox>
            </v-row>
            <v-btn
                class="mr-4"
                type="submit"
                :disabled="invalid"
            >
                {{ $t('notification.submit') }}
            </v-btn>
            <v-btn @click="clear">
                {{ $t('notification.clear') }}
            </v-btn>
        </form>
    </validation-observer>
</template>


<script>
import {required, digits, email, max, regex, numeric}                       from 'vee-validate/dist/rules'
import {extend, ValidationObserver, ValidationProvider, setInteractionMode} from 'vee-validate'

setInteractionMode('eager')

extend('digits', {
    ...digits,
    message: '{_field_} needs to be {length} digits. ({_value_})',
})
extend('numeric', {
    ...numeric,
    message: '{_field_} must be  numeric. ',
})

extend('required', {
    ...required,
    message: '{_field_} can not be empty',
})

extend('max', {
    ...max,
    message: '{_field_} may not be greater than {length} characters',
})

extend('regex', {
    ...regex,
    message: '{_field_} {_value_} does not match {regex}',
})

extend('email', {
    ...email,
    message: 'Email must be valid',
})

export default {
    name      : "GlobalNotification",
    components: {
        ValidationProvider,
        ValidationObserver,
    },
    data      : vm => ({
        editedItem   : {
            title        : null,
            content      : null,
            channel_id   : null,
            channel_ids  : null,
            district_ids : null,
            top          : false,
            status       : null,
            validity     : null,
            url          : null,
            isOperating  : false,
            isReview     : false,
            teamModel_ids: null,

        },
        defaultItem  : {
            title        : null,
            content      : null,
            channel_id   : null,
            channel_ids  : null,
            district_ids : null,
            top          : false,
            status       : null,
            validity     : new Date().toISOString().substr(0, 10),
            url          : null,
            isOperating  : false,
            isReview     : false,
            teamModel_ids: null
        },
        date         : new Date().toISOString().substr(0, 10),
        dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
        menu         : false,
    }),

    computed: {
        computedDateFormatted() {
            return this.formatDate(this.date)
        },
    },

    watch: {
        date(val) {
            this.dateFormatted = this.formatDate(this.date)
        },
    },

    methods: {
        submit() {
            let _this = this;
            let url = '/api/global/notification'
            let result = _this.editedItem
            axios.post(url, result).then((response) => {
                if (response.status === 201) {
                    this.$q.info(this.$t('notification.success'));
                } else {
                    this.$q.error(this.$t('notification.fail'))
                }
            }).catch((error) => {
                this.$q.error(this.$t('notification.fail'))
            });
            this.$refs.observer.validate()
        },
        clear() {
            this.editedItem = this.defaultItem;
            this.$refs.observer.reset()
        },
        formatDate(date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${month}/${day}/${year}`
        },
        parseDate(date) {
            if (!date) return null

            const [month, day, year] = date.split('/')
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
        },
    },
};
</script>

<style scoped>

</style>
