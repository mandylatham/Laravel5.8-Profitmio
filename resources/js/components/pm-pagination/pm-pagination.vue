<template>
    <div class="pm-pagination">
        <a class="paginator-left" href="javascript:;" @click="goBack">
            <chevron-left-icon></chevron-left-icon>
        </a>
        <div class="paginator-input">
            <input tyspe="text" v-model="pagination.page" class="form-control" @keyup.enter="changePage">
            <span>of {{ totalPages }}</span>
        </div>
        <a class="paginator-right" href="javascript:;" @click="goUp">
            <chevron-right-icon></chevron-right-icon>
        </a>
    </div>
</template>
<script>
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
            }
        },
        data() {
            return {};
        },
        computed: {
            totalPages: function () {
                return Math.ceil(this.pagination.total / this.pagination.per_page);
            }
        },
        methods: {
            changePage: function () {
                if (this.pagination.page < 1 || this.pagination.page > this.totalPages) {
                    return;
                }
                this.$emit('page-changed', {page: this.pagination.page})
            },
            goBack: function () {
                if (this.pagination.page <= 1) {
                    return;
                }
                this.$emit('page-changed', {page: this.pagination.page - 1});
            },
            goUp: function () {
                if (this.pagination.page >= this.totalPages) {
                    return;
                }
                this.$emit('page-changed', {page: this.pagination.page + 1});
            }
        }
    }
</script>
