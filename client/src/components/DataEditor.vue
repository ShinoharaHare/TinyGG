<template lang="pug">
v-dialog(persistent, max-width="600", v-model="value")
    v-card-text.white--text.text-center.text-h6.ellipsis(
        style="background: #1e1e1e;"
    )
        | {{ item.key }}
        v-icon(color="white") mdi-arrow-right
        |
        | {{ item.original.url }}

    v-tabs.table-tabs(dark, flat, vertical, v-model="tab") 
        v-tab 
            v-icon.mr-2 mdi-link
            | Shortened
        v-tab-item.pa-4
            v-text-field(dense, outlined, label="Key", v-model="temp.key")
            div
                v-label Original ID
                a.ml-4(@click="tab = 1") {{ item.original.ID }}
                    v-icon.ml-2.mb-1(color="primary") mdi-pencil-outline

            .mt-4
                v-label Creator ID
                a.ml-4(@click="tab = 2") {{ item.creator.ID }}
                    v-icon.ml-2.mb-1(color="primary") mdi-pencil-outline

        v-tab
            v-icon.mr-2 mdi-information-variant
            | Brief
        v-tab-item.pa-4
            .mb-4
                v-label ID
                span.ml-4 {{ item.original.ID }}

            v-text-field(
                dense,
                outlined,
                label="URL",
                v-model="temp.original.url"
            )
            v-text-field(
                dense,
                outlined,
                label="Title",
                v-model="temp.original.title"
            )
            v-text-field(
                dense,
                outlined,
                label="Icon",
                v-model="temp.original.favicon"
            )
                template(#prepend)
                    v-tooltip(bottom)
                        template(#activator="{ on, attrs }")
                            v-icon(v-bind="attrs", v-on="on") mdi-eye

                        span
                            v-img(
                                ref="favicon",
                                max-width="350",
                                :src="temp.original.favicon",
                                @error="error"
                            )
            v-text-field(
                dense,
                outlined,
                label="Thumbnail",
                v-model="temp.original.cover"
            )
                template(#prepend)
                    v-tooltip(bottom)
                        template(#activator="{ on, attrs }")
                            v-icon(v-bind="attrs", v-on="on") mdi-eye

                        span
                            v-img(:src="temp.original.cover", max-width="350")

            v-textarea(
                outlined,
                label="Summary",
                height="70",
                v-model="temp.original.summary"
            )

        v-tab 
            v-icon.mr-2 mdi-ip-network
            | Creator
        v-tab-item.pa-4
            .mb-4
                v-label ID
                span.ml-4 {{ item.creator.ID }}

            v-text-field(
                dense,
                outlined,
                label="IP",
                v-model="temp.creator.IP"
            )

        v-spacer

        v-card-actions
            v-spacer
            v-btn(outlined, color="error", @click="dialog = true") Delete

            v-btn(outlined, @click="$emit('input', false)") Cancel

            v-btn(
                outlined,
                color="light-blue lighten-1",
                :loading="saving",
                @click="save"
            ) Save
            v-spacer

        DeleteDialog(v-model="dialog", :item="item", @delete="this.delete")
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch, Emit } from 'vue-property-decorator'
import { sendMessage } from '@/sysmsg'

import DeleteDialog from '@/components/DeleteDialog.vue'

@Component({ components: { DeleteDialog } })
export default class extends Vue {
    @Prop({ default: false })
    value!: boolean

    @Prop({ required: true })
    item!: any

    tab = 0
    temp: any = {}
    dialog = false

    saving = false

    error(e: any) {
        console.log(this.$refs.favicon)
    }

    async save() {
        this.saving = true
        let { status, data } = await axios.put(`/api/shortened/${this.item.key}`, this.temp)
        this.saving = false

        switch (status) {
            case 200:
                sendMessage('Data Updated')
                this.update(data)
                break

            case 404:
                sendMessage('The Record Does Not Exist', { color: 'error' })
                break

            default:
                sendMessage('Failed To Update The Record', { color: 'error' })
        }
    }

    @Emit()
    delete(item: any) {
    }

    @Emit()
    update(item: any) {
    }

    @Watch('item')
    onItemUpdate() {
        this.temp = Object.assign({}, this.item)
        this.temp.original = Object.assign({}, this.item.original)
        this.temp.creator = Object.assign({}, this.item.creator)
    }
}
</script>

<style lang="scss">
.table-tabs {
    .v-tabs-bar {
        border-radius: 0;
    }
    .v-tabs-items {
        // min-height: 420px;
    }
}
</style>