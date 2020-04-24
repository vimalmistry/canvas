<template>
    <div v-cloak>
        <input
            hidden
            type="file"
            class="custom-file-input"
            :id="'imageUpload' + _uid"
            accept="image/*"
            @change="uploadSelectedImage"
        />
        <div class="mb-0">
            {{ trans.posts.forms.editor.images.picker.greeting }}

            <label :for="'imageUpload' + _uid" class="text-success" style="cursor:pointer;">
                {{ trans.posts.forms.editor.images.picker.action }}
            </label>

            {{ trans.posts.forms.editor.images.picker.item }}

            <span v-if="unsplash">{{ trans.posts.forms.editor.images.picker.operator }}</span>

            <a
                v-if="unsplash"
                href="#"
                @click.prevent="openUnsplash"
                class="text-success text-decoration-none">
                {{ trans.posts.forms.editor.images.picker.unsplash }}
            </a>
        </div>

        <div v-if="showUnsplash">
            <div class="container p-0">
                <input
                    type="text"
                    class="form-control-lg form-control border-0 px-0 bg-transparent"
                    v-if="unsplash"
                    v-model="searchQuery"
                    ref="searchKeyword"
                    :placeholder="trans.posts.forms.editor.images.picker.placeholder"
                />

                <div v-if="!isSearchingUnsplash && unsplashImages.length">
                    <div class="card-columns">
                        <div class="card border-0" v-for="image in unsplashImages">
                            <img
                                v-bind:src="image.urls.small"
                                class="card-img"
                                style="cursor: pointer"
                                @click="closeUnsplashAndInsertImage(image)"
                            />
                        </div>
                    </div>

                    <div class="d-flex pt-3">
                        <button
                            class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
                            type="button"
                            @click="closeUnsplash"
                            @submit.prevent>
                            {{ trans.buttons.general.cancel }}
                        </button>
                        <button
                            class="btn btn-success btn-block font-weight-bold mt-0"
                            type="button"
                            @click="fetchImages(unsplashPage + 1)"
                            v-if="unsplashImages.length === perPage"
                            @submit.prevent>
                            {{ trans.buttons.general.next }}
                        </button>
                    </div>
                </div>

                <div v-if="!isSearchingUnsplash && !unsplashImages.length">
                    <h4 class="text-center py-4">
                        {{ trans.posts.forms.editor.images.picker.search.empty }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'image-picker',

        props: {
            imageUrl: {
                type: String,
                required: false,
            },
        },

        data() {
            return {
                showUnsplash: false,
                searchQuery: '',
                unsplashPage: 1,
                perPage: 12,
                unsplashImages: [],
                isSearchingUnsplash: false,
                selectedUnsplashImage: null,
                unsplash: Canvas.unsplash,
                path: Canvas.path,
                trans: JSON.parse(Canvas.lang),
            }
        },

        watch: {
            searchQuery() {
                this.fetchImages()
            },
        },

        methods: {
            fetchImages(page = 1) {
                if (!this.unsplash) {
                    return alert(this.trans.posts.forms.editor.images.picker.key)
                }

                this.isSearchingUnsplash = true
                this.unsplashPage = page

                this.request()
                    .get(
                        'https://api.unsplash.com/search/photos?client_id=' +
                        this.unsplash +
                        '&orientation=landscape&per_page=' +
                        this.perPage +
                        '&query=' +
                        this.searchQuery +
                        '&page=' +
                        page
                    )
                    .then(response => {
                        this.unsplashImages = response.data.results
                        this.isSearchingUnsplash = false
                    })
                    .catch(error => {
                        this.isSearchingUnsplash = false
                    })
            },

            openUnsplash() {
                let featuredImageModal = document.querySelector(
                    '#featuredImageModal'
                )
                if (featuredImageModal) {
                    featuredImageModal.classList.add('modal-lg')
                }

                let imageModal = document.querySelector('#imageModal')
                if (imageModal) {
                    imageModal.classList.add('modal-lg')
                }

                let currentImage = document.querySelector('#currentImage')
                if (currentImage) {
                    currentImage.classList.add('d-none')
                }

                let searchTerms = [
                    'animal',
                    'art',
                    'city',
                    'emotion',
                    'nature',
                    'smile',
                    'tech',
                ]

                this.searchQuery =
                    searchTerms[Math.floor(Math.random() * searchTerms.length)]
                this.showUnsplash = true

                this.$nextTick(() => {
                    this.$refs.searchKeyword.focus()
                })
            },

            closeUnsplashAndInsertImage(image) {
                this.selectedUnsplashImage = image

                this.$emit('changed', {
                    url: this.selectedUnsplashImage.urls.regular,
                    caption:
                        this.trans.posts.forms.editor.images.picker.caption.by +
                        ' <a href="' +
                        this.selectedUnsplashImage.user.links.html +
                        '" target="_blank">' +
                        this.selectedUnsplashImage.user.name +
                        '</a> ' +
                        this.trans.posts.forms.editor.images.picker.caption.on +
                        ' <a href="https://unsplash.com" target="_blank">Unsplash</a>',
                })

                this.closeUnsplash()
            },

            closeUnsplash() {
                let featuredImageModal = document.querySelector(
                    '#featuredImageModal'
                )
                if (featuredImageModal) {
                    featuredImageModal.classList.remove('modal-lg')
                }

                let imageModal = document.querySelector('#imageModal')
                if (imageModal) {
                    imageModal.classList.remove('modal-lg')
                }

                let currentImage = document.querySelector('#currentImage')
                if (currentImage) {
                    currentImage.classList.remove('d-none')
                }

                this.searchQuery = ''
                this.showUnsplash = false
                this.selectedUnsplashImage = null
            },

            uploadSelectedImage(event) {
                let file = event.target.files[0]
                let formData = new FormData()

                formData.append('image', file, file.name)

                this.$emit('isUploading')

                this.request()
                    .post('/api/media/uploads', formData)
                    .then(response => {
                        this.$emit('changed', {
                            url: response.data,
                        })
                    })
                    .catch(error => {
                        // Add any error debugging...
                    })
            },
        },
    }
</script>
