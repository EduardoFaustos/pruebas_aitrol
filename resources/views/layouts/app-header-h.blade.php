<?php
$imagen = Auth::user()->imagen_url;
if ($imagen == ' ') {
    $imagen = 'avatar.jpg';
}
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$id_empresa = "0992704152001";
//dd($id_empresa);
?>
<nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
    <div class="navbar-header d-xl-block d-none">
        <ul class="nav navbar-nav">
            <li class="nav-item"><a class="navbar-brand" href="{{url('hospital/inicio')}}">
                    <img src="{{asset('/hc4/img/logo1.png')}}" width="80" srcset="">

                    <!--  <h2 class="brand-text mb-0">Vuexy</h2> -->
                </a></li>
        </ul>
    </div>
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
            </ul>
            <!-- <ul class="nav navbar-nav bookmark-icons">
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon" data-feather="mail"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon" data-feather="message-square"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calendar.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon" data-feather="calendar"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon" data-feather="check-square"></i></a></li>
            </ul> -->
            <!-- <ul class="nav navbar-nav">
                <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon text-warning" data-feather="star"></i></a>
                    <div class="bookmark-input search-input">
                        <div class="bookmark-input-icon"><i data-feather="search"></i></div>
                        <input class="form-control input" type="text" placeholder="Bookmark" tabindex="0" data-search="search">
                        <ul class="search-list search-list-bookmark"></ul>
                    </div>
                </li>
            </ul> -->
        </div>
        <ul class="nav navbar-nav align-items-center ml-auto">
            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="{{$classicon}}"></i></a></li>
            <!-- <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon" data-feather="search"></i></a>
                <div class="search-input">
                    <div class="search-input-icon"><i data-feather="search"></i></div>
                    <input class="form-control input" type="text" placeholder="Explora el Sistema..." tabindex="-1" data-search="search">
                    <div class="search-input-close"><i data-feather="x"></i></div>
                    <ul class="search-list search-list-main"></ul>
                </div>
            </li> -->

            <!-- <li class="nav-item dropdown dropdown-notification mr-25"><a class="nav-link" href="javascript:void(0);" data-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span class="badge badge-pill badge-danger badge-up">5</span></a>
                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                    <li class="dropdown-menu-header">
                        <div class="dropdown-header d-flex">
                            <h4 class="notification-title mb-0 mr-auto">Notificaciones</h4>
                            <div class="badge badge-pill badge-light-primary">6 New</div>
                        </div>
                    </li>
                    <li class="scrollable-container media-list"><a class="d-flex" href="javascript:void(0)">
                            <div class="media d-flex align-items-start">
                                <div class="media-left">
                                    <div class="avatar"><img src="" alt="avatar" width="32" height="32"></div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading"><span class="font-weight-bolder">Observaciones Administrativas</span></p><small class="notification-text"> </small>
                                </div>
                            </div>
                        </a><a class="d-flex" href="javascript:void(0)">
                            <div class="media d-flex align-items-start">
                                <div class="media-left">
                                    <div class="avatar"><img src="" alt="avatar" width="32" height="32"></div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading"><span class="font-weight-bolder">Nuevo Mensaje</span>&nbsp;recibido</p><small class="notification-text"> Tienes 10 mensajes sin leer</small>
                                </div>
                            </div>
                        </a><a class="d-flex" href="javascript:void(0)">
                            <div class="media d-flex align-items-start">
                                <div class="media-left">
                                    <div class="avatar bg-light-danger">
                                        <div class="avatar-content">MD</div>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading"><span class="font-weight-bolder">Revised Order 👋</span>&nbsp;checkout</p><small class="notification-text"> MD Inc. order updated</small>
                                </div>
                            </div>
                        </a>

                    </li>
                    <li class="dropdown-menu-footer"><a class="btn btn-primary btn-block" href="javascript:void(0)">Leer todas las notificaciones</a></li>
                </ul>
            </li> -->
            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder">{{ Auth::user()->nombre1}} {{Auth::user()->apellido1}}</span><span class="user-status">{{ Auth::user()->id_tipo}}</span></div><span class="avatar"><img class="round" src="{{asset('/avatars').'/'.$imagen}}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user"><a class="dropdown-item" href="page-profile.html"><i class="mr-50" data-feather="user"></i> Perfil</a><a class="dropdown-item" href="app-email.html"><i class="mr-50" data-feather="mail"></i> Inbox</a><a class="dropdown-item" href="app-todo.html"><i class="mr-50" data-feather="check-square"></i> Task</a><a class="dropdown-item" href="app-chat.html"><i class="mr-50" data-feather="message-square"></i> Chats</a>
                    <div class="dropdown-divider"></div><a class="dropdown-item" href="page-account-settings.html"><i class="mr-50" data-feather="settings"></i> Configuración</a><a class="dropdown-item" href="page-pricing.html"><i class="mr-50" data-feather="credit-card"></i> Pricing</a><a class="dropdown-item" href="page-faq.html"><i class="mr-50" data-feather="help-circle"></i> FAQ</a><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="mr-50" data-feather="power"></i> Cerrar Sesión</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/xls.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/jpg.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/pdf.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
            <div class="d-flex">
                <div class="mr-75"><img src="app-assets/images/icons/doc.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
        </a></li>
    <li class="d-flex align-items-center"><a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Members</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view.html">
            <div class="d-flex align-items-center">
                <div class="avatar mr-75"><img src="app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion justify-content-between"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75" data-feather="alert-circle"></span><span>No results found.</span></div>
        </a></li>
