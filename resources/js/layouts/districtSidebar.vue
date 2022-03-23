<template>
    <div>
        <v-list-item link to="/district">
            <v-list-item-action>
                <v-icon>mdi-view-dashboard</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('district_manager') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/district/user">
            <v-list-item-action>
                <v-icon>mdi-account</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('assign_admin') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/district/channel">
            <v-list-item-action>
                <v-icon>mdi-library-video</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('classify_content') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/district/subject">
            <v-list-item-action>
                <v-icon>mdi-book</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('subject_manager') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/district/classification">
            <v-list-item-action>
                <v-icon>mdi-book</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('classify_subject') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/district/rating">
            <v-list-item-action>
                <v-icon>mdi-book</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('rating_manager') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </div>
</template>

<script>
export default {
    name: "districtSidebar"
}
</script>

<style scoped>

</style>
