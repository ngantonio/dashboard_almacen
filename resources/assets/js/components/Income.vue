<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>

        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ingresos &nbsp;
                   
                   <!--boton para agregar un nuevo ingreso -->
                    <button @click="visualizeDetails()" type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                </div>
                
                <!-- Listado de ingresos -->
                <template v-if="flag_list_incomes == 1">
                   <!-- si la variable flag_list_incomes es = 1, se visualiza el div del listado -->
                  <div class="card-body">
                      <!-- Formulario de busqueda: 
                      por tipo de comprobante,tipo de comprobante y fecha -->
                      <!-- search box -->
                      <div class="form-group row">
                        <div class="col-md-10">
                          <div class="input-group"> 
                            <!-- agregamos directivas v-model al select y al input para poder obtener sus estados-->
                            <select v-model="search_criteria" class="form-control col-md-5">
                                <option value="date" selected>Fecha</option>
                                <option value="ticket_type">Tipo de Comprobante</option>
                                <option value="ticket_number">Numero de Comprobante</option> 
                            </select>
                            <input v-model="text_search" @keyup.enter="listar(1,text_search,search_criteria)" type="text" class="form-control" placeholder="Texto a buscar">
                            <button @click="listar(1,text_search,search_criteria)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                          </div>
                        </div>
                      </div>
                      <!--end search box -->

                      <!-- Category list table -->
                      <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                          <thead>
                              <tr>
                                  <th>Opciones</th>
                                  <th>N°</th>
                                  <th>Registrado por</th>
                                  <th>Proveedor</th>
                                  <th>Tipo comprobante</th>
                                  <th>Serie</th>
                                  <th>Fecha ingreso</th>
                                  <th>Impuesto</th>
                                  <th>Total</th>
                                  <th>Estado</th>
                              </tr>
                          </thead>
                          <tbody>
                            <!-- hacemos un for para iterar por todas las categorias -->
                              <tr v-for="income in arrayIncomes" :key="income.id">
                                  <!--Opciones -->
                                  <td>
                                    <!-- enviamos el objeto articulo de esta fila para visualizar, a la funcion -->
                                      <button @click="viewIncome(income.id)" type="button" class="btn btn-success btn-sm">
                                          <i class="icon-eye"></i>
                                      </button> &nbsp;
                                        
                                      <!--desactivar/anular ingreso -->
                                      <template v-if="income.status == 'Registrado'">           
                                        <button @click="desactivar(income.id)" type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                        </button>
                                      </template>
                                  </td>

                                  <td v-text="income.id"></td>
                                  <!-- funcion user() en el modelo income -->
                                  <td v-text="income.user"></td>
                                  <td v-text="income.name"></td>  <!--provider -->
                                  <td v-text="income.ticket_type"></td>
                                  <td v-text="income.ticket_serie"></td>
                                  <td v-text="income.date"></td>
                                  <td v-text="income.tax"></td>
                                  <td v-text="income.total"></td>
                                  <td v-text="income.status"></td>
                              </tr>
                          </tbody>
                        </table>
                      </div>
                      
                      <!-- End Category list table -->
                      
                      <!-- pagination -->
                      <nav>
                          <ul class="pagination">
                              <!-- si la pagina es mayor que 1, si puedo regresar a una pagina anterior -->
                              <li class="page-item" v-if="pagination.current_page > 1">
                                  <a @click.prevent="cambiarPagina(pagination.current_page -1, text_search, search_criteria)" class="page-link" href="#">Ant</a>
                              </li>
                              <!-- recorre las paginas y coloca la clase active segun sea necesario, isActived, retorna la pagina actual -->
                              <li v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active': '']" class="page-item">
                                  <a @click.prevent="cambiarPagina(page, text_search, search_criteria)" class="page-link" href="#" v-text="page"></a>
                              </li>
                              <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                  <a @click.prevent="cambiarPagina(pagination.current_page + 1, text_search, search_criteria)" class="page-link" href="#">Sig</a>
                              </li>
                          </ul>
                      </nav>
                      <!-- End pagination -->
                  </div>
                </template>
                 <!-- End Listado de ingresos -->

                <!-- Detalles de un ingreso -->
                <template v-else-if="flag_list_incomes == 0">
                  <!-- en caso contrario, la vaiable flag_list_incomes es 0, se muestran los detalles del ingreso-->
                  <div class="card-body">
                    <div class="row form-group border">
                      <!--seleccionar proveedores 
                      * 1. ver nota explicativa de esta funcionalidad al final -->
                      <div class="col-md-8">
                        <div class="form-group">
                          <label>Proveedor(*)</label>
                          <select v-model="provider_id" class="form-control">
                            <option value="0" disabled selected> Seleccione</option>
                            <option v-for="provider in arrayProviders" :key="provider.id" :value="provider.id" v-text="provider.name"></option>
                          </select>
                      
                        </div>
                      </div>
        
                      <!-- Seleccionar impuesto -->
                      <div class="col-md-4">
                        <label>Impuesto</label>
                        <input type="text" class="form-control" v-model="tax">
                      </div>

                      <!-- seleccionar tipo de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label> Tipo de comprobante (*)</label>
                          <select class="form-control" v-model="ticket_type">
                            <option value="0">Seleccione</option>
                            <option value="Boleta">Boleta</option>
                            <option value="Factura">Factura</option>
                            <option value="Ticket">Ticket</option>
                          </select>
                        </div>
                      </div>

                      <!-- seleccionar serie de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Serie de comprobante</label>
                          <input type="text" class="form-control" v-model="ticket_serie" placeholder="000x">
                        </div>
                      </div>
                    
                      <!-- seleccionar numero de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Numero de comprobante</label>
                          <input type="text" class="form-control" v-model="ticket_number" placeholder="00xx">
                        </div>  
                      </div>
                      
                      <!-- Visualizacion de Errores -->
                      <div class="col-md-12">
                        <div v-show="errorDetection" class="form-group row div-error">    
                            <div class="col-md-9 text-center text-error">
                              <div v-for="error in errorMessages" :key="error" v-text="error">
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <!--end form-gruop -->

                    <div class="form-group row border">
                      <div class="col-md-6">
                          <div class="form-group">
                          <label>Artículo
                            <span style="color: red;" v-show="article_id == 0">(*Seleccione)</span>
                          </label>
                            <div class="form-inline">
                              <input type="text" class="form-control" v-model="code" @keyup.enter="getArticle()" placeholder="Ingrese articulo">
                              <!-- este boton despliega un modal que permite buscar, seleccionar y mostrar un articulo-->
                              <button @click="abrirModal()" class="btn btn-primary" >...</button>
                              <input type="text" readonly class="form-control" v-model="article">
                            </div>
                          </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Precio
                            <span style="color: red;" v-show="price == 0">(*Seleccione)</span>
                          </label>
                          <input type="number" step="any" class="form-control" v-model="price">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Cantidad
                            <span style="color: red;" v-show="quantity == 0">(*Seleccione)</span>
                          </label>
                          <input type="number" class="form-control" v-model="quantity">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                        <button @click="addDetail()" class="btn btn-success form-control btnAgregar"><i class="icon-plus"></i>
                        Agregar
                        </button>
                        </div>
                      </div>

                    </div>

                    <!-- Listado de articulos que han sido agregados y totales -->
                    <div class="form-group row border">
                      <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm">
                          <thead>
                            <th>Opciones</th>
                            <th>Articulo</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                          </thead>

                          <!-- si hay al menos un elemento dentro del array de Detalles-->
                          <tbody v-if="arrayDetails.length">

                              <tr v-for="(detail, index) in arrayDetails" :key="detail.id">
                                <td>
                                  <!-- funcionalidad para eliminar un articulo de la lista de detalles -->
                                  <button  @click="dropDetail(index)" class="btn btn-danger btn-sm">
                                    <i class="icon-close"></i>
                                  </button>
                                </td>
                                <!-- capturamos los datos del teclado y enlazamos con el modelo de vue -->
                                <td v-text="detail.article"></td>
                                <td><input v-model="detail.price" type="number" class="form-control"></td>
                                <td><input v-model="detail.quantity" type="number" class="form-control"></td>
                                <td>${{ detail.price*detail.quantity }}</td> <!--subtotal -->
                              </tr>

                              <!-- Totales -->
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Parcial</strong></td>
                                <td>${{ total_parcial= (total-total_tax).toFixed(2) }}</td>
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Impuesto</strong></td>
                                <td>${{ total_tax = ((total*tax)/(1+tax)).toFixed(2) }}</td>
                                <!-- con toFixed() se redondean los decimales -->
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Neto</strong></td>
                                <!-- llamamos al metodo computedTotals dentro de la propiedad "Computed" de vue-->
                                <td>${{total = computedTotals }}</td>
                              </tr>
                          </tbody>

                          <!-- este tbody se mostrara cuando el array de detalles este vacio -->
                          <tbody v-else>
                            <tr>
                              <td colspan="5">No hay articulos agregados ahora...</td>
                            </tr>
                          </tbody>

                        </table>
                      </div>
                    </div>
                    <!-- end listado de articulos que han sido agregados -->
                    
                    <!-- Botones para controlar la visualizacion de los detalles del pedido-->
                    <div class="form-group row">
                      <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" @click="hideDetails()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrar()">Registrar Compra</button>

                      </div>
                    </div>
                  </div>
                </template>
                <!-- End Detalles de un ingreso -->

                <!-- Visualizar los detalles de un ingreso registrado al hacer click en el ojito-->
                <template v-else-if="flag_list_incomes == 2">
                  <!-- en caso contrario, la vaiable flag_list_incomes es 0, se muestran los detalles del ingreso-->
                  <div class="card-body">
                    <div class="row form-group border">
                      <div class="col-md-8">
                        <div class="form-group">
                          <label>Proveedor</label>
                          <p v-text="provider_name"></p>
                        </div>
                      </div>
        
                      <!--impuesto -->
                      <div class="col-md-4">
                        <label>Impuesto</label>
                        <p v-text="tax"></p>
                      </div>

                      <!--tipo de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label> Tipo de comprobante</label>
                          <p v-text="ticket_type"></p>
                        </div>
                      </div>

                      <!-- serie de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Serie de comprobante</label>
                          <p v-text="ticket_serie"></p>
                        </div>
                      </div>
                    
                      <!-- numero de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Numero de comprobante</label>
                          <p v-text="ticket_number"></p>
                        </div>  
                      </div>
                    </div>
                    <!--end form-gruop -->

                    <!-- Listado de articulos que fueron agregados al ingreso -->
                    <div class="form-group row border">
                      <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm">
                          <thead>
                        
                            <th>Articulo</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                          </thead>

                          <!-- si hay al menos un elemento dentro del array de Detalles-->
                          <tbody v-if="arrayDetails.length">

                              <tr v-for="(detail) in arrayDetails" :key="detail.id">
                                <td v-text="detail.name"></td>
                                <td v-text="detail.price"></td>
                                <td v-text="detail.quantity"></td>
                                <td>${{ detail.price*detail.quantity }}</td> <!--subtotal -->
                              </tr>

                              <!-- Totales -->
                              <tr style="background-color: #CEECF5;">
                                <td colspan="3" align="right"><strong> Total Parcial</strong></td>
                                <td>${{ total_parcial= (total-total_tax).toFixed(2) }}</td>
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="3" align="right"><strong> Total Impuesto</strong></td>
                                <td>${{ total_tax = (total*tax).toFixed(2) }}</td>
                                <!-- con toFixed() se redondean los decimales -->
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="3" align="right"><strong> Total Neto</strong></td>
                                <!-- llamamos al metodo computedTotals dentro de la propiedad "Computed" de vue-->
                                <td>${{total}}</td>
                              </tr>
                          </tbody>

                          <!-- este tbody se mostrara cuando el array de detalles este vacio -->
                          <tbody v-else>
                            <tr>
                              <td colspan="4">No hay articulos agregados ahora...</td>
                            </tr>
                          </tbody>

                        </table>
                      </div>
                    </div>
                    <!-- end listado de articulos que han sido agregados -->        
                    <div class="form-group row">
                      <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" @click="hideDetails()">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </template>
                <!-- End Visualizar los detalles de un ingreso registrado al hacer click en el ojito-->
            </div>     
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!-- end container -->

        <!-- Modal para seleccionar un articulo -->
        <!-- :class="{e se anexara la clase mostrar, si la propiedad modal esta en 1 -->
        <div :class="{'mostrar':modal}" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                      <!-- titulo del modal se muestra dinamicamente con vue -->
                        <h4 class="modal-title" v-text="modalTitle"></h4>
                        <button @click="cerrarModal()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <!-- listado de todos los articulos para seleccionar -->
                    <div class="modal-body">
                      <!-- search box -->
                      <div class="form-group row">
                        <div class="col-md-6">
                          <div class="input-group">
                            
                            <!-- agregamos directivas v-model al select y al input para poder obtener sus estados-->
                            <!-- en search_criteria_article se va a almacenar la opcion seleccionada -->
                            <select v-model="search_criteria_article" class="form-control col-md-3">
                                <option value="name">Nombre</option>
                                <option value="code" selected="true">Código</option>
                            </select>
          
                            <input v-model="text_search_article" @keyup.enter="listarArticulos(text_search_article,search_criteria_article)" type="text" class="form-control" placeholder="Texto a buscar">
                            <button @click="listarArticulos(text_search_article,search_criteria_article)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                          </div>
                        </div>
                      </div>
                      <!--end search box -->

                      <div class="table-responsive">
                        <!-- Category list table -->
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Stock</th>
                                    <th>Precio venta</th>
            
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                              <!-- hacemos un for para iterar por todas las categorias -->
                                <tr v-for="article in arrayArticles" :key="article.id">
                                    <!--Opciones -->
                                    <td>
                                      <!-- permite agregar un articulo de la lista al detalle de ingreso -->
                                        <button @click="addArticleToIncome(article)" type="button" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                        </button> &nbsp;
                                
                                    </td>
                                    <td v-text="article.code"></td>
                                    <td v-text="article.name"></td>
                                    <td v-text="article.category_name"></td>
                                    <td v-text="article.stock"></td>
                                    <td v-text="article.sale_price"></td>

                                    <td>
                                      <div v-if="article.active">
                                        <span class="badge badge-success">Activo</span>
                                      </div>

                                      <div v-else>
                                        <span class="badge badge-danger">Inactivo ahora</span>
                                      </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- End Category list table -->   
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="cerrarModal()" type="button" class="btn btn-secondary" >Cerrar</button>
                        <button @click="registrar()" v-if="tipoModal == 1" type="button" class="btn btn-primary">Guardar</button>
                        <button @click="actualizar()" v-if="tipoModal == 2" type="button" class="btn btn-primary">Actualizar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->
    </main>
