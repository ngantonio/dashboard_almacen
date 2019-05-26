<template>
  <li class="nav-item d-md-down-none">
    <a class="nav-link" href="#" data-toggle="dropdown">
        <i class="icon-bell"></i>
        <span class="badge badge-pill badge-danger">{{ notifications.length }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-header text-center">
            <strong>Notificaciones</strong>
        </div>
         
        <!-- si existen notificaciones, las muestro -->
        <div v-if="notifications.length">
          <!-- recorremos por cada objeto notification
           que nos devuelve la funcion acumulate_notifications-->
          <li v-for="item in acumulate_notifications" :key="item.id">
            <a class="dropdown-item" href="#">
              <!-- este json viene de IncomeController, luego de registrar un ingreso -->
              <i class="fa fa-envelope-o"></i> {{item.incomes.msj}} <!-- Muestra : Ingresos -->
              <span class="badge badge-success">{{item.incomes.cantidad}}</span>
              <!-- era necesario: item.data.data.incomes.msj, cuando se recorre el array 
              con el arreglo notifications, directamente -->
            </a>

            <a class="dropdown-item" href="#">
              <i class="fa fa-tasks"></i> {{item.sales.msj}} <!-- Muestra : Ventas -->
              <span class="badge badge-danger">{{item.sales.cantidad}}</span>
            </a>       
          </li>
        </div>

        <!-- si no, muestro un mensaje -->
        <div v-else>
          <a><span>No hay notificaciones</span></a>
        </div>


    </div>
  </li>
</template>

<script>
  export default {
    // definimos una propiedad, esta es la que se envia como parametro
    // en welcome.blade.php
    props: ['notifications'],
    data (){
      return{
        arrayNotifications : [],
      }
    },
    computed:{
      acumulate_notifications : function(){
        // tenemos que mostrar la ultima notificacion realizada
        // Toma todo el objeto notification, que se encuentra en la pocision 0 y lo copia al array
        this.arrayNotifications = Object.values(this.notifications[0]);
        
        if(this.notifications == '')
          return this.arrayNotifications = [];
        else{
          /* Se comprueba si el arrayNotifications es > 3, si es > 3, se estaria ejecutando 
            una consulta axios, es decir, los datos se estan obteniendo desde la base de datos directamente, 
            en este caso, el array tiene mas propiedades y en esa consulta, los datos reales (el objeto) 
            vienen en el indice 4
          
            sino, se esta ejecutando la consulta por el canal broadcast de laravel, (laravel echo y push),
            cuando se hace una nueva compra o venta, este devuelve los datos en el indice 0 del array notifications */
          if(this.arrayNotifications.length > 3)
            return Object.values(this.arrayNotifications[4]);
          else
            return Object.values(this.arrayNotifications[0]);
        }
      }
    }

  }
</script>
