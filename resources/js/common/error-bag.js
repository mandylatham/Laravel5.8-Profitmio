import Vue from 'vue';

export default class ErrorBag {
    constructor() {
        this.errors = {};
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    add(field, message) {
        Vue.set(this.errors, field, this.errors[field] || []);
        this.errors[field].push(message);
    }

    get(field) {
        if (this.errors[field]) {
            return this.errors[field];
        }
    }

    record(errors) {
        this.errors = errors;
    }

    clear(field=null) {
        if (field) {
            Vue.delete(this.errors, field);
            return;
        }

        this.errors = {};
    }
}
