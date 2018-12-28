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

    get(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    record(errors) {
        this.errors = errors;
    }

    clear(field=null) {
        if (field) {
            delete this.errors[field];

            return;
        }

        this.errors = {};
    }
}
// var app = new Vue({
//     el: "#app",
//     data: {
//         posts: {},
//         userId: 1,
//         form: new Form({
//             title: '',
//             body: ''
//         }),
//     },
//     mounted: function () {
//         axios.get('https://jsonplaceholder.typicode.com/posts')
//             .then(response => (this.posts = response.data))
//             .catch(error => this.$toastr.e(error.response.data));
//     },
//     computed: {
//         hasPosts() {
//             return this.posts.length > 0;
//         }
//     },
//     methods: {
//         onSubmit() {
//             this.form.post('https://jsonplaceholder.typicode.com/posts')
//                 .then(this.onSuccess)
//                 .catch(this.onFail);
//         },
//
//         onSuccess(data) {
//             console.log(data.id);
//             this.$toastr.s("Post successfully submitted");
//         },
//
//         onFail(error) {
//             console.log(error);
//             this.$toastr.e("Unable to submit form");
//         }
//     }
// });
