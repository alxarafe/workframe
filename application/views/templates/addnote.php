<!-- Main content -->
<div class="col-lg-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Introduzca la nueva anotación</h3>
</div>
    <?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
    <div class="box-body">
<label for="nota"></label>
<textarea class="form-control" id="nota" name="nota" rows="6"></textarea>
</div>
<div class="box-footer">
    <button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button>
    <button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button>
</div>
<?= $ctrl->close_form(); ?>
</div>
</div>
<?php if (isset($notes) && $notes): ?>
<div class="col-lg-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Anotaciones</h3>
</div>
<div class="box-body">
<?= $ctrl->show_table_form($notes, 
	array(
		array('name'=>'id', 'label'=>'Fecha', 'type'=>'timestamp'), 
		array('name'=>'notes', 'label'=>'Anotación', 'type'=>'text'), 
	), 
	Null
	);
?>
</div>
</div>
</div>
<?php endif ?>