</template>

<script>
    export default {
      data(){
        return {
          // Datos de un Ingreso:
          income_id     : 0,
          provider_id   : 0,
          provider_name : '',  
          user_id       : 0, 
          ticket_type   : 'Boleta',
          ticket_serie  : 0, 
          ticket_number : 0,
          date          : '',     
          tax           : 0.18,
          total         : 0.0,      
          status        : '',

          //Datos de un articulo:
          article_id    : 0,
          code          : 0,
          article       : 0,  // nombre
          quantity      : 0,
          price         : 0,
            
          // arreglos de datos
          arrayDetails  :[],  //Alamacena los detalles (total y cantidad) de articulos seleccionados para un ingreso/venta
          arrayIncomes  :[],  //Almacena todas las ventas traidas del controlador para listarse
          arrayProviders:[],  //Almacena a los proveedores de ventas para poder seleccionar 
          arrayArticles :[],  //Almacena el, o los articulos encontrados

          // Datos para calcular los totales
          total_tax     : 0.0,
          total_parcial : 0.0,

          // Varaibles para el control de la busqueda de Ingresos
          search_criteria: 'date',
          text_search : '',

          // Variables para el control de la busqueda de articulos dentro del modal
          search_criteria_article : "code",
          text_search_article     : "",


          // booleano necesario para abrir y cerrar el modal
          modal : 0,
          modalTitle : '',
          // necesaria para poder decidir el tipo de modal que se debe mostrar 
          // segun se quiera guardar o actualizar 
          tipoModal: '', 
          
          // captar la detencion de errores y los mensages de error
          errorDetection : 0,
          errorMessages :[],

          // Controla la visualizacion del listado; 1 = se visualiza, 0 = oculto
          flag_list_incomes : 1,

          // objeto para manejar la paginacion de categorias 
          pagination : {   
            'total'         : 0,
            'current_page'  : 0,
            'per_page'      : 0,
            'last_page'     : 0,
            'from'          : 0,     
            'to'            : 0          
          },
          offset : 3,

        }
      },
      computed:{  // propiedad computada para obtener la pagina actual 
        isActived : function(){
          return this.pagination.current_page;
        },

        pagesNumber: function(){
          if(!this.pagination.to) // si la pagina es dferente de la ultima
            return [];
        
          // resta para obtener la pagina actual
          var from = this.pagination.current_page - this.offset;
          // si la pagina actual es 0 o menor, se le asigna 1 a from
          if(from < 1)
            from = 1;

          var to = from + (this.offset * 2);
          // si to es >= a la ultima pagina, no serial ogico 
          // y se le asigna ese valor
          if(to >= this.pagination.last_page)
            to = this.pagination.last_page;

          var pagesArray = [];
          while(from <= to) {
            pagesArray.push(from);
            from++;
          }
          return pagesArray;
        },

        // Se calculan los totales de los articulos del ingreso
        // se coloca esta funcion aqui, de manera que se calcule paralelo a las modificaciones
        computedTotals: function(){
          var result = 0;

          /* para cada elemento del array de articulos, multiplica el precio por la cantidad*/
          for(var i = 0; i< this.arrayDetails.length; i++){
            result = result + (this.arrayDetails[i].price * this.arrayDetails[i].quantity);
          }
          return result;
        }

      },
      methods :{
        // Permite visualizar todos los Ingresos
        listar(page, text, criteria){
          let me=this;    
          let url = '/incomes?page=' + page + '&search='+ text + '&criteria='+ criteria;

         // utilizando axios para enviar peticiones http puras
         // utilizando promesas
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              // traemos la data del controlador
              me.arrayIncomes = respuesta.incomes.data;
              me.pagination = respuesta.pagination;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        // Permite listar todos los articulos dentro del modal
        listarArticulos(text, criteria){
          let me=this;

          let url = '/article?search='+ text + '&criteria='+ criteria;
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayArticles = respuesta.articles.data;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        // busca un articulo dentro del arrayDetails de vue
        search(id){
          var find = false;
          var i = 0;

          while(i < this.arrayDetails.length && !find){
            if(this.arrayDetails[i].article_id == id)
              find = true;
            i++;
          }
          return find;
        },

        // agregar un detalle de articulo: Todo lo que el
        // usuario escriba en los inputs al agregar un articulo, 
        // se agrega en las variables de vue esto llenará la tabla de detalles de articulo.
        addDetail(){
          let me = this;

          if(this.article_id > 0 || this.quantity > 0 || this.price > 0){

            //si el articulo ya esta agregado
            if(me.search(me.article_id)){
                swal({
                  type: 'error',
                  title: 'Error',
                  text: 'Este articulo ya se encuentra agregado, para modificarlo por favor, eliminelo de la lista...'
                })
            }else{
              me.arrayDetails.push({
                article_id: me.article_id,
                article:  me.article,
                quantity: me.quantity,
                price: me.price
              });
            }

            // Limpiamos las variables
            me.code = "";
            me.article_id = 0;
            me.quantity = 0;
            me.article = "";
            me.price = 0;
          }
        },

        // Agrega el articulo seleccionado en el modal, al array de articulos
        addArticleToIncome(article = []){
          let me = this;

            //si el articulo ya esta agregado  
            if(me.search(article['id'])){
                swal({
                  type: 'Error',
                  title: 'Error',
                  text: 'Este articulo ya se encuentra agregado...'
                })
            }else{
              
              me.arrayDetails.push({
                article_id: article['id'],
                article: article['name'],
                quantity: 1,
                price: 1
              });
           
            }
            me.code = "";
            me.article_id = 0;
            me.quantity = 0;
            me.article = "";
            me.price = 0;
        },

        // Elimina un registro (articulo) del array De detalles
        dropDetail(idx){
          let me = this;
          me.arrayDetails.splice(idx,1);
        },

        // Almacena un ingreso junto a sus detalles en la base de datos
        registrar(){
          let me=this;

          if(this.validar())
            return;

          axios.post('/incomes/store',{
           
            'provider_id'   :this.provider_id,
            'ticket_type'   :this.ticket_type,
            'ticket_number' :this.ticket_number,
            'ticket_serie'  :this.ticket_serie,
            'tax'           :this.tax,
            'total'         :this.total,
            'data'          :this.arrayDetails

            }).then(function (response) {
              me.hideDetails();
              me.listar(1,'','date');
            }).catch(function (error) {
              console.log(error.response);
            });
            
        },

        selectProvider(){
          let me=this;

          var url = '/provider/getProviders?filter=name';
          axios.get(url)
            .then(function (response) {
              let respuesta = response.data;
              // traemos la data del controlador
              me.arrayProviders = respuesta.providers;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },   

        // Obtiene un articulo, cuando el usuario ingresa su codigo,
        //al realizar un nuevo ingreso  
        getArticle(){
          let me=this;

          var url = '/article/searchArticle?filter='+me.code;
          axios.get(url)
            .then(function (response) {
              let respuesta = response.data;
              me.arrayArticles = respuesta.article;

              // si se encontro un articulo, copia el nombre y el id 
              // a las variables locales de vue
              if(me.arrayArticles.length > 0){
                me.article = me.arrayArticles[0]['name'];
                me.article_id = me.arrayArticles[0]['id'];
              }else{
                //no hay ningun articulo que coincida
                me.article="No existe el articulo";
                me.article_id="0";
              }
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        abrirModal(){
  
          this.modal = 1;
          this.modalTitle = "Seleccione uno o varios articulos";
        
        },

        cerrarModal(){

          this.modal = 0;
          this.modalTitle = ""; 
          this.arrayArticles = [];
          this.search_criteria_article = "code";
          this.text_search_article     = "";      
        },

        validar(){
          this.errorDetection = 0;
          this.errorMessages = [];

          if(this.provider_id == 0) this.errorMessages.push('Seleccione un proveedor');
          if(this.ticket_type == 0) this.errorMessages.push('Seleccione el tipo de comprobante');
          if(!this.ticket_number) this.errorMessages.push('Ingrese el numero de comprobante');
          if(!this.tax) this.errorMessages.push('El impuesto de compra es necesario');
          if(this.arrayDetails.length <= 0) this.errorMessages.push('Debe agregar los articulos de la compra!');

          if(this.errorMessages.length) this.errorDetection = 1;
          
          return this.errorDetection;
        },

        desactivar(id){
          
          swal({
            title: "¿Deseas anular este Ingreso?",
            text: "No podrás deshacer esta opción",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {

              let me = this;
              // hacemos una peticion http para desactivar la categoria
              
              axios.put('/incomes/disable',{
                'id': id
                }).then(function (response) {
                  
                  me.listar(1,'','date');
                  swal("Ingreso anulado!", {
                    icon: "success",
                  });

                }).catch(function (error) {
                  swal("Ha ocurrido un error al desactivar este ingreso",{
                    icon: "error"
                  });
                 console.log(error);
              });
            }

          });
        },

        // Visualiza la tabla de articulos asociados a un ingreso
        visualizeDetails(){
          this.flag_list_incomes = 0;
          this.selectProvider();
        },
        // Oculta la tabla de articulos asociados a un ingreso y resetea las variables
        hideDetails(){
          this.flag_list_incomes = 1;
          this.income_id     = 0;
          this.provider_id   = 0;  
          this.user_id       = 0; 
          this.ticket_type   = 'Boleta',
          this.ticket_serie  = 0; 
          this.ticket_number = 0;
          this.tax           = 0.18,
          this.total         = 0.0,      
          this.status        = '',
          this.article_id    = 0;
          this.code          = 0;
          this.article       = 0;
          this.quantity      = 0;
          this.price         = 0;
          this.arrayDetails  = [];
        },

        // Trae la data que permite visualizar un ingreso y sus detalles 
        // al hacer click en el ojito
        viewIncome(id){

          this.flag_list_incomes = 2;
          let me=this;
          //Obtener los datos del ingreso
          var arrayIncome_temp = [];
          var url = '/incomes/getHeader?id='+id; 

          axios.get(url).then(function (response) {
            let respuesta = response.data;
            arrayIncome_temp = respuesta.income;

            // Hacemos el llenado de variables de vue
            me.provider_name  = arrayIncome_temp[0]['name'];
            me.ticket_type    = arrayIncome_temp[0]['ticket_type'];
            me.ticket_serie   = arrayIncome_temp[0]['ticket_serie'];
            me.ticket_number  = arrayIncome_temp[0]['ticket_number'];
            me.tax            = arrayIncome_temp[0]['tax'];
            me.total          = arrayIncome_temp[0]['total'] 
          })
          .catch(function (error) {
            console.log(error.response);
          });

          // Segunda peticion, traer la data de los detalles:
          var url = '/incomes/getDetailsIncome?id='+ id;
          axios.get(url)
            .then(function (response) {
              let respuesta = response.data;
              me.arrayDetails = respuesta.details;
            })
            .catch(function (error) {
              console.log(error.response);
            });       
        },

        cambiarPagina(pageNumber, text, criteria){
          let me = this;
          // actualiza  la pagina actual
          me.pagination.current_page = pageNumber;
          me.listar(pageNumber, text, criteria); 
        },
      },

      mounted(){
          // hacemos referencia a los metodos definidos arriba
          // que se van a ejecutar
          this.listar(1,this.text_search,this.search_criteria);
          console.log('Component mounted.')
        }
}
    /*
    El que no ama, llegó a amar y no lo hizo por hacer, lo hizo porque le nació, porque amó con todas sus fuerzas y perdió; y tal cicatriz la conmemora hoy ya que hace un año empezó a abrirse la herida. */
    
</script>

<style>
  .modal-content{
    width: 100% !important;
    position: absolute !important;
  }
  .mostrar{
    display: list-item !important;
    opacity: 1 !important;
    position: absolute !important;
    background-color: #3c29297a !important;
  } 
  .div-error{
    display: flex;
    justify-content: center;
  }
  .text-error{
    color: red !important;
  }

  @media(min-width: 600px){
    .btnAgregar{
      margin-top: 2rem;
    }
  }
</style>

