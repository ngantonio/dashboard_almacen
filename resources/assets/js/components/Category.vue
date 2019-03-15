<template>

    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>

        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Categorías
                   
                   <!--boton de insertar categoria, no utiliza jquery para mostrar el modal -->
                    <button @click="abrirModal('category','register')" type="button" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
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
                          - se pueden enviar los parametros text_search y search_criteria a listarCategoria(), proque
                          estan enlazados con las directivas v-model en el input y en el select-->
                          <input v-model="text_search" @keyup.enter="listarCategoria(1,text_search,search_criteria)" type="text" class="form-control" placeholder="Texto a buscar">
                          <button @click="listarCategoria(1,text_search,search_criteria)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                      </div>
                    </div>
                    <!--end search box -->

                    <!-- Category list table -->
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                          <!-- hacemos un for para iterar por todas las categorias -->
                            <tr v-for="category in arrayCategory" :key="category.id">
                                <td>
                                  <!-- enviamos el objeto categoria de esta fila a la funcion -->
                                    <button @click="abrirModal('category','update',category)" type="button" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;

                                    <!--boton para eliminar -->
                                    <template v-if="category.active">  
                                      <button @click="desactivarCategoria(category.id)" type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                      </button>
                                    </template>
                                    <!--si la categoria esta desactivada se muestra este boton para permitir activarla-->
                                    <template v-else>  
                                      <button @click="activarCategoria(category.id)" type="button" class="btn btn-info btn-sm">
                                          <i class="icon-check"></i>
                                      </button>
                                    </template>


                                </td>

                                <td v-text="category.name"></td>
                                <td v-text="category.description"> </td>
                                <td>

                                  <div v-if="category.active">
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
                                    <input v-model="name" type="text" class="form-control" placeholder="Nombre de categoría">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                                <div class="col-md-9">
                                    <input v-model="description" type="text" class="form-control" placeholder="Escribe una descripcion breve para esta categoria...">
                                </div>
                            </div>
                            
                            <!-- mensaje de error -->
                            <div v-show="errorCategoryDetection" class="form-group row div-error">
                                
                                <div class="col-md-9 text-center text-error">
                                  <div v-for="error in errorMessages" :key="error" v-text="error">
                                  </div>
                                </div>
                            </div>
                      

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click="cerrarModal()" type="button" class="btn btn-secondary" >Cerrar</button>
                        <button @click="registrarCategoria()" v-if="tipoModal == 1" type="button" class="btn btn-primary">Guardar</button>
                        <button @click="actualizarCategoria()" v-if="tipoModal == 2" type="button" class="btn btn-primary">Actualizar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->

        <!-- Inicio del modal Eliminar -->
        <div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-danger" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Eliminar Categoría</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Estas seguro de eliminar la categoría?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- Fin del modal Eliminar -->

    </main>

</template>

<script>
    export default {
      data(){
        return {
          name: '',
          description : '',
          // los datos que vienen de get /category se almancenan aqui
          arrayCategory :[],
          modal : 0,    // necesaria para abrir y cerrar el modal
          modalTitle : '',
          tipoModal: '', // necesaria para poder decidir cuando se debe mostrar 
                        // el boton guardar y cuando se debe mostrar el boton actualizar
          errorCategoryDetection : 0,
          errorMessages :[],
          category_id : 0,  // necesaria para almacenar el id de la categoria que se va a actualizar
          
          pagination : {   //necesaria para manejar la paginacion de categorias 
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
        }

      },
      methods :{

        listarCategoria(page, text, criteria){
          let me=this;

          if(criteria == "Nombre")
            criteria = "name";
          else
            criteria = "description";
            

          let url = '/category?page=' + page + '&search='+ text + '&criteria='+ criteria;
         // utilizando axios para enviar peticiones http puras
         // utilizando promesas
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayCategory = respuesta.categories.data;
              // llenamos el array de pagination de vue, 
              //con el array de pagination de laravel
              me.pagination = respuesta.pagination;
            })
            .catch(function (error) {
              // handle error
              console.log(error.response);
            })
            .then(function () {
              // always executed
            });
        },

        registrarCategoria(){
          let me=this;

          if(this.validarCategoria()){
            return;
          }

          axios.post('/category/new',{
            'name': this.name,
            'description': this.description
            }).then(function (response) {
              me.cerrarModal();
              me.listarCategoria(1,'','nombre');
            }).catch(function (error) {
              console.log(error);
            });
        },
        //la accion va a tener dos posible valores, registrar o actualizar
        abrirModal(modelo,accion, data = []){
          switch (modelo) {
            case "category":

              switch(accion){

                case "register":
                  this.modal = 1;
                  this.name = '';
                  this.description = '';
                  this.modalTitle = "Nueva Categoria";
                  this.tipoModal = "1";
                break;
                
        
                case "update":
                  this.modal      = 1;
                  this.modalTitle = "Actualizar Categoria";
                  this.tipoModal  = "2";   

                  this.name        = data['name'];
                  this.description = data['description'];
                  this.category_id = data['id'];

                break;        
              }
              
            break;
          }

        },

        cerrarModal(){
          this.modal = 0;
          this.modalTitle = "";
          this.name = "";
          this.description = "";
        },

        validarCategoria(){
          this.errorCategoryDetection = 0;
          this.errorMessages = [];

          // si el nombre de la categoria es vacio
          if(!this.name) this.errorMessages.push("El nombre de la categoria no puede estar vacio");    
          // si existe un mensaje de error, la variable se activa
          if(this.errorMessages.length) this.errorCategoryDetection = 1;
          
          return this.errorCategoryDetection;
        },

        actualizarCategoria(){

          let me=this;

          if(this.validarCategoria())
            return;
      
          axios.put('/category/update',{
            'name': this.name,
            'description': this.description,
            'id': this.category_id
            }).then(function (response) {
              me.cerrarModal();
              me.listarCategoria(1,'','nombre');
            }).catch(function (error) {
              console.log(error);
            });
        },

        desactivarCategoria(id){
          
          swal({
            title: "Deseas desactivar la categoria?",
            text: "Podrás deshacer esta accion cuando lo desees",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {

              let me = this;
              // hacemos una peticion http para desactivar la categoria
              
              axios.put('/category/disable',{
                'id': id
                }).then(function (response) {
                  
                  me.listarCategoria(1,'','nombre');
                  swal("La categoria ya no esta activa!", {
                    icon: "success",
                  });

                }).catch(function (error) {
                  swal("Ha ocurrido un error al desactivar la categoria",{
                    icon: "error"
                  });
                 console.log(error);
              });
            }

          });
        },

        activarCategoria(id){
          
          let me = this;
          axios.put('/category/enable',{
            'id': id
            }).then(function (response) {
              
              me.listarCategoria(1,'','nombre');
              swal("Activada correctamente!", {
                icon: "success",
              });

            }).catch(function (error) {
              swal("Ha ocurrido un error al desactivar la categoria",{
                icon: "error"
              });
              console.log(error);
          });
        },

        cambiarPagina(pageNumber, text, criteria){
          let me = this;
          // actualiza  la pagina actual
          me.pagination.current_page = pageNumber;
          me.listarCategoria(pageNumber, text, criteria); 
        }

      },
      mounted() {
        // hacemos referencia a los metodos definidos arriba
        // que se van a ejecutar
        this.listarCategoria(1,this.text_search,this.search_criteria);
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
