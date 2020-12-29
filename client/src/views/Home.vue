<template lang="pug">
.mx-auto(style="width: 800px")
    v-card.pa-4
        v-form(v-model="valid")
            v-text-field(
                clearable,
                prepend-icon="mdi-link",
                placeholder="URL to be shortened",
                :rules="[requiredRule, urlRule]",
                v-model="original"
            )
            v-text-field(
                clearable,
                placeholder="Suffix",
                prepend-icon="mdi-arrow-right",
                append-icon="mdi-refresh",
                :prefix="prefix",
                :rules="[requiredRule]",
                v-model="key",
                @click:append="randomKey"
            )

        v-card-actions
            v-spacer
            v-btn(
                outlined,
                color="success",
                :disabled="!valid",
                @click="shorten"
            ) Shorten
            v-spacer

    v-card.mt-2.pa-4
        DataTable(:items="items")
    //- v-data-table(:headers="headers", :items="items")
    //-     template(#item.actions="{ item }")
    //-         v-icon.ml-2(color="primary", @click="copy(item.key)") mdi-content-copy

    //-     template(#item.original="{ item }")
    //-         a(target="_blank", :href="item.original") {{ item.original }}
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { generate } from 'generate-password'

import DataTable from '@/components/DataTable.vue'


@Component({ components: { DataTable } })
export default class extends Vue {
    headers = [
        { text: '', value: 'actions', sortable: false },
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

    valid = false

    key = ''
    original = ''


    get prefix() {
        return `${location.protocol}//${location.host}/`
    }

    requiredRule(v: string) {
        return !!v || 'Required'
    }

    urlRule(v: string) {
        return /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/.test(v) || 'Invalid URL'
    }

    randomKey() {
        this.key = generate({
            length: 6,
            numbers: true,
            excludeSimilarCharacters: true
        })
    }

    async shorten() {
        let res = await axios.post('/api/shorten.php', {
            key: this.key,
            original: this.original
        })
    }
}
</script>