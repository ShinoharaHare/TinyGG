<template lang="pug">
v-data-table.rank-table(
    dark,
    no-footer,
    disable-filtering,
    disable-pagination,
    disable-sort,
    hide-default-footer,
    :headers="headers",
    :items="items",
    :loading="loading"
) 
    template(#top)
        v-toolbar.rounded(flat, color="#1e1e1e")
            v-toolbar-title Click Through Rate

    template(#item.rank="{ item }")
        v-avatar(tile, size="32", v-if="rank(item) <= 3")
            v-img(transition="fab-transition", :src="trophy(item)")

        span(v-else) {{ rank(item) }}
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component
export default class extends Vue {
    headers: any[] = [
        { text: 'Rank', value: 'rank', align: 'center' },
        { text: 'Creator(IP)', value: 'IP' },
        { text: 'Clicks', value: 'clicks' }
    ]

    items: any[] = []
    loading = false

    async getData() {
        this.loading = true
        let { status, data } = await axios.get('/api/rank')
        this.loading = false
        switch (status) {
            case 200:
                this.items = data
                break
        }
    }

    rank(item: any) {
        return this.items.indexOf(item) + 1
    }

    trophy(item: any) {
        let rank = this.rank(item)
        return require(`@/assets/trophy_${rank}.svg`)
    }

    mounted() {
        this.getData()
    }
}
</script>
