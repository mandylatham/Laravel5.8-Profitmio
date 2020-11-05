import Vue from 'vue';
import '../../common';
import axios from 'axios';
import VFacebookLogin from 'vue-facebook-login-component';
import Alert from 'bootstrap-vue';
Vue.use(Alert);

window['app'] = new Vue({
    el: '#settings',
    components: {
        VFacebookLogin
    },
    data: {
        model: {},
        loginOptions: { scope: 'ads_read' }
    },
    mounted() {
        this.saveFacebookAccessTokenUrl = window.saveFacebookAccessTokenUrl;
    },
    methods: {
        handleLogin(response) {
            if(response.authResponse && response.authResponse.accessToken){
                const accessToken = response.authResponse.accessToken;
                axios
                    .post(this.saveFacebookAccessTokenUrl, { accessToken })
                    .then(response => {
                        console.log('Access Token saved');
                        this.notifications.settings = [];
                    })
                    .catch(error => {
                        console.log('error', error);
                        window.PmEvent.fire('errors.api', "Unable to save Facebook Access Token");
                    });
            }
        }
    }
});
