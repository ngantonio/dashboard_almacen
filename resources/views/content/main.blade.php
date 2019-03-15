@extends('welcome')
@section('main_content')

<!-- componente main de vue (hacemos que todo el codigo main utilice vue) -->
<!-- luego de copiar toda esta plantilla html a los componentes de Vue.extend({
    se debe ejecutar nuevamente nmp run dev para actualizar los cambios
    o en su defecto npm run dev --watch-->

    <!--indica que la primera opcion del menu, debe msotrar este componente -->
    <template v-if="menu==0">   
        <h1>El menu ha cambiado a la opcion 0, escritorio</h1>
    </template>

    <template v-if="menu==1">
        <category></category>
    </template>
    
    <template v-if="menu==2">
        <articles></articles>
    </template>

    <template v-if="menu==3">
        <h1>El menu ha cambiado a la opcion 3</h1>
    </template>

    <template v-if="menu==4">
        <h1>El menu ha cambiado a la opcion 4</h1>
    </template>

    <template v-if="menu==5">
        <h1>El menu ha cambiado a la opcion 5</h1>
    </template>

    <template v-if="menu==6">
        <h1>El menu ha cambiado a la opcion 6</h1>
    </template>

    <template v-if="menu==7">
        <h1>El menu ha cambiado a la opcion 7</h1>
    </template>

    <template v-if="menu==8">
        <h1>El menu ha cambiado a la opcion 8</h1>
    </template>

    <template v-if="menu==9">
        <h1>El menu ha cambiado a la opcion 9</h1>
    </template>

    <template v-if="menu==10">
        <h1>El menu ha cambiado a la opcion 10</h1>
    </template>

    <template v-if="menu==11">
        <h1>El menu ha cambiado a la opcion 11</h1>
    </template>

    <template v-if="menu==12">
        <h1>El menu ha cambiado a la opcion 12</h1>
    </template>


@endsection