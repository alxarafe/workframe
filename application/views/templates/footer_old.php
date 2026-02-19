<hr>
</div>
<footer id="footer" class="copyright">
	<p><!--<p>Diseño y desarrollo <a target="_blank" href="https://alxarafe.es">rSanjoSEO</a>.</p>-->(C)<?= date('Y') ?> <a target="_blank" href="https://WorkFrame.es/">WorkFrame</a></p>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/9534e8d57b.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?php echo site_url("js/ie10-viewport-bug-workaround.js") ?>"></script>
<?php
/* 
Como por velocidad se cargan los js al final, tenemos un problema a la hora de generar los mapas, ya que cualquier comando que se
ejecute antes de cargar las librerías no va a funcionar. No puedo cargar los mapas antes porque necesitan jquery.

Este código carga el código $js del array y el siguiente incluye los ficheros php de $php. Con este código PHP se puede por ejemplo
recorrer las direcciones para marcarlas en el mapa, calcular su posición y centrarlo si es necesario.
*/
if (isset($js)) {
	foreach ($js as $item) {
		if ($item == "maps") { // Si se van a usar los mapas hay que incluir sus librerías...
			echo '<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer.js"></script>';
			echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
			//echo '<script type="text/javascript" src="https://raw.github.com/HPNeo/gmaps/master/gmaps.js"></script>';
			if (ENVIRONMENT == "production") {
				echo '<script type="text/javascript" src="https://cdn.rawgit.com/HPNeo/gmaps/master/gmaps.js"></script>';
			} else {
				echo '<script type="text/javascript" src="https://rawgit.com/HPNeo/gmaps/master/gmaps.js"></script>';
			}
		}
		echo '<script src="' . base_url('js/' . $item . '.js') . '"></script>';
	}
}
if (isset($php)) {
	foreach ($php as $script) {
		$file = 'includes/' . $script . '.php';
		if (file_exists($file)) include($file);
	}
}

?>
</body>

</html>