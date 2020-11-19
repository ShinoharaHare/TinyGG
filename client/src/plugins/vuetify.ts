import Vue from 'vue';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

import zhHant from 'vuetify/src/locale/zh-Hant'

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            light: {
            }
        }
    },
    lang: {
        // locales: { zhHant },
        // current: 'zhHant'
    }
});
