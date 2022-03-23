<template>
    <v-app>
        <v-snackbar v-model="show" :color="color" top text timeout="2000">
            {{ message }}
            <template v-slot:action="{ attrs }">
                <v-btn text v-bind="attrs" @click="show = false">
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>
<script>
export default {
    data() {
        return {
            show   : false,
            message: "",
            color  : ""
        };
    },

    created() {
        this.$store.subscribe((mutation, state) => {
            if (mutation.type === "snackbar/showMessage") {
                this.message = state.snackbar.content;
                this.color = state.snackbar.color;
                this.show = true;
            }
        });
    }
};
</script>

<style scoped>

</style>
