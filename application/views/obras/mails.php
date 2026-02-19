<!-- Main content -->
<?= $ctrl->close_form(array('action' => base_url('/'.$this->uri->uri_string).(isset($_GET['return_to'])?'?return_to='.$_GET['return_to']:''),'message'=>isset($message)?$message:null)) ?>
<h1><?= $title ?></h1>
<?php if ($data): ?>
<table id="the_table" class="table table-hover" align="center">
<tr><td>Fecha</td><td>Remitente</td><td>Asunto</td><td></td><td></td></tr>
<?php foreach ($data as $key=>$value): ?>
<tr id="fila<?=$key?>" onClick="showDetails(this);">
<td><?= $value['date']; ?></td>
<td><?= $value['sender']; ?></td>
<td><?= $value['subject']; ?></td>
<td><?= 
	$this->edit_field(
                (integer)$value['id'], 
		'id_file', 
		array(
			'id'=>'id_file',
			'name'=>'id_file',
			'type'=>'dbselect',
			//'label'=>'Expediente',
			'value'=>$value['id_file']
		), 
		$value['id_file'], 
		$all_files); 
?></td>
<td><a class="btn btn-danger" href="<?= base_url('mail/delete/'.$value['id']) ?>">Borrar</a></td>
</tr>
<tr id="fila<?=$key.'-html'?>" class="fila<?=$key?>" style="display:none">
<td colspan="2"><?= html_entity_decode($value['html']); ?></td>
<td colspan="2"><?= '<pre style="white-space:pre-wrap; word-wrap:break-word;">'.$value['plain'].'</pre>'; ?></td>
</tr>
<?php endforeach ?>
</table>
<?php else: ?>
<h3>No hay correos pendientes de clasificar</h3>
<?php endif ?>
<hr>
<span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Asignar</button></span>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?= $ctrl->close_form(); ?>
