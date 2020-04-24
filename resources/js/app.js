import Vue from 'vue'
import Routes from './routes'
import { store } from './store'
import NProgress from 'nprogress'
import VueRouter from 'vue-router'
import moment from 'moment-timezone'
import HelperMixin from "./mixins/HelperMixin"
import RequestMixin from "./mixins/RequestMixin"

require('bootstrap')

window.Popper = require('popper.js').default

Vue.mixin(HelperMixin)
Vue.mixin(RequestMixin)

// Set the default timezone
moment.tz.setDefault(Canvas.timezone)

// Prevent the production tip on Vue startup
Vue.config.productionTip = false

Vue.use(VueRouter)

const router = new VueRouter({
    routes: Routes,
    mode: 'history',
    base: Canvas.path,
})

NProgress.configure({
    showSpinner: false,
    easing: 'ease',
    speed: 300,
})

router.beforeEach((to, from, next) => {
    NProgress.start()
    next()
})

router.afterEach(() => {
    NProgress.done()
})

const app = new Vue({
    el: '#canvas',
    router,
    store,
})

// Give the store access to the root Vue instance
store.$app = app
