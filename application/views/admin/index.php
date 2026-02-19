<!-- Main content -->
<div class="container">
<h4>Parametrización de usuarios</h4>
<div class="row">
    <a href="<?= site_url('administracion/users') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Usuarios</a>
    <a href="<?= site_url('administracion/roles') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Roles</a>
</div>
<h4>Parametrización de ficheros de la aplicación</h4>
<div class="row">
    <a href="<?= site_url('administracion/sections') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Secciones</a>
    <a href="<?= site_url('administracion/categories') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Categorías</a>
    <a href="<?= site_url('administracion/workcenters') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Delegaciones</a>
    <a href="<?= site_url('administracion/workers') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Empleados</a>
    <a href="<?= site_url('administracion/vehicles') ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Vehículos</a>
</div>
<h4>Al finalizar use el menu lateral o pulse en Salir</h4>
<div class="row">
    <a href="<?= site_url('/') ?>" class="<?= $ctrl->my_class_button('danger'); ?>">Salir</a>
</div>
</div>
