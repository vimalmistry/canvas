<template>
    <div>
        <page-header>
            <template slot="action">
                <router-link :to="{ name: 'topics-create' }" class="btn btn-sm btn-outline-success font-weight-bold">
                    {{ trans.buttons.topics.create }}
                </router-link>
            </template>
        </page-header>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="d-flex justify-content-between">
                            <h1 class="mt-2">
                                {{ trans.topics.header }}
                            </h1>

                            <div class="dropdown my-auto">
                                <a id="navbarDropdown" href="#" class="nav-link px-0 pb-0 pt-3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" viewBox="0 0 24 24" class="icon-search">
                                        <circle cx="10" cy="10" r="7" style="fill:none"/>
                                        <path class="primary" d="M16.32 14.9l1.1 1.1c.4-.02.83.13 1.14.44l3 3a1.5 1.5 0 0 1-2.12 2.12l-3-3a1.5 1.5 0 0 1-.44-1.14l-1.1-1.1a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"/>
                                    </svg>
                                </a>
                                <div id="searchDropdown" class="dropdown-menu dropdown-menu-right py-0 shadow-sm" aria-labelledby="dropdownMenuButton">
                                    <form class="pl-2 w-100">
                                        <div class="form-group mb-0">
                                            <input
                                                id="search"
                                                v-model="search"
                                                type="text"
                                                class="form-control border-0 pl-0 bg-transparent"
                                                :placeholder="trans.topics.search.input"
                                                autofocus
                                            />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div v-if="isReady">
                            <div v-if="topics.length" class="mt-2">
                                <div v-for="topic in filteredList" class="d-flex border-top py-3 align-items-center">
                                    <div class="mr-auto">
                                        <p class="mb-0 py-1">
                                            <router-link :to="{name: 'topics-edit', params: { id: topic.id }}" class="font-weight-bold text-lg lead text-decoration-none">
                                                {{ topic.name }}
                                            </router-link>
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span class="text-muted mr-3">
                                            {{ topic.posts_count }} {{ trans.tags.posts }}
                                        </span>
                                        {{ trans.topics.details.created }}
                                        {{ moment(topic.created_at).fromNow() }}
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <a v-if="loadMore" href="#!" class="btn btn-link text-success text-decoration-none font-weight-bold btn-block" @click="limit += 10">
                                        {{ trans.buttons.general.load }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" viewBox="0 0 24 24" class="icon-cheveron-down">
                                            <path class="primary" fill-rule="evenodd" d="M15.3 10.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"/>
                                        </svg>
                                    </a>
                                </div>

                                <p v-if="!filteredList.length">
                                    {{ trans.topics.search.empty }}
                                </p>
                            </div>
                            <p v-else class="mt-2">
                                {{ trans.topics.empty.description }}
                                <router-link to="/topics/create" class="text-success text-decoration-none">
                                    {{ trans.topics.empty.action }}
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
    import PageHeader from '../../components/PageHeader'

    export default {
        name: 'topics-index',

        components: {
            PageHeader,
        },

        data() {
            return {
                topics: [],
                search: '',
                limit: 10,
                loadMore: false,
                isReady: false,
                trans: JSON.parse(Canvas.lang),
            }
        },

        computed: {
            /**
             * Filter topics by their name.
             *
             * @source https://codepen.io/AndrewThian/pen/QdeOVa
             */
            filteredList() {
                let filtered = this.topics.filter(tag => {
                    return tag.name
                        .toLowerCase()
                        .includes(this.search.toLowerCase())
                })

                this.loadMore = Object.keys(filtered).length > this.limit

                return this.limit ? filtered.slice(0, this.limit) : this.topics
            },
        },

        mounted() {
            this.fetchData()
        },

        methods: {
            fetchData() {
                this.request()
                    .get('/api/topics')
                    .then(response => {
                        this.topics = response.data
                        this.isReady = true
                    })
                    .catch(error => {
                        // Add any error debugging...
                    })
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
</style>
