
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('category', require('./components/Category.vue'));
Vue.component('articles', require('./components/Article.vue'));
Vue.component('clients', require('./components/Client.vue'));
Vue.component('providers', require('./components/Provider.vue'));
Vue.component('roles', require('./components/Role.vue'));
Vue.component('users', require('./components/User.vue'));
Vue.component('incomes', require('./components/Income.vue'));
Vue.component('sales', require('./components/Sales.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));

const app = new Vue({
    //App es un identificador
    //data es una propiedad
    el: '#app',
    data: {
        // permitirá controlar que vista se debe de mostrar
        // para cada opcion que se seleccione del sidebar
        menu : 0
    }
});
