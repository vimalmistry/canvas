<template>
    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="font-weight-bold lead">
                        {{ trans.posts.forms.settings.header }}
                    </p>

                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.settings.slug.label }}</label>
                            <a
                                href="#"
                                class="text-decoration-none"
                                v-if="activePost.title"
                                v-tooltip="{ placement: 'right' }"
                                :title="trans.posts.forms.seo.sync.title"
                                @click.prevent="syncSlug()">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="25">
                                    <circle cx="12" cy="12" r="10" style="fill:none"/>
                                    <path class="primary" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/>
                                </svg>
                            </a>
                            <input
                                type="text"
                                class="form-control border-0 px-0 bg-transparent"
                                @input="update"
                                name="slug"
                                v-model="activePost.slug"
                                :title="trans.posts.forms.settings.slug.label"
                                :placeholder="trans.posts.forms.settings.slug.placeholder"/>
                            <div v-if="activePost.errors.slug" class="invalid-feedback d-block">
                                <strong>{{ activePost.errors.slug[0] }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.settings.summary.label }}</label>
                            <textarea
                                rows="1"
                                id="settings"
                                name="summary"
                                class="form-control border-0 px-0 bg-transparent resize-none"
                                v-model="activePost.summary"
                                @input="update"
                                :placeholder="trans.posts.forms.settings.summary.placeholder">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.settings.topic.label }}</label>
                            <topic-select :topics="topics" :assigned="activePost.topic"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.settings.tags.label }}</label>
                            <tag-select :tags="tags" :tagged="activePost.tags"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none" data-dismiss="modal">
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
    import TagSelect from './TagSelect'
    import TopicSelect from './TopicSelect'
    import Tooltip from '../directives/Tooltip'

    export default {
        name: 'settings-modal',

        props: {
            tags: {
                type: Array,
                required: false,
            },
            topics: {
                type: Array,
                required: false,
            },
        },

        components: {
            TagSelect,
            TopicSelect,
        },

        directives: {
            Tooltip,
        },

        data() {
            return {
                allTags: [],
                allTopics: [],
                trans: JSON.parse(Canvas.lang),
            }
        },

        computed: mapState(['activePost']),

        mounted() {
            $('#settingsModal').on('shown.bs.modal', function () {
                autosize($('#settings'))
            })

            this.allTags = this.tags
            this.allTopics = this.topics
        },

        methods: {
            syncSlug() {
                this.activePost.slug = this.slugify(this.activePost.title)
                this.$parent.save()
            },

            update: _.debounce(function (e) {
                this.$parent.save()
            }, 1200),
        },
    }
</script>
