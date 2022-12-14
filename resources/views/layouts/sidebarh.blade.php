<?php
$imagen = Auth::user()->imagen_url;
if ($imagen == ' ') {
    $imagen = 'avatar.jpg';
}
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();
$id_empresa = $empresa->id;
//dd($id_empresa);
?>
<div class="horizontal-menu">
			<nav class="navbar top-navbar">
				<div class="container">
					<div class="navbar-content">
						<a href="#" class="navbar-brand">
							SI<span>AAM</span>
						</a>
                        <nav class="navbar-nav justify-content-center">
                                <a class="navbar-brand" href="#" style="margin-left: 40px;"> 
                                    <img src="{{asset('/hc4/img/logo.png')}}"  width="80px" height="30px" class="d-inline-block align-top" alt="">
                                </a>
                        </nav>
						<ul class="navbar-nav">
<!-- 							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="flag-icon flag-icon-us mt-1" title="us"></i> <span class="font-weight-medium ml-1 mr-1 d-none d-md-inline-block">English</span>
								</a>
								<div class="dropdown-menu" aria-labelledby="languageDropdown">
									<a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-us" title="us" id="us"></i> <span class="ml-1"> English </span></a>
									<a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-fr" title="fr" id="fr"></i> <span class="ml-1"> French </span></a>
									<a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-de" title="de" id="de"></i> <span class="ml-1"> German </span></a>
									<a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-pt" title="pt" id="pt"></i> <span class="ml-1"> Portuguese </span></a>
									<a href="javascript:;" class="dropdown-item py-2"><i class="flag-icon flag-icon-es" title="es" id="es"></i> <span class="ml-1"> Spanish </span></a>
								</div>
							</li>-->
							<li class="nav-item dropdown nav-apps">
								<a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i data-feather="grid"></i>
								</a>
								<div class="dropdown-menu" aria-labelledby="appsDropdown">
									<div class="dropdown-header d-flex align-items-center justify-content-between">
										<p class="mb-0 font-weight-medium">Web Apps</p>
										<a href="javascript:;" class="text-muted">Edit</a>
									</div>
									<div class="dropdown-body">
										<div class="d-flex align-items-center apps">
											<a href="pages/apps/chat.html"><i data-feather="message-square" class="icon-lg"></i><p>Chat</p></a>
											<a href="pages/apps/calendar.html"><i data-feather="calendar" class="icon-lg"></i><p>Calendar</p></a>
											<a href="pages/email/inbox.html"><i data-feather="mail" class="icon-lg"></i><p>Email</p></a>
											<a href="pages/general/profile.html"><i data-feather="instagram" class="icon-lg"></i><p>Profile</p></a>
										</div>
									</div>
									<div class="dropdown-footer d-flex align-items-center justify-content-center">
										<a href="javascript:;">View all</a>
									</div>
								</div>
							</li> 
							<li class="nav-item dropdown nav-messages">
								<a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i data-feather="mail"></i>
								</a>
								<div class="dropdown-menu" aria-labelledby="messageDropdown">
									<div class="dropdown-header d-flex align-items-center justify-content-between">
										<p class="mb-0 font-weight-medium">9 New Messages</p>
										<a href="javascript:;" class="text-muted">Clear all</a>
									</div>
									<div class="dropdown-body">
										<a href="javascript:;" class="dropdown-item">
											<div class="figure">
												<img src="https://via.placeholder.com/30x30" alt="userr">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>Leonardo Payne</p>
													<p class="sub-text text-muted">2 min ago</p>
												</div>	
												<p class="sub-text text-muted">Project status</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="figure">
												<img src="https://via.placeholder.com/30x30" alt="userr">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>Carl Henson</p>
													<p class="sub-text text-muted">30 min ago</p>
												</div>	
												<p class="sub-text text-muted">Client meeting</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="figure">
												<img src="https://via.placeholder.com/30x30" alt="userr">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>Jensen Combs</p>												
													<p class="sub-text text-muted">1 hrs ago</p>
												</div>	
												<p class="sub-text text-muted">Project updates</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="figure">
												<img src="https://via.placeholder.com/30x30" alt="userr">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>Amiah Burton</p>
													<p class="sub-text text-muted">2 hrs ago</p>
												</div>
												<p class="sub-text text-muted">Project deadline</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="figure">
												<img src="https://via.placeholder.com/30x30" alt="userr">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>Yaretzi Mayo</p>
													<p class="sub-text text-muted">5 hr ago</p>
												</div>
												<p class="sub-text text-muted">New record</p>
											</div>
										</a>
									</div>
									<div class="dropdown-footer d-flex align-items-center justify-content-center">
										<a href="javascript:;">View all</a>
									</div>
								</div>
							</li>
							<li class="nav-item dropdown nav-notifications">
								<a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i data-feather="bell"></i>
									<div class="indicator">
										<div class="circle"></div>
									</div>
								</a>
								<div class="dropdown-menu" aria-labelledby="notificationDropdown">
									<div class="dropdown-header d-flex align-items-center justify-content-between">
										<p class="mb-0 font-weight-medium">6 New Notifications</p>
										<a href="javascript:;" class="text-muted">Clear all</a>
									</div>
									<div class="dropdown-body">
										<a href="javascript:;" class="dropdown-item">
											<div class="icon">
												<i data-feather="user-plus"></i>
											</div>
											<div class="content">
												<p>New customer registered</p>
												<p class="sub-text text-muted">2 sec ago</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="icon">
												<i data-feather="gift"></i>
											</div>
											<div class="content">
												<p>New Order Recieved</p>
												<p class="sub-text text-muted">30 min ago</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="icon">
												<i data-feather="alert-circle"></i>
											</div>
											<div class="content">
												<p>Server Limit Reached!</p>
												<p class="sub-text text-muted">1 hrs ago</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="icon">
												<i data-feather="layers"></i>
											</div>
											<div class="content">
												<p>Apps are ready for update</p>
												<p class="sub-text text-muted">5 hrs ago</p>
											</div>
										</a>
										<a href="javascript:;" class="dropdown-item">
											<div class="icon">
												<i data-feather="download"></i>
											</div>
											<div class="content">
												<p>Download completed</p>
												<p class="sub-text text-muted">6 hrs ago</p>
											</div>
										</a>
									</div>
									<div class="dropdown-footer d-flex align-items-center justify-content-center">
										<a href="javascript:;">View all</a>
									</div>
								</div>
							</li>
							<li class="nav-item dropdown nav-profile">
								<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<img src="{{asset('/avatars').'/'.$imagen}}" alt="profile">
								</a>
								<div class="dropdown-menu" aria-labelledby="profileDropdown">
									<div class="dropdown-header d-flex flex-column align-items-center">
										<div class="figure mb-3">
											<img src="{{asset('/avatars').'/'.$imagen}}" alt="">
										</div>
										<div class="info text-center">
											<p class="name font-weight-bold mb-0">{{ Auth::user()->name}}</p>
											<p class="email text-muted mb-3">{{ Auth::user()->email}}</p>
										</div>
									</div>
									<div class="dropdown-body">
										<ul class="profile-nav p-0 pt-3">
											<li class="nav-item">
												<a href="#" class="nav-link">
													<i data-feather="user"></i>
													<span>Perfil</span>
												</a>
											</li>
											<li class="nav-item">
												<a href="javascript:;" class="nav-link">
													<i data-feather="edit"></i>
													<span>Editar Perfil</span>
												</a>
											</li>
