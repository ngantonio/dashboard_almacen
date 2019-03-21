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
                    <i class="fa fa-align-justify"></i> Usuarios
                   
                   <!--boton de insertar categoria, no utiliza jquery para mostrar el modal -->
                    <button @click="abrirModal('register')" type="button" class="btn btn-secondary">
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
                              <option value="document_number">Identificador</option>
                              <option value="email">Email</option>
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
                                <th>Nombre</th>
                                <th>Id</th>
                                <th>Número</th>
                                <th>Direccion</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                          <!-- hacemos un for para iterar por todas las categorias -->
                            <tr v-for="user in arrayUsers" :key="user.id">
                                <!--Opciones -->
                                <td>
                                  <!-- enviamos el objeto articulo de esta fila a la funcion -->
                                    <button @click="abrirModal('update',user)" type="button" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <!--boton para desactivar -->
                                    <template v-if="user.active">  
                                      <button @click="desactivar(user.id)" type="button" class="btn btn-danger btn-sm">
                                          <i class="icon-trash"></i>
                                      </button>
                                    </template>
                                    <!--si el producto está desactivado se muestra este boton para permitir activarlo-->
                                    <template v-else>  
                                      <button @click="activar(user.id)" type="button" class="btn btn-info btn-sm">
                                          <i class="icon-check"></i>
                                      </button>
                                    </template>
                                </td>
                                <td v-text="user.name"></td>
                                <td v-text="user.document_type"></td>
                                <td v-text="user.document_number"></td>
                                <td v-text="user.address"></td>
                                <td v-text="user.phone_number"></td>
                                <td v-text="user.email"></td>
                                <td v-text="user.username"></td>
                                <td v-text="user.rolename"></td>
                                <td>
                                  <div v-if="user.active">
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
                                <label class="col-md-3 form-control-label" for="email-input">Tipo de Identificación</label>
                                <div class="col-md-9">
                                  <select v-model="document_type" class="form-control col-md-3">
                                    <option value="CI">CI</option>
                                    <option value="PASS">PASS</option>
                                  </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">N° Identificación</label>
                                <div class="col-md-9">
                                    <input v-model="document_number" type="text" class="form-control">
                                </div>
                            </div>
                           
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Dirección</label>
                                <div class="col-md-9">
                                    <input v-model="address" type="text" class="form-control">
                                </div>
                            </div>
                            
                              <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">N° de Teléfono</label>
                                <div class="col-md-9">
                                    <input v-model="phone_number" type="number" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Email</label>
                                <div class="col-md-9">
                                    <input v-model="email" type="email" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Nombre de usuario*</label>
                                <div class="col-md-9">
                                    <input v-model="username" type="text" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Contraseña*</label>
                                <div class="col-md-9">
                                    <input v-model="password" type="text" class="form-control">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-md-3 form-control-label" for="email-input">Rol*</label>
                                <div class="col-md-9">
                                  <select v-model="id_role" class="form-control col-md-3">
                                    <option value="0" disabled>Seleccione</option>
                                    <option v-for="role in arrayRole" :key="role.id" :value="role.id" v-text="role.rolename"></option>
                                  </select>
                                </div>
                            </div>

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

    export default {
      data(){
        return {
          // Datos de un articulo:
          name            :'',          
          document_type   :'CI',
          document_number :'',
          address         :'',
          phone_number    :'',
          email           :'',
          person_id       : 0,
          username        :'',
          password        :'',
          id_role         : 0,
          active          :'',
          arrayRole       :[],

          // la data recibida de get/article se almancena aqui
          arrayUsers :[],
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

        listar(page, text, criteria){
          let me=this;

          if(criteria == "Nombre")
            criteria = "name";

          if(criteria == "Identificador")
            criteria = "document_number";
          else
            criteria = "email";
          
            
          let url = '/users?page=' + page + '&search='+ text + '&criteria='+ criteria;
         // utilizando axios para enviar peticiones http puras
         // utilizando promesas
          axios.get(url)
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayUsers = respuesta.users.data;
              me.pagination = respuesta.pagination;
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

          axios.post('/users/new',{
            'name'            :  this.name,                
            'document_type'   :  this.document_type,
            'document_number' :  this.document_number,
            'address'         :  this.address,
            'phone_number'    :  this.phone_number,
            'email'           :  this.email,
            'username'        :  this.username,
            'password'        :  this.password,
            'id_role'         :  this.id_role,

            }).then(function (response) {
              me.cerrarModal();
              me.listar(1,'','Nombre');
            }).catch(function (error) {
              console.log(error.response);
            });
        },
        //la accion va a tener dos posible valores, registrar o actualizar
        abrirModal(accion, data = []){

          this.selectRole();
          switch(accion){

            case "register":

              this.modal = 1;
              this.modalTitle = "Registrar Usuario";
              this.tipoModal = "1";

              this.name            ='';          
              this.document_type   ='CI';
              this.document_number ='';
              this.address         ='';
              this.phone_number    ='';
              this.email           ='';
              this.username        ='';
              this.password        ='';
              this.id_role         =0;
              this.person_id       =0;          
              break;
            
            case "update":

              this.modal      = 1;
              this.modalTitle = data['username'];
              this.tipoModal  = "2";   

              this.name            = data['name'];          
              this.document_type   = data['document_type'];
              this.document_number = data['document_number'];
              this.address         = data['address'];
              this.phone_number    = data['phone_number'];
              this.email           = data['email'];
              this.person_id       = data['id'];
              this.username        = data['username'];
              this.password        = data['password'];
              this.id_role         = data['id_role'];
            break;        
          }
        
        },

        cerrarModal(){
          this.modal = 0;
          this.modalTitle = "";

          this.name            ='';          
          this.document_type   ='CI';
          this.document_number ='';
          this.address         ='';
          this.phone_number    ='';
          this.email           ='';
          this.person_id       = 0;
          this.contact_name    ='';
          this.contact_phone   ='';
          this.username        ='';
          this.password        ='';
          this.id_role         =0;  
        },

        validar(){
          this.errorDetection = 0;
          this.errorMessages = [];

          if(!this.name) this.errorMessages.push("El campo nombre es requerido");
          if(!this.username) this.errorMessages.push("El nombre de usuario es requerido");
          if(!this.password) this.errorMessages.push("Debes introducir una contraseña válida");
          if(this.id_role == 0) this.errorMessages.push("Debes establecer un Rol válido");                              
          
          // si existe un mensaje de error, la variable se activa 
          if(this.errorMessages.length) this.errorDetection = true;   
          
          return this.errorDetection;
        },

        actualizar(){

          let me=this;

          if(this.validar())
            return;
      
          axios.put('/users/update',{
            'name'            :  this.name,                
            'document_type'   :  this.document_type,
            'document_number' :  this.document_number,
            'address'         :  this.address,
            'phone_number'    :  this.phone_number,
            'email'           :  this.email,
            'username'        :  this.username,
            'password'        :  this.password,
            'id_role'         :  this.id_role,
            'id'              :  this.person_id,
            }).then(function (response) {
              me.cerrarModal();
              me.listar(1,'','nombre');
            }).catch(function (error) {
              console.log(error.response);
            });
        },

        cambiarPagina(pageNumber, text, criteria){
          let me = this;
          // actualiza  la pagina actual
          me.pagination.current_page = pageNumber;
          me.listar(pageNumber, text, criteria); 
        },

        desactivar(id){
          
          swal({
            title: "¿Deseas desactivar a este usuario?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {

              let me = this;
              // hacemos una peticion http para desactivar la categoria
              
              axios.put('/users/disable',{
                'id': id
                }).then(function (response) {
                  
                  me.listar(1,'','Nombre');
                  swal("¡Usuario descativado!", {
                    icon: "success",
                  });

                }).catch(function (error) {
                  swal("Ha ocurrido un error al desactivar este usuario",{
                    icon: "error"
                  });
                 console.log(error.response);
              });
            }

          });
        },

        activar(id){
          
          let me = this;
          axios.put('/users/enable',{
            'id': id
            }).then(function (response) {
              
              me.listar(1,'','Nombre');
              swal("¡El usuario esta activo ahora!", {
                icon: "success",
              });

            }).catch(function (error) {
              swal("Ha ocurrido un error al desactivar este usuario",{
                icon: "error"
              });
              console.log(error.response);
          });
        },

        selectRole(){

          let me=this;    
          axios.get('role/select')
            .then(function (response) {
              //almacenamos el objeto de la respuesta (la consula a la bd)
              let respuesta = response.data;
              me.arrayRole = respuesta.roles;
            })
            .catch(function (error) {
              console.log(error.response);
            });
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
