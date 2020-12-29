import Vue from 'vue'
import VueRouter, { RouteConfig } from 'vue-router'

Vue.use(VueRouter)

const routes: Array<RouteConfig> = [
    {
        path: '/',
        name: 'Home',
        meta: { title: 'Home | TinyGG' },
        component: () => import('@/views/Home.vue')
    },
    {
        path: '/manage',
        name: 'Manage',
        meta: { title: 'Manage | TinyGG' },
        component: () => import('@/views/Manage.vue')
    }
]

const router = new VueRouter({
    mode: 'hash',
    base: process.env.BASE_URL,
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title
    next()
})

export default router
