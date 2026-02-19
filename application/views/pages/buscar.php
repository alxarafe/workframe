<div class="row">
<article class="col-md-4">
<header>
<h2><a href="<?= base_url('clientes/cliente') ?>" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></a> Clientes</h2>
</header>
<?php if ($customers) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($customers) ?></td>
	</tr>
	<?php foreach($customers as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("clientes/cliente/".$value['id']) ?>"><?= $value['name'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ningún cliente</p>
<?php endif ?>
</article>
<article class="col-md-4">
<header>
<h2><a href="<?= base_url('expedientes/expediente') ?>" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></a> Expedientes</h2>
</header>
<?php if ($files) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($files) ?></td>
	</tr>
	<?php foreach($files as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("expedientes/expediente/".$value['id']) ?>"><?= $value['name'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ningún expediente</p>
<?php endif ?>
</article>
<article class="col-md-4">
<header>
<h2><a href="<?= base_url('ordenes/orden') ?>" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></a> Órdenes de trabajo</h2>
</header>
<?php if ($workorders) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($workorders) ?></td>
	</tr>
	<?php foreach($workorders as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("ordenes/orden/".$value['id']) ?>"><?= $value['name'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ninguna órden de trabajo</p>
<?php endif ?>
</article>
</div>
<div class="row">
<article class="col-md-4">
<header>
<h2>Notas en clientes</h2>
</header>
<?php if ($customernotes) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($customernotes) ?></td>
	</tr>
	<?php foreach($customernotes as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("clientes/cliente/".$value['id_customer']) ?>"><?= $value['notes'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ninguna nota en clientes</p>
<?php endif ?>
</article>
<article class="col-md-4">
<header>
<h2>Notas en expedientes</h2>
</header>
<?php if ($filenotes) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($filenotes) ?></td>
	</tr>
	<?php foreach($filenotes as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("expedientes/expediente/".$value['id_file']) ?>"><?= $value['notes'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ninguna nota en expedientes</p>
<?php endif ?>
</article>
<article class="col-md-4">
<header>
<h2>Notas en órdenes de trabajo</h2>
</header>
<?php if ($workordernotes) : ?>
<table class="table table-hover" align="center">
	<tr>
	<td>#</td>
	<td>Total <?= count($workordernotes) ?></td>
	</tr>
	<?php foreach($workordernotes as $key=>$value) : ?>
		<tr id="fila<?= $key ?>">
		<td><?= $value['id'] ?></td>
		<td><a href="<?= site_url("ordenes/orden/".$value['id_order']) ?>"><?= $value['notes'] ?></a></td>
		</tr>
	<?php endforeach ?>
</table>
<?php else : ?>
<p>No se ha encontrado ninguna nota en órdenes de trabajo</p>
<?php endif ?>
</article>
</div>