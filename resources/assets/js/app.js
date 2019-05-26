
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.$ = window.jQuery = require('jquery');
window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('category', require('./components/Category.vue'));
Vue.component('articles', require('./components/Article.vue'));
Vue.component('clients', require('./components/Client.vue'));
Vue.component('providers', require('./components/Provider.vue'));
Vue.component('roles', require('./components/Role.vue'));
Vue.component('users', require('./components/User.vue'));
Vue.component('incomes', require('./components/Income.vue'));
Vue.component('sales', require('./components/Sales.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));
Vue.component('notification', require('./components/Notification.vue'));

const app = new Vue({
    //App es un identificador
    //data es una propiedad
    el: '#app',
    data: function(){
        return{
            // permitirá controlar que vista se debe de mostrar
            // para cada opcion que se seleccione del sidebar
            menu : 0,
            // permite controlar y visualizar las notificaciones
            notifications : []
        };
       
    },
    //Permite obtener la data de todas las notificaciones desde
    //El controlador NotificationController
    // El metodo create se ejecuta alc rearse el objeto vue
    created() {
        let me = this;
        axios.post('notification/get').then(function(response){
            me.notifications = response.data;
        }).catch(function(error) {
            console.log(error);
        });

        //Abrimos ahora el canal, para la transmision en broadcast
        //capturamos el userId del usuario actual
        //la etiqueta meta, se debe crear en el archivo principal  welcomereturn ['data' => $this->Global_Data
        
        var userId = $('meta[name="userId"]').attr('content');

        // esta funcion echo.private escucha los eventos de transmision usando 
        // el metodo notifications, de echo
        Echo.private('App.User.'+ userId).notification((notification)=>{
            // alimentar el arreglo de notiticaciones cada vez que haya una nueva venta o compra
            // con la nueva notificacion que se envia via broadcast
            // con unshift agrego al inicio del array
            me.notifications.unshift(notification);
        });

        //Despues de aqui, habilitar el BroadcastService provider en Config/App.php
        
    },
});
