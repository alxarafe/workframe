<!-- Main content -->
<div class="jumbotron">
<article class="container">
	<header>
	<h1>Aplicación</h1>
	</header>
	<p>Algún mensaje sobre la aplicación.</p>
</article>
</div>
<?php if ($this->is_user): ?>
<div class="container">
	<form action="<?= site_url('/buscar') ?>">
	<input name="cad" type="text" placeholder="<?= lang('what_are_you_locking_for') ?>">
	<input type="submit" value="Buscar">
	</form>
</div>
<?php endif; ?>
<div class="container">
<div class="row">
<article class="col-md-4">
<header>
<h2>Mensaje 1</h2>
</header>
<p>Línea 1 sobre el mensaje 1.</p>
<p>Línea 2 sobre el mensaje 1.</p>
<p><a class="btn btn-primary" href="#">Botón 1</a></p>
</article>
<article class="col-md-4">
<header>
<h2>Mensaje 2</h2>
</header>
<p>Línea 1 sobre el mensaje 2.</p>
<p>Línea 2 sobre el mensaje 2.</p>
<p><a class="btn btn-primary" href="#">Botón 2</a></p>
</article>
<article class="col-md-4">
<header>
<h2>Mensaje 3</h2>
</header>
<p>Línea 1 sobre el mensaje 3.</p>
<p>Línea 2 sobre el mensaje 3.</p>
<p><a class="btn btn-primary" href="#">Botón 3</a></p>
</article>
</div>
</div>
