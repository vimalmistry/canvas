<template>
    <div>
        <page-header>
            <template slot="action">
                <router-link :to="{ name: 'posts-create' }" class="btn btn-sm btn-outline-success font-weight-bold">
                    {{ trans.buttons.posts.create }}
                </router-link>
            </template>
        </page-header>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h1 class="mt-2">{{ trans.stats.header }}</h1>

                        <div v-if="isReady">
                            <div v-if="posts.all.length">
                                <p class="mt-3 mb-4">
                                    {{ trans.stats.subtext }}
                                </p>

                                <div class="card-deck mb-4">
                                    <div class="card shadow bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted small text-uppercase font-weight-bolder">
                                                {{ trans.stats.cards.views.title }}
                                            </h5>
                                            <p class="card-text display-4">
                                                {{ suffixedNumber(views.count) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card shadow bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted small text-uppercase font-weight-bold">
                                                {{ trans.stats.cards.posts.title }}
                                            </h5>
                                            <p class="card-text display-4">
                                                {{ posts.drafts_count + posts.published_count }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card shadow bg-transparent">
                                        <div class="card-body">
                                            <h5 class="card-title text-muted small text-uppercase font-weight-bold">
                                                {{ trans.stats.cards.publishing.title }}
                                            </h5>
                                            <ul>
                                                <li>
                                                    {{ posts.published_count }}
                                                    {{ trans.stats.cards.publishing.details.published }}
                                                </li>
                                                <li>
                                                    {{ posts.drafts_count }}
                                                    {{ trans.stats.cards.publishing.details.drafts }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <line-chart :views="JSON.parse(views.trend)"></line-chart>

                                <div class="mt-4">
                                    <div class="d-flex border-top py-3 align-items-center" v-for="post in filteredList">
                                        <div class="mr-auto">
                                            <p class="mb-1 mt-2">
                                                <router-link :to="{name: 'stats-show', params: { id: post.id }}" class="font-weight-bold text-lg lead text-decoration-none">
                                                    {{ post.title }}
                                                </router-link>
                                            </p>
                                            <p class="text-muted mb-2">
                                                {{ post.read_time }} ―
                                                <router-link :to="{name: 'posts-edit', params: { id: post.id }}" class="text-decoration-none text-muted">
                                                    {{ trans.buttons.posts.edit }}
                                                </router-link>
                                                ―
                                                <router-link :to="{name: 'stats-show', params: { id: post.id }}" class="text-decoration-none text-muted">
                                                    {{ trans.buttons.stats.show }}
                                                </router-link>
                                            </p>
                                        </div>
                                        <div class="ml-auto d-none d-lg-block">
                                            <span class="text-muted mr-3">
                                                {{ suffixedNumber(post.views_count) }}
                                                {{ trans.stats.views }}
                                            </span>
                                            {{ trans.stats.details.created }}
                                            {{ moment(post.created_at).fromNow() }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <a href="#!" class="btn btn-link text-success text-decoration-none font-weight-bold btn-block" @click="limit += 7" v-if="loadMore">
                                            {{ trans.buttons.general.load }}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" viewBox="0 0 24 24" class="icon-cheveron-down">
                                                <path class="secondary" fill-rule="evenodd" d="M15.3 10.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p class="mt-3">{{ trans.stats.empty }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
    import LineChart from '../../components/LineChart'
    import PageHeader from '../../components/PageHeader'

    export default {
        name: 'stats-index',

        components: {
            LineChart,
            PageHeader,
        },

        data() {
            return {
                posts: [],
                views: {},
                limit: 7,
                loadMore: false,
                isReady: false,
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.fetchData()
        },

        methods: {
            fetchData() {
                this.request()
                    .get('/api/stats')
                    .then(response => {
                        this.posts = response.data.posts
                        this.views = response.data.views
                        this.isReady = true
                    })
                    .catch(error => {
                        // Add any error debugging...
                    })
            },
        },

        computed: {
            /**
             * There isn't much to filter here since no search option exists on
             * this component. Simply increment the post list array by the
             * limit that was given on the click event.
             *
             * @source https://codepen.io/AndrewThian/pen/QdeOVa
             */
            filteredList() {
                this.loadMore = Object.keys(this.posts.all).length > this.limit

                return this.limit
                    ? this.posts.all.slice(0, this.limit)
                    : this.posts.all
            },
        },
    }
</script>
