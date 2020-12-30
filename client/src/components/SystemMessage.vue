<template lang="pug">
v-snackbar(
    app,
    v-model="show",
    :color="color",
    :timeout="timeout",
    :multi-line="multiline"
) {{ message }}
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator'
import { Bus, IMessageData } from '@/sysmsg'

@Component
export default class extends Vue {
    show = false
    message = ''
    timeout = 3000
    color = ''
    multiline = false

    onMessage(data: IMessageData) {
        this.color = data.color
        this.timeout = data.timeout
        this.message = data.message
        this.multiline = data.multiline
        this.show = true
    }

    created() {
        Bus.$on('message', this.onMessage)
    }
}
</script>

<style lang="scss" scoped>
</style>
