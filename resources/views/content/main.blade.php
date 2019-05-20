@extends('welcome')
@section('main_content')

<!-- componente main de vue (hacemos que todo el codigo main utilice vue) -->
<!-- luego de copiar toda esta plantilla html a los componentes de Vue.extend({
    se debe ejecutar nuevamente nmp run dev para actualizar los cambios
    o en su defecto npm run dev --watch-->

    <!--indica que la primera opcion del menu, debe msotrar este componente -->
    <template v-if="menu==0">   
        <h1>Componente escritorio</h1>
    </template>

    <template v-if="menu==1">
        <category></category>
    </template>
    
    <template v-if="menu==2">
        <articles></articles>
    </template>

    <template v-if="menu==3">
        <incomes></incomes>
    </template>

    <template v-if="menu==4">
        <providers></providers>
    </template>

    <template v-if="menu==5">
        <h1>ventas</h1>
    </template>

    <template v-if="menu==6">
        <clients></clients>
    </template>

    <template v-if="menu==7">
        <users></users>
    </template>

    <template v-if="menu==8">
       <roles></roles>
    </template>

    <template v-if="menu==9">
        <h1>Reporte de ingresos</h1>
    </template>

    <template v-if="menu==10">
        <h1>Reporte de ventas</h1>
    </template>

    <template v-if="menu==11">
        <h1>Ayuda</h1>
    </template>

    <template v-if="menu==12">
        <h1>Acerca de</h1>
    </template>
@endsection