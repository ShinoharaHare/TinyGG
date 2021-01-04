<template lang="pug">
v-data-table(:headers="headers", :items="items", :loading="loading" height="300" )
    template(#item.original="{ item }")
        v-tooltip(top)
            template(#activator="{ on, attrs }")
                a.ellipsis(
                    style="max-width: 200px",
                    target="_blank",
                    :href="item.original.url",
                    v-bind="attrs",
                    v-on="on"
                ) {{ item.original.url }}
            span
                v-img(:src="item.original.thumbnail", max-width="350")

    template(#item.title="{ item }")
        v-tooltip(top, max-width="300")
            template(#activator="{ on, attrs }")
                span.ellipsis(
                    style="max-width: 100px",
                    v-bind="attrs",
                    v-on="on"
                ) {{ decode(item.original.title) }}
            span
                span {{ decode(item.original.title) }}
                v-divider.my-1.white
                span {{ decode(item.original.summary) }}

    template(#item.favicon="{ item }")
        v-tooltip(top)
            template(#activator="{ on, attrs }")
                v-avatar(size="24", v-bind="attrs", v-on="on")
                    v-img(:src="item.original.favicon")
            span {{ item.original.favicon }}

    template(#item.actions="{ item }")
        v-icon(color="primary", @click="copy(item.key)") mdi-clipboard-text-multiple-outline
        v-icon.ml-2(color="error", @click="showDialog(item)") mdi-trash-can-outline

        DeleteDialog(v-model="dialog", :item="selected", @delete="onDelete")
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch, Emit } from 'vue-property-decorator'
import { decodeHTMLEntities, copyToClipboard } from '@/utils'
import { sendMessage } from '@/sysmsg'

import DeleteDialog from '@/components/DeleteDialog.vue'


@Component({ components: { DeleteDialog } })
export default class extends Vue {
    @Prop({ default: () => [] })
    items!: any[]

    @Prop({ default: false })
    loading!: boolean

    dialog = false
    selected: any = {}

    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Title', value: 'title' },
        { text: 'Favicon', value: 'favicon', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' }
    ]

    decode(str: string) {
        return decodeHTMLEntities(str)
    }

    copy(key: string) {
        let text = `${location.protocol}//${location.host}/${key}`
        // navigator.clipboard.writeText(text)
        copyToClipboard(text)
        sendMessage('Shortened URL copied!')
    }

    showDialog(item: any) {
        this.dialog = true
        this.selected = item
    }

    onDelete(item: any) {
        this.update(this.items.filter(x => x !== item))
    }

    @Emit()
    update(nItems: any[]) {
    }
}
</script>
