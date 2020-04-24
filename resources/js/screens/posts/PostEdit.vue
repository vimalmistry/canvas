<template>
    <div>
        <page-header>
            <template slot="status">
                <ul class="navbar-nav mr-auto flex-row float-right">
                    <li class="text-muted font-weight-bold">
                        <div class="d-inline-block">
                            <span v-if="isDraft">{{ trans.nav.context.draft }}</span>
                            <span v-else>{{ trans.nav.context.published }}</span>
                        </div>

                        <span v-if="post.isSaving" class="pl-2">{{ trans.nav.notify.saving }}</span>
                        <span v-if="post.hasSuccess" class="pl-2 text-success">
                            {{ trans.nav.notify.success }}
                        </span>
                    </li>
                </ul>
            </template>

            <template slot="action">
                <a v-if="isDraft" href="#" class="btn btn-sm btn-outline-success font-weight-bold" @click="showPublishModal">
                    {{ trans.buttons.posts.ready }}
                </a>

                <a v-else href="#" class="btn btn-sm btn-outline-success font-weight-bold" @click="save">
                    {{ trans.buttons.general.save }}
                </a>
            </template>

            <template slot="menu">
                <div class="dropdown">
                    <a id="navbarDropdown" class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" class="icon-dots-horizontal">
                            <path class="primary" fill-rule="evenodd" d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <router-link v-if="!isDraft" :to="{ name: 'stats-show', params: { id: id } }" class="dropdown-item">
                            {{ trans.nav.controls.stats }}
                        </router-link>
                        <div v-if="!isDraft" class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" @click="showSettingsModal">
                            {{ trans.nav.controls.settings }}
                        </a>
                        <a href="#" class="dropdown-item" @click="showFeaturedImageModal">
                            {{ trans.nav.controls.image }}
                        </a>
                        <a href="#" class="dropdown-item" @click="showSeoModal">
                            {{ trans.nav.controls.seo }}
                        </a>
                        <a v-if="!isDraft" href="#" class="dropdown-item" @click.prevent="convertToDraft">
                            {{ trans.buttons.general.draft }}
                        </a>
                        <a v-if="id !== 'create'" href="#" class="dropdown-item text-danger" @click="showDeleteModal">
                            {{ trans.buttons.general.delete }}
                        </a>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8" v-if="isReady">
                        <div class="form-group row my-3">
                            <div class="col-lg-12">
                                <textarea-autosize
                                    :placeholder="trans.posts.forms.editor.title"
                                    class="form-control-lg form-control border-0 pl-0 font-serif bg-transparent"
                                    @input.native="update"
                                    rows="1"
                                    v-model="post.title"
                                />
                            </div>
                        </div>

                        <quill-editor></quill-editor>
                    </div>
                </div>
            </div>
        </main>

        <publish-modal v-if="isReady" ref="publishModal"/>

        <settings-modal
            v-if="isReady"
            ref="settingsModal"
            :post="post"
            :tags="tags"
            :topics="topics"
        />

        <featured-image-modal v-if="isReady" ref="featuredImageModal"/>

        <seo-modal v-if="isReady" ref="seoModal"/>

        <delete-modal
            v-if="isReady"
            ref="deleteModal"
            @delete="deletePost"
            :header="trans.posts.delete.header"
            :message="trans.posts.delete.warning"
        />
    </div>
</template>

<script>
    import Vue from 'vue'
    import $ from 'jquery'
    import {mapGetters} from 'vuex'
    import SeoModal from '../../components/SeoModal'
    import PageHeader from '../../components/PageHeader'
    import DeleteModal from '../../components/DeleteModal'
    import VueTextAreaAutosize from 'vue-textarea-autosize'
    import PublishModal from '../../components/PublishModal'
    import SettingsModal from '../../components/SettingsModal'
    import QuillEditor from '../../components/editor/QuillEditor'
    import FeaturedImageModal from '../../components/FeaturedImageModal'

    Vue.use(VueTextAreaAutosize)

    export default {
        name: 'posts-edit',

        components: {
            PublishModal,
            FeaturedImageModal,
            DeleteModal,
            QuillEditor,
            PageHeader,
            SeoModal,
            SettingsModal,
        },

        data() {
            return {
                post: {},
                tags: [],
                topics: [],
                id: this.$route.params.id || 'create',
                isReady: false,
                trans: JSON.parse(Canvas.lang),
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => {
                vm.request()
                    .get('/api/posts/' + vm.id)
                    .then(response => {
                        vm.$store.dispatch('setActivePost', response.data.post)

                        vm.post = vm.$store.getters.activePost
                        vm.tags = response.data.tags
                        vm.topics = response.data.topics
                        vm.isReady = true
                    })
                    .catch(error => {
                        vm.$router.push({name: 'posts'})
                    })
            })
        },

        beforeRouteLeave(to, from, next) {
            // Reset the form status to avoid it flashing on the next screen load
            this.post.isSaving = false
            this.post.hasSuccess = false

            next()
        },

        computed: {
            ...mapGetters(['isDraft']),
        },

        methods: {
            save() {
                this.post.errors = []
                this.post.isSaving = true
                this.post.hasSuccess = false

                this.$store.dispatch('saveActivePost', {
                    data: this.post,
                    id: this.id,
                })

                if (this.id === 'create') {
                    this.id = this.post.id
                }
            },

            update: _.debounce(function (e) {
                this.save()
            }, 1200),

            convertToDraft() {
                this.post.published_at = ''
                this.save()
            },

            deletePost() {
                this.$store.dispatch('deletePost', this.post.id)

                $(this.$refs.deleteModal.$el).modal('hide')
            },

            showPublishModal() {
                $(this.$refs.publishModal.$el).modal('show')
            },

            showSettingsModal() {
                $(this.$refs.settingsModal.$el).modal('show')
            },

            showFeaturedImageModal() {
                $(this.$refs.featuredImageModal.$el).modal('show')
            },

            showSeoModal() {
                $(this.$refs.seoModal.$el).modal('show')
            },

            showDeleteModal() {
                $(this.$refs.deleteModal.$el).modal('show')
            },
        },
    }
</script>

<style scoped>
    textarea {
        font-size: 42px;
    }
</style>
