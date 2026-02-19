<!-- Main content -->
<?= '';//open_form(array('action' => base_url('/'.$this->uri->uri_string).(isset($_GET['return_to'])?'?return_to='.$_GET['return_to']:''),'message'=>isset($message)?$message:null)) ?>
<div class="row">
<div class="col-md-6">
    <?= $ctrl->new_record_form('files', $title, $data, $structure, $id, $config); ?>
</div>
<div class="col-md-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Órdenes de trabajo</h3>
</div>
<div class="box-body">
    <?php if ($exists): ?>
        <?=
    $ctrl->show_table_form($workorders, array(
		array('name'=>'id', 'label'=>'ID', 'type'=>'number', 'auto'=>true, 'input'=>$this->is_admin, 'readonly'=>false), 
		array('name'=>'name', 'label'=>'Nombre', 'input'=>$this->is_admin, 'link' => '/ordenes/orden/#', 'readonly'=>true), 
		//array('name'=>'id_file', 'label'=>'Exp.', 'type'=>'text', 'readonly'=>true), 
		array('name'=>'date', 'label'=>'Fecha', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>true), 
	), 
	Null
	);
?>
</div>
<div class="box-footer">
<a href="<?= base_url("ordenes/orden?return_to=$id") ?>" class="btn btn-default">Nueva órden de trabajo</a>
<?php else: ?>
<h3>Genere el expediente antes de insertar órdenes de trabajo</h3>
<?php endif ?>
</div>
</div>
</div>
</div>
<?= ''; // close_form(); ?>
<?php if ($exists): ?>
    <div class="row">
<div class="col-md-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Correos electrónicos</h3>
</div>
<div class="box-body">
<?= '';/*show_table_form($mails, 
	array(
		array('name'=>'date', 'label'=>'Fecha', 'type'=>'timestamp'), 
		array('name'=>'subject', 'label'=>'Asunto', 'type'=>'text'), 
		//array('name'=>'sendmail', 'label'=>'Send', 'type'=>'link', 'value'=>base_url("/expedientes/sendmail/$id/#")), 
	), 
	Null
	);*/
?>
<table id="the_table" class="table table-hover" align="center">
<?php if ($mails): ?>
<tr><td>&nbsp;</td><td>Fecha</td><td>Asunto</td><td>&nbsp;</td></tr>
<?php foreach ($mails as $key=>$value): ?>
<tr id="fila<?=$key?>" onClick="showDetails(this);">
<td><p class="panel panel-xs"><i class="fa fa-<?= ($value['id_user'])==0?'chevron-down':'chevron-right'?>"></i></p></td>
<td><?= $value['date']; ?></td>
<td><?= $value['subject']; ?></td>
<?php if ($value['id_user']==0): ?>
<td><a href="<?= base_url("expedientes/sendmail/$id/".$value['id']) ?>" class="btn btn-xs"><i class="fa fa-reply" title="Responder a éste mensaje"></i></a></td>
<?php else: ?>
<td>&nbsp;</td>
<?php endif; ?>
</tr>
<tr id="fila<?=$key.'-html'?>" class="fila<?=$key?>" style="display:none">
<?php
$msg=html_entity_decode($value['html']);
if (trim($msg)=='') {
	$msg=nl2br($value['plain']);
}
?>
<td colspan="4"><?= $msg; ?></td>
<?php /*
<td colspan="2"><?= '<pre style="white-space:pre-wrap; word-wrap:break-word;">'.$value['plain'].'</pre>'; ?></td>
*/ ?>
</tr>
<?php endforeach ?>
<?php else:?>
<p>No hay correos electrónicos</p>
<?php endif; ?>
</table>
</div>
<div class="box-footer">
<a href="<?= base_url("expedientes/sendmail/$id") ?>" class="btn btn-default">Enviar correo</a>
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
<a href="<?= base_url("expedientes/notas/$id") ?>" class="btn btn-default">Insertar anotación</a>
</div>
</div>
</div>
</div>
<?php endif; ?>
