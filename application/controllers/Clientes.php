<?php

/**
 * rSanjoSEO.
 *
 * Copyright (c)2014-2026, Rafael San José Tovar
 *
 * @author      rSanjoSEO (rsanjose@alxarafe.com)
 * @copyright   Copyright (c)2018, Rafael San José Tovar (https://alxarafe.es/)
 * @license     Prohibida su distribución total o parcial. Uso sujeto a contrato comercial.
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Ya buscaremos la forma de eliminar este requier_once
require_once APPPATH . 'core/MY_Dbcontroller.php';

class Clientes extends MY_Dbcontroller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_user) {
            redirect('/');
        }
        $this->public_page = false;

        $this->data['title']        = 'Edición de clientes';
        $this->data['description']  = 'Edición de clientes';
        $this->data['keywords']     = '';

        $this->load->model(array('bbdd_model', 'obras_model'));
    }

    public function index()
    {
        $this->edit_table(
            'Gestión de clientes',
            'customers',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'contact', 'label' => 'Persona de contacto', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre de la persona de contacto con un máximo de 60 caracteres.', 'filter' => true),
                //array('name'=>'address', 'label'=>'Dirección', 'input'=>$this->is_admin, 'readonly'=>false, 'pattern'=>'.{1,50}', 'title'=>'Escriba la dirección (calle, número, planta y puerta) del cliente con un máximo de 50 caracteres.', 'filter'=>true),
                //array('name'=>'zip', 'label'=>'C.P.', 'input'=>$this->is_admin, 'readonly'=>false, 'pattern'=>'.{1,10}', 'title'=>'Escriba el código postal con un máximo de 10 caracteres.', 'filter'=>true),
                array('name' => 'locality', 'label' => 'Población', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la localidad del cliente con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,30}', 'title' => 'Escriba el nombre de la provincia del cliente con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'telephone', 'label' => 'Teléfono', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,15}', 'title' => 'Escriba el teléfono del cliente con un máximo de 15 caracteres.', 'filter' => true),
                array('name' => 'email', 'label' => 'Correo electrónico', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el correo electrónico del cliente con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'list', 'type' => 'link', 'label' => 'Informe', 'param' => 'id', 'value' => base_url('/reports/clientes/#'), 'newpage' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            array(
                'new' => base_url('/clientes/cliente'),
                'edit' => base_url('/clientes/cliente/#'),
                'report_url' => base_url('/reports/clientes/'),
            )
        );
    }

    public function cliente($id = null)
    {
        $cli = $this->bbdd_model->get_record('customers', $id);
        $email = isset($cli) && isset($cli['email']) ? $cli['email'] : 'none';

        $this->edit_record(
            'Gestión de clientes',
            'customers',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 30 caracteres.', 'filter' => true),
                array('name' => 'contact', 'label' => 'Persona de contacto', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre de la persona de contacto con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'address', 'label' => 'Dirección', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba la dirección (calle, número, planta y puerta) del cliente con un máximo de 30 caracteres.', 'filter' => true),
                array('name' => 'zip', 'label' => 'C.P.', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,10}', 'title' => 'Escriba el código postal con un máximo de 10 caracteres.', 'filter' => true),
                array('name' => 'locality', 'label' => 'Población', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la localidad del cliente con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,30}', 'title' => 'Escriba el nombre de la provincia del cliente con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'telephone', 'label' => 'Teléfono', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,15}', 'title' => 'Escriba el teléfono del cliente con un máximo de 15 caracteres.', 'filter' => true),
                array('name' => 'email', 'label' => 'Correo electrónico', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el correo electrónico del cliente con un máximo de 50 caracteres.', 'filter' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            $id,
            array(
                'view' => 'obras/cliente',
                'return_to' => '/clientes',
                'delete' => true,
                'extradata' => array(
                    'files' => $this->bbdd_model->qry2array("SELECT * FROM files WHERE id_customer='$id'"),
                    'mails' => $this->bbdd_model->qry2array("SELECT * FROM mails WHERE sender='$email' ORDER BY date DESC"),
                    'notes' => $this->bbdd_model->qry2array("SELECT * FROM customernotes WHERE id_customer='$id' ORDER BY id DESC"),
                ),
            )
        );
    }

    public function notas($id)
    {
        $rec = $this->bbdd_model->get_record('customers', $id);
        if (!$rec) {
            redirect(base_url('/'));
        }

        $this->data['notes'] = $this->bbdd_model->qry2array("SELECT * FROM customernotes WHERE id_customer='$id' ORDER BY id DESC");

        if (isset($_POST['cancel'])) {
            redirect("clientes/cliente/$id");
        }

        if (isset($_POST['save'])) {
            $timestamp = date("Y-m-d H:i:s");
            $notes = $_POST['nota'];
            $this->bbdd_model->save_data(
                'customernotes',
                array('id' => "'$timestamp'"),
                array(
                    'id' => "'$timestamp'",
                    'id_customer' => $id,
                    notes => "'" . addslashes($notes) . "'"
                )
            );
            redirect("clientes/cliente/$id");
        }

        $this->data['title'] = 'Anotaciones de ' . $rec['name'];

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/addnote', $this->data);
        $this->load->view('templates/footer');
    }
}