</ul>
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
<div class="horizontal-menu-wrapper">
    <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">

            </ul>
        </div>
        <div class="shadow-bottom"></div>
        
        <div class="navbar-container main-menu-content" data-menu="menu-container">

            <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">

                <li class="dropdown nav-item col-md-2 col-xs-12"><a class="nav-link dropdown-toggle d-flex align-items-center" href="#"><i data-feather="layers"></i><span>Procedimientos</span></a>
                    <ul class="dropdown-menu">
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="{{route('hospital.quirofano',['tipo' => 1])}}" data-toggle="dropdown" data-i18n="Email"><i class="fa fa-stethoscope"></i><span data-i18n="Email">Cirugia</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="{{route('hospital.quirofano',['tipo' => 0])}}" data-toggle="dropdown" data-i18n="Chat"><i data-feather="image"></i><span data-i18n="Chat">Imagen</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item col-md-2 col-xs-12"><a class=" nav-link d-flex align-items-center" href="{{route('hospital_laboratorio.index')}}"><i class="fa fa-flask"></i><span data-i18n="flask">Laboratorio</span></a>
                    <ul class="dropdown-menu">
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-email.html" data-toggle="dropdown" data-i18n="Email"><i data-feather="mail"></i><span data-i18n="Email">Email</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-chat.html" data-toggle="dropdown" data-i18n="Chat"><i data-feather="message-square"></i><span data-i18n="Chat">Chat</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-todo.html" data-toggle="dropdown" data-i18n="Todo"><i data-feather="check-square"></i><span data-i18n="Todo">Todo</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-calendar.html" data-toggle="dropdown" data-i18n="Calendar"><i data-feather="calendar"></i><span data-i18n="Calendar">Calendar</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-kanban.html" data-toggle="dropdown" data-i18n="Kanban"><i data-feather="grid"></i><span data-i18n="Kanban">Kanban</span></a>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Invoice"><i data-feather="file-text"></i><span data-i18n="Invoice">Invoice</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-invoice-list.html" data-toggle="dropdown" data-i18n="List"><i data-feather="circle"></i><span data-i18n="List">List</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-invoice-preview.html" data-toggle="dropdown" data-i18n="Preview"><i data-feather="circle"></i><span data-i18n="Preview">Preview</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-invoice-edit.html" data-toggle="dropdown" data-i18n="Edit"><i data-feather="circle"></i><span data-i18n="Edit">Edit</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-invoice-add.html" data-toggle="dropdown" data-i18n="Add"><i data-feather="circle"></i><span data-i18n="Add">Add</span></a>
                                </li>
                            </ul>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-file-manager.html" data-toggle="dropdown" data-i18n="File Manager"><i data-feather="save"></i><span data-i18n="File Manager">File Manager</span></a>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="eCommerce"><i data-feather="shopping-cart"></i><span data-i18n="eCommerce">eCommerce</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-ecommerce-shop.html" data-toggle="dropdown" data-i18n="Shop"><i data-feather="circle"></i><span data-i18n="Shop">Shop</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-ecommerce-details.html" data-toggle="dropdown" data-i18n="Details"><i data-feather="circle"></i><span data-i18n="Details">Details</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-ecommerce-wishlist.html" data-toggle="dropdown" data-i18n="Wishlist"><i data-feather="circle"></i><span data-i18n="Wishlist">Wishlist</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-ecommerce-checkout.html" data-toggle="dropdown" data-i18n="Checkout"><i data-feather="circle"></i><span data-i18n="Checkout">Checkout</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="User"><i data-feather="user"></i><span data-i18n="User">User</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-user-list.html" data-toggle="dropdown" data-i18n="List"><i data-feather="circle"></i><span data-i18n="List">List</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-user-view.html" data-toggle="dropdown" data-i18n="View"><i data-feather="circle"></i><span data-i18n="View">View</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="app-user-edit.html" data-toggle="dropdown" data-i18n="Edit"><i data-feather="circle"></i><span data-i18n="Edit">Edit</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item col-md-2 col-xs-12"><a class="nav-link d-flex align-items-center" href="{{route('hospital.master_farmacia')}}"><i class="fa fa-pills"></i><span data-i18n="User Interface">Farmacias</span></a>
                    <ul class="dropdown-menu">
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ui-typography.html" data-toggle="dropdown" data-i18n="Typography"><i data-feather="type"></i><span data-i18n="Typography">Typography</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ui-colors.html" data-toggle="dropdown" data-i18n="Colors"><i data-feather="droplet"></i><span data-i18n="Colors">Colors</span></a>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ui-feather.html" data-toggle="dropdown" data-i18n="Feather"><i data-feather="eye"></i><span data-i18n="Feather">Feather</span></a>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Cards"><i data-feather="credit-card"></i><span data-i18n="Cards">Cards</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="card-basic.html" data-toggle="dropdown" data-i18n="Basic"><i data-feather="circle"></i><span data-i18n="Basic">Basic</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="card-advance.html" data-toggle="dropdown" data-i18n="Advance"><i data-feather="circle"></i><span data-i18n="Advance">Advance</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="card-statistics.html" data-toggle="dropdown" data-i18n="Statistics"><i data-feather="circle"></i><span data-i18n="Statistics">Statistics</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="card-analytics.html" data-toggle="dropdown" data-i18n="Analytics"><i data-feather="circle"></i><span data-i18n="Analytics">Analytics</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="card-actions.html" data-toggle="dropdown" data-i18n="Card Actions"><i data-feather="circle"></i><span data-i18n="Card Actions">Card Actions</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Components"><i data-feather="briefcase"></i><span data-i18n="Components">Components</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-alerts.html" data-toggle="dropdown" data-i18n="Alerts"><i data-feather="circle"></i><span data-i18n="Alerts">Alerts</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-avatar.html" data-toggle="dropdown" data-i18n="Avatar"><i data-feather="circle"></i><span data-i18n="Avatar">Avatar</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-badges.html" data-toggle="dropdown" data-i18n="Badges"><i data-feather="circle"></i><span data-i18n="Badges">Badges</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-breadcrumbs.html" data-toggle="dropdown" data-i18n="Breadcrumbs"><i data-feather="circle"></i><span data-i18n="Breadcrumbs">Breadcrumbs</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-buttons.html" data-toggle="dropdown" data-i18n="Buttons"><i data-feather="circle"></i><span data-i18n="Buttons">Buttons</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-carousel.html" data-toggle="dropdown" data-i18n="Carousel"><i data-feather="circle"></i><span data-i18n="Carousel">Carousel</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-collapse.html" data-toggle="dropdown" data-i18n="Collapse"><i data-feather="circle"></i><span data-i18n="Collapse">Collapse</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-divider.html" data-toggle="dropdown" data-i18n="Divider"><i data-feather="circle"></i><span data-i18n="Divider">Divider</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-dropdowns.html" data-toggle="dropdown" data-i18n="Dropdowns"><i data-feather="circle"></i><span data-i18n="Dropdowns">Dropdowns</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-list-group.html" data-toggle="dropdown" data-i18n="List Group"><i data-feather="circle"></i><span data-i18n="List Group">List Group</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-media-objects.html" data-toggle="dropdown" data-i18n=""><i data-feather="circle"></i><span data-i18n="">Media Objects</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-modals.html" data-toggle="dropdown" data-i18n="Modals"><i data-feather="circle"></i><span data-i18n="Modals">Modals</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-navs-component.html" data-toggle="dropdown" data-i18n="Navs Component"><i data-feather="circle"></i><span data-i18n="Navs Component">Navs Component</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-pagination.html" data-toggle="dropdown" data-i18n="Pagination"><i data-feather="circle"></i><span data-i18n="Pagination">Pagination</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-pill-badges.html" data-toggle="dropdown" data-i18n="Pill Badges"><i data-feather="circle"></i><span data-i18n="Pill Badges">Pill Badges</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-pills-component.html" data-toggle="dropdown" data-i18n="Pills Component"><i data-feather="circle"></i><span data-i18n="Pills Component">Pills Component</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-popovers.html" data-toggle="dropdown" data-i18n="Popovers"><i data-feather="circle"></i><span data-i18n="Popovers">Popovers</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-progress.html" data-toggle="dropdown" data-i18n="Progress"><i data-feather="circle"></i><span data-i18n="Progress">Progress</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-spinner.html" data-toggle="dropdown" data-i18n="Spinner"><i data-feather="circle"></i><span data-i18n="Spinner">Spinner</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-tabs-component.html" data-toggle="dropdown" data-i18n="Tabs Component"><i data-feather="circle"></i><span data-i18n="Tabs Component">Tabs Component</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-timeline.html" data-toggle="dropdown" data-i18n="Timeline"><i data-feather="circle"></i><span data-i18n="Timeline">Timeline</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-bs-toast.html" data-toggle="dropdown" data-i18n="Toasts"><i data-feather="circle"></i><span data-i18n="Toasts">Toasts</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="component-tooltips.html" data-toggle="dropdown" data-i18n="Tooltips"><i data-feather="circle"></i><span data-i18n="Tooltips">Tooltips</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Extensions"><i data-feather="box"></i><span data-i18n="Extensions">Extensions</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-sweet-alerts.html" data-toggle="dropdown" data-i18n="Sweet Alert"><i data-feather="circle"></i><span data-i18n="Sweet Alert">Sweet Alert</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-blockui.html" data-toggle="dropdown" data-i18n="Block UI"><i data-feather="circle"></i><span data-i18n="Block UI">BlockUI</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-toastr.html" data-toggle="dropdown" data-i18n="Toastr"><i data-feather="circle"></i><span data-i18n="Toastr">Toastr</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-sliders.html" data-toggle="dropdown" data-i18n="Sliders"><i data-feather="circle"></i><span data-i18n="Sliders">Sliders</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-drag-drop.html" data-toggle="dropdown" data-i18n="Drag &amp; Drop"><i data-feather="circle"></i><span data-i18n="Drag &amp; Drop">Drag &amp; Drop</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-tour.html" data-toggle="dropdown" data-i18n="Tour"><i data-feather="circle"></i><span data-i18n="Tour">Tour</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-clipboard.html" data-toggle="dropdown" data-i18n="Clipboard"><i data-feather="circle"></i><span data-i18n="Clipboard">Clipboard</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-media-player.html" data-toggle="dropdown" data-i18n="Media player"><i data-feather="circle"></i><span data-i18n="Media player">Media Player</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-context-menu.html" data-toggle="dropdown" data-i18n="Context Menu"><i data-feather="circle"></i><span data-i18n="Context Menu">Context Menu</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-swiper.html" data-toggle="dropdown" data-i18n="swiper"><i data-feather="circle"></i><span data-i18n="swiper">Swiper</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-tree.html" data-toggle="dropdown" data-i18n="Tree"><i data-feather="circle"></i><span data-i18n="Tree">Tree</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-ratings.html" data-toggle="dropdown" data-i18n="Ratings"><i data-feather="circle"></i><span data-i18n="Ratings">Ratings</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="ext-component-i18n.html" data-toggle="dropdown" data-i18n="l18n"><i data-feather="circle"></i><span data-i18n="l18n">l18n</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Page Layouts"><i data-feather="layout"></i><span data-i18n="Page Layouts">Page Layouts</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="layout-boxed.html" data-toggle="dropdown" data-i18n="Layout Boxed"><i data-feather="circle"></i><span data-i18n="Layout Boxed">Layout Boxed</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="layout-without-menu.html" data-toggle="dropdown" data-i18n="Without Menu"><i data-feather="circle"></i><span data-i18n="Without Menu">Without Menu</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="layout-empty.html" data-toggle="dropdown" data-i18n="Layout Empty"><i data-feather="circle"></i><span data-i18n="Layout Empty">Layout Empty</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="layout-blank.html" data-toggle="dropdown" data-i18n="Layout Blank"><i data-feather="circle"></i><span data-i18n="Layout Blank">Layout Blank</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item col-md-2 col-xs-12"><a class="nav-link d-flex align-items-center" href="{{route('hospitalizacion.master')}}"><i class="fa fa-procedures"></i><span data-i18n="Pages">Hospitalizacion</span></a>
                </li>
                <li class="nav-item col-md-2 col-xs-12"><a class="nav-link d-flex align-items-center" href="{{route('hospital.emergencia')}}"><i class="fa fa-briefcase-medical"></i><span data-i18n="Charts &amp; Maps">Emergencia</span></a>
                    <ul class="dropdown-menu">
                        <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-toggle="dropdown" data-i18n="Charts"><i data-feather="pie-chart"></i><span data-i18n="Charts">Charts</span></a>
                            <ul class="dropdown-menu">
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="chart-apex.html" data-toggle="dropdown" data-i18n="Apex"><i data-feather="circle"></i><span data-i18n="Apex">Apex</span></a>
                                </li>
                                <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="chart-chartjs.html" data-toggle="dropdown" data-i18n="Chartjs"><i data-feather="circle"></i><span data-i18n="Chartjs">Chartjs</span></a>
                                </li>
                            </ul>
                        </li>
                        <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="maps-leaflet.html" data-toggle="dropdown" data-i18n="Leaflet Maps"><i data-feather="map"></i><span data-i18n="Leaflet Maps">Leaflet Maps</span></a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item col-md-2 col-xs-12"><a class="nav-link d-flex align-items-center" href="{{route('hospital.gcuartos')}}"><i class="fa fa-calendar"></i><span data-i18n="Pages">Recepcion</span></a>
                </li>

            </ul>
        </div>
    </div>
</div>