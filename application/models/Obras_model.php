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

class Obras_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function loadStructure()
	{
		$this->bbddStructure = array(
			'sections' => array(
				'fields' => array(
					'id'		=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'		=> array('Type' => 'varchar(60)'),
					'active'	=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
				'keys' => array('name'),
				'values' => array(
					array(
						'id' => 1,
						'name' => "'Material de construcción'",
						'active' => 1
					),
					array(
						'id' => 2,
						'name' => "'Fontanería'",
						'active' => 1
					),
					array(
						'id' => 3,
						'name' => "'Materiales varios'",
						'active' => 1
					),
					array(
						'id' => 4,
						'name' => "'Herramientas'",
						'active' => 1
					),
				)
			),
			'categories' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
				'keys' => array('name'),
				'values' => array(
					array(
						'id' => 1,
						'name' => "'Fontanero'",
						'active' => 1
					),
					array(
						'id' => 2,
						'name' => "'Carpintero'",
						'active' => 1
					),
				)
			),
			'workcenters' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
				'keys' => array('name'),
				'values' => array(
					array(
						'id' => 1,
						'name' => "'Madrid'",
						'active' => 1
					),
					array(
						'id' => 2,
						'name' => "'Sevilla'",
						'active' => 1
					),
				)
			),
			'workers' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'id_workcenter'	=> array('Type' => 'int(10) unsigned'),
					'id_category'	=> array('Type' => 'int(10) unsigned'),
					'email' => array('Type' => 'varchar(50)'),
					'temporary_from' => array('Type' => 'date'),
					'temporary_to' => array('Type' => 'date'),
					'active' => array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			'vehicles' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'id_workcenter'	=> array('Type' => 'int(10) unsigned'),
					'license_plate'	=> array('Type' => 'varchar(15)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			'customers' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'contact'		=> array('Type' => 'varchar(60)'),
					'address'		=> array('Type' => 'varchar(50)'),
					'zip'			=> array('Type' => 'varchar(10)'),
					'locality'		=> array('Type' => 'varchar(50)'),
					'town'			=> array('Type' => 'varchar(25)'),
					'telephone'		=> array('Type' => 'varchar(15)'),
					'email'			=> array('Type' => 'varchar(50)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			'customernotes' => array(
				'fields' => array(
					'id'			=> array('Type' => 'timestamp', 'Key' => 'PRI', 'Default' => 'CURRENT_TIMESTAMP'),
					'id_customer'	=> array('Type' => 'int(10) unsigned'),
					'source' 		=> array('Type' => 'tinyint(1)', 'Default' => 0),
					'notes'			=> array('Type' => 'text'),
				),
			),
			'files' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'id_customer'	=> array('Type' => 'int(10) unsigned'),
					'date'			=> array('Type' => 'date'),
					'locality'		=> array('Type' => 'varchar(50)'),
					'town'			=> array('Type' => 'varchar(50)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			'filenotes' => array(
				'fields' => array(
					'id'			=> array('Type' => 'timestamp', 'Key' => 'PRI', 'Default' => 'CURRENT_TIMESTAMP'),
					'id_file'		=> array('Type' => 'int(10) unsigned'),
					'source' 		=> array('Type' => 'tinyint(1)', 'Default' => 0),
					'notes'			=> array('Type' => 'text'),
				),
			),
			'orderstatus' => array(
				'fields' => array(
					'id' => array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name' => array('Type' => 'varchar(25)'),
					'visible' => array('Type' => 'tinyint(1)', 'Null' => 'YES', 'Default' => 0),
					'active' => array('Type' => 'tinyint(1)', 'Null' => 'YES', 'Default' => 0),
				),
				'keys' => array('name'),
				'values' => array(
					array(
						'id' => 1,
						'name' => "'Activa'",
						'visible' => 1,
						'active' => 1
					),
					array(
						'id' => 2,
						'name' => "'Finalizada'",
						'visible' => 0,
						'active' => 1
					),
					array(
						'id' => 3,
						'name' => "'Cancelada'",
						'visible' => 0,
						'active' => 1
					),
				)
			),
			'workorders' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'id_file'		=> array('Type' => 'int(10) unsigned'),
					'date'			=> array('Type' => 'date'),
					'end_date'		=> array('Type' => 'date', 'Default' => '9999-12-31'),
					'start_hour'	=> array('Type' => 'time', 'Default' => '09:00:00'),
					'id_foreman' => array('Type' => 'int(10) unsigned'),
					'address' => array('Type' => 'varchar(50)'),
					'zip' => array('Type' => 'varchar(10)'),
					'locality' => array('Type' => 'varchar(50)'),
					'town' => array('Type' => 'varchar(50)'),
					'status' => array('Type' => 'tinyint(1)', 'Default' => 1),
					'active' => array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			'workordernotes' => array(
				'fields' => array(
					'id'			=> array('Type' => 'timestamp', 'Key' => 'PRI', 'Default' => 'CURRENT_TIMESTAMP'),
					'id_order'		=> array('Type' => 'int(10) unsigned'),
					'source' 		=> array('Type' => 'tinyint(1)', 'Default' => 0),
					'notes'			=> array('Type' => 'text'),
				),
			),
			'workordervehicles' => array(
				'fields' => array(
					'id_order'		=> array('Type' => 'int(10) unsigned'),
					'id_vehicle'	=> array('Type' => 'int(10) unsigned'),
				),
			),
			'workorderworkers' => array(
				'fields' => array(
					'id_order'		=> array('Type' => 'int(10) unsigned'),
					'id_worker'		=> array('Type' => 'int(10) unsigned'),
				),
			),
			'workparts' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(60)'),
					'id_order'		=> array('Type' => 'int(10) unsigned'),
					'id_foreman'	=> array('Type' => 'int(10) unsigned'),
					'special_time'	=> array('Type' => 'tinyint(1)', 'Default' => 0),
					'imagen' => array('Type' => 'char(3)', 'Default' => Null),
					'factura'		=> array('Type' => 'char(3)', 'Default' => Null),
					'notes'			=> array('Type' => 'text'),
					'date'			=> array('Type' => 'date'),
				),
			),
			'partvehicles' => array(
				'fields' => array(
					'id_part'		=> array('Type' => 'int(10) unsigned'),
					'id_vehicle'	=> array('Type' => 'int(10) unsigned'),
				),
			),
			'partworkers' => array(
				'fields' => array(
					'id_part'		=> array('Type' => 'int(10) unsigned'),
					'id_worker'		=> array('Type' => 'int(10) unsigned'),
					'going_start'	=> array('Type' => 'time'),
					'going_end'		=> array('Type' => 'time'),
					'back_start'	=> array('Type' => 'time'),
					'back_end'		=> array('Type' => 'time'),
					'morning_from'	=> array('Type' => 'time'),
					'morning_to'	=> array('Type' => 'time'),
					'afternoon_from' => array('Type' => 'time'),
					'afternoon_to'	=> array('Type' => 'time'),
					'allowances'	=> array('Type' => 'char(1)', 'Default' => null),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			/*
			'workpartsdata' => array(
				'fields' => array(
					'id_part'		=> array('Type' => 'int(10) unsigned'),
					'start-work'	=> array('Type' => 'timestamp'),
					'end-work'		=> array('Type' => 'timestamp'),
					'special-time'	=> array('Type' => 'tinyint(1)', 'Default' => 0),
					'start-going'	=> array('Type' => 'timestamp'),
					'end-going'		=> array('Type' => 'timestamp'),
					'start-return'	=> array('Type' => 'timestamp'),
					'end-return'	=> array('Type' => 'timestamp'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Default' => 1),
				),
			),
			*/
		);
	}

	public function _vehicle_availability($id_vehicle, $date = null, $id = null)
	{
		if (!isset($date)) $date = date('Y-m-d');
		$res = $this->qry2array("
		SELECT a.id as id, a.name, a.date, a.end_date 
		FROM workorders a LEFT JOIN workordervehicles b
		ON a.id=b.id_order 
		WHERE '$date'<=a.end_date AND b.id_vehicle='$id_vehicle'");
		$ret = '';
		if ($res) {
			foreach ($res as $data) {
				if (!isset($id) || $data['id'] != $id) { // Evitar avisar para la orden actual
					$orden = $data['id'] . '-' . $data['name'];
					$fecha_ini = date('d-m-Y', strtotime($data['date']));
					$fecha_fin = date('d-m-Y', strtotime($data['end_date']));
					if ($data['date'] == $data['end_date']) {
						$fecha = "el día $fecha_ini";
					} else if ($data['end_date'] == '9999-12-31') {
						$fecha = "desde el día $fecha_ini sin fin previsto";
					} else {
						$fecha = "del día $fecha_ini al $fecha_fin";
					}
					$ret .= ($ret != '' ? "\n" : '') . "Ocupado $fecha por $orden.";
				}
			}
		}
		return $ret;
	}

	public function checkvehicle($id_vehicle, $workorder, $start_date = null, $end_date = null)
	{
		if (!isset($start_date) || !isset($end_date)) return false;
		$res = $this->qry2array("
		SELECT a.id as id, a.name, a.date, a.end_date 
		FROM workorders a LEFT JOIN workordervehicles b
		ON a.id=b.id_order 
		WHERE b.id_vehicle='$id_vehicle' AND a.id != $workorder AND (
			(a.date BETWEEN '$start_date' AND '$end_date') OR 
			(a.end_date  BETWEEN '$start_date' AND '$end_date') OR 
			('$start_date' BETWEEN a.date AND a.end_date) OR
			('$end_date' BETWEEN a.date AND a.end_date)
		)");

		$ret = '';
		if ($res) {
			foreach ($res as $cad) {
				$ret .= ($ret == '' ? '' : "\n") . ("Solapa con orden {$cad['id']}-{$cad['name']} del " . date('d-m-Y', strtotime($cad['date'])) . ' al ' . date('d-m-Y', strtotime($cad['end_date']))) . '.';
			}
		} else
			$ret = false;

		return $ret;
	}

	public function checkworker($id_worker, $workorder, $start_date = null, $end_date = null)
	{
		if (!isset($start_date) || !isset($end_date)) return false;
		$res = $this->qry2array("
		SELECT a.id as id, a.name, a.date, a.end_date 
		FROM workorders a LEFT JOIN workorderworkers b
		ON a.id=b.id_order 
		WHERE b.id_worker='$id_worker' AND a.id != $workorder AND (
			(a.date BETWEEN '$start_date' AND '$end_date') OR 
			(a.end_date  BETWEEN '$start_date' AND '$end_date') OR 
			('$start_date' BETWEEN a.date AND a.end_date) OR
			('$end_date' BETWEEN a.date AND a.end_date)
		)");
		$ret = '';
		if ($res) {
			foreach ($res as $cad) {
				$ret .= ($ret == '' ? '' : "\n") . ("Solapa con orden {$cad['id']}-{$cad['name']} del " . date('d-m-Y', strtotime($cad['date'])) . ' al ' . date('d-m-Y', strtotime($cad['end_date']))) . '.';
			}
		} else
			$ret = false;

		return $ret;
	}

	public function _worker_availability($id_worker, $date = null, $id = null)
	{
		if (!isset($date)) $date = date('Y-m-d');
		$res = $this->qry2array("
		SELECT a.id as id, a.name, a.date, a.end_date 
		FROM workorders a LEFT JOIN workorderworkers b
		ON a.id=b.id_order 
		WHERE '$date'<=a.end_date AND b.id_worker='$id_worker'");
		$ret = '';
		if ($res) {
			foreach ($res as $data) {
				if (!isset($id) || $data['id'] != $id) { // Evitar avisar para la orden actual
					$orden = $data['id'] . '-' . $data['name'];
					$fecha_ini = date('d-m-Y', strtotime($data['date']));
					$fecha_fin = date('d-m-Y', strtotime($data['end_date']));
					if ($data['date'] == $data['end_date']) {
						$fecha = "el día $fecha_ini";
					} else if ($data['end_date'] == '9999-12-31') {
						$fecha = "desde el día $fecha_ini sin fin previsto";
					} else {
						$fecha = "del día $fecha_ini al $fecha_fin";
					}
					$ret .= ($ret != '' ? "\n" : '') . "Ocupado $fecha por $orden.";
				}
			}
		}
		return $ret;
	}
}
