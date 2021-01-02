<template lang="pug">
v-app(dark)
    v-app-bar(app, dark, dense, height="0")
        template(#extension)
            span(style="position: absolute")
                v-avatar(style="float: left")
                    v-img(
                        transition="fab-transition",
                        :src="require('@/assets/hen.svg')"
                    )
                h1(style="float: left") TinyGG

            v-tabs(fixed-tabs, dark)
                v-tab(to="/")
                    v-icon.mr-2 mdi-home
                    | Home
                v-tab(to="/manage") 
                    v-icon.mr-2 mdi-cogs
                    | Manage

    v-main.align-center
        transition(:name="transition", mode="out-in")
            router-view

    SystemMessage
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'

import SystemMessage from '@/components/SystemMessage.vue'


@Component({ components: { SystemMessage } })
export default class extends Vue {
    transition = ''

    @Watch('$route')
    onRouteChange(to: any, from: any) {
        switch (to.name) {
            case 'Home':
                this.transition = 'scroll-x-reverse-transition'
                break

            case 'Manage':
                this.transition = 'scroll-x-transition'
                break
        }
    }
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

.logo {
    position: absolute;
}
</style>

<style lang="scss">
::-webkit-scrollbar {
    display: none;
}

.ellipsis {
    display: inline-block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
