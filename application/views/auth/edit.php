<?= form_open(site_url(/*$this->lang->lang().*/'/auth/edit/'.$user['username']), array('class' => 'form-horizontal')); ?>

<div class="form-group">

	<?= form_label(lang('form_username'), 'username', array('class' => 'col-lg-2 control-label')); ?>
	<div class="col-lg-10">
	<?= form_error('username') ?>
	<?= form_input(
		array(
			'type'=>'text', 
			'name'=>'username', 
			'id'=>'username', 
			'placeholder' => lang('form_username'), 
			'value'=>(set_value('name') ? set_value('name') : (set_value('username') ? set_value('username') : (isset($username) ? $username : $user['username']))), 
			'class' => 'form-control'
		)); ?>
	</div>

	<?= form_label(lang('form_email'), 'email', array('class' => 'col-lg-2 control-label')); ?>
	<div class="col-lg-10">
	<?= form_error('email') ?>
	<?= form_input(
		array(
			'type'=>'email', 
			'name'=>'email', 
			'id'=>'email', 
			'placeholder' => lang('form_email'), 
			'value'=>(set_value('email') ? set_value('email') : (set_value('email') ? set_value('email') : (isset($email) ? $email : $user['email']))), 
			'class' => 'form-control')
		); ?>
	</div>

	<div hidden>
	<?= form_label(lang('form_password'), 'password', array('class' => 'col-lg-2 control-label')); ?>
	<div class="col-lg-10">
	<?= form_error('password') ?>
	<?= form_input(
		array(
			'type'=>'password', 
			'name'=>'password', 
			'id'=>'password', 
			'placeholder' => lang('form_password'), 
			'value'=>(set_value('password') ? set_value('password') : ""), 
			'class' => 'form-control')
		); ?>
	</div>
	</div>

</div>
<div class="form-group">
	<div class="col-lg-2"></div>
	<div class="col-lg-10">
	<?= form_label(lang('form_newpassword_message')); ?>
	</div>
</div>
<div class="form-group">
	<?= form_label(lang('form_newpassword'), 'password', array('class' => 'col-lg-2 control-label')); ?>
	<div class="col-lg-10">
	<?= form_error('newpassword') ?>
	<?= form_input(
		array(
			'type'=>'password', 
			'name'=>'newpassword', 
			'id'=>'password', 
			'placeholder' => lang('form_newpassword'), 
			'value'=>(set_value('newpassword') ? set_value('newpassword') : ""), 
			'class' => 'form-control')
		); ?>
	</div>

	<?= form_label(lang('form_password2'), 'passconf', array('class' => 'col-lg-2 control-label')); ?>
	<div class="col-lg-10">
	<?= form_input(
		array(
			'type'=>'password', 
			'name'=>'passconf', 
			'id'=>'passconf', 
			'placeholder' => lang('form_password2'), 
			'value'=>(set_value('passconf') ? set_value('passconf') : ""), 
			'class' => 'form-control')
		); ?>
	</div>
</div>
<div class="form-group">
	<div class="col-lg-offset-2 col-lg-10">
	  <button type="submit" class="btn btn-primary"><?= lang('auth_save_changes') ?></button>
	</div>
</div>
<?php /*
<?php if ($social) : ?>
<table class="table table-hover">
<thead>
<th></th>
<th></th>
<th>Red social</th>
<th>Nombre de perfil</th>
<th>Nombre de usuario</th>
</thead>
<tbody>
<?php foreach ($social as $key=>$value) : ?>
<tr>
<td>Eliminar</td>
<td><?= ($value['id']==$user['social_id'] ? "Defecto" : "Seleccionar") ?></td>
<td><?= $this->PROVIDERS[$value['id_network']] ?></td>
<td><?= $value['profile'] ?></td>
<td><?= $value['display_name'] ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>
*/ ?>

<?= form_close() ?>
