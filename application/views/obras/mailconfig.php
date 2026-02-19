<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string) . (isset($_GET['return_to']) ? 'return_to?=' . $_GET['return_to'] : ''), 'message' => isset($message) ? $message : null)) ?>
<div class="row">
<div class="col-lg-12">
<h1>Configuración del correo electrónico</h1>
<?= $ctrl->edit_field('', 'server', array('id', 'server', 'type' => 'text', 'label' => 'Servidor de correo', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['server']) ? $_POST['server'] : $server); ?>
<?= $ctrl->edit_field('', 'email', array('id', 'server', 'type' => 'text', 'label' => 'Dirección de correo', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['email']) ? $_POST['email'] : $email); ?>
<?= $ctrl->edit_field('', 'password', array('id', 'server', 'type' => 'text', 'label' => 'Contraseña', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['password']) ? $_POST['password'] : $password); ?>
<?= $ctrl->edit_field('', 'port', array('id', 'server', 'type' => 'text', 'label' => 'Puerto', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['port']) ? $_POST['port'] : $port); ?>
<?= $ctrl->edit_field('', 'outserver', array('id', 'server', 'type' => 'text', 'label' => 'Servidor salida', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['outserver']) ? $_POST['outserver'] : $outserver); ?>
<?= $ctrl->edit_field('', 'outport', array('id', 'server', 'type' => 'text', 'label' => 'Puerto salida', 'value' => (isset($data['server']) ? $data['server'] : 'mail.google.es')), isset($_POST['outserver']) ? $_POST['outport'] : $outport); ?>
</div>
</div>
<span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button></span>
<span><button name="exit" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?php $ctrl->close_form(); ?>
