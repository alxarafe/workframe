<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
<div class="container">
    <?= $ctrl->edit_field('horizontal-form', 'from', array('label' => 'Desde', 'type' => 'date'), '2000-01-01'); ?>
    <?= $ctrl->edit_field('horizontal-form', 'to', array('label' => 'Hasta', 'type' => 'date'), '9999-12-31'); ?>
</div>
<span><button name="report" class="<?= $ctrl->my_class_button('primary'); ?>" type="submit">Imprimir</button></span>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?= $ctrl->close_form(); ?>
