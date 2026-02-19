<?php
function getImage($image, $watermark=true)
{
	$ret=$file="image_not_found.png";
	$filename=($watermark ? FCPATH.'imgcache/'.$image : FCPATH.'upload/'.$image);
	if ($watermark && !file_exists($filename))
	{
		$pos=strpos($image, '/');
		if ($pos)
		{
			$crear=FCPATH.'imgcache/'.substr($image, 0, $pos);
			echo "[$crear]";
			if (!is_dir($crear)) mkdir($crear);
		}
		
		$source=FCPATH.'upload/'.$image;
		list($ancho, $alto) = getimagesize($source);
		if ($ancho != $alto)
		{
			die("La imagen $source no es cuadrada");
		}
		else if ($ancho != 600)
		{
			$im = imagecreatetruecolor(600, 600);
			$tmp=imagecreatefromjpeg($source);
			imagecopyresized($im, $tmp, 0, 0, 0, 0, 600, 600, $ancho, $alto);
			imagedestroy($tmp);
		} 
		else
		{
			// Cargar la estampa y la foto para aplicarle la marca de agua
			$im = imagecreatefromjpeg($source);
		}
		
		
		$estampa = imagecreatefromjpeg(FCPATH.'upload/watermark.jpg');

		/*
		// Primero crearemos nuestra imagen de la estampa manualmente desde GD
		$estampa = imagecreatetruecolor(100, 70);
		imagefilledrectangle($estampa, 0, 0, 99, 69, 0x0000FF);
		imagefilledrectangle($estampa, 9, 9, 90, 60, 0xFFFFFF);
		$im = imagecreatefromjpeg('foto.jpeg');
		imagestring($estampa, 5, 20, 20, 'libGD', 0x0000FF);
		imagestring($estampa, 3, 20, 40, '(c) 2007-9', 0x0000FF);
		*/

		// Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
		$margen_dcho = 10;
		$margen_inf = 10;
		$sx = imagesx($estampa);
		$sy = imagesy($estampa);

		// Fusionar la estampa con nuestra foto con una opacidad del 50%
		imagecopymerge($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa), 5);

		// Guardar la imagen en un archivo y liberar memoria
		imagepng($im, FCPATH.'imgcache/'.$image);
		imagedestroy($im);
		
	}
	if (file_exists($filename))
	{
		$ret=$image;
	}
	return base_url('imgcache/'.$ret);
}

function toFloat($dato) {
	return str_replace(",", ".", preg_replace("/[^0-9,.]/", "", $dato));
}

function js_escape($text)
{
    return str_replace(array("'",'\\'), array("\'","\\"), $text);
}

function limpiar($String){
    $String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
    $String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
    $String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
    $String = str_replace(array('í','ì','î','ï'),"i",$String);
    $String = str_replace(array('é','è','ê','ë'),"e",$String);
    $String = str_replace(array('É','È','Ê','Ë'),"E",$String);
    $String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
    $String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
    $String = str_replace(array('ú','ù','û','ü'),"u",$String);
    $String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
    $String = str_replace(array('[','^','´','`','¨','~',']'),"",$String);
    $String = str_replace("ç","c",$String);
    $String = str_replace("Ç","C",$String);
    $String = str_replace("ñ","n",$String);
    $String = str_replace("Ñ","N",$String);
    $String = str_replace("Ý","Y",$String);
    $String = str_replace("ý","y",$String);
     
    $String = str_replace("&aacute;","a",$String);
    $String = str_replace("&Aacute;","A",$String);
    $String = str_replace("&eacute;","e",$String);
    $String = str_replace("&Eacute;","E",$String);
    $String = str_replace("&iacute;","i",$String);
    $String = str_replace("&Iacute;","I",$String);
    $String = str_replace("&oacute;","o",$String);
    $String = str_replace("&Oacute;","O",$String);
    $String = str_replace("&uacute;","u",$String);
    $String = str_replace("&Uacute;","U",$String);
	
    $String = str_replace(array('(', ')', ':', ',', '.', ';', '=', '/','\\')," ",$String);
	$String = preg_replace('/[^a-zA-Z0-9 -]/', '', $String);
    $String = str_replace(' ',"-",$String);
	
    return strtolower($String);
}

// Devuelve una URL corta (si puede). Si falla bit.ly, retorna la misma URL que le ha sido enviada
function get_short_url($url) {
	$user  = "o_5pn1ma91vo";
	$akey  = "R_4078464db0a24c64b6326c53c4103f01";
	$path  = "http://api.bit.ly/shorten?version=2.0.1";
	$bitly = $path."&longUrl=".urlencode($url)."&login=".$user."&apiKey=".$akey;	
	$data = file_get_contents($bitly);
	$obj = json_decode($data);

	if ($obj->errorCode == 0) {
		return $obj->results->$url->shortUrl;
	} else {
		return $url;
	}
}

// Obtiene la IP del cliente
function get_real_ip() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		return $_SERVER['HTTP_CLIENT_IP'];
		
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	
	return $_SERVER['REMOTE_ADDR'];
}

// Devuelve código HTML con un enlace a la $url, mostrando $text y como alternativo $title
// La diferencia con anchor está en que el enlace que se genera guarda en la tabla 'redireccionies' los datos de quién va picando en el enlace.
function r_anchor($lang, $url, $text, $title=Null) {
	$t = (isset($title) ? " title='$title'" : "");
	return "<a target='_blank' href='" . site_url($lang . "/redir/to/" . urlencode(str_replace('/', '|', $url))) . "'$t>$text</a>";
}