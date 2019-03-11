<template>
    <div class="pm-pagination">
        <button class="paginator-left" type="button" @click="goBack" :disabled="totalPages <= 1 || pagination.page <= 1">
            <chevron-left-icon></chevron-left-icon>
        </button>
        <div class="paginator-input">
            <input tyspe="text" v-model="pagination.page" class="form-control" @keyup.enter="changePage">
            <span>of {{ totalPages }}</span>
        </div>
        <button class="paginator-right" type="button" @click="goUp" :disabled="totalPages <= 1 || pagination.page >= totalPages">
            <chevron-right-icon></chevron-right-icon>
        </button>
    </div>
</template>
<script>
    const VueScrollTo = require('vue-scrollto');

    export default {
        props: {
            pagination: {
                type: Object,
                required: false,
                default: function () {
                    return {
                        page: 1,
                        per_page: 15,
                        total: 15
                    };
                }
            },
            scrollElement: {
                type: String,
                required: false
            }
        },
        data() {
            return {};
        },
        computed: {
            totalPages: function () {
                return this.pagination.total && this.pagination.per_page ? Math.ceil(this.pagination.total / this.pagination.per_page) : 0;
            }
        },
        methods: {
            changePage: function () {
                if (this.pagination.page < 1 || this.pagination.page > this.totalPages) {
                    return;
                }
                this.scrollUp();
                this.$emit('page-changed', {page: this.pagination.page})
            },
            goBack: function () {
                if (this.pagination.page <= 1) {
                    return;
                }
                this.scrollUp();
                this.$emit('page-changed', {page: this.pagination.page - 1});
            },
            goUp: function () {
                if (this.pagination.page >= this.totalPages) {
                    return;
                }
                this.scrollUp();
                this.$emit('page-changed', {page: this.pagination.page + 1});
            },
            scrollUp: function () {
                let el = this.$el.parentNode;
                if (this.scrollElement) {
                    el = this.scrollElement;
                }
                if (el) {
                    VueScrollTo.scrollTo(el, 500, {
                        container: '.main-content'
                    });
                }
            }
        }
    }
</script>
