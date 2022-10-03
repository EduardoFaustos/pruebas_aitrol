<?php 
  $imagen= Auth::user()->imagen_url;
  if($imagen==' '){
    $imagen='avatar.jpg';
  }
?>

<style type="text/css">
.navbar, .logo {
    /*background: url({{asset('/imagenes')}}/cat-nav-content.png) repeat-x scroll 0 0 transparent;*/
  }
 
  .example-8 .navbar-brand {
    background: url({{asset('/imagenes')}}/login.png) center / contain no-repeat;
    width: 200px;
    transform: translateX(-60%);
    left: 50%;
    position: absolute;
  }
</style>

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top example-8" role="navigation" style="margin-left:0; background-color: #fff">
      <!-- Sidebar toggle button-->
      
      <a class="navbar-brand text-hide" href="#">Brand Text
        </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu" >
        <ul class="nav navbar-nav">
            
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{asset('/avatars').'/'.$imagen}}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs" style="color:black">{{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{asset('/avatars').'/'.$imagen}}" class="img-circle" alt="User Image">

                <p style="color:black">
                  Hola {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
               @if (Auth::guest())
                  <div class="pull-left">
                    <a href="{{ route('login') }}" class="btn btn-default btn-flat">Login</a>
                  </div>
               @else
                 <div class="pull-left">
                    <a href="{{ route('perfil.editar') }}" class="btn btn-default btn-flat">Perfil</a>
                  </div>
                 <div class="pull-right">
                    <a class="btn btn-default btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Cerrar Sesion 
                    </a>
                 </div>
                @endif
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      {{ csrf_field() }}
   </form>