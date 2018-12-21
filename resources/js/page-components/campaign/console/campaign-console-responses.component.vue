<template>
    <div class="container-fluid list-campaign-container">

        <div class="row align-items-end no-gutters">
            <div class="col-6">
                <button class="btn btn-round btn-sm">
                    <chevron-left-icon class="chevron-left-icon"></chevron-left-icon>
                    Responses
                </button>
            </div>
            <div class="col-6">
                <div class="input-group mb-2 float-right">
                    <div class="input-group-prepend">
                        <search-icon class="search-icon"></search-icon>
                    </div>
                    <input type="text" class="form-control">
                </div>
            </div>
        </div>

        <div class="row align-items-end no-gutters mt-4 mb-3">
            <div class="col-12">
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/folder.png" alt="folder"></a>
                <a class="icon" href="javascript:;"><img src="../../../../img/icons/tag.png" alt="tag"></a>
            </div>
        </div>

        <div class="row align-items-end no-gutters">
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns" :disable-folding="true" v-on:row-clicked="showPanel">
                </pm-responsive-table>
            </div>
        </div>

        <slideout-panel></slideout-panel>
    </div>
</template>
<script>
    require("./campaign-console-responses.scss");
    import {ChevronLeftIcon} from 'vue-feather-icons';
    import {SearchIcon} from 'vue-feather-icons';

    export default {
        data() {
            return {
                rows: [
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    },
                    {
                        name: 'Bailey O’Reilly Jr. Ferry',
                        email: 'uhodkiewicz@hotmail.com',
                        date: '1 week ago'
                    }
                ],
                columns: [
                    {
                        field: 'name',
                        classes: ['console-response-name']
                    },
                    {
                        field: 'email',
                        classes: ['console-response-email']
                    },
                    {
                        field: 'date',
                        classes: ['console-response-date']
                    }
                ],
                panel1Form: {
                    openOn: "right"
                }
            };
        },
        components: {
            ChevronLeftIcon,
            SearchIcon,
        },
        methods: {
            showPanel: function({row}) {
                const panel = this.$showPanel({
                    component: 'communication-side-panel',
                    cssClass: 'communication-side-panel',
                    props: {}
                });
            }
        }
    }
</script>
