<template lang="pug">
.mx-auto.wrapper(style="width: 600px")
    .header.white--text.text-center
        v-avatar(tile size="100" )
            v-img(
                transition="fab-transition"
                :src="require('@/assets/settings.svg')",
            )
        span.text-h2 Data Mangager

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
            v-data-table(
                :headers="headers",
                :items="items",
                :loading="loading"
            )
                template(#item.actions="{ item }")
                    v-icon.mr-2(@click="showEditor(item)") mdi-pencil

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

                        span {{ item.original.url }}

                template(#item.creator="{ item }")
                    td {{ item.creator.IP }}

        DataEditor(
            v-model="editor",
            :item="selected",
            @update="onItemUpdate",
            @delete="onItemDelete"
        )
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import DataEditor from '@/components/DataEditor.vue'

@Component({ components: { DataEditor } })
export default class extends Vue {
    filters = [
        { text: 'All', value: 'all' },
        { text: 'Creator', value: 'creator' },
    ]

    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' },
        { text: 'Creator', value: 'creator' }
    ]

    loading = false

    editor = false
    index: number | null = null
    items: any[] = []

    get selected() {
        if (this.index === null) {
            return {
                original: {},
                creator: {}
            }
        }
        return this.items[this.index]
    }

    showEditor(item: any) {
        this.editor = true
        this.index = this.items.indexOf(item)
    }

    onItemUpdate(item: any) {
        this.$set(this.items, this.index!, item)
    }

    onItemDelete(item: any) {
        this.index = null
        this.editor = false
        this.items.splice(this.index!, 1)
    }

    async getData() {
        this.loading = true
        let { status, data } = await axios.get('/api/shortened/all')
        this.loading = false

        switch (status) {
            case 200:
                this.items.push(...data)
                break
        }

        console.log(data)
    }

    mounted() {
        this.getData()
    }
}
</script>

<style lang="scss" scoped>
.wrapper {
    position: relative;
    top: -20px;
}

.header {
    margin-bottom: 80px;
}
</style>