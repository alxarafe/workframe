<!-- Main content -->
<?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
<?= $ctrl->table_form($data, $structure, $config); ?>
<?php /*
 * Ya no se usan filtros.
  <span><button name="filter" class="<?= $ctrl->my_class_button('primary'); ?>" type="submit">Aplicar filtros</button></span>
  <span><button name="unfilter" class="<?= $ctrl->my_class_button('primary'); ?>" type="submit">Borrar filtros</button></span>
 */

?>
<span><button name="cancel" class="<?= $ctrl->my_class_button('danger'); ?>" type="submit" formnovalidate>Salir</button></span>
<?= $ctrl->close_form(); ?>
