<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string) . (isset($_GET['return_to']) ? '?return_to=' . $_GET['return_to'] : ''), 'message' => isset($message) ? $message : null, 'enctype' => "multipart/form-data")) ?>
<?php 
/*
 * 1er bloque:
 * - Columna izquieda con los datos del parte
 * - Columna derecha con vehículos y operarios
 */
?>
<div class="row">
<div class="col-lg-6">
    <?= $ctrl->new_record_form('editparts', $title, $data, $structure, $id, $config, true, false); ?>
</div>
<div class="col-lg-6">
<div class="col-lg-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Vehículos</h3>
<p>No olvide seleccionar los vehículos utilizados</p>
</div>
<div class="box-body">
<?php if (isset($id) && ($id!='')): ?>
<?php if ($part_vehicles): ?>
<table id="vehicles" class="table table-hover" align="center">
<tr>
<td> </td><td>id</td><td>Vehiculo</td><td>Matrícula</td>
</tr>
<?php foreach ($part_vehicles as $value): ?>
<tr>
<td><input id="v<?=$value['id']?>" name="v[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?>
<td><?= $value['name'] ?>
<td><?= $value['license_plate'] ?>
</tr>
<?php endforeach ?>
</table>
<?php else: ?>
<p>No hay vehículos</p>
<?php endif; ?>
</div>
</div>
</div>

<div class="col-lg-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Datos de los empleados</h3>
</div>
<div class="box-body">
<?php if (isset($id) && ($id!='')): ?>
<p>Los datos del operario se copiarán de los del anterior <strong>si se dejan en blanco</strong>.</p>
<p><strong>No olvide marcar los operarios que hayan trabajado</strong>. Si todos los datos son iguales, cumplimente sólo el primero de ellos.</p>
<?php if ($part_workers): ?>
<div class="nav-tabs-custom">
<ul class="nav nav-tabs">
<li class="active"><a href="#ida" data-toggle="tab">Ida</a></li>
<li><a href="#vuelta" data-toggle="tab">Vuelta</a></li>
<li><a href="#manana" data-toggle="tab">Mañana</a></li>
<li><a href="#tarde" data-toggle="tab">Tarde</a></li>
<li><a href="#dietas" data-toggle="tab">Dietas</a></li>
</ul>
<div class="tab-content">
<div class="active tab-pane" id="ida">

<h4>Desplazamientos de ida</h4>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td>
<td>id</td>
<td>Nombre</td>
<td>Desde/Hasta</td>
</tr>
<?php foreach ($part_workers as $value): ?>
<tr>
<td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?></td>
<td><?= $value['name'] ?></td>
<td>
<input id="wgoing_start<?=$value['id']?>" name="wgoing_start[<?=$value['id']?>]" type="time" value="<?=isset($value['going_start'])?$value['going_start']:null?>" />
<input id="wgoing_end<?=$value['id']?>" name="wgoing_end[<?=$value['id']?>]" type="time" value="<?=isset($value['going_end'])?$value['going_end']:null?>" />
</td>
<?php endforeach ?>
</table>
    
</div>
<div class="tab-pane" id="vuelta">

<h4>Desplazamientos de vuelta</h4>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td>
<td>id</td>
<td>Nombre</td>
<td>Desde/Hasta</td>
</tr>
<?php foreach ($part_workers as $value): ?>
<tr>
<td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?></td>
<td><?= $value['name'] ?></td>
<td>
<?php
	foreach(
		array(
			'back_start',
			'back_end',
                    ) as $field) : ?>
<input id="w<?=$field.$value['id']?>" name="w<?=$field.'['.$value['id'].']'?>" type="time" value="<?=isset($value[$field])?$value[$field]:null?>" />
<?php endforeach; ?>
</td>
<?php endforeach ?>
</table>
    
</div>
<div class="tab-pane" id="manana">

<h4>Horario de primer turno (mañana)</h4>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td>
<td>id</td>
<td>Nombre</td>
<td>Desde/Hasta</td>
</tr>
<?php foreach ($part_workers as $value): ?>
<tr>
<td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?></td>
<td><?= $value['name'] ?></td>
<td>
<?php
	foreach(
		array(
			'morning_from',
			'morning_to',
                    ) as $field) : ?>
<input id="w<?=$field.$value['id']?>" name="w<?=$field.'['.$value['id'].']'?>" type="time" value="<?=isset($value[$field])?$value[$field]:null?>" />
<?php endforeach; ?>
</td>
<?php endforeach ?>
</table>
    
</div>
<div class="tab-pane" id="tarde">

<h4>Horario de 2º turno (tarde)</h4>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td>
<td>id</td>
<td>Nombre</td>
<td>Desde/Hasta</td>
</tr>
<?php foreach ($part_workers as $value): ?>
<tr>
<td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?></td>
<td><?= $value['name'] ?></td>
<td>
<?php
	foreach(
		array(
			'afternoon_from',
			'afternoon_to'
                    ) as $field) : ?>
