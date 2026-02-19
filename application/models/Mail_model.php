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

class Mail_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function loadStructure()
	{
		$this->bbddStructure = array(
			'mails' => array(
				'fields' => array(
					'id'		=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'subject'	=> array('Type' => 'varchar(250)'),
					'sender'	=> array('Type' => 'varchar(250)'),
					'date'		=> array('Type' => 'datetime'),
					'html'		=> array('Type' => 'longtext'),
					'plain'		=> array('Type' => 'longtext'),
					'id_file'	=> array('Type' => 'int(10) unsigned', 'Default' => 0),
					'id_user'	=> array('Type' => 'int(10) unsigned', 'Default' => 0),
				),
			),
		);
		/*
		$this->bbddStructure = array(
			'mail_attachments' => array(
				'fields' => array(
					'id'		=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'id_mail'	=> array('Type' => 'int(10) unsigned'),
					'name'		=> array('Type' => 'varchar(60)'),
					'type'		=> array('Type' => 'varchar(10)'),
				),
			),
		);
		*/
	}

	public function save_mail($mail)
	{
		$id = $this->next_id('mails');
		$this->save_data(
			'mails',
			array('id' => $id),
			array(
				'id' => $id,
				'subject' => $mail['subject'],
				'sender' => $mail['from'],
				'date' => $mail['date'],
				'html' => $mail['html'],
				'plain' => $mail['plain'],
			)
		);
	}
}
