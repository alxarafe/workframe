<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="title" content="<?= $title ?>" />
<meta name="description" content="<?= $description ?>" />
<meta name="author" content="rSanjoSEO" />
<link rel="shortcut icon" href="<?= base_url('img/favicon.png')?>" type="image/png" />
<?php if ($this->public_page) : ?>
<meta name="keywords" content="<?= isset($keywords)?$keywords:'' ?>" />
<meta name="distribution" content="global" />
<meta name="Revisit" content="7 days" />
<meta name="robots" content="all" />
<meta name="rating" content="general" />
<?php else : ?>
<meta name="robots" content="noindex, nofollow" />
<?php endif ?>
<title><?= $title ?></title>
<?php
if (isset($css)) {
	foreach($css as $item) {
		echo '<link href="' . base_url() . 'css/' . $item . '.css" rel="stylesheet" />';
	}
}
?>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- Google Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<?php if ((ENVIRONMENT=="production" || ENVIRONMENT=="testing") && (_GOOGLE_ANALYTICS_CODE != '')): // Sólo si estamos en producción, metemos la información de Analytics ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', '<?= _GOOGLE_ANALYTICS_CODE ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php endif ?>
</head>
<body class="hold-transition skin-blue">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b> WF</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>W</b>ork<b>F</b>rame</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
		<?php /* // Icono y desplegable de mensajes 
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                <!-- inner menu: contains the messages -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#">
                      <div class="pull-left">
                        <!-- User Image -->
                        <img src="skins/default/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <!-- Message title and timestamp -->
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <!-- The message -->
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <!-- end message -->
                </ul>
                <!-- /.menu -->
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <!-- /.messages-menu -->
		  */ ?>

		<?php /* // Icono y desplegable de notificaciones
          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">
                  <li><!-- start notification -->
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
		  */ ?>
		  
		<?php /* // Icono y desplegable de tareas 
          <!-- Tasks Menu -->
          <li class="dropdown tasks-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <!-- Inner menu: contains the tasks -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <!-- Task title and progress text -->
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <!-- The progress bar -->
                      <div class="progress xs">
                        <!-- Change the css width attribute to simulate progress -->
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
		  
		  */ ?>
		  
		<?php /* // Usuario
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="skins/default/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">Alexander Pierce</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="skins/default/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                <p>
                  Alexander Pierce - Web Developer
                  <small>Member since Nov. 2012</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
		  */ ?>
		  
		  
		  <?php /* // Botón de menú lateral derecho
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
		  */ ?>
		  
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
		    <?php if ($this->is_user): ?>
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?= $this->user['username'] ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <p>
                  <?= $this->user['username'] ?>
                  <small>Creado el <?= date('j-m-Y \a \l\a\s h:i:s', strtotime($this->user['register_date'])) ?></small>
                </p>
              </li>
			  <?php /*
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="<?= site_url('auth/edit/'.$this->user['username']) ?>">Edit</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    &nbsp;
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="<?= site_url('auth/logout') ?>">Logout</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
			  */ ?>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                    <a href="<?= site_url('auth/edit/'.$this->user['username']) ?>">Editar</a>
                </div>
                <div class="pull-right">
                    <a href="<?= site_url('auth/logout') ?>">Cerrar sesión</a>
                </div>
              </li>
            </ul>
			<?php else: ?>
				<li<?= $this->uri->segment(1)=='auth'?' class="active"':''; ?> ><a href="<?= site_url()?>auth"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
			<?php endif ?>
          </li>
		  
        </ul>
		
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
	<?php /*
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="skins/default/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Alejandro el Pieza</p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </form>
      <!-- /.search form -->
	*/ ?>
      <!-- search form (Optional) -->
      <form action="<?= site_url('/buscar') ?>" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="cad" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </form>
      <!-- /.search form -->
	
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <!-- <li class="header">HEADER</li> -->
        <!-- Optionally, you can add icons to the links -->
		<li<?= $this->uri->segment(1)==''?' class="active"':''; ?>><a href="<?= site_url('/') ?>"><i class="fa fa-home"></i> <span>Inicio</span></a></li>
		<?php if ($this->is_admin): ?>
		<li<?= $this->uri->segment(1)=='administracion'?' class="active"':''; ?> class="treeview">
			<a href="#"><i class="fa fa-cog"></i> <span>Administración</span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
			</a>
			<ul class="treeview-menu">
				<li<?= $this->uri->segment(2)=='users'?' class="active"':''; ?>><a href="<?= site_url('/administracion/users') ?>"><i class="fa fa-users"></i> <span>Usuarios</span></a></li>
				<li<?= $this->uri->segment(2)=='roles'?' class="active"':''; ?>><a href="<?= site_url('/administracion/roles') ?>"><i class="fa fa-user-secret"></i> <span>Roles</span></a></li>
				<li<?= $this->uri->segment(2)=='sections'?' class="active"':''; ?>><a href="<?= site_url('/administracion/sections') ?>"><i class="fa fa-level-up"></i> <span>Secciones</span></a></li>
				<li<?= $this->uri->segment(2)=='categories'?' class="active"':''; ?>><a href="<?= site_url('/administracion/categories') ?>"><i class="fa fa-tags"></i> <span>Categorías</span></a></li>
				<li<?= $this->uri->segment(2)=='workcenters'?' class="active"':''; ?>><a href="<?= site_url('/administracion/workcenters') ?>"><i class="fa fa-building"></i> <span>Delegaciones</span></a></li>
				<li<?= $this->uri->segment(2)=='workers'?' class="active"':''; ?>><a href="<?= site_url('/administracion/workers') ?>"><i class="fa fa-user"></i> <span>Empleados</span></a></li>
                    <li<?= $this->uri->segment(2) == 'vehicles' ? ' class="active"' : ''; ?>><a href="<?= site_url('/administracion/vehicles') ?>"><i class="fa fa-car"></i> <span>Vehículos</span></a></li>
                    <li<?= $this->uri->segment(2) == 'orderstatus' ? ' class="active"' : ''; ?>><a href="<?= site_url('/administracion/orderstatus') ?>"><i class="fa fa-file"></i> <span>Estados de órdenes</span></a></li>
                </ul>
            </li>
		<?php endif ?>
		<?php if ($this->is_user): ?>
		<li<?= $this->uri->segment(1)=='clientes'?' class="active"':''; ?>><a href="<?= site_url('/clientes') ?>"><i class="fa fa-thumbs-o-up"></i> Clientes</a></li>
		<?php endif ?>
		<?php if ($this->is_user): ?>
		<li<?= $this->uri->segment(1)=='expedientes'?' class="active"':''; ?>><a href="<?= site_url('/expedientes') ?>"><i class="fa fa-file-o"></i> Expedientes</a></li>
		<?php endif ?>
		<?php if ($this->is_user): ?>
		<li<?= $this->uri->segment(1)=='ordenes'?' class="active"':''; ?>><a href="<?= site_url('/ordenes') ?>"><i class="fa fa-spinner"></i> Órdenes de trabajo</a></li>
		<?php endif ?>
		<?php if ($this->is_admin): ?>
		<li<?= $this->uri->segment(1)=='mail'?' class="active"':''; ?> class="treeview">
			<a href="#"><i class="fa fa-envelope-o"></i> <span>Correo electrónico</span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
			<ul class="treeview-menu">
			<li<?= $this->uri->segment(2)==''?' class="active"':''; ?>><a href="<?= site_url('/mail') ?>"><i class="fa fa-envelope"></i> Procesar correo electrónico</a></li>
			<li<?= $this->uri->segment(2)=='config'?' class="active"':''; ?>><a href="<?= site_url('/mail/config') ?>"><i class="fa fa-cog"></i> Configurar</a></li>
			</ul>
		</li>
		<?php else: ?>
		<?php if ($this->is_user): ?>
		<li<?= $this->uri->segment(1)=='mail'?' class="active"':''; ?>><a href="<?= site_url('/mail') ?>">Correos electrónicos</a></li>
		<?php endif ?>
		<?php endif ?>
		<?php if ($this->is_admin || isset($this->user) || (isset($this->user['id_worker']) && ($this->user['id_worker']>0))): ?>
		<li<?= $this->uri->segment(1)=='partes'?' class="active"':''; ?>><a href="<?= site_url('/partes') ?>"><i class="fa fa-tablet"></i> Partes de trabajo</a></li>
		<?php endif ?>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?=$title.(isset($subtitle)?"<small>$subtitle</small>":'')?></h1>
	  <?php /*
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol>
	  */ ?>
    </section>
<section class="content">