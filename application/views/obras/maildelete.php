<!-- Main content -->
<?= $ctrl->close_form(array('action' => base_url('/'.$this->uri->uri_string).(isset($_GET['return_to'])?'return_to?='.$_GET['return_to']:''),'message'=>isset($message)?$message:null)) ?>
<div class="row">
<div class="col-lg-12">
<h1><?= $title ?></h1>

<div class='form-group'>
	<div class='col-sm-3'>
	<label class='control-label'>Asunto:</label>
	</div>
	<div class='col-sm-9'>
	<p class="form-control"><?= $subject ?></p>
	</div>
</div>
<div class='form-group'>
	<div class='col-sm-3'>
	<label class='control-label'>Mensaje:</label>
	</div>
	<div class='col-sm-9'>
	<textarea class="form-control" rows="15"><?= $plain ?></textarea>
	</div>
</div>
<hr>
</div>
<span><button name="delete" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit">Borrar</button></span>
<span><button name="exit" class="<?= $ctrl->my_class_button('primary'); ?>" type="submit">Salir</button></span>
<?= $ctrl->close_form(); ?>
