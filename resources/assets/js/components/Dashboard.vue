<template>
  <main class="main">

    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>

    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          
        </div>
        <div class="card-body">
          <div class="row">
            <!--Grafico de compras -->
            <div class="col-md-6">
              <div class="card card-chart">

                <div class="card-header">
                  <h4>Ingresos</h4>
                </div>
                <!-- graficos -->
                <div class="card-content">
                  <canvas id="incomes">

                  </canvas>
                </div>

                <div class="card-footer">
                  <p>Compras de los ultimos meses</p>
                </div>
              </div>
            </div>

            <!--grafico estadistico de ventas -->
            <div class="col-md-6">
              <div class="card card-chart">

                <div class="card-header">
                  <h4>Ventas</h4>
                </div>
                <!-- graficos -->
                <div class="card-content">
                  <canvas id="sales">

                  </canvas>
                </div>

                <div class="card-footer">
                  <p>Ventas de los ultimos meses</p>
                </div>
              </div>
            </div>
          </div>
      
        </div>
      </div>
    </div>
  </main>
</template>

<script>
  import Chart from 'chart.js';
  export default {
    data (){
      return{

        months: ['undefined','Enero','Febrero','Marzo', 'Abril', 'Mayo',
                  'Junio','Julio','Agosto','Septiembre','Octubre',
                  'Noviembre','Diciembre'], 

        // Ingresos
        chart_location_i : null,  //Elemento html donde se ubica el chart 
        chart_income_i   : null,  //Variable que contine al chart
        incomes_data     : [],    //Array que almacena la data de ingresos que se recibe del controlador
        months_data      : [],    //todos los meses transcurridos del año en curso (numeros)
        total_incomes    : [],     // El total de gananacias de cada mes, de los meses trsncurridos del año en curso

        
        // Ventas 
        chart_location_v : null,  //Elemento html donde se ubica el chart 
        chart_income_v   : null,  //Variable que contine al chart
        sales_data       : [],    //Array que almacena la data de ventas que se recibe del controlador
        total_sales      : [],     //El total de gananacias de cada mes, de los meses trsncurridos del año en curso 
        
        arrayLabels : [],       
      }
    },
    methods: {

      getData(){
        let me=this;
        var url = '/dashboard';
        axios.get(url)
          .then(function (response) {
            let respuesta = response.data;
            // Traemos la data del controlador
            me.incomes_data = respuesta.incomes;
            me.sales_data   = respuesta.sales;
            // Cargamos la data
            me.loadData();
          })
          .catch(function (error) {
            console.log(error.response);
          });
      },

      loadData(){
        let me=this;

        // Asignamos los datos del array de ingresos y ventas
        // a las variables locales..
        me.incomes_data.map(function(x){
          me.months_data.push(x.month);
          me.total_incomes.push(x.total);
        });

        me.sales_data.map(function(y){
          me.total_sales.push(y.total);
        });

        // Llenamos el array de etiquetas para el grafico
        // con los meses del año, segun los numeros devueltos por la DB 
        me.months_data.map(function(i) {
          me.arrayLabels.push(me.months[i]);
        });
        
        // llenamos los graficos
        me.loadIncomesChart();
        me.loadSalesChart();
      },

      loadIncomesChart(){
        
        let me = this;
        // obtenemos el elemento
        me.chart_location_i = document.getElementById('incomes').getContext('2d');
        
        // Implementación del grafico
        me.chart_income_i = new Chart(me.chart_location_i, {
          type: 'bar',
          data: {
              //Arreglo de todas las etiquetas que va a tener el grafico
              // es decir, los meses transcurridos del año
              labels: me.arrayLabels,
              datasets: [{
                  label: 'Ingresos del Almacen',
                  data: me.total_incomes, //ganancias de cada mes
                  backgroundColor: 'rgba(255, 99, 132, 0.2)',
                  borderColor:'rgba(255, 99, 132, 0.2)',
                  borderWidth: 1
              }]
          },
          options: {
              scales: {
                  yAxes: [{
                      ticks: {
                          beginAtZero: true
                      }
                  }]
              }
          }
        }); 
      },

      loadSalesChart(){

        let me = this;
        // obtenemos el elemento
        me.chart_location_v = document.getElementById('sales').getContext('2d');
        
        // Implementación del grafico
        me.chart_income_v = new Chart(me.chart_location_v, {
          type: 'bar',
          data: {
              //Arreglo de todas las etiquetas que va a tener el grafico
              // es decir, los meses transcurridos del año
              labels: me.arrayLabels,
              datasets: [{
                  label: 'Ventas',
                  data: me.total_sales, //ganancias de cada mes
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  borderColor:'rgba(54, 162, 235, 0.2)',
                  borderWidth: 1
              }]
          },
          options: {
              scales: {
                  yAxes: [{
                      ticks: {
                          beginAtZero: true
                      }
                  }]
              }
          }
        }); 
      }

    },
    mounted() {
      this.getData();
    },
  }
</script>