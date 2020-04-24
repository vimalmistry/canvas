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
                        <div class="d-flex justify-content-between">
                            <h1 class="mt-2">{{ trans.posts.header }}</h1>

                            <div class="dropdown my-auto">
                                <a href="#" class="nav-link px-0 pb-0 pt-3" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" viewBox="0 0 24 24" class="icon-search">
                                        <circle cx="10" cy="10" r="7" style="fill:none"/>
                                        <path class="primary" d="M16.32 14.9l1.1 1.1c.4-.02.83.13 1.14.44l3 3a1.5 1.5 0 0 1-2.12 2.12l-3-3a1.5 1.5 0 0 1-.44-1.14l-1.1-1.1a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"/>
                                    </svg>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right py-0 shadow-sm" id="searchDropdown" aria-labelledby="dropdownMenuButton">
                                    <form class="pl-2 w-100">
                                        <div class="form-group mb-0">
                                            <input v-model="search" type="text" class="form-control border-0 pl-0 bg-transparent" id="search" :placeholder="trans.posts.search.input" autofocus/>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div v-if="isReady">
                            <div v-if="posts.length" class="mt-2">
                                <div class="d-flex border-top py-3 align-items-center" v-for="post in filteredList">
                                    <div class="mr-auto py-1">
                                        <p class="mb-1">
                                            <router-link :to="{name: 'posts-edit', params: { id: post.id }}" class="font-weight-bold text-lg lead text-decoration-none">
                                                {{ post.title }}
                                            </router-link>
                                        </p>
                                        <p class="mb-1" v-if="post.summary">
                                            {{ trim(post.summary, 200) }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <span v-if="isPublished(post)">
                                                {{ trans.posts.details.published}}
                                                {{ moment(post.published_at).fromNow() }}
                                            </span>

                                            <span v-if="isDraft(post)" class="text-danger">
                                                {{ trans.posts.details.draft }}
                                            </span>

                                            <span v-if="isScheduled(post)" class="text-danger">
                                                {{ trans.posts.details.scheduled }}
                                            </span>
                                            ― {{ trans.posts.details.updated }}
                                            {{ moment(post.updated_at).fromNow() }}
                                        </p>
                                    </div>
                                    <div class="ml-auto d-none d-lg-block pl-3">
                                        <router-link :to="{name: 'posts-edit', params: { id: post.id }}">
                                            <div v-if="post.featured_image" id="featuredImage" class="mr-2 shadow-inner" :style="{backgroundImage:'url(' + post.featured_image +')',}"></div>
                                            <div v-else class="mx-3 align-middle">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="45" viewBox="0 0 24 24" class="icon-camera">
                                                    <path class="primary" d="M6.59 6l2.7-2.7A1 1 0 0 1 10 3h4a1 1 0 0 1 .7.3L17.42 6H20a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h2.59zM19 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-7 8a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"/>
                                                    <path class="primary" d="M12 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                                </svg>
                                            </div>
                                        </router-link>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <a href="#!" class="btn btn-link text-success text-decoration-none font-weight-bold btn-block" @click="limit += 7" v-if="loadMore">
                                        {{ trans.buttons.general.load }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" viewBox="0 0 24 24" class="icon-cheveron-down">
                                            <path class="primary" fill-rule="evenodd" d="M15.3 10.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"/>
                                        </svg>
                                    </a>
                                </div>

                                <p v-if="!filteredList.length">
                                    {{ trans.posts.search.empty }}
                                </p>
                            </div>
                            <p v-else class="mt-2">
                                {{ trans.posts.empty.description }}
                                <router-link to="/posts/create">
                                    {{ trans.posts.empty.action }}
                                </router-link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
    import moment from 'moment'
    import PageHeader from '../../components/PageHeader'

    export default {
        name: 'posts-index',

        components: {
            PageHeader,
        },

        data() {
            return {
                posts: [],
                search: '',
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
                    .get('/api/posts')
                    .then(response => {
                        this.posts = response.data
                        this.isReady = true
                    })
                    .catch(error => {
                        // Add any error debugging...
                    })
            },

            isDraft(post) {
                return post.published_at == null || post.published_at === ''
            },

            isScheduled(post) {
                return moment(post.published_at).isAfter(
                    moment(new Date())
                        .format()
                        .slice(0, 19)
                        .replace('T', ' ')
                )
            },

            isPublished(post) {
                return moment(post.published_at).isBefore(
                    moment(new Date())
                        .format()
                        .slice(0, 19)
                        .replace('T', ' ')
                )
            },
        },

        computed: {
            /**
             * Filter posts by their title.
             *
             * @source https://codepen.io/AndrewThian/pen/QdeOVa
             */
            filteredList() {
                let filtered = this.posts.filter(post => {
                    return post.title
                        .toLowerCase()
                        .includes(this.search.toLowerCase())
                })

                this.loadMore = Object.keys(filtered).length > this.limit

                return this.limit ? filtered.slice(0, this.limit) : this.posts
            },
        },
    }
</script>

<style scoped>
    #navbarDropdown {
        margin-top: -8px;
    }

    #searchDropdown {
        min-width: 15rem;
    }

    #featuredImage {
        background-size: cover;
        width: 57px;
        height: 57px;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
    }
</style>
