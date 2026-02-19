<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
<?= $ctrl->little_table_form($data, $structure, $config); ?>
<span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Aceptar</button></span>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?= $ctrl->close_form(); ?>
