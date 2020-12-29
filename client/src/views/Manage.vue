<template lang="pug">
.mx-auto(style="width: 800px")
    v-card
        v-card-text
            v-overflow-btn.mx-12(label="Filter", :items="filters")

            //- div
            //-     v-text-field(outlined label="ID")
            //-     v-text-field(outlined label="IPv4")
            //-     v-text-field(outlined label="IPv6")

        v-card-actions
            v-spacer
            v-btn.mx-auto(outlined, color="primary") Query
            v-spacer

    v-card.mt-4(max-height="600")
        v-card-text
            v-data-table(:headers="headers", :items="items")
                //- template(#top)
                //-     v-toolbar(flat)
                //-         v-toolbar-title Shortened URLs
                //-         v-divider.mx-4(vertical, inset)
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
                    v-icon.mr-2(@click="showEditor(item)") mdi-pencil

        DataEditor(v-model="editor.show", :item="editor.item")
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import DataEditor from '@/components/DataEditor.vue'

@Component({ components: { DataEditor } })
export default class extends Vue {
    editor = {
        show: false,
        item: null
    }

    filters = [
        { text: 'All', value: 'all' },
        { text: 'Creator', value: 'creator' },
    ]

    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Title', value: 'title' },
        { text: 'Favicon', value: 'favicon', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' }
    ]

    items = [
        {
            key: 'google',
            title: 'Google',
            original: 'https://www.google.com',
            favicon: 'https://tronclass.ntou.edu.tw/static/assets/images/favicon-a39daaa2.ico',
            thumbnail: 'https://q5n8c8q9.rocketcdn.me/wp-content/uploads/2019/09/YouTube-thumbnail-size-guide-best-practices-top-examples.png',
            summary: '在 YouTube 上盡情享受自己喜愛的影片和音樂、上傳原創內容，並與親朋好友和全世界觀眾分享你的影片。'
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

    showEditor(item: any) {
        this.editor.show = true
        this.editor.item = item
    }
}
</script>