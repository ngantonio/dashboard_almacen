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
                    <i class="fa fa-align-justify"></i> Administación de Articulos
                   
                   <!--boton de insertar categoria, no utiliza jquery para mostrar el modal -->
                    <button @click="abrirModal('register')" type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo Articulo
                    </button>
                    <button @click="loadPDF()" type="button" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp; Generar Reporte
                    </button>
                </div>
                
                
                <div class="card-body">
                    <!-- search box -->
                    <div class="form-group row">
                      <div class="col-md-6">
                        <div class="input-group">
                          
                          <!-- agregamos directivas v-model al select y al input para poder obtener sus estados-->
                          <select v-model="search_criteria" class="form-control col-md-3">
                              <option value="Nombre">Nombre</option>
                              <option value="Descripcion">Descripción</option>
                          </select>
                          <!-- 
                          - cuando una directiva comienza con @, es la forma simplificada de escribir
                            la directiva v-on 
                          - se pueden enviar los parametros text_search y search_criteria a listar(), proque
                          estan enlazados con las directivas v-model en el input y en el select-->
                          <input v-model="text_search" @keyup.enter="listar(1,text_search,search_criteria)" type="text" class="form-control" placeholder="Texto a buscar">
                          <button @click="listar(1,text_search,search_criteria)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                      </div>
                    </div>
                    <!--end search box -->

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
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                          <!-- hacemos un for para iterar por todas las categorias -->
                            <tr v-for="article in arrayArticles" :key="article.id">
                                <!--Opciones -->
                                <td>
                                  <!-- enviamos el objeto articulo de esta fila a la funcion -->
                                    <button @click="abrirModal('update',article)" type="button" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;

                                    <!--boton para eliminar -->
                                    <template v-if="article.active">  
                                      <button @click="desactivar(article.id)" type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                      </button>
                                    </template>
                                    <!--si el producto está desactivado se muestra este boton para permitir activarlo-->
                                    <template v-else>  
                                      <button @click="activar(article.id)" type="button" class="btn btn-info btn-sm">
                                          <i class="icon-check"></i>
                                      </button>
                                    </template>
                                </td>
                                <td v-text="article.code"></td>
                                <td v-text="article.name"></td>
                                <td v-text="article.category_name"></td>
                                <td v-text="article.stock"></td>
                                <td v-text="article.sale_price"></td>
                                <td v-text="article.description"></td>
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
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>

        <!--Inicio del modal agregar/actualizar, quitamos el atributo id, para poder trabajar con vue-->
        <!-- :class="{'mostrar':modal}", quiere decir que se anexara la clase mostrar, si la propiedad modal esta en 1 -->
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
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                <div class="col-md-9">
                                    <input v-model="name" type="text" class="form-control" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                                <div class="col-md-9">
                                    <input v-model="description" type="text" class="form-control" placeholder="Escribe una descripcion breve...">
                                </div>
                            </div>

                            <!--seleccionar categoria -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Categoria</label>
                                <div class="col-md-9">
                                  <select v-model="category_id" class="form-control">
                                      <option value="0" disabled selected> Seleccione</option>
                                      <option v-for="category in arrayCategories" :key="category.id" :value="category.id" v-text="category.name"></option>
                                  </select>
                                </div>
                            </div>
                            <!-- codigo -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Codigo</label>
                                <div class="col-md-9">
                                    <input v-model="code" type="text" class="form-control" placeholder="Codigo de identificación">
                                    <barcode :value="code" :options="{format:'EAN-13'}"> 
                                      Generando...
                                    </barcode> 
                                </div>
                            </div>
                            <!-- stock -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">N° stock</label>
                                <div class="col-md-9">
                                    <input v-model="stock" type="text" class="form-control" placeholder="N° stock">
                                </div>
                            </div>
                            <!-- precio de venta -->
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Precio de Venta</label>
                                <div class="col-md-9">
                                    <input v-model="sale_price" type="number" class="form-control">
                                </div>
                            </div>

                            
                            <!-- mensajes de error -->
                            <div v-show="errorDetection" class="form-group row div-error">    
                                <div class="col-md-9 text-center text-error">
                                  <div v-for="error in errorMessages" :key="error" v-text="error">
                                  </div>
                                </div>
                            </div>
                      

                        </form>
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

    import VueBarcode from 'vue-barcode';
    export default {
      data(){
        return {
          // Datos de un articulo:
          name         : '',
          description  : '',
          category_id  : '',
          code         : '',
          category_name: '',
          stock        : 0,
          sale_price   : 0.0,
          article_id   : 0,

          // la data recibida de get/article se almancena aqui
          arrayArticles :[],
          // booleano necesario para abrir y cerrar el modal
          modal : 0,
          modalTitle : '',
          // necesaria para poder decidir el tipo de modal que se debe mostrar 
          // segun se quiera guardar o actualizar 
          tipoModal: '', 
          
          // captar la detencion de errores y los mensages de error
          errorDetection : 0,
          errorMessages :[],

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

          // criterio inicial de busqueda
          search_criteria: 'Nombre',
          text_search : '',

          //listado de categorias para seleccionar
          arrayCategories :[],
        }
      },
      components: {
        'barcode': VueBarcode
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
        }

      },
      methods :{

        listar(page, text, criteria){
          let me=this;

          if(criteria == "Nombre")
            criteria = "name";
          else
            criteria = "description";
            

          let url = '/article?page=' + page + '&search='+ text + '&criteria='+ criteria;
         // utilizando axios para enviar peticiones http puras
         // utilizando promesas
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayArticles = respuesta.articles.data;
              me.pagination = respuesta.pagination;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        listarCategorias(){
          
          let me=this;
         
          axios.get('category/active')
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayCategories = respuesta.categories;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            });
        },

        registrar(){
          let me=this;

          if(this.validar())
            return;

          axios.post('/article/new',{
            'name'         : this.name,
            'description'  : this.description,
            'category_id'  : this.category_id,
            'code'         : this.code,
            'stock'        : this.stock,
            'sale_price'   : this.sale_price,
            }).then(function (response) {
              me.cerrarModal();
              me.listar(1,'','nombre');
            }).catch(function (error) {
              console.log(error.response);
            });
        },
        //la accion va a tener dos posible valores, registrar o actualizar
        abrirModal(accion, data = []){
          
          this.listarCategorias();
          switch(accion){

            case "register":

              this.modal = 1;
              this.modalTitle = "Nuevo articulo";
              this.tipoModal = "1";

              this.name = '';
              this.description  = '';
              this.category_id  =  '';
              this.code         =  '';
              this.category_name=  '';
              this.stock        =  0;
              this.sale_price   =  0.0;
              this.article_id   =  0;
            break;
            
            case "update":

              this.modal      = 1;
              this.modalTitle = "Actualizando "+ data['name'];
              this.tipoModal  = "2";   

              this.name        = data['name'];
              this.description = data['description'];
              this.category_id  = data['category_id'];
              this.code         = data['code'];
              this.stock        = data['stock'];
              this.sale_price   = data['sale_price'];
              this.article_id   = data['id'];
            break;        
          }
        
        },

        cerrarModal(){
          this.modal = 0;
          this.modalTitle = "";

          this.name = '';
          this.description  = '';
          this.category_id  =  '';
          this.code         =  '';
          this.category_name=  '';
          this.stock        =  0;
          this.sale_price   =  0.0;
          this.article_id   =  0;
        },

        validar(){
          this.errorDetection = 0;
          this.errorMessages = [];

          // si el nombre del producto es vacio
          if(!this.name) this.errorMessages.push("El nombre no puede estar vacio");          
          if(this.category_id == 0) this.errorMessages.push("Debes seleccionar una categoria");    
          if(!this.stock) this.errorMessages.push("El stock del articulo debe ser un numero y no puede estar vacio");    
          if(!this.sale_price) this.errorMessages.push("El precio del articulo debe ser un numero y no puede estar vacio");    
          
          // si existe un mensaje de error, la variable se activa 
          if(this.errorMessages.length) this.errorDetection = true;   
          
          return this.errorDetection;
        },

        actualizar(){

          let me=this;

          if(this.validar())
            return;
      
          axios.put('/article/update',{
            'name'         : this.name,
            'description'  : this.description,
            'category_id'  : this.category_id,
            'code'         : this.code,
            'stock'        : this.stock,
            'sale_price'   : this.sale_price,
            'id'           : this.article_id
            }).then(function (response) {
              me.cerrarModal();
              me.listar(1,'','nombre');
            }).catch(function (error) {
              console.log(error.response);
            });
        },

        desactivar(id){
          
          swal({
            title: "¿Deseas desactivar este articulo?",
            text: "Podrás deshacer esta accion cuando lo desees",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {

              let me = this;
              // hacemos una peticion http para desactivar la categoria
              
              axios.put('/article/disable',{
                'id': id
                }).then(function (response) {
                  
                  me.listar(1,'','nombre');
                  swal("Producto desactivado!", {
                    icon: "success",
                  });

                }).catch(function (error) {
                  swal("Ha ocurrido un error al desactivar este producto",{
                    icon: "error"
                  });
                 console.log(error);
              });
            }

          });
        },

        activar(id){
          
          let me = this;
          axios.put('/article/enable',{
            'id': id
            }).then(function (response) {
              
              me.listar(1,'','nombre');
              swal("Activada correctamente!", {
                icon: "success",
              });

            }).catch(function (error) {
              swal("Ha ocurrido un error al desactivar el articulo",{
                icon: "error"
              });
              console.log(error);
          });
        },

        loadPDF(){
          window.open('http://localhost:8000/article/pdf','_blank');
        },

        cambiarPagina(pageNumber, text, criteria){
          let me = this;
          // actualiza  la pagina actual
          me.pagination.current_page = pageNumber;
          me.listar(pageNumber, text, criteria); 
        }
      },
      mounted() {
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
</style>
