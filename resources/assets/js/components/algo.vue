


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
          modal : 1,
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

        // agregar un detalle de articulo a una venta: Todo lo que el
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
                  text: '¡Este articulo ya se encuentra agregado!'
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
        addArticleToSale(article = []){
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
           
            'client_id'     :this.client_id,
            'ticket_type'   :this.ticket_type,
            'ticket_number' :this.ticket_number,
            'ticket_serie'  :this.ticket_serie,
            'tax'           :this.tax,
            'total'         :this.total,
            'data'          :this.arrayDetails

            }).then(function (response) {
              me.hideDetails();
              me.listar(1,'','ticket_number');
            }).catch(function (error) {
              console.log(error.response);
            });
            
        },

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

        // Obtiene un articulo, cuando el usuario ingresa su codigo,
        // al realizar una nueva venta  
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
                me.price = me.arrayArticles[0]['sale_price'];
                me.stock = me.arrayArticles[0]['stock'];
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

          this.flag_list_sales = 2;

          let me=this;
          //Obtener los datos del ingreso
          var arrayIncome_temp = [];
          var url = '/sale/getHeader?id='+id; 

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
      },

      mounted(){
          // hacemos referencia a los metodos definidos arriba
          // que se van a ejecutar
          this.listar(1,this.text_search,this.search_criteria);
          console.log('Component mounted.')
        }
    }    
</script>