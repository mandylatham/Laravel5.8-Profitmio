import ErrorBag from './error-bag';
import axios from 'axios';

export default class Form {
    /**
     * Create a form
     */
    constructor(data) {
        this.originalData = data;

        for (let field in data) {
            this[field] = data[field];
        }

        this.errors = new ErrorBag();
    }

    /**
     * Fetch all form elements
     */
    data() {
        if (this.hasFile()) {
            console.log("form has a file");
            // If files are present, return a FormData object
            let formData = new FormData();
            for (let property in this.originalData) {
                formData.append(property, this[property]);
            }
            return formData;
        }

        console.log("form does not have a file");
        // Default parameter handling
        let data = {};
        for (let property in this.originalData) {
            data[property] = this[property];
        }
        return data;
    }

    hasFile() {
        let hasFile = false;
        for (let property in this.originalData) {
            if (typeof this[property].name === 'string') {
                hasFile = true;
            }
        }
        return hasFile;
    }

    updateData(field) {

    }

    reset() {
        for (let field in this.originalData) {
            this[field] = '';
        }

        this.errors.clear();
    }

    post(url) {
        return this.submit('post', url);
    }

    get(url) {
        return this.submit('get', url);
    }

    submit(method, url) {
        return new Promise((resolve, reject) =>  {
            if (method === 'get' || method === 'delete') {
                axios[method](url, { params: this.data() }) 
                .then(response => {
                        this.onSuccess(response.data);
                        resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);

                    reject(error.response.data);
                });
            } else {
                axios[method](url, this.data(),{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    this.onSuccess(response.data);

                    resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);

                    reject(error.response.data);
                });
            }
        });
    }

    onSuccess(data) {
    }

    onFail(errors) {
        this.errors.record(errors);
    }
}
