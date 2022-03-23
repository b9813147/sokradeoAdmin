<template>
    <div>
        <v-list-item link to="/global/recommendedVideo">
            <v-list-item-action>
                <v-icon>mdi-view-dashboard</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('global_recommended_video') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/global/notification">
            <v-list-item-action>
                <v-icon>mdi-bell</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('send_notification') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/global/district">
            <v-list-item-action>
                <v-icon>mdi-school</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('event.districtManage') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/global/group">
            <v-list-item-action>
                <v-icon>mdi-cogs</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('channel_manager') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item link to="/global/event">
            <v-list-item-action>
                <v-icon>mdi-calendar-alert</v-icon>
            </v-list-item-action>
            <v-list-item-content>
                <v-list-item-title>{{ $t('event.eventManage') }}</v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </div>
</template>

<script>
export default {
    name: "globalSidebar"
}
</script>

<style scoped>

</style>
