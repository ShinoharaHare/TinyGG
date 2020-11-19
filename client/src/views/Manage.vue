<template lang="pug">
div
    v-card.mx-auto.pa-4(width="800", max-height="600")
        v-data-table(:headers="headers", :items="items")
            template(#top)
                v-toolbar(flat)
                    v-toolbar-title Shortened URLs
                    v-divider.mx-4(vertical inset )
                        

            template(#item.actions="{ item }")
                v-icon.mr-2(small) mdi-pencil
                v-icon(small) mdi-delete
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component
export default class extends Vue {
    headers = [
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' },
        { text: 'Actions', value: 'actions', sortable: false }
    ]

    items = [
        {
            key: 'google',
            original: 'https://www.google.com'
        },
        {
            key: 'M6pcIv',
            original: 'https://tronclass.com.tw'
        },
        {
            key: 'U6r23l',
            original: 'https://www.youtube.com'
        },
        {
            key: 'ntou',
            original: 'https://www.ntou.edu.tw'
        }
    ]
}
</script>