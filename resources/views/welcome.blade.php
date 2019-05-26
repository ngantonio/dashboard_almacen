<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema Ventas Laravel Vue Js- IncanatoIT">
    <meta name="author" content="Gabriel Antonio">
    <meta name="keyword" content="Sistema ventas Laravel Vue Js, Sistema compras Laravel Vue Js">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 

    <!-- Tag meta para obtener el Id de usuario -->
    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : ''}}">  

    <title>Administración de Almacén</title>
    <!-- Icons -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/simple-line-icons.min.css" rel="stylesheet">
    <!-- Main styles for this application -->
    <link href="css/style.css" rel="stylesheet">
</head>


<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    
    <!-- inicio de vue -->
    <div id="app">
    
        <!--Navbar -->
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
            <span class="navbar-toggler-icon"></span>
            </button>
           
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
            <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item px-3">
                    <a class="nav-link" href="#">Escritorio</a>
                </li>
                <li class="nav-item px-3">
                    <a class="nav-link" href="#">Configuraciones</a>
                </li>
            </ul>
            <ul class="nav navbar-nav ml-auto">
                
                <!-- Notificaciones aqui -->
                <!-- enviamos como parametro (o propiedad), al arreglo de notificaciones
                el parametro  "notifications", va a ser igual al arreglo notifications 
                definido en app.js -->
                <notification :notifications="notifications"></notification>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                        <span class="d-md-down-none"> {{ Auth::user()->username }} </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>
                        <a class="dropdown-item" href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-lock"></i> Cerrar sesión</a>
                            <!-- el estilo es para que no sea visible -->
                            <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                    </div>
                </li>
            </ul>
        </header>
        <!--end navbar -->

        <div class="app-body">
            <!-- auth()->user()-->
            @auth
                <!-- si es un administrador -->
                @if(Auth::user()->id_role == 1)
                    <!-- sidebar -->
                    @include('content.sidebar_admin')
                    <!-- End sidebar -->
                @elseif(Auth::user()->id_role == 2)
                    <!-- si es un vendedor -->
                    @include('content.sidebar_seller')
                @else
                    @include('content.sidebar_ware')
                @endif
            @endauth

            <!-- Principal Content -->
            @yield('main_content')
            <!-- End principal Content -->
        </div>

    </div>
    <!--fin de vue -->
   
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- plugin vue.js -->
    <script src="js/app.js"></script>
    <!-- Bootstrap and necessary plugins -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/pace.min.js"></script>
    <!-- Plugins and scripts required by all views -->
    <script src="js/Chart.min.js"></script>
    <!-- GenesisUI main scripts -->
    <script src="js/template.js"></script>
    
</body>

</html>
