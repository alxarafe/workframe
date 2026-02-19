<?php

/**
 * Alxarafe WorkFrame.
 *
 * Copyright (C) 2018 - 2026 Rafael San José Tovar <rsanjose@alxarafe.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
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
