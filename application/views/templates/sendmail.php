<!-- Main content -->
<div class="col-lg-12">
<div class="box box-info">
<div class="box-header with-border">
<h3 class="box-title">Enviar correo</h3>
</div>
    <?= $ctrl->open_form(array('action' => base_url('/' . $this->uri->uri_string), 'message' => isset($message) ? $message : null)) ?>
    <div class="box-body">
<div class="form-group">
<label for="subject" class="col-sm-2 control-label">Asunto:</label>
<div class="col-sm-10">
<input id='subject' name='subject' type='text' pattern='.{1,130}' title='Escriba un asunto para el mensaje (de 1 a 130 caracteres)' class='form-control' value='<?= $subject ?>' />
</div>
</div>
<label for="body"></label>
<textarea class="form-control" id="body" name="body" rows="18"><?= $body ?></textarea>
</div>
<div class="box-footer">
<button name="send" class="btn btn-default" type="submit">Enviar</button>
<button name="cancel" class="btn btn-danger pull-right" type="submit" formnovalidate>Salir</button></span>
</div>
<?= $ctrl->close_form(); ?>
</div>
</div>