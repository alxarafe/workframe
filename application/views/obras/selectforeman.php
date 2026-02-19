<!-- Main content -->
<div class="col-lg-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title"><?= $title ?></h3>
</div>
    <?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string) . (isset($_GET['return_to']) ? 'return_to?=' . $_GET['return_to'] : ''), 'message' => isset($message) ? $message : null)) ?>
    <div class="box-body">
<label for="body"></label>
<?php if ($all_foremen): ?>
<?= 
	$ctrl->edit_field(
        '', 
		'id_foreman', 
		array(
			'id'=>'id_foreman',
			'name'=>'id_foreman',
			'type'=>'dbselect',
			'label'=>'Encargado',
			'value'=>$id_foreman
		), 
		$id_foreman, 
		$all_foremen).'<hr><hr>'; 
?>
</div>
<div class="box-footer">
<button name="select" class="btn btn-default" type="submit">Seleccionar</button>
<?php else: ?>
<p>No hay encargados de obra</p>
</div>
<div class="box-footer">
<?php endif; ?>
<button name="cancel" class="btn btn-danger pull-right" type="submit" formnovalidate>Salir</button></span>
<?php $ctrl->close_form(); ?>
</div>
</div>