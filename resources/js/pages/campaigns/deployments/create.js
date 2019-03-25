import Vue from 'vue';
import './../../../common';
import axios from 'axios';
import VueFormWizard from 'vue-form-wizard';
import {generateRoute} from "../../../common/helpers";
Vue.use(VueFormWizard);
import DatePicker from 'vue2-datepicker';
import moment from 'moment';


window['app'] = new Vue({
    el: '#deployments-create',
    components: {
        'spinner-icon': require('./../../../components/spinner-icon/spinner-icon').default,
        'input-errors': require('./../../../components/input-errors/input-errors').default,
        'date-pick': require('./../../../components/date-pick/date-pick').default,
        DatePicker,
        'editor': require('vue2-ace-editor'),
    },
    data: {
        datePickInputClasses: {
            class: 'form-control'
        },
        contactMethods: [{
            value: 'all-sms',
            label: 'All SMS-able'
        }, {
            value: 'all-email',
            label: 'All Email-able'
        }, {
            value: 'sms-only',
            label: 'Only SMS-able which aren\'t Email-able'
        }, {
            value: 'email-only',
            label: 'Only Email-able which aren\'t SMS-able'
        }, {
            value: 'no-resp-email',
            label: 'All Email-able who haven\'t responded yet'
        }, {
            value: 'no-resp-sms',
            label: 'All Sms-able who haven\'t responded yet'
        }],
        groups: [],
        searchFilters: {
            contact_method: {
                value: 'all-sms',
                label: 'All SMS-able'
            },
            data_source_conquest: true,
            data_source_database: true,
            recipients: ['all'],
            max: null
        },
        loading: false,
        showGlobalLoader: false,
        totalRecipients: 0,
        templateData: {
            template: null,
            email_subject: null,
            email_text: null,
            email_html: '',
            text_message: null,
            text_message_image: null,
            type: 'sms'
        },
        templates: []
    },
    mounted() {
        this.templates = window.templates;
        this.fetchRecipientsGroup();
    },
    methods: {
        fetchRecipientsGroup() {
            this.showGlobalLoader = true;
            const recipients = [...this.searchFilters.recipients];
            const dataSources = [];
            if (this.searchFilters.data_source_conquest) {
                dataSources.push('conquest');
            }
            if (this.searchFilters.data_source_database) {
                dataSources.push('database');
            }
            axios
                .get(window.searchRecipientsUrl, {
                    params: {
                        contact: this.searchFilters.contact_method.value,
                        max: this.searchFilters.max,
                        lists: recipients,
                        data_source: dataSources
                    }
                })
                .then(response => {
                    this.groups = response.data.groups;
                    this.showGlobalLoader = false;
                    this.totalRecipients = response.data.total;
                });
        },
        fetchTemplate() {
            if (this.templateData.template) {
                this.showGlobalLoader = true;
                const url = generateRoute(window.getTemplateJsonUrl, {templateId: this.templateData.template.id});
                axios.get(url)
                    .then(response => {
                        this.showGlobalLoader = false;
                        const template = response.data;
                        this.templateData.type = template.type;
                        if (template.type === 'email') {
                            this.templateData.email_subject = template.email_subject;
                            this.templateData.email_text = template.email_text;
                            this.templateData.email_html = template.email_html;
                        }
                        if (template.type === 'sms') {
                            this.templateData.text_message = template.text_message;
                            this.templateData.text_message_image = template.text_message_image;
                        }
                    });
            } else {
                this.templateData.text_message = '';
                this.templateData.text_message_image = '';
                this.templateData.email_text = '';
                this.templateData.email_html = '';
                this.templateData.email_subject = '';
            }
        },
        initEditor: function (editor) {
            require('brace/mode/html').default;
            require('brace/theme/chrome').default;
        },
        save() {
            this.showGlobalLoader = true;
            const dataSources = [];
            if (this.searchFilters.data_source_conquest) {
                dataSources.push('conquest');
            }
            if (this.searchFilters.data_source_database) {
                dataSources.push('database');
            }
            axios
                .post(window.addGroupsUrl, {
                    contact: this.searchFilters.contact_method.value,
                    max: this.searchFilters.max,
                    total: this.totalRecipients,
                    group_count: this.groups.length,
                    lists: this.searchFilters.recipients,
                    sources: dataSources
                })
                .then(response => {
                    const data = {...this.templateData};
                    this.groups.forEach(group => {
                        const dateTime = moment(group.datetime);
                        console.log('dateTime', dateTime);
                        data[group.name + '_date'] = dateTime.format('YYYY-MM-DD');
                        data[group.name + '_time'] = dateTime.format('HH:mm');
                    });
                    return axios.post(window.createCampaignUrl, data);
                })
                .then(response => {
                    this.showGlobalLoader = false;
                    this.$swal({
                        title: 'Drop Created!',
                        type: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.replace(window.dropsIndexUrl);
                    });
                })
                .catch(error => {
                    this.showGlobalLoader = false;
                });
        }
    }
});
