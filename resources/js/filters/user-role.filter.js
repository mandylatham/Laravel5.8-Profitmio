import Vue from 'vue';

Vue.filter('userRole', function (value, shortVersion = false) {
    if (value === 'admin') {
        return shortVersion ? 'CA' : 'Company Admin';
    } else if (value === 'user') {
        return shortVersion ? 'CU' : 'Company User';
    } else if (value === 'site_admin') {
        return shortVersion ? 'SA' : 'Site Admin';
    }
});
