<template>
    <div v-cloak>
        <div style="position: relative">
            <div id="sidebarControls">
                <button
                    class="btn btn-outline-light btn-circle border"
                    type="button"
                    @click="openSidebarControls">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" viewBox="0 0 24 24" class="icon-add-circle">
                        <circle cx="12" cy="12" r="10" style="fill:none"/>
                        <path class="secondary" d="M13 11h4a1 1 0 0 1 0 2h-4v4a1 1 0 0 1-2 0v-4H7a1 1 0 0 1 0-2h4V7a1 1 0 0 1 2 0v4z"/>
                    </svg>
                </button>
                <div class="controls pl-3 d-none">
                    <button
                        class="btn btn-outline-light btn-circle border mr-1"
                        type="button"
                        @click="showImageModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" viewBox="0 0 24 24" class="icon-camera">
                            <path class="secondary" d="M6.59 6l2.7-2.7A1 1 0 0 1 10 3h4a1 1 0 0 1 .7.3L17.42 6H20a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8c0-1.1.9-2 2-2h2.59zM19 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-7 8a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"/>
                            <path class="secondary" d="M12 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        </svg>
                    </button>
                    <button
                        class="btn btn-outline-light btn-circle border mr-1"
                        type="button"
                        @click="showHTMLModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" viewBox="0 0 24 24" class="icon-code">
                            <rect width="18" height="18" x="3" y="3" style="fill:none" rx="2"/>
                            <path class="secondary" d="M8.7 13.3a1 1 0 0 1-1.4 1.4l-2-2a1 1 0 0 1 0-1.4l2-2a1 1 0 1 1 1.4 1.4L7.42 12l1.3 1.3zm6.6 0l1.29-1.3-1.3-1.3a1 1 0 1 1 1.42-1.4l2 2a1 1 0 0 1 0 1.4l-2 2a1 1 0 0 1-1.42-1.4zm-3.32 3.9a1 1 0 0 1-1.96-.4l2-10a1 1 0 0 1 1.96.4l-2 10z"/>
                        </svg>
                    </button>
                    <button
                        class="btn btn-outline-light btn-circle border mr-2"
                        type="button"
                        @click="insertDivider">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" viewBox="0 0 24 24" class="icon-dots-horizontal">
                            <path class="secondary" fill-rule="evenodd" d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div ref="editor"></div>

            <image-modal
                ref="imageModal"
                @addingImage="insertImage"
            />

            <html-modal
                ref="htmlModal"
                @addingHTML="insertHTML"
            />
        </div>
    </div>
</template>

