<template lang="pug">
v-card.pb-12(flat, color="transparent")
    .mx-auto(style="width: 800px")
        .white--text.text-center.mb-12
            v-avatar.mr-2(tile, size="200")
                v-img(
                    transition="slide-y-transition",
                    :src="require('@/assets/web-search-engine.svg')"
                )
            span.text-h2 TinyGG
                .text-h5 Make URLs Shorter

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
                    counter,
                    placeholder="Suffix",
                    prepend-icon="mdi-arrow-right",
                    append-icon="mdi-refresh",
                    maxlength="10",
                    :prefix="prefix",
                    :rules="[requiredRule, suffixRule]",
                    v-model="key",
                    @click:append="randomKey"
                )

            v-card-actions
                v-spacer
                v-btn(
                    outlined,
                    color="success",
                    :disabled="!valid",
                    :loading="shortening",
                    @click="shorten"
                ) Shorten
                v-spacer

        v-card.mt-2.pa-4
            DataTable(
                :items="items",
                :loading="loading",
                @update="items = $event"
            )

    RankTable
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { generate } from 'generate-password'
import { sendMessage } from '@/sysmsg'

import DataTable from '@/components/DataTable.vue'
import RankTable from '@/components/RankTable.vue'


@Component({ components: { DataTable, RankTable } })
export default class extends Vue {
    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' }
    ]

    items: any[] = []

    valid = false

    key = ''
    original = ''

    loading = false

    shortening = false

    get prefix() {
        return `${location.protocol}//${location.host}/`
    }

    requiredRule(v: string) {
        return !!v || 'Required'
    }

    urlRule(v: string) {
        return /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/.test(v) || 'Invalid URL'
    }

    suffixRule(v: string) {
        return /^[^\\\/\?\:]+$/.test(v) || 'Invalid Characters'
    }

    randomKey() {
        this.key = generate({
            length: 6,
            numbers: true,
            excludeSimilarCharacters: true
        })
    }

    async shorten() {
        this.shortening = true
        let { status, data } = await axios.post('/api/shortened', {
            key: this.key,
            original: this.original
        })

        this.shortening = false

        switch (status) {
            case 201:
                this.original = ''
                this.key = ''
                this.items.push(data)
                sendMessage('URL shortened!')
                break

            case 409:
                sendMessage('Suffix duplicated!', { color: 'error' })
                break
        }
    }

    async getData() {
        this.loading = true
        let { status, data } = await axios.get('/api/shortened', {
            params: {
                filter: 'ip'
            }
        })
        this.loading = false

        switch (status) {
            case 200:
                this.items.push(...data)
                break
        }
    }

    mounted() {
        this.getData()
    }
}
</script>


<style lang="scss" scoped>
.rank-table {
    position: absolute;
    top: 50px;
    right: 100px;
}
</style>