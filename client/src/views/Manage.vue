<template lang="pug">
div(style="height: 100%; width: 100%")
    ManageAuth

    .mx-auto(style="width: 600px; position: relative; top: 100px")
        .white--text.text-center
            v-avatar(tile, size="100")
                v-img(
                    transition="fab-transition",
                    :src="require('@/assets/settings.svg')"
                )
            span.text-h2 Data Mangager

        v-card(style="margin-top: 80px")
            v-card-text
                v-form(v-model="valid" )
                    v-overflow-btn(
                        label="Filter",
                        :items="filters",
                        v-model="filter"
                    )
                    v-expand-transition(group, mode="out-in")
                        v-text-field(
                            outlined,
                            label="URL",
                            :rules="[v => !!v || 'Required']"
                            v-model="url",
                            v-if="filter == 'url'"
                        )
                        v-text-field(
                            outlined,
                            label="Title Length",
                            prefix="Greater Than",
                            type="number",
                            v-model="length",
                            v-if="filter == 'title-length'"
                        )

            v-card-actions
                v-spacer
                v-btn.mx-auto(
                    outlined,
                    color="primary",
                    :loading="loading",
                    :disabled="!valid"
                    @click="query"
                ) Query
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
import ManageAuth from '@/components/ManageAuth.vue'


@Component({ components: { DataEditor, ManageAuth } })
export default class extends Vue {
    filter = 'all'
    url = ''
    length = 0
    valid = false

    filters = [
        { text: 'All', value: 'all' },
        // { text: 'Creator', value: 'creator' },
        { text: 'URL', value: 'url' },
        { text: 'Title Length', value: 'title-length' }
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

    get queryOptions() {
        let query: any = {}
        switch (this.filter) {
            case 'url':
                query.filter = 'url'
                query.url = this.url
                break

            case 'title-length':
                query.filter = 'title-length'
                query.length = this.length
                break

            case 'all':
        }
        return query
    }

    async query() {
        this.loading = true
        let { status, data } = await axios.get('/api/shortened', { params: this.queryOptions })
        this.loading = false

        switch (status) {
            case 200:
                this.items = data
                break
        }
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

    mounted() {
    }
}
</script>

<style lang="scss" scoped>
.header {
    margin-bottom: 80px;
}
</style>