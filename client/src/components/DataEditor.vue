<template lang="pug">
v-dialog(persistent, max-width="600", v-model="value")
    v-card-text.white--text.text-center.text-h6(style="background: #1e1e1e")
        | {{ temp.key }} 
        v-icon(color="white") mdi-arrow-right
        |  {{ temp.original }}

    v-tabs.table-tabs(dark, flat, vertical, v-model="tab") 
        v-tab Shortened
        v-tab-item.pa-4
            v-text-field(dense, outlined, label="Key", v-model="temp.key")
            div
                v-label Original
                a.ml-4(@click="tab = 1") {{  }}

            .mt-4
                v-label Creator
                a.ml-4(@click="tab = 2") {{  }}

        v-tab Brief
        v-tab-item.pa-4
            .mb-4
                v-label ID
                span.ml-4 {{ }}

            v-text-field(dense, outlined, label="URL", v-model="temp.original")
            v-text-field(dense, outlined, label="Title", v-model="temp.title")
            v-text-field(dense, outlined, label="Icon", v-model="temp.favicon")
                template(#prepend)
                    v-tooltip(bottom)
                        template(#activator="{ on, attrs }")
                            v-icon(v-bind="attrs", v-on="on") mdi-eye

                        span
                            v-img(
                                ref="favicon",
                                max-width="350",
                                :src="temp.favicon",
                                @error="error"
                            )
            v-text-field(
                dense,
                outlined,
                label="Thumbnail",
                v-model="temp.thumbnail"
            )
                template(#prepend)
                    v-tooltip(bottom)
                        template(#activator="{ on, attrs }")
                            v-icon(v-bind="attrs", v-on="on") mdi-eye

                        span
                            v-img(:src="temp.thumbnail", max-width="350")

            v-textarea(
                outlined,
                no-resize,
                label="Summary",
                height="70",
                v-model="temp.summary"
            )

        v-tab Creator
        v-tab-item.pa-4
            .mb-4
                v-label ID
                span.ml-4 {{ }}

            v-text-field(dense, outlined, label="IPv4")
            v-text-field(dense, outlined, label="IPv6")

        v-spacer

        v-card-actions
            v-spacer
            v-btn(outlined, color="error", @click="") Delete
            v-btn(outlined, @click="$emit('input', false)") Cancel
            v-btn(outlined, color="light-blue lighten-1", @click="save") Save
            v-spacer
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch } from 'vue-property-decorator'

@Component
export default class extends Vue {
    @Prop({ default: false })
    value!: boolean

    @Prop({ required: true })
    item!: any

    tab = 0

    temp: any = {}

    error(e: any) {
        console.log(this.$refs.favicon)
    }

    async save() {

    }

    @Watch('item')
    onItemUpdate() {
        this.temp = this.item
    }
}
</script>

<style lang="scss">
.table-tabs {
    .v-tabs-bar {
        border-radius: 0;
    }
    .v-tabs-items {
        // min-height: 420px;
    }
}
</style>