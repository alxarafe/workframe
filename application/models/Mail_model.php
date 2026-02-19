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