<input id="w<?=$field.$value['id']?>" name="w<?=$field.'['.$value['id'].']'?>" type="time" value="<?=isset($value[$field])?$value[$field]:null?>" />
<?php endforeach; ?>
</td>
<?php endforeach ?>
</table>
    
</div>
<div class="tab-pane" id="dietas">

<h4>Dietas</h4>
<table id="workers" class="table table-hover" align="center">
<tr>
<td> </td>
<td>id</td>
<td>Nombre</td>
<td>Dietas</td>
</tr>
<?php foreach ($part_workers as $value): ?>
<tr>
<td><input id="w<?=$value['id']?>" name="w[<?=$value['id']?>]" type="checkbox" <?= isset($value['checked'])?'checked':'' ?> /></td>
<td><?= $value['id'] ?></td>
<td><?= $value['name'] ?></td>
<td>
<select id="wallowances<?=$value['id']?>" class="selectpicker form-control" name="wallowances[<?=$value['id']?>]" data-live-search="true">
<option value='' <?= !isset($value['allowances']) || $value['allowances']==''?'selected':'' ?>>Seleccione una opción</option>
<option value='0' <?= isset($value['allowances']) && $value['allowances']=='0'?'selected':'' ?>>Sin dietas</option>
<option value='M' <?= isset($value['allowances']) && $value['allowances']=='M'?'selected':'' ?>>Media pensión</option>
<option value='C' <?= isset($value['allowances']) && $value['allowances']=='C'?'selected':'' ?>>Pensión completa</option>
<option value='P' <?= isset($value['allowances']) && $value['allowances']=='P'?'selected':'' ?>>Con pernoctación</option>
</select>
</td>
</tr>
<?php endforeach ?>
</table>
</div>
</div>
</div>
<?php else: ?>
<h3>No hay operarios?</h3>
<?php endif ?>
</div> <?php // de la columna derecha ?>
<?php else: ?>
<h3>Genere la orden de trabajo antes de asignar recursos</h3>
<?php endif ?>
</div> <?php // de la columna derecha ?>
</div>
</div>
</div> <!-- Fin del primer bloque -->

<?php 
/*
 * 2º bloque:
 * - Columna izquierda con la foto del parte de trabajo
 * - Columna derecha con la foto de las facturas
 */
?>

<div class="row">
    
<div class="col-lg-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Fotografía del parte de trabajo</h3>
</div>
<div class="box-body">
        <?php $imagen = isset($data['imagen']) ? $data['imagen'] : '';
        if (isset($id) && ($id != '')): ?>
        <?php $buttontype='primary'; ?>
            <?php if (file_exists("img/{$id}.{$imagen}")) : $buttontype = 'danger'; ?>
                <img src="<?= base_url("img/{$id}.{$imagen}") . '?' . time() ?>" class="img-responsive">
            <?php else: ?>
                No hay imagen disponible
            <?php endif ?>
</div>
<div class="box-footer">
    <label class="<?= $ctrl->my_class_button($buttontype); ?>" for="part_image">
                <input id="part_image" name="part_image" type="file" accept="image/*" style="display:none" 
	onchange="$('#part-image-name').html(this.files[0].name)">
	Imagen del parte
</label>
    <span class="<?= $ctrl->my_class_button('success'); ?>" id="part-image-name"></span>
        <?php endif ?>
</div>
</div>
</div>
    
<div class="col-lg-6">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Facturas de dietas</h3>
</div>
<div class="box-body">
        <?php $factura = isset($data['factura']) ? $data['factura'] : '';
        if (isset($id) && ($id != '')): ?>
        <?php $buttontype='primary'; ?>
            <?php if (file_exists("img/f{$id}.{$factura}")) : $buttontype = 'danger'; ?>
                <img src="<?= base_url("img/f{$id}.{$factura}") . '?' . time() ?>" class="img-responsive">
            <?php else: ?>
                No hay imagen disponible
            <?php endif ?>
        </div>
<div class="box-footer">
    <label class="<?= $ctrl->my_class_button($buttontype); ?>" for="fact_image">
                <input id="fact_image" name="fact_image" type="file" accept="image/*" style="display:none" 
	onchange="$('#fact-image-name').html(this.files[0].name)">
	Imagen de la(s) factura(s)
</label>
    <span class="<?= $ctrl->my_class_button('success'); ?>" id="fact-image-name"></span>
        <?php endif ?>
</div>
</div>
</div>
    
</div>  <!-- fin del 2º bloque -->

<?php /*
<hr>
<span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button></span>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
*/ ?>
    <?= $ctrl->close_form(); ?>
<?php else: ?>
<h3>Genere la orden de trabajo antes de asignar recursos</h3>
</div>
</div>
</div>
<?php endif ?>