<script>
    import _ from 'lodash'
    import $ from 'jquery'
    import Quill from 'quill'
    import {mapState} from 'vuex'
    import Parchment from 'parchment'
    import HTMLBlot from './HTMLBlot'
    import ImageBlot from './ImageBlot'
    import HTMLModal from './HTMLModal'
    import ImageModal from './ImageModal'
    import DividerBlot from './DividerBlot'

    export default {
        name: 'quill-editor',

        props: {
            value: {
                type: String,
                default: '',
            },
        },

        components: {
            'html-modal': HTMLModal,
            'image-modal': ImageModal,
        },

        data() {
            return {
                editor: null,
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.editor = this.createEditor()

            this.handleEditorValue()
            this.handleClicksInsideEditor()
            this.initSideControls()
        },

        computed: mapState(['activePost']),

        watch: {
            'activePost.body'(val) {
                this.update()
            },
        },

        methods: {
            createEditor() {
                Quill.register(ImageBlot, true)
                Quill.register(DividerBlot, true)
                Quill.register(HTMLBlot, true)

                const icons = Quill.import('ui/icons')
                icons.header[3] = require('!html-loader!quill/assets/icons/header-3.svg')

                let quill = new Quill(this.$refs.editor, {
                    modules: {
                        syntax: true,
                        toolbar: [
                            ['bold', 'italic', 'code', 'link'],
                            [{header: '2'}, {header: '3'}],
                            ['blockquote', 'code-block'],
                        ],
                    },
                    theme: 'bubble',
                    scrollingContainer: 'html, body',
                    placeholder: this.trans.posts.forms.editor.body,
                })

                /**
                 * Temporary workaround for customizing the link tooltip.
                 *
                 * @link https://github.com/quilljs/quill/issues/1107#issuecomment-259938173
                 */
                let tooltip = quill.theme.tooltip
                let input = tooltip.root.querySelector('input[data-link]')

                input.dataset.link = this.trans.posts.forms.editor.link

                return quill
            },

            handleEditorValue() {
                this.editor.root.innerHTML = this.$store.getters.activePost.body

                this.editor.on('text-change', (delta, oldContents, source) => {
                    this.$store.dispatch(
                        'updatePostBody',
                        this.editor.getText() ? this.editor.root.innerHTML : ''
                    )
                })
            },

            handleClicksInsideEditor() {
                this.editor.root.addEventListener('click', event => {
                    let blot = Parchment.find(event.target, true)

                    if (blot instanceof ImageBlot) {
                        let values = blot.value(blot.domNode)['captioned-image']

                        values.existingBlot = blot

                        this.showImageModal(values)
                    }
                })
            },

            initSideControls() {
                let Block = Quill.import('blots/block')

                this.editor.on(Quill.events.EDITOR_CHANGE, (eventType, range) => {
                    let sidebarControls = document.getElementById('sidebarControls')

                    if (eventType !== Quill.events.SELECTION_CHANGE) return

                    if (range == null) return

                    if (range.length === 0) {
                        let [block, offset] = this.editor.scroll.descendant(
                            Block,
                            range.index
                        )

                        if (block != null && block.domNode.firstChild instanceof HTMLBRElement) {
                            let lineBounds = this.editor.getBounds(range)

                            sidebarControls.classList.remove('active')

                            sidebarControls.style.display = 'block'

                            sidebarControls.style.left = lineBounds.left - 50 + 'px'
                            sidebarControls.style.top = lineBounds.top - 2 + 'px'
                        } else {
                            sidebarControls.style.display = 'none'

                            sidebarControls.classList.remove('active')
                        }
                    } else {
                        sidebarControls.style.display = 'none'

                        sidebarControls.classList.remove('active')
                    }
                })
            },

            openSidebarControls() {
                document.getElementById('sidebarControls').classList.toggle('active')

                this.editor.focus()
            },

            showImageModal(data = null) {
                this.$emit('openingImageModal', data)

                $(this.$refs.imageModal.$el).modal('show')
            },

            showHTMLModal() {
                $(this.$refs.htmlModal.$el).modal('show')
            },

            insertImage({url, caption, existingBlot, layout}) {
                let values = {
                    url: url,
                    caption: caption,
                    layout: layout,
                }

                if (existingBlot) {
                    return existingBlot.replaceWith('captioned-image', values)
                }

                let range = this.editor.getSelection(true)

                this.editor.insertEmbed(
                    range.index,
                    'captioned-image',
                    values,
                    Quill.sources.USER
                )

                this.editor.setSelection(range.index + 1, Quill.sources.SILENT)
            },

            insertHTML({content}) {
                let range = this.editor.getSelection(true)

                this.editor.insertEmbed(
                    range.index,
                    'html',
                    {
                        content: content,
                    },
                    Quill.sources.USER
                )

                this.editor.setSelection(range.index + 1, Quill.sources.SILENT)
            },

            insertDivider() {
                let range = this.editor.getSelection(true)

                this.editor.insertText(range.index, '\n', Quill.sources.USER)
                this.editor.insertEmbed(
                    range.index + 1,
                    'divider',
                    true,
                    Quill.sources.USER
                )
                this.editor.setSelection(range.index + 2, Quill.sources.SILENT)
            },

            update: _.debounce(function (e) {
                this.$parent.save()
            }, 1200),
        },
    }
</script>

<style>
    @import '~quill/dist/quill.bubble.css';

    .ql-container {
        box-sizing: border-box;
        font-family: 'Merriweather', serif;
        height: 100%;
        margin: 0;
        position: relative;
    }

    .ql-editor {
        font-family: 'Merriweather', serif;
        font-size: 1.1rem;
        line-height: 1.9;
        padding: 0;
        overflow-y: visible;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }

    .ql-editor p,
    .ql-editor ul,
    .ql-editor ol,
    .ql-editor h1,
    .ql-editor h2,
    .ql-editor h3,
    .ql-editor blockquote,
    .ql-editor pre {
        min-width: 100%;
    }

    .ql-editor h2 {
        margin-top: 0 !important;
        margin-bottom: 33px !important;
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 2.6rem;
    }

    .ql-editor h3 {
        margin-bottom: 33px !important;
        font-size: 17px !important;
        font-weight: bold;
        line-height: 2.6rem;
    }

    .ql-editor p,
    .ql-editor ol,
    .ql-editor ul,
    .ql-editor pre,
    .ql-editor blockquote {
        margin-bottom: 33px !important;
    }

    .ql-editor blockquote {
        font-style: italic !important;
    }

    .ql-bubble .ql-editor pre.ql-syntax {
        background-color: rgba(0, 0, 0, 0.05);
        border: none;
        color: #000;
        overflow-x: auto;
        padding: 1em;
    }

    .ql-editor h1,
    .ql-editor h2 {
        margin-top: 56px;
        margin-bottom: 15px;
    }

    .ql-editor ol,
    .ql-editor ul {
        padding-left: 0;
    }

    .ql-editor ol li,
    .ql-editor ul li {
        margin-bottom: 20px;
    }

    .ql-editor.ql-blank::before {
        left: 0;
        font-style: normal;
    }

    .ql-container hr {
        margin-top: 0;
        border: none;
        letter-spacing: 1em;
        text-align: center;
    }

    .ql-container hr:before {
        content: '...';
    }

    .btn-circle {
        width: 40px;
        height: 40px;
        padding: 6px 0;
        border-radius: 9999px;
        text-align: center;
        line-height: 1.42857;
    }

    #sidebarControls {
        margin-top: -8px;
        display: none;
        position: absolute;
        z-index: 10;
        left: -60px !important;
    }

    #sidebarControls button:hover {
        background-color: transparent;
    }

    #sidebarControls button:focus {
        -webkit-box-shadow: none !important;
        -moz-box-shadow: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    #sidebarControls.active .controls {
        display: inline-block !important;
    }

    .embedded_image {
        margin-bottom: 33px !important;
        cursor: default;
    }

    .embedded_image[data-layout='wide'] img {
        max-width: 1024px;
    }

    .embedded_image img {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        display: block;
    }

    .embedded_image:hover img {
        cursor: pointer !important;
        box-shadow: 0 0 0 3px #03a87c !important;
    }

    div.embedded_image[data-laout='wide'] {
        width: 100vw !important;
        position: relative !important;
        left: 50% !important;
        margin-left: -50vw !important;
    }

    .embedded_image p {
        margin-bottom: 0 !important;
        color: #6c757d;
        padding-top: 1rem;
        font-size: 0.9rem;
        line-height: 1.6;
        font-weight: 400;
        text-align: center;
        font-family: 'Karla', sans-serif;
    }

    @media screen and (max-width: 1024px) {
        .embedded_image[data-layout='wide'] img {
            max-width: 100%;
        }
    }

    @media (max-width: 767px) {
        #sidebarControls {
            display: none !important;
        }
    }
</style>
