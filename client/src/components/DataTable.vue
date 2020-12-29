<template lang="pug">
v-data-table(:headers="headers", :items="items")
    template(#item.original="{ item }")
        v-tooltip(top)
            template(#activator="{ on, attrs }")
                a(
                    target="_blank",
                    :href="item.original",
                    v-bind="attrs",
                    v-on="on"
                ) {{ item.original }}
            span
                v-img(:src="item.thumbnail", max-width="350")

    template(#item.title="{ item }")
        v-tooltip(top)
            template(#activator="{ on, attrs }")
                span(v-bind="attrs", v-on="on") {{ item.title }}
            span {{ item.summary }}

    template(#item.favicon="{ item }")
        v-tooltip(top)
            template(#activator="{ on, attrs }")
                v-avatar(size="24", v-bind="attrs", v-on="on")
                    v-img(:src="item.favicon")
            span {{ item.favicon }}

    template(#item.actions="{ item }")
        v-icon.mr-2(color="primary", @click="copy(item.key)") mdi-content-copy

        v-snackbar(bottom, :timeout="1500", v-model="snackbar") Shortened URL Copied!
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator'

@Component
export default class extends Vue {
    @Prop()
    items!: any[]

    snackbar = false

    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Title', value: 'title' },
        { text: 'Favicon', value: 'favicon', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' }
    ]

    copy(key: string) {
        let text = `${location.protocol}//${location.host}/${key}`
        navigator.clipboard.writeText(text)
        this.snackbar = true
    }
}
</script>