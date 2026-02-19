<h1>Asignación de roles a <?= (isset($usuario)?$usuario['username']:'usuario') ?></h1>
<?php if (!$usuario): ?>
<h2>No hay datos</h2>
<p>Regresar a <a href="<?= base_url('/administracion') ?>">menú de administración</a></p>
<?php else: ?>
    <?= $ctrl->open_form(array('action' => base_url('/administracion/editroles/' . $usuario['username']), 'message' => isset($message) ? $message : null)) ?>
    <table id="the_table" class="table table-hover" align="center">
<?php if ($roles) foreach($roles as $key=>$value): ?>
	<tr>
        <td hidden><?= $ctrl->edit_field($key, 'id', array('name' => 'id', 'type' => 'number', 'hidden' => true), $value['id']); ?></td>
                    <td><?= $ctrl->edit_field($key, 'name', array('name' => 'name', 'type' => 'checkbox', 'class' => 'flat-red', 'label' => $value['name']), isset($usuario['roles'][$key]) ? 1 : 0); ?></td>
                </tr>
<?php endforeach ?>
</table>
<span>
        <button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button>
        <button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button>
    </span>
    <?= $ctrl->close_form(); ?>
<?php endif ?>
