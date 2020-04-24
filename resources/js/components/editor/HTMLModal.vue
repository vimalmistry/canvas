<template>
    <div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" id="htmlModal" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="font-weight-bold lead">
                        {{ trans.posts.forms.editor.html.label }}
                    </p>
                    <div class="form-group row">
                        <div class="col-lg-12 mx-0 px-0">
                            <textarea
                                ref="content"
                                cols="30"
                                rows="10"
                                class="form-control border-0 bg-transparent"
                                :placeholder="trans.posts.forms.editor.html.placeholder"
                                style="resize: none"
                                v-model="content">
                            </textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                        @click="addHTML"
                        data-dismiss="modal">
                        {{ trans.buttons.general.done }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'html-modal',

        data() {
            return {
                content: '',
                trans: JSON.parse(Canvas.lang),
            }
        },

        mounted() {
            this.$nextTick(() => this.$refs.content.focus())
        },

        methods: {
            addHTML() {
                this.$emit('addingHTML', {
                    content: this.content,
                })

                this.content = ''
            },
        },
    }
</script>
