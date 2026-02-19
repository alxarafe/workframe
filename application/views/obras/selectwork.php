<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string) . (isset($_GET['return_to']) ? '?return_to=' . $_GET['return_to'] : ''), 'message' => isset($message) ? $message : null)) ?>
<?php if ($data): ?>
<table id="the_table" class="table table-hover" align="center">
<tr><td>Expediente</td><td>Obra</td><td>Inicio</td><td>Fin</td><td>Partes</td></tr>
<?php foreach ($data as $key=>$value): ?>
<tr id="fila<?=$key?>" onClick="showDetails(this);">
<td><?= $value['id_file']; ?></td>
<td><strong><?= $value['id']; ?></strong> <?= $value['name']; ?></td>
<td><?= $value['date']; ?></td>
<td><?= $value['end_date']; ?></td>
<td><a href="<?= base_url('partes/parte/'.$foreman.'/'.$value['id'].'?return_to='.$value['id']) ?>">Nuevo</a> <span class="badge"><?= $value['qty']; ?></span></td>
</tr>
<?php if (isset($details[$value['id']]) && $details[$value['id']]): ?>
<?php foreach ($details[$value['id']] as $dkey=>$dvalue): ?>
<tr id="fila<?=$key.'-'.$dkey?>" class="fila<?=$key?>" style="display:none">
<td></td>
<td><?= $dvalue['name']; ?></td>
<td><?= $dvalue['date']; ?></td>
<td></td>
<td><a href="<?= base_url('partes/parte/'.$foreman.'/'.$value['id'].'/'.$dvalue['id']) ?>">Editar</td>
</tr>
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
</table>
<?php
/*
= $ctrl->little_table_form(
	$data, 
	array(
		array('name'=>'id', 'label'=>'ID', 'type'=>'number', 'auto'=>true, 'input'=>$this->is_admin), 
		array('name'=>'name', 'label'=>'Nombre', 'input'=>$this->is_admin, 'readonly'=>false, 'pattern'=>'.{1,60}', 'title'=>'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter'=>true), 
		array('name'=>'id_file', 'label'=>'Expediente', 'type'=>'dbselect', 'table'=>'files', 'input'=>$this->is_admin, 'readonly'=>false, 'filter'=>true),
		array('name'=>'date', 'label'=>'Inicio', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>false, 'filter'=>true), 
		array('name'=>'end_date', 'label'=>'Fin', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>false, 'filter'=>true), 
		//array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
	), null
);
*/
 ?>
    <span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button></span>
<?php else: ?>
<p><?= $foremanname ?> no tiene asignada ninguna obra en estos momentos</p>
<?php endif; ?>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?php if (isset($id) && ($id!='')): ?>
    <span><a href="<?= base_url("expedientes/notas/$id") ?>" class="<?= $ctrl->my_class_button('primary'); ?>">Insertar anotación</a></span>
<?php endif; ?>
