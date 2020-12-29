<template lang="pug">
v-app(dark)
    v-app-bar(app, dark, dense, height="0")
        template(#extension)
            v-tabs(fixed-tabs, dark)
                v-tab(to="/") Home
                v-tab(to="/manage") Manage

    v-main.align-center
        v-scale-transition(mode="out-in" )
            router-view
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { Route } from 'vue-router';

@Component
export default class extends Vue {
}
</script>

<style lang="scss" scoped>
.v-tabs {
    flex: unset;
}
.v-main {
    background-color: #29539b;
    background-image: linear-gradient(315deg, #29539b 0%, #1e3b70 74%);
}
</style>

<style lang="scss">
::-webkit-scrollbar {
    display: none;
}
</style>
