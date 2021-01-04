<template lang="pug">
v-slide-y-reverse-transition(mode="out-in")
    .overlay-wrapper(v-if="value")
        v-overlay.text-center(:absolute="true")
            .padlock.mx-auto(:class="{ unlock: unlocked }")
                .keyhole

            v-card.mt-4.pa-2(light)
                v-form.d-flex.align-center(v-model="valid")
                    v-text-field(
                        label="Admin Password",
                        prepend-icon="mdi-lock",
                        type="password",
                        :rules="[(v) => !!v || 'Required']",
                        v-model="password"
                    )
                    v-btn.ml-2(
                        outlined,
                        color="light-blue",
                        :loading="loading",
                        :disabled="!valid",
                        @click="authenticate"
                    ) Authenticate
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { State, Action } from 'vuex-class'
import { sendMessage } from '@/sysmsg'

@Component
export default class extends Vue {
    @State
    authenticated!: boolean
    @Action('authenticate')
    _authenticate!: Function

    value = false
    valid = false
    password = ''
    loading = false
    unlocked = true

    async authenticate() {
        this.loading = true
        let auth = await this._authenticate(this.password)
        this.loading = false

        if (auth) {
            this.unlocked = true
        } else {
            sendMessage('Authentication Failed', { color: 'error' })
        }
    }

    @Watch('authenticated')
    onAuthChange(n: boolean, o: boolean) {
        if (o != n) {
            setTimeout(() => this.value = !n, 500)
        }
    }

    mounted() {
        setTimeout(() => this.value = !this.authenticated, 300)
        setTimeout(() => this.unlocked = false, 500)
    }
}
</script>

<style lang="scss" scoped>
.overlay-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 100vw;
    z-index: 4;
}

.padlock {
    position: relative;
    width: 260px;
    height: 200px;
    background-image: linear-gradient(
        to bottom right,
        #f2bc23 49.9%,
        #eab02a 50%
    );
    border-radius: 20px 20px 100px 100px;
    margin-top: 125px;
}

.padlock.unlock::before {
    transform: translate(-50%, -60%);
}

.padlock.unlock::after {
    transform: translate(-50%, calc(-50% - 15px)) rotate(90deg);
}

.padlock::before {
    content: "";
    position: absolute;
    left: 50%;
    z-index: -1;
    width: 200px;
    height: 250px;
    border-radius: 125px;
    border: 40px solid #dbe1e4;
    transform: translate(-50%, -50%);
    transition: transform 300ms cubic-bezier(0.17, 0.67, 0.65, 1.52);
    -webkit-clip-path: polygon(
        0% 0%,
        100% 0%,
        100% 65%,
        50% 65%,
        50% 57%,
        22% 57%,
        22% 51%,
        15% 51%,
        14% 52%,
        14% 53%,
        15% 54%,
        22% 54%,
        18% 57%,
        2% 57%,
        0% 55%
    );
    clip-path: polygon(
        0% 0%,
        100% 0%,
        100% 65%,
        50% 65%,
        50% 57%,
        22% 57%,
        22% 51%,
        15% 51%,
        14% 52%,
        14% 53%,
        15% 54%,
        22% 54%,
        18% 57%,
        2% 57%,
        0% 55%
    );
}

.padlock::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 1;
    width: 20px;
    height: 100px;
    background-image: linear-gradient(to right, #ccc 49.9%, #aaa 50%);
    border-radius: 10px;
    transform: translate(-50%, calc(-50% - 15px));
    transition: transform 180ms;
}

.keyhole {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60px;
    height: 60px;
    background-color: #2d3237;
    border-radius: 50%;
    transform: translate(-50%, calc(-50% - 15px));
}

.keyhole::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 30px;
    height: 30px;
    background-color: #3d464d;
    border-radius: 50%;
    transform: translate(-50%, -50%);
}

.keyhole::after {
    content: "";
    position: absolute;
    bottom: -30px;
    left: 50%;
    width: 30px;
    height: 35px;
    background-color: inherit;
    transform: translateX(-50%);
}
</style>