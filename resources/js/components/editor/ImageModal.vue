<template>
    <div class="modal fade" id="editorImage" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" id="imageModal" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="font-weight-bold lead">
                        {{ trans.posts.forms.editor.images.picker.uploader.label }}
                    </p>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div v-if="imageUrl" id="currentImage">
                                <img :src="imageUrl" class="w-100"/>

                                <div class="input-group py-2">
                                    <input
                                        type="text"
                                        class="form-control border-0 px-0 bg-transparent"
                                        v-model="caption"
                                        :placeholder="trans.posts.forms.editor.images.picker.uploader.caption.placeholder"
                                        ref="caption"/>
                                </div>

                                <div class="input-group py-2">
                                    <select
                                        class="custom-select border-0 px-0 bg-transparent"
                                        v-model="layout">
                                        <option value="default">
                                            {{ trans.posts.forms.editor.images.picker.uploader.layout.default }}
                                        </option>
                                        <option value="wide">
                                            {{ trans.posts.forms.editor.images.picker.uploader.layout.wide }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <image-picker
                                v-if="!imageUrl"
                                @changed="updateImage"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                        @click="applyImage"
                        data-dismiss="modal">
                        {{ trans.buttons.general.done }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import $ from 'jquery'
    import ImagePicker from '../ImagePicker'

    export default {
        name: 'image-modal',

        components: {
            ImagePicker,
        },

        data() {
            return {
                existingBlot: null,
                imageUrl: null,
                layout: 'default',
                caption: '',
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.$parent.$on('openingImageModal', data => {
                if (data) {
                    this.showImageModal()

                    this.caption = data.caption
                    this.imageUrl = data.url
                    this.layout = data.layout || 'default'
                    this.existingBlot = data.existingBlot
                }
            })
        },

        methods: {
            clear() {
                this.existingBlot = null
                this.imageUrl = null
                this.layout = 'default'
                this.caption = ''
            },

            updateImage({url, caption}) {
                this.imageUrl = url
                this.caption = caption ? caption : ''
            },

            applyImage() {
                if (!this.imageUrl) {
                    return
                }

                this.$emit('addingImage', {
                    url: this.imageUrl,
                    caption: this.caption,
                    existingBlot: this.existingBlot,
                    layout: this.layout,
                })

                this.clear()
            },

            showImageModal() {
                $('#editorImage').modal('show')
            },
        },
    }
</script>
