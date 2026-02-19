<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string) . (isset($_GET['return_to']) ? '?return_to=' . $_GET['return_to'] : ''), 'message' => isset($message) ? $message : null)) ?>
<div class="row">
<div class="col-md-6"> <?php // Columna izquierda ?>
<div class="col-lg-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Notificación a encargado</h3>
</div>
<div class="box-body">
<p><?= isset($foreman_name) && ($foreman_name != '')?"Encargado: <strong>$foreman_name</strong>":'Encargado no definido. Es posible que necesite guardar los cambios.' ?></p>
<?php if (isset($foreman_mail) && $foreman_mail != ''): ?>
</div>
<div class="box-footer">
<a href="<?= base_url("ordenes/sendmail/$id") ?>" class="btn btn-default">Notificar a <?= $foreman_mail ?></a>
<?php else: ?>
<p>No hay correo electrónico disponible para el envío.</p>
<p>Revise los datos del usuario asociado al encargado.</p>
<?php endif ?>
</div>
</div>
</div>
<div class="col-md-12">
    <?= $ctrl->new_record_form('workorders', $title, $data, $structure, $id, $config); ?>
</div>
    <?php if ($exists): ?>
        <div class="col-md-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Partes de trabajo</h3>
</div>
<div class="box-body">
        <?=
        $ctrl->show_table_form($workparts, array(
			array('name'=>'id', 'label'=>'ID', 'type'=>'number', 'auto'=>true, 'input'=>$this->is_admin, 'readonly'=>false), 
			array('name'=>'name', 'label'=>'Nombre', 'input'=>$this->is_admin, 'link' => '/partes/partid/#', 'readonly'=>true),
			//array('name'=>'id_file', 'label'=>'Exp.', 'type'=>'text', 'readonly'=>true), 
			array('name'=>'date', 'label'=>'Fecha', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>true), 
		), 
		Null
		);
?>
        <?php if (isset($foreman_id) && $foreman_id > 0): ?>
    <button id="xxx" name="newworkpart" class="btn btn-default" type="submit">Nuevo parte de trabajo</button>
        <?php endif ?>
    </div>
</div>
</div>
<div class="col-lg-12">
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
    <button name="newnote" class="btn btn-default" type="submit">Insertar anotación</button>
    </div>
</div>
</div>
<?php endif ?>
</div> <?php // de la columna izquierda ?>
<div class="col-md-6"> <?php // Columna derecha ?>

    <?php if ($exists): ?>
        <div class="col-md-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Jefe de equipo</h3>
</div>
<div class="box-body">
<?php if ($all_foremen): ?>
<?= 
	$ctrl->edit_field(
            '', 
		'id_foreman', 
		array(
			'id'=>'id_foreman',
			'name'=>'id_foreman',
			'type'=>'dbselect',
			'label'=>'Jefe de equipo',
			'value'=>isset($data['id_foreman'])?$data['id_foreman']:0
		), 
		isset($data['id_foreman'])?$data['id_foreman']:0, 
		$all_foremen).'<hr><hr>'; 
?>
<?php else: ?>
<p><strong>No hay jefes de equipo</strong></p>
<p>Vaya a usuarios, cree un usuario para cada jefe de equipo y asígnele su operario. Los operarios asociados a usuarios son considerados jefes de equipo.</p>
<?php endif; ?>
</div>
</div>
</div>

<div class="col-md-12">
<div class="nav-tabs-custom">
<ul class="nav nav-tabs">
<li class="active"><a href="#vehicles" data-toggle="tab">Vehículos</a></li>
<?php foreach($categories as $key=>$cat): ?>
<?php if (isset($workers[$key]) && count($workers[$key])>0): /*test_array("$key",$workers[$key],false);*/ ?>
<li><a href="#<?= $key ?>" data-toggle="tab"><?= $cat ?></a></li>
<?php endif ?>
<?php endforeach ?>
</ul>
<div class="tab-content">
<div class="active tab-pane" id="vehicles">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Vehículos</h3>
</div>
<div class="box-body">
<?php if ($all_vehicles): ?>
<table id="vehicles" class="table table-hover" align="center">
<tr>
<td> </td><td>id</td><td>Vehiculo</td><td>Matrícula</td>
</tr>
<?php foreach ($all_vehicles as $value): ?>
<tr>
<td><input id="v<?=$value['id']?>" name="v[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> Asignado</input></td>
<td><?= $value['id'].(isset($value['warning']) && ($value['warning']!='')?" <span title='".$value['warning']."'><span class='glyphicon glyphicon-exclamation-sign' /></span>":'') ?></td>
<td><?= $value['name'] ?></td>
<td><?= $value['license_plate'] ?></td>
</tr>
<?php endforeach ?>
</table>
<?php else: ?>
<p>No hay vehículos</p>
<?php endif; ?>
</div>
</div>
</div>
<?php 
/*
	$categories es un array con las categorías ($categories['slug']='Nombre')
	$workers contiene los trabajadores ($workers['slug'][]='Nombre')
	
	Siendo slug un nombre en minúsculas sin símbolos (salvo subrayado) ni espacios.
*/
foreach($categories as $key=>$cat): ?>
<div class="tab-pane" id="<?= $key ?>">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title"><?= $cat ?></h3>
<div class="box-body">
<?php if (isset($workers[$key])): ?>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td><td>id</td><td>Nombre</td>
</tr>
<?php foreach ($workers[$key] as $value): ?>
<tr id="fw<?=$value['id']?>" <?= isset($value['checked'])?'':'hidden'?>>
    <td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" onchange="hideMe('<?=$key.$value['id']?>', this);" type="checkbox" <?= isset($value['checked'])?'checked':'' ?>></input></td>
<td><?= $value['id'].(isset($value['warning']) && ($value['warning']!='')?" <span title='".$value['warning']."'><span class='glyphicon glyphicon-exclamation-sign' /></span>":'') ?></td>
<td><?= $value['name'] ?>
</tr>
<?php endforeach ?>
</table>
    
<div class='form-group'>
<div class='col-sm-9'>
<select id='nw<?=$key?>' class='selectpicker form-control' name='nw<?=$key?>' data-live-search='true'>
    <option value='0' checked>Seleccione una opción</option>
    <?php if ($workers[$key] && count($workers[$key])>0): ?>
    <?php foreach($workers[$key] as $value): ?>
    <?php if ($value['active']): ?>
    <option <?= (isset($value['warning']) && ($value['warning']) != '')?'style="background: lightgray; color: red;"':'' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
</select>
</div>
<div class='col-sm-3'>
<a class="btn btn-default" href='javascript:;' onclick="addWorker('<?=$key?>');" role="button">Seleccionar</a>
</div>
</div>    

    
<?php else: ?>
<p>No hay operarios</p>
<?php endif; ?>
</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>    
</div>
</div>
        <?= $ctrl->close_form(); ?>
    <?php else: ?>
<h3>Genere la orden de trabajo antes de asignar recursos</h3>
<?php endif ?>
</div> <?php // de la columna derecha ?>