<!-- 											<li class="nav-item">
												<a href="javascript:;" class="nav-link">
													<i data-feather="repeat"></i>
													<span>Switch User</span>
												</a>
											</li> -->
											<li class="nav-item">
												<a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
													<i data-feather="log-out"></i>
													<span>Cerrar Sesi??n</span>
												</a>
											</li>
											<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
												{{ csrf_field() }}
											</form>
										</ul>
									</div>
								</div>
							</li>
						</ul>
						<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
							<i data-feather="menu"></i>			
						</button>
					</div>
				</div>
			</nav>
			<nav class="bottom-navbar">
				<div class="container">
					<ul class="nav page-navigation">
                        <div class="col-md-12" style="text-align: center;">
                             <i class="mdi mdi-hospital-building"></i>
							  &nbsp;
							  &nbsp;
                             <a href="{{url('hospital/inicio')}}" style="color:black" >GESTI??N HOSPITALARIA</a> 
                        </div>
						<li class="nav-item">
							<a class="nav-link" href="#">
								<i class="link-icon" data-feather="image"></i>
								<span class="menu-title">Im??genes</span>
							</a>
						</li>
						<li class="nav-item">
						    <a class="nav-link" href="{{route('hospital.farmacia')}}">
								<i class="link-icon mdi mdi-microscope"></i>
								<span class="menu-title">Laboratorio</span>
							</a>
							
						</li>
                        <li class="nav-item">
						    <a class="nav-link" href="{{route('hospital.farmacia')}}">
								<i class="link-icon mdi mdi-medical-bag"></i>
								<span class="menu-title">Farmacia</span>
							</a>
							
						</li>
                        <li class="nav-item">
						    <a class="nav-link" href="{{route('hospital.quirofano')}}">
								<i class="link-icon mdi mdi-needle"></i>
								<span class="menu-title">Cirug??a</span>
							</a>
							
						</li>
                        <li class="nav-item">
						    <a class="nav-link" href="{{route('hospital.gcuartos')}}">
								<i class="link-icon mdi mdi-bed"></i>
								<span class="menu-title">Hospitalizaci??n</span>
							</a>
							
						</li>
                        <li class="nav-item">
						    <a class="nav-link" href="{{route('hospital.emergencia')}}">
								<i class="link-icon mdi mdi-hospital"></i>
								<span class="menu-title">Emergencia</span>
							</a>
							
						</li>
						<!-- <li class="nav-item mega-menu">
							<a href="#" class="nav-link">
								<i class="link-icon" data-feather="feather"></i>
								<span class="menu-title">UI Kit</span>
								<i class="link-arrow"></i>
							</a>
							<div class="submenu">
								<div class="col-group-wrapper row">
									<div class="col-group col-md-9">
										<div class="row">
											<div class="col-12">
												<p class="category-heading">Basic</p>
												<div class="submenu-item">
													<div class="row">
														<div class="col-md-4">
															<ul>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/alerts.html">Alerts</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/badges.html">Badges</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/breadcrumbs.html">Breadcrumbs</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/buttons.html">Buttons</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/button-group.html">Buttn Group</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/cards.html">Cards</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/carousel.html">Carousel</a></li>
															</ul>
														</div>
														<div class="col-md-4">
															<ul>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/collapse.html">Collapse</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/dropdowns.html">Dropdowns</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/list-group.html">List group</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/media-object.html">Media object</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/modal.html">Modal</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/navs.html">Navs</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/navbar.html">Navbar</a></li>
															</ul>
														</div>
														<div class="col-md-4">
															<ul>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/pagination.html">Pagination</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/popover.html">Popovers</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/progress.html">Progress</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/scrollbar.html">Scrollbar</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/scrollspy.html">Scrollspy</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/spinners.html">Spinners</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/ui-components/tooltips.html">Tooltips</a></li>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-group col-md-3">
										<div class="row">
											<div class="col-12">
												<p class="category-heading">Advanced</p>
												<div class="submenu-item">
													<div class="row">
														<div class="col-md-12">
															<ul>
																<li class="nav-item"><a class="nav-link" href="pages/advanced-ui/cropper.html">Cropper</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/advanced-ui/owl-carousel.html">Owl carousel</a></li>
																<li class="nav-item"><a class="nav-link" href="pages/advanced-ui/sweet-alert.html">Sweetalert</a></li>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="link-icon" data-feather="inbox"></i>
								<span class="menu-title">Forms</span>
								<i class="link-arrow"></i>
							</a>
							<div class="submenu">
								<ul class="submenu-item">
									<li class="nav-item"><a class="nav-link" href="pages/forms/basic-elements.html">Basic Elements</a></li>
									<li class="nav-item"><a class="nav-link" href="pages/forms/advanced-elements.html">Advanced Elements</a></li>
									<li class="nav-item"><a class="nav-link" href="pages/forms/editors.html">Editors</a></li>
									<li class="nav-item"><a class="nav-link" href="pages/forms/wizard.html">Wizard</a></li>
								</ul>
							</div>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="link-icon" data-feather="pie-chart"></i>
								<span class="menu-title">Data</span>
								<i class="link-arrow"></i>
							</a>
							<div class="submenu">
								<div class="row">
									<div class="col-md-6">
										<ul class="submenu-item pr-0">
											<li class="category-heading">Charts</li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/apex.html">Apex</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/chartjs.html">ChartJs</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/flot.html">Float</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/morrisjs.html">Morris</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/peity.html">Peity</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/charts/sparkline.html">Sparkline</a></li>
										</ul>
									</div>
									<div class="col-md-6">
										<ul class="submenu-item pl-0">
											<li class="category-heading">Tables</li>
											<li class="nav-item"><a class="nav-link" href="pages/tables/basic-table.html">Basic Tables</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/tables/data-table.html">Data Table</a></li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">
								<i class="link-icon" data-feather="smile"></i>
								<span class="menu-title">Icons</span>
								<i class="link-arrow"></i>
							</a>
							<div class="submenu">
								<ul class="submenu-item">
									<li class="nav-item"><a class="nav-link" href="pages/icons/feather-icons.html">Feather Icons</a></li>
									<li class="nav-item"><a class="nav-link" href="pages/icons/flag-icons.html">Flag Icons</a></li>
									<li class="nav-item"><a class="nav-link" href="pages/icons/mdi-icons.html">Mdi Icons</a></li>
								</ul>
							</div>
						</li>
						<li class="nav-item mega-menu">
							<a href="#" class="nav-link">
								<i class="link-icon" data-feather="book"></i>
								<span class="menu-title">Sample Pages</span>
								<i class="link-arrow"></i>
							</a>
							<div class="submenu">
								<div class="col-group-wrapper row">
									<div class="col-group col-md-6">
										<p class="category-heading">Special Pages</p>
										<div class="submenu-item">
											<div class="row">
												<div class="col-md-6">
													<ul>
														<li class="nav-item"><a class="nav-link" href="pages/general/blank-page.html">Blank page</a></li>
														<li class="nav-item"><a class="nav-link" href="pages/general/faq.html">Faq</a></li>
														<li class="nav-item"><a class="nav-link" href="pages/general/invoice.html">Invoice</a></li>
													</ul>
												</div>
												<div class="col-md-6">
													<ul>
														<li class="nav-item"><a class="nav-link" href="pages/general/profile.html">Profile</a></li>
														<li class="nav-item"><a class="nav-link" href="pages/general/pricing.html">Pricing</a></li>
														<li class="nav-item"><a class="nav-link" href="pages/general/timeline.html">Timeline</a></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
									<div class="col-group col-md-3">
										<p class="category-heading">Auth Pages</p>
										<ul class="submenu-item">
											<li class="nav-item"><a class="nav-link" href="pages/auth/login.html">Login</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/auth/register.html">Register</a></li>
										</ul>
									</div>
									<div class="col-group col-md-3">
										<p class="category-heading">Error Pages</p>
										<ul class="submenu-item">
											<li class="nav-item"><a class="nav-link" href="pages/error/404.html">404</a></li>
											<li class="nav-item"><a class="nav-link" href="pages/error/500.html">500</a></li>
										</ul>
									</div>
								</div>
							</div>
						</li>
						<li class="nav-item">
							<a href="https://www.nobleui.com/html/documentation/docs.html" target="_blank" class="nav-link">
								<i class="link-icon" data-feather="hash"></i>
								<span class="menu-title">Documentation</span></a>
						</li> -->
					</ul>
				</div>
			</nav>
		</div>