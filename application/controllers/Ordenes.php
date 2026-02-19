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

class Ordenes extends MY_Dbcontroller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_user) {
            redirect('/');
        }
        $this->public_page = false;

        $this->data['title'] = 'Edición de ordenes';
        $this->data['description'] = 'Edición de ordenes';
        $this->data['keywords'] = '';

        $this->load->model(array('bbdd_model', 'obras_model'));

        $this->data['js'][] = 'ordenes';
    }

    public function index()
    {
        $this->edit_table(
            'Gestión de ordenes',
            'workorders',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'status', 'label' => 'Estado', 'type' => 'dbselect', 'table' => 'orderstatus', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'id_file', 'label' => 'Expediente', 'type' => 'dbselect', 'table' => 'files', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'end_date', 'label' => 'Finalización', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'start_hour', 'label' => 'Hora de inicio', 'type' => 'time', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'address', 'label' => 'Dirección', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba la dirección (calle, número, planta y puerta) de la orden de trabajo con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'zip', 'label' => 'Código postal', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,10}', 'title' => 'Código postal de la orden de trabajo con un máximo de 10 caracteres.', 'filter' => true),
                array('name' => 'locality', 'label' => 'Población', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la población', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la provincia (y país si procede)', 'filter' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            array(
                'new' => base_url('/ordenes/orden'),
                'edit' => base_url('/ordenes/orden/#'),
                'tables' => array(
                    'files' => $this->bbdd_model->get_table('files', true),
                    'orderstatus' => $this->bbdd_model->get_table('orderstatus', true),
                ),
            )
        );
    }

    function edit_record($title, $tablename, $structure, $id, $config = null)
    {
        if (isset($_POST['save']) && isset($_POST['end_date'])) {
            if ($_POST['end_date'] == '') {
                $_POST['end_date'] = $_POST['date'];
            }
            if ($_POST['name'] == '') {
                $exp = $this->bbdd_model->get_record('files', $_POST['id_file']);
                if ($exp) {
                    $_POST['name'] = $exp['name'];
                }
            }
        }

        parent::edit_record($title, $tablename, $structure, $id, $config);
    }

    public function sendmail($id = null)
    {

        $foreman_id = 0;
        $workorder = $this->bbdd_model->get_record('workorders', $id);
        if (isset($workorder) && isset($workorder['id_foreman']) && $workorder['id_foreman'] > 0) {
            $foreman_id = $workorder['id_foreman'];
            $sql = "SELECT a.id, a.email, a.id_worker, b.name FROM users a LEFT JOIN workers b ON a.id_worker=b.id WHERE a.id_worker=$foreman_id";
            $registro = $this->bbdd_model->qry2array($sql);
            if (isset($registro) && $registro) {
                $registro = $registro[0];
            }
        }
        $foreman_name = '';
        $foreman_mail = '';
        if (isset($registro) && isset($registro['email'])) {
            $foreman_name = $registro['name'];
            $foreman_mail = $registro['email'];
        }

        if (!$workorder || $foreman_mail == '') {
            redirect(base_url('/'));
        }

        // Esto cambiará según el controlador
        if (isset($_POST['cancel'])) {
            redirect("ordenes/orden/$id");
        }

        if (isset($_POST['send'])) {
            $timestamp = date("Y-m-d H:i:s");
            $body = $_POST['body'];
            $subject = $_POST['subject'];

            $msg = PHPMailer\sendmail($foreman_mail, $subject, $body, $this->user['username'], $foreman_name);
            $this->data['message'] = $msg;
            if ($msg == MESSAGESENT) {
                redirect("ordenes/orden/$id");
            }
        }

        $this->data['title'] = "Enviar correo a $foreman_name ($foreman_mail)";

        if (isset($_POST['body'])) {
            $this->data['subject'] = $_POST['subject'];
            $this->data['body'] = $_POST['body'];
        } else {
            $exped = $this->bbdd_model->get_record('files', $workorder['id_file']);
            $vehicles = $this->bbdd_model->qry2array("SELECT b.license_plate, name FROM workordervehicles a LEFT JOIN vehicles b ON a.id_vehicle=b.id WHERE a.id_order=$id");
            $workers = $this->bbdd_model->qry2array("SELECT b.name FROM workorderworkers a LEFT JOIN workers b ON a.id_worker=b.id WHERE a.id_order=$id");
            $notas = $this->bbdd_model->qry2array("SELECT * FROM workordernotes a WHERE a.id_order=$id ORDER BY a.id");

            $file = $workorder['id_file'] . ' ' . $exped['name'];
            $order = $workorder['id'] . ' ' . $workorder['name'];
            $start = date('d-m-Y', strtotime($workorder['date']));
            $end = date('d-m-Y', strtotime($workorder['end_date']));
            $hour = $workorder['start_hour'];
            $locality = $exped['locality'];
            $town = $exped['town'];

            $body = '';
            $body .= "Notificación de orden de trabajo\r\n";
            $body .= "\r\n";
            $body .= "Expediente: $file\r\n";
            $body .= "Orden de trabajo: $order\r\n";
            $body .= "Fecha de comienzo: $start\r\n";
            $body .= "Fecha prevista de finalización: $end\r\n";
            $body .= "Hora de entrada: $hour\r\n";
            if (isset($locality) && $locality !== '') {
                $body .= "Población: $locality\r\n";
            }
            if (isset($town) && $town !== '') {
                $body .= "Provincia: $town\r\n";
            }
            $body .= "\r\n";
            $body .= "Asignación de recursos\r\n";
            $body .= "\r\n";
            if ($vehicles) {
                $body .= "Vehículos\r\n";
                foreach ($vehicles as $value) {
                    $body .= " - {$value['license_plate']} - {$value['name']}\r\n";
                }
            } else {
                $body .= " - No se han asignado vehículos.\r\n";
            }
            $body .= "\r\n";
            if ($workers) {
                $body .= "Empleados\r\n";
                foreach ($workers as $value) {
                    $body .= " - {$value['name']}\r\n";
                }
            } else {
                $body .= " - No se han asignado empleados.\r\n";
            }
            $body .= "\r\n";
            if ($notas) {
                $body .= "Notas\r\n";
                foreach ($notas as $value) {
                    $body .= " - {$value['id']}: {$value['notes']}\r\n";
                }
            } else {
                $body .= " - No hay notas.\r\n";
            }

            $subject = "Notificación de orden de trabajo $order";

            $this->data['subject'] = $subject;
            $this->data['body'] = $body;
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/sendmail', $this->data);
        $this->load->view('templates/footer');
    }

    public function orden($id = null, $delete = '')
    {
        if (isset($id)) {
            $registro = $this->bbdd_model->get_record('workorders', $id);
        }

        if ($delete == 'delete') {
            $registro = $this->bbdd_model->get_record('workorders', $id);
            $this->bbdd_model->delete_record('workorders', $id);
            redirect(base_url('expedientes/expediente/' . $registro['id_file']));
        }

        $save = isset($_POST['save']) || isset($_POST['newworkpart']) || isset($_POST['newnote']);
        $newwp = isset($_POST['newworkpart']);
        $newnote = isset($_POST['newnote']);
        if ($save) {
            $_POST['save'] = '';
        }

        if (isset($_GET['return_to'])) {
            $_POST['return_to'] = $_GET['return_to'];
        }

        if (isset($_POST['return_to'])) {
            $ret = 'expedientes/expediente/' . $_POST['return_to'];
            $exp = $this->bbdd_model->get_record('files', $_POST['return_to']);

            /*
              echo $ret;
              test_array('Expediente',$exp);
             */

            $rel = array(
                'id_file' => $_POST['return_to'],
                'locality' => $exp['locality'] ?? '',
                'town' => $exp['town'] ?? '',
            );
        } else {
            $ret = 'ordenes' . (!isset($_POST['cancel']) && isset($id) ? '/orden/' . $id : '');
        }

        if (isset($id)) {
            $temp = $this->bbdd_model->get_record('workorders', $id);
            $date = isset($_POST['fecha']) ? $_POST['fecha'] : $temp['date'];
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $temp['end_date'];
            $exp = $this->bbdd_model->get_record('files', $temp['id_file']);
        }
        $date = $date ?? date('Y-m-d');

        /*
          $fecha=null;
          if (isset($_POST['fecha'])) {
          $fecha=$_POST['fecha'];
          } else if (isset($id)) {
          $temp=$this->bbdd_model->get_record('workorders', $id);
          if (isset($temp['date'])) {
          $fecha=$temp['date'];
          }
          }
         */

        $all_vehicles = $this->bbdd_model->get_table('vehicles', false);
        $all_workers = $this->bbdd_model->get_table('workers', false, $date);
        if ($all_workers) {
            foreach ($all_workers as $key => $value) {
                $cat = $this->bbdd_model->get_record('categories', $value['id_category']);
                if ($cat) {
                    $all_workers[$key]['category'] = $cat['name'];
                }
            }
        }

        if ($save && isset($id)) {
            /*
              test_array('Exp:',$exp,false);
              test_array('Post:',$_POST,false);
             */

            $locality = (trim($_POST['locality']) == '' && trim($exp['locality']) != '') ? $exp['locality'] : $_POST['locality'];
            $town = (trim($_POST['town']) == '' && trim($exp['town']) != '') ? $exp['town'] : $_POST['town'];

            $this->bbdd_model->save_data(
                'workorders',
                array('id' => $id),
                array(
                    'id' => $id,
                    'id_foreman' => $_POST['id_foreman'] ?? 0,
                    'locality' => "'$locality'",
                    'town' => "'$town'",
                )
            );

            // die("Guardando workorder $id. Locality: '$locality', Town: '$town'.");

            if ($all_vehicles) {
                foreach ($all_vehicles as $key => $value) {
                    if (isset($_POST['v'][$key])) {
                        $this->bbdd_model->save_data(
                            'workordervehicles',
                            array('id_order' => $id, 'id_vehicle' => $value['id']),
                            array('id_order' => $id, 'id_vehicle' => $value['id'])
                        );
                    } else {
                        $this->bbdd_model->runqry("DELETE FROM workordervehicles WHERE id_order=$id AND id_vehicle=" . $value['id']);
                    }
                }
            }
            if ($all_workers) {
                //test_array('all_workers:',$all_workers,false);
                foreach ($all_workers as $key => $value) {
                    //echo "<p>id=$id, key=$key, value_id={$value['id']}</p>";
                    if (isset($_POST['w'][$key])) {
                        //test_array("Key: $key",$value,false);
                        $this->bbdd_model->save_data(
                            'workorderworkers',
                            array('id_order' => $id, 'id_worker' => $value['id']),
                            array('id_order' => $id, 'id_worker' => $value['id'])
                        );
                    } else {
                        //echo "<h4>Borrando id_order=$id, id_worker={$value['id']}</h4>";
                        $this->bbdd_model->runqry("DELETE FROM workorderworkers WHERE id_order=$id AND id_worker=" . $value['id']);
                    }
                }
                //die('Comprobar');
            }
        }

        $foreman_id = 0;
        $registro = $this->bbdd_model->get_record('workorders', $id);
        if (isset($registro) && isset($registro['id_foreman']) && $registro['id_foreman'] > 0) {
            $foreman_id = $registro['id_foreman'];
            $sql = "SELECT a.id, a.email, a.id_worker, b.name FROM users a LEFT JOIN workers b ON a.id_worker=b.id WHERE a.id_worker=$foreman_id";
            $registro = $this->bbdd_model->qry2array($sql);
            if (isset($registro) && $registro) {
                $registro = $registro[0];
            }
        }

        $foreman_name = '';
        $foreman_mail = '';
        if (isset($registro) && isset($registro['email'])) {
            $foreman_name = $registro['name'];
            $foreman_mail = $registro['email'];
        }

        $vehicles = $this->bbdd_model->get_records('workordervehicles', "id_order='$id'", false, 'id_vehicle');

        if ($all_vehicles) {
            foreach ($all_vehicles as $key => $value) {
                //if (isset($fecha)) $all_vehicles[$key]['warning']=$this->obras_model->vehicle_availability($value['id'], $fecha, $id);
                if (isset($date) && isset($end_date)) {
                    $all_vehicles[$key]['warning'] = $this->obras_model->checkvehicle($value['id'], $id, $date, $end_date);
                }
                if (isset($vehicles[$key])) {
                    $all_vehicles[$key]['checked'] = true;
                }
            }
        }

        $workers = $this->bbdd_model->get_records('workorderworkers', "id_order='$id'", false, 'id_worker');

        /*
          test_array('All Vehicles',$all_vehicles,false);
          test_array('Vehicles',$vehicles,false);
          test_array('All Workers',$all_workers,false);
          test_array('Workes',$workers,true);
         */

        if ($all_workers) {
            foreach ($all_workers as $key => $value) {
                // if (isset($fecha)) $all_workers[$key]['warning']=$this->obras_model->worker_availability($value['id'], $fecha, $id);
                if (isset($date) && isset($end_date)) {
                    $all_workers[$key]['warning'] = $this->obras_model->checkworker($value['id'], $id, $date, $end_date);
                }
                if (isset($workers[$key])) {
                    $all_workers[$key]['checked'] = true;
                }
            }
        }

        if ($all_workers) {
            foreach ($all_workers as $worker) {
                $cat = isset($worker['category']) ? $worker['category'] : 'Operarios';
                $key = preg_replace("/[^a-z0-9_]/", '_', strtolower($cat));
                $categories[$key] = $cat;
                $workers[$key][] = $worker;
            }
        }

        $this->edit_record(
            'Orden de trabajo',
            'workorders',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'status', 'label' => 'Estado', 'type' => 'dbselect', 'table' => 'orderstatus', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'id_file', 'label' => 'Expediente', 'type' => 'dbselect', 'table' => 'files', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'end_date', 'label' => 'Finalización', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true, 'default' => '0000-00-00'),
                array('name' => 'start_hour', 'label' => 'Hora de comienzo', 'type' => 'time', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true, 'default' => '09:00'),
                array('name' => 'address', 'label' => 'Dirección', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba la dirección (calle, número, planta y puerta) de la orden de trabajo con un máximo de 50 caracteres.', 'filter' => true),
                array('name' => 'zip', 'label' => 'Código postal', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,10}', 'title' => 'Código postal de la orden de trabajo con un máximo de 10 caracteres.', 'filter' => true),
                array('name' => 'locality', 'label' => 'Población', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la población', 'filter' => true),
                array('name' => 'town', 'label' => 'Provincia', 'readonly' => false, 'pattern' => '.{1,50}', 'title' => 'Escriba el nombre de la provincia (y país si procede)', 'filter' => true),
                //array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
            ),
            $id,
            array(
                'view' => 'obras/orden',
                'return_to' => $ret,
                'related_to' => isset($rel) ? $rel : null,
                'tables' => array(
                    'files' => $this->bbdd_model->get_table('files', true),
                    'orderstatus' => $this->bbdd_model->get_table('orderstatus', true),
                ),
                'extradata' => array(
                    'foreman_id' => $foreman_id,
                    'foreman_name' => $foreman_name,
                    'foreman_mail' => $foreman_mail,
                    'all_vehicles' => $all_vehicles,
                    //'all_workers'=>$all_workers,
                    'categories' => $categories ?? [],
                    'workers' => $workers,
                    'all_foremen' => $this->bbdd_model->qry2array("SELECT DISTINCT a.id_worker as id, b.name, b.active FROM `users` a LEFT JOIN `workers` b ON a.id_worker=b.id WHERE id_worker>0 GROUP BY a.id_worker ORDER BY id"),
                    //'vehicles'=>$this->bbdd_model->qry2array("SELECT * FROM vehicles WHERE id_order='$id'"),
                    //'workers'=>$this->bbdd_model->qry2array("SELECT * FROM workers WHERE id_order='$id'"),
                    'workparts' => $this->bbdd_model->qry2array("SELECT * FROM workparts WHERE id_order='$id'"),
                    'notes' => $this->bbdd_model->qry2array("SELECT * FROM workordernotes WHERE id_order='$id' ORDER BY id DESC"),
                ),
            )
        );

        //test_array('Data',$this->data);

        if ($newnote) {
            redirect("ordenes/notas/$id");
        }

        if ($newwp && ($foreman_id > 0)) {
            redirect("partes/parte/$foreman_id/$id");
        }
    }

    public function notas($id)
    {
        $rec = $this->bbdd_model->get_record('workorders', $id);
        if (!$rec) {
            redirect(base_url('/'));
        }

        $this->data['notes'] = $this->bbdd_model->qry2array("SELECT * FROM workordernotes WHERE id_order='$id' ORDER BY id DESC");

        if (isset($_POST['cancel'])) {
            redirect("ordenes/orden/$id");
        }

        if (isset($_POST['save'])) {
            $timestamp = date("Y-m-d H:i:s");
            $notes = $_POST['nota'];
            $this->bbdd_model->save_data(
                'workordernotes',
                array('id' => "'$timestamp'"),
                array(
                    'id' => "'$timestamp'",
                    'id_order' => $id,
                    notes => "'" . addslashes($notes) . "'"
                )
            );
            redirect("ordenes/orden/$id");
        }

        $this->data['title'] = 'Anotaciones de ' . $rec['name'];

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/addnote', $this->data);
        $this->load->view('templates/footer');
    }
}
