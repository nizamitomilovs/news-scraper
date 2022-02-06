require('./bootstrap');

window.Vue = require('vue').default;

import router from './router';
import App from './layouts/App.vue';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);


import swal from 'sweetalert2';
window.swal = swal;
window.Fire = new Vue();

const app = new Vue({
    router,
    el: '#app',
    render: h => h(App)
});
