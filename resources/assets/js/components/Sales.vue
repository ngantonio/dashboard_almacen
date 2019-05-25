<!-- con v-model se hace el enlace con vue, se almacena
el valor obtenido en esa variable de vue -->
<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ventas &nbsp;            
                   <!--boton para agregar un nuevo ingreso, vizualizeDetails() muestra el formulario -->
                    <button @click="visualizeDetails()" type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nueva venta
                    </button>
                </div>
                
                <!-- Listado de ventas -->
                <template v-if="flag_list_sales == 1">
                   <!-- si la variable flag_list_incomes es = 1, se visualiza el div del listado -->
                  <div class="card-body">
                      <!-- Formulario de busqueda: por tipo de comprobante,tipo de comprobante y fecha -->
                      <!-- search box -->
                      <div class="form-group row">
                        <div class="col-md-10">
                          <div class="input-group"> 
                            <!-- agregamos directivas v-model al select y al input para poder obtener sus estados-->
                            <select v-model="search_criteria" class="form-control col-md-5">
                              <option value="ticket_number" selected>Número de Comprobante</option> 
                              <option value="ticket_type">Tipo de Comprobante</option>
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
                                  <th>Cliente</th>
                                  <th>Tipo comprobante</th>
                                  <th>Serie</th>
                                  <th>Fecha venta</th>
                                  <th>Impuesto</th>
                                  <th>Total</th>
                                  <th>Estado</th>
                              </tr>
                          </thead>
                          <tbody>
                            <!-- hacemos un for para iterar por todo el array de Ingresos -->
                              <tr v-for="sale in arraySales" :key="sale.id">
                                  <!--Opciones -->
                                  <td>
                                    <!-- enviamos el objeto ingreso de esta fila para visualizar detalladamente, a la funcion viewIncome-->
                                      <button @click="viewSale(sale.id)" type="button" class="btn btn-success btn-sm">
                                          <i class="icon-eye"></i>
                                      </button>
                                       <button @click="loadPDF(sale.id)" type="button" class="btn btn-info btn-sm">
                                          <i class="icon-doc"></i>
                                      </button>
                                      <!--desactivar/anular ingreso -->
                                      <template v-if="sale.status == 'Registrado'">           
                                        <button @click="desactivar(sale.id)" type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                        </button>
                                      </template>
                                      
                                  </td>

                                  <td v-text="sale.id"></td>
                                  <!-- funcion user() en el modelo sale -->
                                  <td v-text="sale.user"></td>
                                  <td v-text="sale.name"></td>  <!--provider -->
                                  <td v-text="sale.ticket_type"></td>
                                  <td v-text="sale.ticket_serie"></td>
                                  <td v-text="sale.date"></td>
                                  <td v-text="sale.tax"></td>
                                  <td v-text="sale.total"></td>
                                  <td v-text="sale.status"></td>
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
                 <!-- End Listado de ventas -->

                <!-- Detalles de una venta -->
                <!-- Si flag_list_sales es 0 (al presionar el boton *Nuevo*), se muestra el formulario para agregar un ingreso-->
                <template v-else-if="flag_list_sales == 0"> 
                  <div class="card-body">
                    <div class="row form-group border">
                      <!--seleccionar Cliente -->
                      <div class="col-md-8">
                        <div class="form-group">
                          <label>Cedula Cliente
                            <span style="color: red;" v-show="client_dni == 0">(*Seleccione)</span>
                          </label>
                          <div class="form-inline">
                            <!-- al presionar enter, se llama a la funcion getClient() la cual buscara y mostrará a un cliente -->
                            <input type="text" class="form-control" v-model="client_dni" @keyup.enter="selectClient()" placeholder="Documento de identidad">
                            <!-- este boton despliega un modal que permite buscar, seleccionar y mostrar un articulo-->
                            <input type="text" readonly class="form-control" v-model="client_name">
                          </div>
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Descuento</label>
                          <input type="number" value="0" class="form-control" v-model="discount">
                        </div>
                      </div>

                       <!-- Seleccionar impuesto -->
                      <div class="col-md-2">
                        <label>Impuesto(*)</label>
                        <input type="text" class="form-control" v-model="tax">
                      </div>   

                      <!-- seleccionar tipo de comprobante -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <label> Tipo de comprobante (*)</label>
                          <select class="form-control" v-model="ticket_type">
                            <option value="0" disable selected >Seleccione</option>
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
                          <label>Número de comprobante</label>
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
                    <!--end form-group -->

                    <div class="form-group row border">
                      <div class="col-md-6">
                          <div class="form-group">
                          <label>Artículo
                            <span style="color: red;" v-show="article_id == 0">(*Seleccione)</span>
                          </label>
                            <div class="form-inline">
                              <!-- al presionar enter, se llama a la funcion getArticle() la cual buscara y traerá el articulo por su codigo -->
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
                          <input type="number" class="form-control" disabled v-model="price">
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
   
                    <!-- Muestra una table con el listado de articulos que han sido agregados 
                    en tiempo real y los totales -->
                    <div class="form-group row border">
                      <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm">
                          <thead>
                            <th>Opciones</th>
                            <th class="col-md-4">Articulo</th>
                            <th class="col-md-2">Precio</th>
                            <th class="col-md-2">Cantidad</th>
                            <th class="col-md-2">Descuento</th>
                            <th class="col-md-2">Subtotal</th>
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
                                <td><input v-model="detail.price" type="number" class="form-control" disabled="true"></td>
                                
                                <!-- se valida que la cantidad que se ingrese en la tabla, sea menor que el stock -->
                                <td>
                                  <span style="color:red;" v-show="detail.quantity > detail.stock"> stock disp: {{ detail.stock }}</span>
                                  <input v-model="detail.quantity" type="number" class="form-control">
                                </td>
                                
                                <!-- se valida que el descuento  sea mayor al subtotal precio.cantidad -->
                                <td>
                                  <span style="color:red;" v-show="detail.discount > (detail.price * detail.quantity)"> descuento invalido</span>
                                  <input v-model="detail.discount" type="number" class="form-control">
                                </td>
                                <td>${{ detail.price*detail.quantity - detail.discount }}</td> <!--subtotal -->
                              </tr>

                              <!-- Totales calculados en tiempo real-->
                              <tr style="background-color: #CEECF5;">
                                <td colspan="5" align="right"><strong> Total Parcial</strong></td>
                                <td>${{ total_parcial= (total-total_tax).toFixed(2) }}</td>
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="5" align="right"><strong> Total Impuesto</strong></td>
                                <td>${{ total_tax = ((total*tax)/(1+tax)).toFixed(2) }}</td>
                                <!-- con toFixed() se redondean los decimales -->
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="5" align="right"><strong> Total Neto</strong></td>
                                <!-- llamamos al metodo computedTotals dentro de la propiedad "Computed" de vue-->
                                <td>${{total = computedTotals }}</td>
                              </tr>
                          </tbody>

                          <!-- Si no hay ningun elemento dentro del array... -->
                          <tbody v-else>
                            <tr>
                              <td colspan="6">No hay articulos agregados ahora...</td>
                            </tr>
                          </tbody>

                        </table>
                      </div>
                    </div>
                    <!-- end listado de articulos que han sido agregados -->
                    
                    <!-- Botones para cerrar o registrar el ingreso-->
                    <div class="form-group row">
                      <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" @click="hideDetails()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrar()">Registrar Venta</button>
                      </div>
                    </div>
                  </div>
                </template>
                <!-- End Detalles de un ingreso -->

                <!-- Visualizar los detalles de una venta registrada al hacer click en el ojito
                    cuando una venta es registrada, se pueden visualizar sus detalles-->
                <template v-else-if="flag_list_sales == 2">
                  <div class="card-body">
                    <div class="row form-group border">
                      <div class="col-md-8">
                        <div class="form-group">
                          <label>Cliente</label>
                          <p v-text="client_name"></p>
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
                            <th>Descuento</th> 
                            <th>Subtotal</th>
                          </thead>

                          <!-- si hay al menos un elemento dentro del array de Detalles muestra cada articulo-->
                          <tbody v-if="arrayDetails.length">
                              <tr v-for="(detail) in arrayDetails" :key="detail.id">
                                <td v-text="detail.article"></td>
                                <td v-text="detail.price"></td>
                                <td v-text="detail.quantity"></td>
                                <td v-text="detail.discount"></td>
                                <td>${{ detail.price*detail.quantity - detail.discount }}</td> <!--subtotal -->
                              </tr>

                              <!-- Totales -->
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Parcial</strong></td>
                                <td>${{ total_parcial= (total-total_tax).toFixed(2) }}</td>
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Impuesto</strong></td>
                                <td>${{ total_tax = (total*tax).toFixed(2) }}</td>
                                <!-- con toFixed() se redondean los decimales -->
                              </tr>
                              <tr style="background-color: #CEECF5;">
                                <td colspan="4" align="right"><strong> Total Neto</strong></td>
                                <!-- llamamos al metodo computedTotals dentro de la propiedad "Computed" de vue-->
                                <td>${{total}}</td>
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
                    <div class="form-group row">
                      <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" @click="hideDetails()">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </template>
                <!-- End Visualizar los detalles de un ingreso registrado al hacer click en el ojito-->
            </div>     
        </div>
        <!-- end container -->

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
                            <!-- Se puede buscar un articulo por nombre y codigo -->
                            <select v-model="search_criteria_article" class="form-control col-md-3">
                                <option value="name">Nombre</option>
                                <option value="code" selected>Código</option>
                            </select>
                            <input v-model="text_search_article" @keyup.enter="listarArticulos(text_search_article,search_criteria_article)" type="text" class="form-control" placeholder="Texto a buscar">
                            <button @click="listarArticulos(text_search_article,search_criteria_article)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                          </div>
                        </div>
                      </div>
                      <!--end search box -->

                      <!-- Despliega la informacion de cada articulo -->
                      <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Stock</th>
                                    <th>Precio venta</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                              <!-- hacemos un for para iterar por todos los ingresos -->
                                <tr v-for="article in arrayArticles" :key="article.id">
                                    <td>
                                      <!-- permite agregar un articulo de esta lista, al detalle de ingreso -->
                                        <button @click="addArticleModal(article)" type="button" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td v-text="article.code"></td>
                                    <td v-text="article.name"></td>
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
                        <!-- End table -->   
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="cerrarModal()" type="button" class="btn btn-secondary" >Cerrar</button>
                        <button @click="registrar()" v-if="tipoModal == 1" type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
                <!--modal-content -->
            </div>
            <!--modal-dialog -->
        </div>
  </main>
       <!-- :class="{e se anexara la clase mostrar, si la propiedad modal esta en 1 -->
