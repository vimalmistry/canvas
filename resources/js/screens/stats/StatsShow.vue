<template>
    <div>
        <page-header>
            <template slot="action">
                <router-link to="/stats" class="btn btn-sm btn-outline-success font-weight-bold my-auto ml-auto">
                    {{ trans.buttons.stats.index }}
                </router-link>
            </template>

            <template slot="menu">
                <div class="dropdown">
                    <a id="navbarDropdown" class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" class="icon-dots-horizontal">
                            <path class="primary" fill-rule="evenodd" d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <router-link :to="{ name: 'posts-edit', params: { id: id } }" class="dropdown-item">
                            {{ trans.buttons.posts.edit }}
                        </router-link>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4">
            <div class="container" v-if="isReady">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h5 class="text-muted small text-uppercase font-weight-bold mt-2">
                            {{ trans.stats.details.published }}
                            {{ moment(post.published_at).format('MMM D, YYYY') }}
                        </h5>
                        <h1 class="mb-4">{{ post.title }}</h1>

                        <line-chart :views="JSON.parse(views)"></line-chart>
                    </div>

                    <div class="col-md-5 mt-4">
                        <h5 class="text-muted small text-uppercase font-weight-bold border-bottom pb-2">
                            {{ trans.stats.details.views }}
                        </h5>

                        <div v-if="traffic">
                            <div v-for="(views, host, index) in traffic">
                                <div class="d-flex py-2 align-items-center">
                                    <div class="mr-auto">
                                        <div v-if="host === trans.stats.details.referer.other">
                                            <p class="mb-0 py-1">
                                                <img :src="`https://favicons.githubusercontent.com/${host}`" :alt="host" class="mr-1"/>
                                                <a href="#" v-tooltip="{placement: 'right'}" class="text-decoration-none" :title="trans.stats.details.referer.unknown">
                                                    {{ host }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 24 24" class="icon-help">
                                                        <path class="primary" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"/>
                                                        <path class="secondary" d="M12 19.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1-5.5a1 1 0 0 1-2 0v-1.41a1 1 0 0 1 .55-.9L14 10.5C14.64 10.08 15 9.53 15 9c0-1.03-1.3-2-3-2-1.35 0-2.49.62-2.87 1.43a1 1 0 0 1-1.8-.86C8.05 6.01 9.92 5 12 5c2.7 0 5 1.72 5 4 0 1.3-.76 2.46-2.05 3.24L13 13.2V14z"/>
                                                    </svg>
                                                </a>
                                            </p>
                                        </div>
                                        <div v-else>
                                            <p class="mb-0 py-1">
                                                <img :src="`https://favicons.githubusercontent.com/${host}`" :alt="host" class="mr-1"/>
                                                <a :href="'http://' + host" class="text-decoration-none" target="_blank">
                                                    {{ host }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="text-muted">{{ suffixedNumber(views) }} {{ trans.stats.views }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="py-2 font-italic">
                            {{ trans.stats.details.empty }}
                        </p>
                    </div>

                    <div class="col-md-5 mt-4">
                        <h5 class="text-muted small text-uppercase font-weight-bold border-bottom pb-2">
                            {{ trans.stats.details.reading.header }}
                        </h5>

                        <div v-if="popular_reading_times">
                            <div v-for="(percentage, time, index) in popular_reading_times">
                                <div class="d-flex py-2 align-items-center">
                                    <div class="mr-auto">
                                        <p class="mb-0 py-1">
                                            {{ time }}
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="text-muted">{{
                                            percentage + '%'
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="py-2 font-italic">
                            {{ trans.stats.details.empty }}
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
    import Tooltip from '../../directives/Tooltip'
    import LineChart from '../../components/LineChart'
    import PageHeader from '../../components/PageHeader'

    export default {
        name: 'stats-show',

        components: {
            LineChart,
            PageHeader,
        },

        directives: {
            Tooltip,
        },

        data() {
            return {
                id: this.$route.params.id,
                post: null,
                views: null,
                popular_reading_times: null,
                traffic: null,
                isReady: false,
                trans: JSON.parse(Canvas.lang),
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.request()
                    .get('/api/stats/' + vm.id)
                    .then(response => {
                        vm.post = response.data.post
                        vm.views = response.data.views
                        vm.traffic = Array.isArray(response.data.traffic)
                            ? null
                            : response.data.traffic
                        vm.popular_reading_times = Array.isArray(
                            response.data.popular_reading_times
                        )
                            ? null
                            : response.data.popular_reading_times

                        vm.isReady = true
                    })
                    .catch(error => {
                        vm.$router.push({name: 'stats'})
                    })
            })
        },
    }
</script>

<style scoped>
    img {
        width: 15px;
        height: 15px;
    }
</style>
