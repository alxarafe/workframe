<!-- Main content -->
<?= '';//open_form(array('action' => base_url('/'.$this->uri->uri_string).(isset($_GET['return_to'])?'return_to?='.$_GET['return_to']:''),'message'=>isset($message)?$message:null)) ?>
<div class="row">
<div class="col-md-6">
    <?= $ctrl->new_record_form('customers', $title, $data, $structure, $id, $config); ?>
</div>
<div class="col-md-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Expedientes</h3>
</div>
<div class="box-body">
<?php if (isset($id) && ($id!='')): ?>
    <?=
    $ctrl->show_table_form($files, array(
		array('name'=>'id', 'label'=>'ID', 'type'=>'number', 'auto'=>true, 'input'=>$this->is_admin, 'readonly'=>false), 
		array('name'=>'name', 'label'=>'Nombre', 'input'=>$this->is_admin, 'link' => '/expedientes/expediente/#', 'readonly'=>true), 
		//array('name'=>'id_file', 'label'=>'Exp.', 'type'=>'text', 'readonly'=>true), 
		array('name'=>'date', 'label'=>'Fecha', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>true), 
	), 
	Null
	);
?>
</div>
<div class="box-footer">
<a href="<?= base_url("expedientes/expediente?return_to=$id") ?>" class="btn btn-default">Nuevo expediente</a>
<?php else: ?>
<h3>Genere la ficha de cliente antes de insertar expedientes</h3>
<?php endif ?>
</div>
</div>
</div>
</div>
<?= ''; //close_form(); ?>
<?php if (isset($id) && ($id!='')): ?>
<div class="row">
<div class="col-md-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Correos electrónicos</h3>
</div>
<div class="box-body">
        <?=
        $ctrl->show_table_form($mails, array(
		array('name'=>'date', 'label'=>'Fecha', 'type'=>'timestamp'), 
		array('name'=>'subject', 'label'=>'Asunto', 'type'=>'text'), 
	), 
	Null
	);
?>
</div>
<div class="box-footer">
</div>
</div>
</div>
<div class="col-lg-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Anotaciones</h3>
</div>
<div class="box-body">
        <?=
        $ctrl->show_table_form($notes, array(
		array('name'=>'id', 'label'=>'Fecha', 'type'=>'timestamp'), 
		array('name'=>'notes', 'label'=>'Anotación', 'type'=>'text'), 
	), 
	Null
	);
?>
</div>
<div class="box-footer">
<a href="<?= base_url("clientes/notas/$id") ?>" class="btn btn-default">Insertar anotación</a>
</div>
</div>
</div>
</div>
<?php endif; ?>
