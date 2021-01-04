<template lang="pug">
v-card.pb-12(flat, color="transparent")
    ManageAuth
    v-card(flat, color="transparent", style="margin: 100px auto 0 auto")
        .white--text.text-center
            v-avatar(tile, size="100")
                v-img(
                    transition="fab-transition",
                    :src="require('@/assets/settings.svg')"
                )
            span.text-h2 Data Mangager

        v-container.mt-4.px-12
            v-row
                v-col(cols="4")
                    v-card(ref="filters")
                        v-card-text
                            v-form(v-model="valid")
                                v-label Filter
                                v-overflow-btn(
                                    dense,
                                    label="Filter",
                                    :items="filters",
                                    v-model="filter"
                                )
                                v-expand-transition
                                    div(v-if="filter == 'shortened'")
                                        v-text-field(
                                            dense,
                                            outlined,
                                            label="Key",
                                            v-model="key"
                                        )
                                        v-text-field(
                                            dense,
                                            outlined,
                                            label="Min Click",
                                            type="number",
                                            v-model="click"
                                        )

                                v-expand-transition
                                    div(v-if="filter == 'brief'")
                                        v-text-field(
                                            dense,
                                            outlined,
                                            label="URL",
                                            v-model="url"
                                        )
                                        v-text-field(
                                            dense,
                                            outlined,
                                            label="Title",
                                            v-model="title"
                                        )
                                        v-textarea(
                                            dense,
                                            outlined,
                                            label="Summary",
                                            v-model="summary"
                                        )
                                        v-row
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Min Title Length",
                                                    type="number",
                                                    v-model="min"
                                                )
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Max Title Length",
                                                    type="number",
                                                    v-model="max"
                                                )
                                v-expand-transition
                                    v-text-field(
                                        dense,
                                        outlined,
                                        label="IP",
                                        v-model="ip",
                                        v-if="filter == 'creator'"
                                    )

                                v-expand-transition
                                    div(v-if="filter == 'complex'")
                                        v-label Shortened
                                        v-row
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Key",
                                                    v-model="key"
                                                )
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Min Click",
                                                    type="number",
                                                    v-model="click"
                                                )
                                        v-label Brief
                                        v-row 
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="URL",
                                                    v-model="url"
                                                )
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Title",
                                                    v-model="title"
                                                )
                                        v-row
                                            v-col
                                                v-textarea(
                                                    dense,
                                                    outlined,
                                                    label="Summary",
                                                    v-model="summary"
                                                )
                                        v-row
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Min Title Length",
                                                    type="number",
                                                    v-model="min"
                                                )
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="Max Title Length",
                                                    type="number",
                                                    v-model="max"
                                                )
                                        v-label Creator
                                        v-row
                                            v-col
                                                v-text-field(
                                                    dense,
                                                    outlined,
                                                    label="IP",
                                                    v-model="ip"
                                                )
                        v-card-actions
                            v-spacer
                            v-btn.mx-auto(
                                outlined,
                                color="primary",
                                :loading="loading",
                                :disabled="!valid",
                                @click="query"
                            ) Query
                            v-spacer
                v-col
                    v-card
                        v-card-text
                            v-data-table.rounded(
                                :height="500",
                                :headers="headers",
                                :items="items",
                                :loading="loading"
                            )
                                template(#item.actions="{ item }")
                                    v-icon.mr-2(@click="showEditor(item)") mdi-pencil

                                template(#item.title="{ item }")
                                    v-tooltip(top)
                                        template(#activator="{ on, attrs }")
                                            .ellipsis(
                                                style="max-width: 400px",
                                                v-bind="attrs",
                                                v-on="on"
                                            ) {{ item.original.title }}

                                        span {{ item.original.title }}

                                template(#item.url="{ item }")
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
                                    td {{ item.creator.ip }}

    DataEditor(
        v-model="editor",
        :item="selected",
        @update="onItemUpdate",
        @delete="onItemDelete"
    )
</template>

<script lang="ts">
import { Vue, Component, Ref, Watch } from 'vue-property-decorator'
import DataEditor from '@/components/DataEditor.vue'
import ManageAuth from '@/components/ManageAuth.vue'


@Component({ components: { DataEditor, ManageAuth } })
export default class extends Vue {
    @Ref('filters') readonly card!: Vue

    filter = 'all'

    key = ''
    click = 0

    url = ''
    title = ''
    summary = ''
    min = 0
    max = 100

    ip = ''

    valid = false

    filters = [
        { text: 'All', value: 'all' },
        { text: 'Shortened', value: 'shortened' },
        { text: 'Brief', value: 'brief' },
        { text: 'Creator', value: 'creator' },
        { text: 'Complex', value: 'complex' }
    ]

    headers = [
        { text: '', value: 'actions', sortable: false },
        { text: 'Key', value: 'key' },
        { text: 'Title', value: 'title' },
        { text: 'URL', value: 'url' },
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
        query.filter = this.filter
        switch (this.filter) {
            case 'shortened':
                query.key = this.key
                query.click = this.click
                break

            case 'brief':
                query.url = this.url
                query.title = this.title
                query.summary = this.summary
                query.min = this.min
                query.max = this.max
                break

            case 'creator':
                query.ip = this.ip
                break

            case 'complex':
                query.key = this.key
                query.click = this.click
                query.url = this.url
                query.title = this.title
                query.summary = this.summary
                query.min = this.min
                query.max = this.max
                query.ip = this.ip
                break

            case 'all':
                delete query.filter
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

    requiredRule(v: string) {
        return !!v || 'Required'
    }

    showEditor(item: any) {
        this.editor = true
        this.index = this.items.indexOf(item)
    }

    onItemUpdate(item: any) {
        this.$set(this.items, this.index!, item)
    }

    onItemDelete(item: any) {
        this.items.splice(this.index!, 1)
        this.index = null
        this.editor = false
    }

    mounted() {
    }
}
</script>

<style lang="scss" scoped>
</style>