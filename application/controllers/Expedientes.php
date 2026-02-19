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

use PHPMailer\PHPMailer;

// Ya buscaremos la forma de eliminar este requier_once
require_once APPPATH . 'core/MY_Dbcontroller.php';
require_once APPPATH . 'libraries/sendmail.php';

class Expedientes extends MY_Dbcontroller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_user) {
            redirect('/');
        }
        $this->public_page = false;

        $this->data['title']        = 'Edición de expedientes';
        $this->data['description']  = 'Edición de expedientes';
        $this->data['keywords']     = '';

        $this->load->model(array('bbdd_model', 'obras_model'));
    }

    public function index()
    {
        $this->edit_table(
            'Gestión de expedientes',
            'files',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'id_customer', 'label' => 'Cliente', 'type' => 'dbselect', 'table' => 'customers', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'locality', 'label' => 'Población', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la población', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la provincia (y país si procede)', 'filter' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            array(
                'new' => base_url('/expedientes/expediente'),
                'edit' => base_url('/expedientes/expediente/#'),
                //'return_to'=>$ret,
                'tables' => array(
                    'customers' => $this->bbdd_model->get_table('customers', true),
                ),
            )
        );
    }

    public function sendmail($id = null, $_msg = 0)
    {
        $cli = null;
        $exp = $this->bbdd_model->get_record('files', $id);
        if ($exp && ($exp['id_customer'] > 0)) {
            $cli = $this->bbdd_model->get_record('customers', $exp['id_customer']);
        }

        if ($cli) {
            $customer_name = $cli['name'];
            $customer_mail = $cli['email'];
        } else {
            $this->data['message'] = 'No hay cliente, o no tiene correo electrónico asociado';
        }

        $message_subject = null;
        $message_body = null;
        if ($_msg != 0) {
            $msg = $this->bbdd_model->get_record('mails', $_msg);
            if ($msg) {
                $customer_mail = $msg['sender'];
                $message_subject = 'RE: ' . $msg['subject'];

                $message_body = "En respuesta a:\r\n\r\n";
                $message_body .= "> " . wordwrap($msg['plain'], 75, "\r\n> ");
                $message_body .= "\r\n";
            }
        }

        // Esto cambiará según el controlador
        if (isset($_POST['cancel'])) {
            redirect("expedientes/expediente/$id");
        }

        if ($cli && isset($_POST['send'])) {
            $timestamp = date("Y-m-d H:i:s");
            $body = $_POST['body'];
            $subject = $_POST['subject'];

            $msg = PHPMailer\sendmail($customer_mail, $subject, $body, $this->user['username'], $customer_name);
            $this->data['message'] = $msg;
            if ($msg == MESSAGESENT) {
                $_id = $this->bbdd_model->next_id('mails');
                $this->bbdd_model->save_data(
                    'mails',
                    array('id' => $_id),
                    array(
                        'id' => $_id,
                        'subject' => "$subject",
                        'sender' => $this->user['username'],
                        'date' => $timestamp,
                        'html' => nl2br($body),
                        'plain' => $body,
                        'id_file' => $id,
                        'id_user' => $this->user['id'],
                    )
                );

                redirect("expedientes/expediente/$id");
            }
        }

        $this->data['title'] = "Acerca del expediente ({$exp['id']}) {$exp['name']}";

        if (isset($_POST['body'])) {
            $this->data['subject'] = $_POST['subject'];
            $this->data['body'] = $_POST['body'];
        } else {
            $subject = $message_subject == '' ? $this->data['title'] : $message_subject;
            $body = $message_body;

            $this->data['subject'] = $subject;
            $this->data['body'] = $body;
        }


        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/sendmail', $this->data);
        $this->load->view('templates/footer');
    }

    public function expediente($id = null, $delete = '')
    {
        if ($delete == 'delete') {
            $this->bbdd_model->delete_record('files', $id);
            redirect(base_url('expedientes'));
        }

        if (isset($_GET['return_to'])) {
            $_POST['return_to'] = $_GET['return_to'];
        }

        if (isset($_POST['return_to'])) {
            $cliente = $this->bbdd_model->get_record('customers', $_POST['return_to']);
            $ret = 'clientes/cliente/' . $_POST['return_to'];
            $rel = array(
                'id_customer' => $_POST['return_to'],
                'locality' => $cliente['locality'] ?? '',
                'town' => $cliente['town'] ?? '',
            );
        } else {
            $ret = 'expedientes' . (!isset($_POST['cancel']) && isset($id) ? '/expediente/' . $id : '');
        }

        $this->edit_record(
            'Edición de expediente',
            'files',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'id_customer', 'label' => 'Cliente', 'type' => 'dbselect', 'table' => 'customers', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'locality', 'label' => 'Población', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la población', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la provincia (y país si procede)', 'filter' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            $id,
            array(
                'view' => 'obras/expediente',
                'return_to' => $ret,
                'related_to' => isset($rel) ? $rel : null,
                'tables' => array(
                    'customers' => $this->bbdd_model->get_table('customers', true),
                ),
                'extradata' => array(
                    'workorders' => $this->bbdd_model->qry2array("SELECT * FROM workorders WHERE id_file='$id'"),
                    'notes' => $this->bbdd_model->qry2array("SELECT * FROM filenotes WHERE id_file='$id' ORDER BY id DESC"),
                    'mails' => $this->bbdd_model->qry2array("SELECT * FROM mails WHERE id_file='$id' ORDER BY date DESC"),
                ),
            )
        );
    }

    public function notas($id)
    {
        $rec = $this->bbdd_model->get_record('files', $id);
        if (!$rec) {
            redirect(base_url('/'));
        }

        $this->data['notes'] = $this->bbdd_model->qry2array("SELECT * FROM filenotes WHERE id_file='$id' ORDER BY id DESC");

        if (isset($_POST['cancel'])) {
            redirect("expedientes/expediente/$id");
        }

        if (isset($_POST['save'])) {
            $timestamp = date("Y-m-d H:i:s");
            $notes = $_POST['nota'];
            $this->bbdd_model->save_data(
                'filenotes',
                array('id' => "'$timestamp'"),
                array(
                    'id' => "'$timestamp'",
                    'id_file' => $id,
                    notes => "'" . addslashes($notes) . "'"
                )
            );
            redirect("expedientes/expediente/$id");
        }

        $this->data['title'] = 'Anotaciones de ' . $rec['name'];

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/addnote', $this->data);
        $this->load->view('templates/footer');
    }
}
