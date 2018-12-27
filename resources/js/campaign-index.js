import Vue from 'vue'
import './common-components';

window.app = new Vue({
    el: '#campaign-index',
    data: {
        pageVariables: {}
    },
    methods: {
        addPageVariables(name, value) {
            this.pageVariables = Object.assign({}, this.pageVariables, {[name]: value})
        },
        getPageVariable(name) {
            return this.pageVariables[name];
        }
    }
});
