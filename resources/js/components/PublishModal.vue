<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="font-weight-bold">{{ trans.posts.forms.publish.header }}</label>

                            <p class="text-muted">
                                {{ trans.posts.forms.publish.subtext.details }}

                                <span class="font-weight-bold">{{ Canvas.timezone }}</span>

                                {{ trans.posts.forms.publish.subtext.timezone }}.
                            </p>

                            <div class="d-flex flex-row">
                                <select class="input pr-2" v-model="components.month">
                                    <option v-for="value in Array.from({ length: 12 }, (_, i) => String(i + 1).padStart(2, '0'))" :value="value">
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">/</span>
                                <select class="input px-2" v-model="components.day">
                                    <option v-for="value in Array.from({ length: 31 }, (_, i) => String(i + 1).padStart(2, '0'))" :value="value">
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">/</span>
                                <select class="input px-2" v-model="components.year">
                                    <option v-for="value in Array.from({ length: 15 }, (_, i) => i + new Date().getFullYear() - 10)" :value="value">
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="pl-3"> </span>
                                <select class="input px-2" v-model="components.hour">
                                    <option v-for="value in Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0'))" :value="value">
                                        {{ value }}
                                    </option>
                                </select>

                                <span class="px-1">:</span>
                                <select class="input pl-2" v-model="components.minute">
                                    <option v-for="value in Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'))" :value="value">
                                        {{ value }}
                                    </option>
                                </select>
                            </div>

                            <p class="mt-3 text-danger font-italic" v-if="isScheduled">
                                Your post will publish at
                                {{ moment(this.activePost.published_at).format('h:mm A') }}
                                on
                                {{ moment(this.activePost.published_at).format('MMMM DD, YYYY') }}.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-lg order-lg-last px-0">
                            <a href="#" v-if="shouldPublish" class="btn btn-success btn-block font-weight-bold mt-0" @click="scheduleOrPublish" data-dismiss="modal">
                                {{ trans.buttons.posts.publish }}
                            </a>

                            <a href="#" v-else class="btn btn-success btn-block font-weight-bold mt-0" @click="scheduleOrPublish">
                                {{ trans.buttons.posts.schedule }}
                            </a>
                        </div>

                        <div class="col-lg order-lg-first px-0">
                            <button
                                v-if="isScheduled"
                                @click="cancelScheduling"
                                type="button"
                                class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                                data-dismiss="modal"
                            >
                                {{ trans.buttons.posts.cancel }}
                            </button>

                            <button
                                v-else
                                type="button"
                                class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                                data-dismiss="modal"
                            >
                                {{ trans.buttons.general.cancel }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'
    import {mapState} from 'vuex'

    export default {
        name: 'publish-modal',

        data() {
            return {
                components: {
                    day: '',
                    month: '',
                    year: '',
                    hour: '',
                    minute: '',
                },
                result: '',
                trans: JSON.parse(Canvas.lang),
            }
        },

        computed: {
            ...mapState(['activePost']),

            shouldPublish() {
                return moment(this.result).isBefore(
                    moment(new Date())
                        .format()
                        .slice(0, 19)
                        .replace('T', ' ')
                )
            },

            isScheduled() {
                return (
                    this.activePost.published_at &&
                    moment(this.result).isAfter(
                        moment(new Date())
                            .format()
                            .slice(0, 19)
                            .replace('T', ' ')
                    )
                )
            },
        },

        mounted() {
            this.generateDatePicker(
                this.activePost.published_at ||
                moment(new Date())
                    .format()
                    .slice(0, 19)
                    .replace('T', ' ')
            )
        },

        watch: {
            value(val) {
                this.generateDatePicker(val)
            },

            components: {
                handler: function () {
                    this.result =
                        this.components.year +
                        '-' +
                        this.components.month +
                        '-' +
                        this.components.day +
                        ' ' +
                        this.components.hour +
                        ':' +
                        this.components.minute +
                        ':00'
                },

                deep: true,
            },
        },

        filters: {
            moment: function (date) {
                return moment.tz(date).format('YYYY-MM-DD hh:mm:ss')
            },
        },

        methods: {
            generateDatePicker(val) {
                let date = moment(val + ' Z').utc()

                this.components = {
                    month: date.format('MM'),
                    day: date.format('DD'),
                    year: date.format('YYYY'),
                    hour: date.format('HH'),
                    minute: date.format('mm'),
                }
            },

            scheduleOrPublish() {
                this.activePost.published_at = this.result
                this.$parent.save()
            },

            cancelScheduling() {
                this.activePost.published_at = ''
                this.$parent.save()
            },
        },
    }
</script>

<style scoped>
    select {
        width: auto;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .input {
        border: none;
        background-color: transparent;
    }
</style>
