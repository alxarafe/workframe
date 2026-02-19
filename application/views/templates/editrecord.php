<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
<?= $ctrl->record_form($data, $structure, $id, $config); ?>
<span><button name="save" class="<?= $ctrl->my_class_button('success'); ?>" type="submit">Guardar</button></span>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
&nbsp;
&nbsp;
&nbsp;
<?php if ($candelete): ?>
    <span align="right"><button name="delete" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit">Borrar</button></span>
<?php else: ?>
        <span align="right"><button name="delete" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" disabled>Borrar <span class="badge badge-light"> <?= $usos ?> </span></button></span>
    <?php endif ?>
    <?= $ctrl->close_form(); ?>