</template>

<script>
    export default {
      data(){
        return {
          // Datos de un Ingreso:
          sale_id       : 0,
          client_id     : 0,
          client_dni    : 0,
          client_name   : '',  
          user_id       : 0, 
          ticket_type   : 'Factura',
          ticket_serie  : 0, 
          ticket_number : 0,
          date          : '',     
          tax           : 0.18,
          total         : 0.0,
          discount      : 0.0,      
          status        : '',

          //Datos de un articulo:
          article_id    : 0,
          code          : 0,
          article       : 0,  // nombre
          quantity      : 0,
          price         : 0,
          stock         : 0,
            
          // arreglos de datos
          arrayDetails  :[],  //Alamacena los detalles (total y cantidad) de articulos seleccionados para un ingreso/venta
          arraySales  :[],  //Almacena todas las ventas traidas del controlador para listarse
          arrayArticles :[],  //Almacena el, o los articulos encontrados

          // Datos para calcular los totales
          total_tax     : 0.0,
          total_parcial : 0.0,

          // Varaibles para el control de la busqueda de Ingresos
          search_criteria: 'ticket_number',
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
          flag_list_sales : 1,

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

          /* para cada elemento del array de articulos, recorre los subtotales y los acumula 
           multiplica el precio por la cantidad*/
          for(var i = 0; i< this.arrayDetails.length; i++){
            result = result + (this.arrayDetails[i].price * this.arrayDetails[i].quantity - this.arrayDetails[i].discount);
          }
          return result;
        }

      },
      methods :{

        // Permite visualizar todos los Ingresos
        listar(page, text, criteria){
          let me=this;    
          let url = '/sale?page=' + page + '&search='+ text + '&criteria='+ criteria;

         // utilizando axios para enviar peticiones http puras
         // utilizando promesas
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              // traemos la data del controlador
              me.arraySales = respuesta.sales.data;
              me.pagination = respuesta.pagination;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        // Permite listar todos los articulos que esten disponibles para su venta 
        // dentro del modal
        listarArticulos(text, criteria){
          let me=this;

          let url = '/article/getArticlesToSale?search='+ text + '&criteria='+ criteria;
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

        // busca un articulo por codigo en la base de datos,
        // cuando el usuario ingresa su codigo y presiona enter
        // al realizar una nueva venta y lo agrega a arrayArticles  
        getArticle(){
          let me=this;

          var url = '/article/searchArticleStock?filter='+me.code;
          axios.get(url)
            .then(function (response) {
              let respuesta = response.data;
              me.arrayArticles = respuesta.article;

              // si se encontro un articulo, copia el nombre y el id 
              // a las variables locales de vue
              if(me.arrayArticles.length > 0){
                me.article = me.arrayArticles[0]['name'];
                me.article_id = me.arrayArticles[0]['id'];
                me.code = me.arrayArticles[0]['code'];
                me.price = me.arrayArticles[0]['sale_price'];
                me.stock = me.arrayArticles[0]['stock'];
              }else{
                //no hay ningun articulo que coincida
                me.article="Articulo no encontrado";
                me.article_id="0";
                me.code="0";
              }
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        // Agrega un articulo al arrayArticles que es seleccionado dentro 
        // del modal 
        addArticleModal(article = []){
          let me = this;

            //si el articulo ya esta agregado  
            if(me.search(article['id'])){
                swal({
                  type: 'error',
                  title: 'Error',
                  text: 'Este articulo ya se encuentra agregado...'
                })
            }else{
              
              me.arrayDetails.push({
                article_id: article['id'],
                article: article['name'],
                code: article['code'],
                quantity: 1,
                price: article['sale_price'],
                discount:0,
                stock: article['stock']
              });
            }
            me.code = "";
            me.article_id = 0;
            me.quantity = 0;
            me.article = "";
            me.price = 0;
            me.discount = 0 ;
            me.stock = 0;
        },

        // Agrega un articulo seleccionado, al arrayDetails, cuando se
        // presiona el boton "agregar" decir, crea un nuevo detalle de articulo
        addDetail(){
          let me = this;

          if(this.article_id > 0 || this.quantity > 0 || this.price > 0){

            //si el articulo ya esta agregado
            if(me.search(me.article_id)){
                swal({
                  type: 'error',
                  title: 'Error',
                  text: '¡Este articulo ya se encuentra agregado!'
                })
            }else{
              // validando que la cantidad de articulo que se agrege al detalle de venta
              // tenga un stock > 0
              if(me.quantity > me.stock) {
                //error
                swal({
                  type: 'error',
                  title: 'Error',
                  text: '¡No hay suficientes articulos!'
                })
              }else{
                me.arrayDetails.push({
                  article_id: me.article_id,
                  code: me.code,
                  article:  me.article,
                  quantity: me.quantity,
                  price: me.price,
                  discount: me.discount,
                  stock: me.stock

                });
              }
            }
            // Limpiamos las variables
            me.code = "";
            me.article_id = 0;
            me.quantity = 0;
            me.article = "";
            me.price = 0;
            me.discount = 0;
            me.stock = 0;
          }
        },
        // Elimina un registro (articulo) del arrayDetails
        dropDetail(idx){
          let me = this;
          me.arrayDetails.splice(idx,1);
        },

        // Almacena una venta en la base de datos
        //junto a sus detalles, los detalles son todos los articulos
        // involucrados y su cantidad y descuentos
        registrar(){
          let me=this;

          if(this.validar())
            return;
          
          axios.post('/sale/store',{  
            'client_id'     :this.client_id,
            'ticket_type'   :this.ticket_type,
            'ticket_number' :this.ticket_number,
            'ticket_serie'  :this.ticket_serie,
            'tax'           :this.tax,
            'total'         :this.total,
            'data'          :this.arrayDetails,

            }).then(function (response) {
              // con hideDetails() quito el formulario de nueva venta
              me.hideDetails();
              me.listar(1,'','ticket_number');
              //visualizamos el reporte una vez se registre una venta
              me.loadPDF(response.data.id);
            }).catch(function (error) {
              console.log(error.response);
            });
            
        },

        //Busca a un cliente en la base de datos
        //dado su numero de identificacion
        selectClient(){
          let me=this;

          var url = '/client/getClient?doc_number='+ me.client_dni;
          axios.get(url)
            .then(function (response) {
              let respuesta = response.data;

              // traemos la data del controlador
              me.client_name = respuesta.client[0]['name'];
              me.client_id = respuesta.client[0]['id'];
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

          if(this.client_id == 0) this.errorMessages.push('No hay cliente seleccionado');
          if(this.ticket_type == 0) this.errorMessages.push('Seleccione el tipo de comprobante');
          if(!this.ticket_number) this.errorMessages.push('Ingrese el numero de comprobante');
          if(!this.tax) this.errorMessages.push('El impuesto de compra es necesario');
          if(this.arrayDetails.length <= 0) this.errorMessages.push('Debe agregar los articulos de la compra!');

          //Validar que la cantidad de cada articulo sea menor que el stock existente
          this.arrayDetails.map(function(x){
            if(x.quantity > x.stock) this.errorMessages.push('¡Hay uno más articulos cuya cantidad supera al stock!');
          });

          if(this.errorMessages.length) this.errorDetection = 1;
          
          return this.errorDetection;
        },

        desactivar(id){
          
          swal({
            title: "¿Deseas anular este Venta?",
            text: "No podrás deshacer esta opción",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {

              let me = this;
              // hacemos una peticion http para desactivar la categoria
              
              axios.put('/sale/disable',{
                'id': id
                }).then(function (response) {
                  
                  me.listar(1,'','ticket_number');
                  swal("Venta anulada!", {
                    icon: "success",
                  });

                }).catch(function (error) {
                  swal("Ha ocurrido un error al desactivar la venta",{
                    icon: "error"
                  });
                 console.log(error);
              });
            }

          });
        },

        // Visualiza la tabla de articulos asociados a un ingreso
        visualizeDetails(){
          this.flag_list_sales = 0;
        },
        // Oculta la tabla de articulos asociados a un ingreso y resetea las variables
        hideDetails(){

          this.flag_list_sales = 1;

          this.sale_id       = 0,
          this.client_id     = 0,
          this.client_dni    = 0,
          this.client_name   = '',
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
          this.stock         = 0;
          this.discount      = 0;
          this.arrayDetails  = [];
        },

        // Trae la data que permite visualizar un ingreso y sus detalles 
        // al hacer click en el ojito
        viewSale(id){

          this.flag_list_sales = 2;

          let me=this;
          //Obtener los datos del ingreso
          var arraySales_temp = [];
          var url = '/sale/getHeader?id='+id; 

          axios.get(url).then(function (response) {
            let respuesta = response.data;
            arraySales_temp = respuesta.sale;

            // Hacemos el llenado de variables de vue
            me.client_name    = arraySales_temp[0]['name'];
            me.ticket_type    = arraySales_temp[0]['ticket_type'];
            me.ticket_serie   = arraySales_temp[0]['ticket_serie'];
            me.ticket_number  = arraySales_temp[0]['ticket_number'];
            me.tax            = arraySales_temp[0]['tax'];
            me.total          = arraySales_temp[0]['total'] 
          })
          .catch(function (error) {
            console.log(error.response);
          });

          // Segunda peticion, traer la data de los detalles:
          var url = '/sale/getDetailsSale?id='+ id;
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

        loadPDF(id){
          window.open('http://localhost:8000/sale/pdf/'+ id +','+'_blank');
        },
      },

      mounted(){
          // hacemos referencia a los metodos definidos arriba
          // que se van a ejecutar
          this.listar(1,this.text_search,this.search_criteria);
          console.log('Component mounted.')
        }
    }    
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