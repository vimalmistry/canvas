<template>
    <div class="modal fade" id="seoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="font-weight-bold lead">
                        {{ trans.posts.forms.seo.header }}
                    </p>

                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">
                                {{ trans.posts.forms.seo.meta }}
                                <a
                                    href="#"
                                    class="text-decoration-none"
                                    v-tooltip="{ placement: 'right' }"
                                    :title="trans.posts.forms.seo.sync.description"
                                    @click.prevent="syncMetaDescription()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                                        <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                    </svg>
                                </a>
                            </label>
                            <textarea
                                rows="1"
                                id="meta_description"
                                name="meta_description"
                                class="form-control border-0 px-0 bg-transparent"
                                @input="update"
                                v-model="activePost.meta.meta_description"
                                :placeholder="trans.posts.forms.seo.meta">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">
                                {{ trans.posts.forms.seo.facebook.title.label }}
                                <a
                                    href="#"
                                    class="text-decoration-none"
                                    v-tooltip="{ placement: 'right' }"
                                    :title="trans.posts.forms.seo.sync.title"
                                    @click.prevent="syncOgTitle()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                                        <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                    </svg>
                                </a>
                            </label>
                            <input
                                name="og_title"
                                type="text"
                                @input="update"
                                class="form-control border-0 px-0 bg-transparent"
                                :title="trans.posts.forms.seo.facebook.title.label"
                                v-model="activePost.meta.og_title"
                                :placeholder="trans.posts.forms.seo.facebook.title.placeholder"
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">
                                {{ trans.posts.forms.seo.facebook.description.label }}
                                <a
                                    href="#"
                                    class="text-decoration-none"
                                    v-tooltip="{ placement: 'right' }"
                                    :title="trans.posts.forms.seo.sync.description"
                                    @click.prevent="syncOgDescription()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                                        <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                    </svg>
                                </a>
                            </label>
                            <textarea
                                id="og_description"
                                rows="1"
                                name="og_description"
                                class="form-control border-0 px-0 bg-transparent"
                                @input="update"
                                v-model="activePost.meta.og_description"
                                :placeholder="trans.posts.forms.seo.facebook.description.placeholder">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">
                                {{ trans.posts.forms.seo.twitter.title.label }}
                                <a
                                    href="#"
                                    class="text-decoration-none"
                                    v-tooltip="{ placement: 'right' }"
                                    :title="trans.posts.forms.seo.sync.title"
                                    @click.prevent="syncTwitterTitle()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                                        <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                    </svg>
                                </a>
                            </label>
                            <input
                                type="text"
                                class="form-control border-0 px-0 bg-transparent"
                                name="twitter_title"
                                @input="update"
                                v-model="activePost.meta.twitter_title"
                                :title="trans.posts.forms.seo.twitter.title.label"
                                :placeholder="trans.posts.forms.seo.twitter.title.placeholder"
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">
                                {{ trans.posts.forms.seo.twitter.description.label }}
                                <a
                                    href="#"
                                    class="text-decoration-none"
                                    v-tooltip="{ placement: 'right' }"
                                    :title="trans.posts.forms.seo.sync.description"
                                    @click.prevent="syncTwitterDescription()">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                                        <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                    </svg>
                                </a>
                            </label>
                            <textarea
                                id="twitter_description"
                                rows="1"
                                name="twitter_description"
                                class="form-control border-0 px-0 bg-transparent"
                                @input="update"
                                v-model="activePost.meta.twitter_description"
                                :placeholder="trans.posts.forms.seo.twitter.description.placeholder">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.seo.canonical.label }}</label>
                            <input
                                type="text"
                                @input="update"
                                class="form-control border-0 px-0 bg-transparent"
                                name="canonical_link"
                                v-model="activePost.meta.canonical_link"
                                :title="trans.posts.forms.seo.canonical.label"
                                :placeholder="trans.posts.forms.seo.canonical.placeholder"
                            />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
                        data-dismiss="modal">
                        {{ trans.buttons.general.done }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash'
    import $ from 'jquery'
    import autosize from 'autosize'
    import {mapState} from 'vuex'
    import Tooltip from '../directives/Tooltip'

    export default {
        name: 'seo-modal',

        data() {
            return {
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            $('#seoModal').on('shown.bs.modal', function () {
                autosize($('#meta_description'))
                autosize($('#og_description'))
                autosize($('#twitter_description'))
            })
        },

        computed: mapState(['activePost']),

        directives: {
            Tooltip,
        },

        methods: {
            update: _.debounce(function (e) {
                this.$parent.save()
            }, 1200),

            syncMetaDescription() {
                this.activePost.meta.meta_description = this.activePost.summary
                this.$parent.save()
            },

            syncOgTitle() {
                this.activePost.meta.og_title = this.activePost.title
                this.$parent.save()
            },

            syncOgDescription() {
                this.activePost.meta.og_description = this.activePost.summary
                this.$parent.save()
            },

            syncTwitterTitle() {
                this.activePost.meta.twitter_title = this.activePost.title
                this.$parent.save()
            },

            syncTwitterDescription() {
                this.activePost.meta.twitter_description = this.activePost.summary
                this.$parent.save()
            },
        },
    }
</script>
