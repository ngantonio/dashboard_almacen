## Descripción General 

####  Proyecto de compras y ventas de artículos y administración de un almacén realizado con Laravel. 

## Contenido

1. [Reglas del Negocio](#rules) 
2. [Librerías y herramientas utilizadas](#tech) 
3. [Escalabilidad](#scal) 
4. [Habilidades adquiridas en el desarrollo del proyecto](#skills) 



<a name="rules"></a>
## 1. Reglas del Negocio


Esta aplicación web consiste en un sistema que permite administrar el almacén de una tienda; sus ingresos y sus ventas, La tienda vende artículos de distintas categorías o departamentos a sus clientes (una venta simple) ademas de comprar artículos a sus distintos proveedores.

Existen 5 tipos de usuario de quienes se almacena información en el sistema, el cliente, el proveedor, un usuario habilitado unicamente para gestionar y crear los ingresos al almacén, es decir el almacenero o wharehouser, un usuario habilitado unicamente para gestionar y crear las ventas que se hacen en la tienda, un vendedor, y un usuario administrador del sistema; que tiene ambas funcionalidades descritas anteriormente ademas de gestionar los usuarios y los roles de usuario.

El sistema maneja 3 roles para quienes tienen contacto directo con el; Administrador, wharehouser o almacenero y vendedor. 

Al entrar al sistema el usuario debe autenticarse con su nombre de usuario y contraseña y según su rol, se le mostraran en el sidebar las opciones a las que tiene permitido acceder.


####  Almacén: 

Al desplegar el sidebar-menú, y hacer click en el dropdown
**Almacén** el usuario podrá visualizar los submenús
**Articulos** y **Categorías**.

En el submenú *Articulos* el usuario podrá visualizar un listado con los últimos 100 artículos que han sido agregados al almacén de la tienda, ordenados por fecha adicionalmente puede realizar una búsqueda más avanzada por el nombre del artículo o su código presionando *Enter*
o el boton *Buscar*. En este listado se permite la opción de editar los datos de un articulo, como de desactivarlo. La desactivación se realiza de forma lógica.

Así mismo puede agregar un nuevo articulo al almacén presionando el botón *Nuevo Artículo* donde se desplegará un modal con el formulario de datos necesarios para registrar un articulo y asignarle una categoría, así
como un generador de código de barras.

En el submenú **Categorias** el usuario podrá visualizar un listado con todas las categorías de artículos existentes, con opciones para editarlas o desactivarlas lógicamente, así como para realizar una búsqueda directa por el Nombre o el Estado de la categoría. 

Del mismo modo se permite agregar una nueva categoría de artículo presionando el botón "**Nueva**"

#### Compras y ventas: 

Para Visualizar las compras realizadas a proveedores, se debe presionar el botón de menú para desplegar el sidebar
menú y seleccionar la opción **Compras** que desplegará un dropdown mostrando las opciones **Ingresos** y **Proveedores**.

##### Ingresos:

Para la Opción **Ingresos**, se mostraran las ultimas 20 compras realizadas ordenadas por fecha. Ademas de tener una opción de búsqueda, que permite filtrar los resultados por numero de comprobante de compra o por tipo de comprobante (factura, boleta, ticket) luego de presionar el botón *Buscar*.

Para cada compra, se tienen dos opciones, La primera permite visualizar los detalles de esa compra, como los artículos comprados, el proveedor, la fecha, y el comprobante y la opción permite anular la compra.

Cuando se anula una compra, se ejecuta un procedimiento almacenado en la base de datos, que actualiza el stock
de los artículos que pertenecen a dicha compra, restando la cantidad del pedido realizada para cada articulo, puesto que al anularla el almacén real nunca recibe ese pedido de compra. 

Las compras se eliminan de forma lógica, cambian a un status
de "Anulado".

Se puede agregar una nueva compra presionando el botón *Nueva*, para este caso aparecerá en la pantalla un formulario pidiendo el llenado de los campos necesarios para registrar una compra. 

En la sección de articulo de dicho formulario, el usuario puede ingresar el código de barras del articulo
directamente en el recuadro de color gris y presionar *Enter*, si el articulo existe, aparecerá a su nombre a un lado del cuadro de texto, una vez hecho esto, se deben rellenar los campos de *Cantidad* y *Precio* y finalmente presionar el botón **Agregar**. Al presionar este botón, el articulo queda agregado al listado de artículos. 

Para una búsqueda más detallada, el usuario podrá presionar el botón [...] a la derecha del recuadro gris, que abrirá un modal permitiendo buscar más de un articulo, de forma manual y agregarlos a la compra, presionando el botón del *check* en color verde a la izquierda de cada articulo de la lista. Se puede modificar la cantidad y el precio de venta de cada articulo. 

Una vez completada la compra, el usuario debe presionar el botón **Registrar Compra**, al presionar este botón se ejecuta un procedimiento almacenado en la base de datos que aumenta la cantidad del stock de cada producto involucrado en la compra y a su vez se genera una notificación que indica al usuario que se ha registrado una compra nueva de forma exitosa en el día actual. 

* *El sistema, para una primera versión solo permite que puedan comprarse artículos que están ya registrados dentro del almacén, los artículos de una compra que no estén en el almacén (nuevos), deben agregarse en la sección de "Artículos".*

##### Proveedores:

Para la opción de proveedores, se muestran todos los proveedores registrados en el sistema, junto a una opción *Nuevo* que permite registrar a un nuevo proveedor. 

##### Ventas:

El procedimiento descrito anteriormente para Las compras, los proveedores y sus operaciones, es similar para el menú **Ventas** y los submenús **Ventas** y **Clientes**, es decir, el proceso de visualización, búsqueda y filtrado, creación de nuevos registros y procedimientos almacenados que modifican el stock de artículos involucrados en una venta al registrarla y al anularla. Adicional a ello, cada venta listada tiene un botón con icono de hoja, donde se puede descargar un comprobante de venta en formato PDF, que indica los detalles de esa venta. 

#### Acceso:

Esta opción permite visualizar y administrar a los usuarios del sistema y sus roles, y es controlada solo por los usuarios cuyo rol sea "Admin". 

En el submenú **Usuarios** se listan todos los usuarios del sistema, con opciones para editar sus datos y desactivarlos, así como un campo de búsqueda que permite filtrar por Nombre o Identificador, presionando *Enter* o en el botón *Buscar*.

Al Hacer click en *Nuevo Usuario* se despliega un modal con los campos necesarios para registrar a un usuario, así como la elección de su rol. 

En el Submenú **Roles** se listan todos los roles existentes así como la opción para desactivar alguno. No se permite agregar más roles al sistema.

<a name="tech"></a>
## 2. Liberias y herramientas utilizadas

Esta aplicación web se encuentra implementada en Laravel v-5.7 con Vue.js v-2.5.

El uso de componentes Vue junto a Laravel viene dado para proveerle a la aplicación un estilo de Single Page Aplication. Obteniendo los datos de forma dinámica desde los controladores de Laravel sin recargar el navegador y trayendo los datos a las vistas solo cuando sean requeridos.

* Libreria Axios, necesaria para comunicar a los componentes Vue con los controladores de Laravel mediante peticiones Ajax.

* Libreria vue-barcode, necesaria para permitir la generación de códigos de barra al registrar un nuevo articulo.

* Liberia sweet-alert, necesaria para gestionar los mensajes de confirmación y alertas presentadas a los usuarios. 

* Libreria Chart.js, necesaria para la implementación de gráficos estadísticos de compras y ventas en el dashboard
de la aplicación.

* Laravel-echo v-1.5.3, necesario para gestionar la difusión de notificaciones via broadcast de canal privado, dentro de la misma aplicación.

* Pusher v-4.4, necesaria para gestionar las notificaciones en tiempo real.

<a name="scal"></a>
## 3. Escalabilidad


####  De concepto 

1. Convertir todo el concepto de la aplicación en un almacén orientado a tiendas por departamentos. 

2. Convertir el modulo *"Categorías"* en un modulo llamado *"Departamentos"* y que cada departamento tenga asignado un descuento, de tal forma que se permita que si un departamento tiene un porcentaje de descuento, el mismo porcentaje se le aplique a cada articulo de ese departamento. 

3. Crear un modulo para promociones, que involucre a una o variascategoríass que se encuentren enpromociónn en un rango de fecha determinada. 4. Crear una opción para generar reportes de ingresos y ventas por mes o por semana.


####  De diseño 

1. Crear un formulario de búsqueda avanzada para obtener y visualizar los artículos, ingresos y ventas, y que este se le presente de forma inicial a usuario de forma que él pueda decidir como visualizar los registros. De tal forma que no se traigan todos los registros. En el caso de las ventas e ingresos, visualizar las ventas y los ingresos iniciales del mes en curso y permitir buscar otros registros por meses o rangos de fechas.

2. Agregar funcionalidad para poder visualizar los artículos por categorías, trayendo n registros por pagina, así como visualizar los últimos artículos agregados. Mejorar la búsqueda por código. 

3. Cambiar algunas funcionalidades y el diseño de las vistas.

<a name="tech"></a>
## 4. Habilidades adquiridas en el desarrollo del proyecto

* Reforzamiento de comandos del Laravel Client Artisan.
* Métodos de la clase DB, que permiten hacer consultas a las tablas de forma más especifica a diferencia de eloquent. 
* Creación e integración de componentes Vue en laravel, así como el desarrollo de los mismos y toda la lógica detrás de ellos. 
* Métodos especiales de Vue y su hilo de ejecución.
* Reforzamiento de Rutas, middlewares y grupos de rutas.
* Implementación de un sistema de notificaciones básico por broadcast con laravel Echo y su aparición en tiempo real con pusher.


Las habilidades más básicas fueron adquiridas en la realización del siguiente proyecto: https://github.com/ngantonio/shoppingMarket 