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
            // If files are present, return a FormData object
            let formData = new FormData();
            for (let property in this.originalData) {
                formData.append(property, this[property]);
            }
            return formData;
        }

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
            if (typeof this[property] === 'object' &&
                    this[property] != null &&
                    this[property].name != undefined &&
                    typeof this[property].name === 'string') {
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

    put(url) {
        return this.submit('put', url);
    }

    patch(url) {
        return this.submit('patch', url);
    }

    post(url) {
        return this.submit('post', url);
    }

    get(url) {
        return this.submit('get', url);
    }

    delete(url) {
        return this.submit('delete', url);
    }

    submit(method, url) {
        if (!url) {
            return Promise.reject('No url passed.');
        }
        return new Promise((resolve, reject) =>  {
            if (method === 'get' || method === 'delete') {
                axios[method](url, { params: this.data() })
                  .then(response => {
                      resolve(response.data);
                  }, error => {
                      reject(error);
                  });

            } else {
                axios[method](url, this.data())
                  .then(response => {
                      resolve(response.data);
                  }, error => {
                      reject(error);
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
