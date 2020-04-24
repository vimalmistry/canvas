<template>
    <div>
        <page-header>
            <template slot="status">
                <ul class="navbar-nav mr-auto flex-row float-right">
                    <li class="text-muted font-weight-bold">
                        <span v-if="form.isSaving">{{ trans.nav.notify.saving }}</span>
                        <span v-if="form.hasSuccess" class="text-success">{{ trans.nav.notify.success }}</span>
                    </li>
                </ul>
            </template>

            <template slot="action">
                <a href="#" class="btn btn-sm btn-outline-success font-weight-bold" :class="{ disabled: form.name === '' }" @click="saveTag" :aria-label="trans.buttons.general.save">
                    {{ trans.buttons.general.save }}
                </a>
            </template>

            <template slot="menu">
                <div class="dropdown" v-if="id !== 'create'">
                    <a id="navbarDropdown" class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" class="icon-dots-horizontal">
                            <path class="primary" fill-rule="evenodd" d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a href="#" class="dropdown-item text-danger" @click="showDeleteModal">
                            {{ trans.buttons.general.delete }}
                        </a>
                    </div>
                </div>
            </template>
        </page-header>

        <main class="py-4" v-if="isReady">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="form-group row my-5">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    name="name"
                                    autocomplete="off"
                                    v-model="form.name"
                                    title="Name"
                                    @keyup.enter="saveTag"
                                    class="form-control-lg form-control border-0 px-0 bg-transparent"
                                    :placeholder="trans.tags.forms.placeholder"
                                />

                                <div v-if="form.errors.name" class="invalid-feedback d-block">
                                    <strong>{{ form.errors.name[0] }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <p class="lead text-muted">
                                    <span class="text-success">{{ form.slug }}</span>
                                </p>
                                <div v-if="form.errors.slug" class="invalid-feedback d-block">
                                    <strong>{{ form.errors.slug[0] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <delete-modal
            ref="deleteModal"
            @delete="deleteTag"
            :header="trans.tags.delete.header"
            :message="trans.tags.delete.warning"
        >
        </delete-modal>
    </div>
</template>

<script>
    import $ from 'jquery'
    import PageHeader from '../../components/PageHeader'
    import DeleteModal from '../../components/DeleteModal'

    export default {
        name: 'tags-edit',

        components: {
            PageHeader,
            DeleteModal,
        },

        data() {
            return {
                tag: null,
                id: this.$route.params.id || 'create',
                form: {
                    id: '',
                    name: '',
                    slug: '',
                    errors: [],
                    isSaving: false,
                    hasSuccess: false,
                },
                isReady: false,
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.fetchData()
        },

        watch: {
            'form.name'(val) {
                this.form.slug = this.slugify(val)
            },
        },

        methods: {
            fetchData() {
                this.request()
                    .get('/api/tags/' + this.id)
                    .then(response => {
                        this.tag = response.data
                        this.form.id = response.data.id

                        if (this.id !== 'create') {
                            this.form.name = response.data.name
                            this.form.slug = response.data.slug
                        }

                        this.isReady = true
                    })
                    .catch(error => {
                        this.$router.push({name: 'tags'})
                    })
            },

            saveTag() {
                this.form.errors = []
                this.form.isSaving = true
                this.form.hasSuccess = false

                this.request()
                    .post('/api/tags/' + this.id, this.form)
                    .then(response => {
                        this.form.isSaving = false
                        this.form.hasSuccess = true
                        this.id = response.data.id
                        this.tag = response.data
                    })
                    .catch(error => {
                        this.form.isSaving = false
                        this.form.errors = error.response.data.errors
                    })
            },

            deleteTag() {
                this.request()
                    .delete('/api/tags/' + this.id)
                    .then(response => {
                        $(this.$refs.deleteModal.$el).modal('hide')

                        this.$router.push({name: 'tags'})
                    })
                    .catch(error => {
                        // Add any error debugging...
                    })
            },

            showDeleteModal() {
                $(this.$refs.deleteModal.$el).modal('show')
            },
        },
    }
</script>
