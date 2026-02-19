<?php

/**
 * rSanjoSEO.
 *
 * Copyright (c)2014-2026, Rafael San José Tovar
 *
 * @author		rSanjoSEO (rsanjose@alxarafe.com)
 * @copyright	Copyright (c)2018, Rafael San José Tovar (https://alxarafe.es/)
 * @license		Prohibida su distribución total o parcial. Uso sujeto a contrato comercial.
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Buscar_model extends MY_Model
{

	public function loadStructure()
	{
		$this->bbddStructure = array();
	}

	public function buscar($cad, $show_inactives = false)
	{
		$result = array();

		/*
		$cad=iconv("UTF-8", "UTF-8//TRANSLIT", utf8_encode(strtolower($cad)));
		echo "<p>Buscando... [$cad]</p>";
		*/

		$result['customers'] = $this->qry2array("SELECT * FROM customers WHERE (
													name LIKE '%$cad%' OR
													contact LIKE '%$cad%' OR
													address LIKE '%$cad%' OR
													zip LIKE '%$cad%' OR
													locality LIKE '%$cad%' OR
													town LIKE '%$cad%' OR
													telephone LIKE '%$cad%' OR
													email LIKE '%$cad%')" . ($show_inactives ? ' AND actives=1' : ''));
		$result['customernotes'] = $this->qry2array("SELECT * FROM customernotes WHERE CONVERT(notes USING utf8mb4) LIKE '%$cad%'");
		$result['files'] = $this->qry2array("SELECT * FROM files WHERE name LIKE '%$cad%'" . ($show_inactives ? ' AND actives=1' : ''));
		$result['workorders'] = $this->qry2array("SELECT * FROM workorders WHERE name LIKE '%$cad%'" . ($show_inactives ? ' AND actives=1' : ''));

		//$result['customernotes']=$this->qry2array("SELECT * FROM customernotes WHERE CONVERT(notes USING latin1) LIKE '%$cad%'");
		$result['filenotes'] = $this->qry2array("SELECT * FROM filenotes WHERE CONVERT(notes USING utf8mb4) LIKE '%$cad%'");
		$result['workordernotes'] = $this->qry2array("SELECT * FROM workordernotes WHERE CONVERT(notes USING utf8mb4) LIKE '%$cad%'");

		//test_array('Result:',$result,false);

		return $result;
	}
}
