<template lang="pug">
div
    v-card.mx-auto.pa-4(width="500")
        v-text-field(
            clearable,
            placeholder="URL to be shortened",
            v-model="original"
        )
        v-text-field(
            clearable,
            placeholder="Key",
            prefix="tinyygg.herokuapp.com/",
            v-model="key"
        )

        v-card-actions
            v-spacer
            v-btn(color="warning") Random Key
            v-btn(color="success", @click="shorten") Shorten

    v-card.mx-auto.mt-2.pa-4(width="500")
        v-data-table(:headers="headers")
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'

@Component
export default class extends Vue {
    headers = [
        { text: 'Key', value: 'key' },
        { text: 'Original', value: 'original' }
    ]

    key = ''
    original = ''

    async shorten() {
        let res = await axios.post('/api/shorten.php', {
            key: this.key,
            original: this.original
        })
    }
}
</script>