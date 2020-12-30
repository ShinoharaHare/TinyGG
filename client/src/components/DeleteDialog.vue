<template lang="pug">
v-dialog(max-width="300", :value="value", @input="$emit('input', $event)")
    v-card
        v-card-title 
            v-icon.mr-2(color="error") mdi-alert-circle-outline
            | Confirm Deletion
        v-card-text.text-center
            | Are You Sure You Want To Delete This ?
        v-card-actions
            v-spacer
            v-btn(
                outlined,
                color="error",
                :loading="loading",
                @click="this.delete"
            ) Delete
            v-btn(outlined, @click="$emit('input', false)") Cancel
            v-spacer
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch } from 'vue-property-decorator'
import { sendMessage } from '@/sysmsg'

@Component
export default class extends Vue {
    @Prop({ default: false })
    value!: boolean

    @Prop({ required: true })
    item!: any

    loading = false

    async delete() {
        this.loading = true
        let { status } = await axios.delete(`/api/shortened/${this.item.key}`)
        this.loading = false

        switch (status) {
            case 204:
                this.$emit('delete')
                sendMessage('Deletion succeeded')
                break;

            case 404:
                this.$emit('fail')
                sendMessage('Failed to delete')
                break;
        }

        this.$emit('input', false)
    }
}
</script>