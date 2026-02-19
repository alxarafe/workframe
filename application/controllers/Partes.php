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

class Partes extends MY_Dbcontroller
{
    private $id_foreman;

    public function __construct()
    {
        parent::__construct();

        if ($this->is_user) {
            $this->id_foreman = 0;
        } elseif ($this->user['id_worker'] > 0) {
            $this->id_foreman = $this->user['id_worker'];
        } else {
            redirect('/');
        }

        $this->public_page = false;

        $this->data['title'] = 'Edición de partes de trabajo';
        $this->data['description'] = 'Edición de partes de trabajo';
        $this->data['keywords'] = '';

        $this->load->model(array('bbdd_model', 'obras_model'));
    }

    public function index($foreman = 0, $todas = 0)
    {
        if (isset($_POST['exit'])) {
            redirect(base_url('/'));
        }

        if (isset($_POST['select'])) {
            redirect(base_url('/partes/index/' . $_POST['id_foreman'] . '/' . $todas));
        }

        if (isset($_POST['cancel'])) {
            redirect(base_url('/partes/index/0/' . $todas));
        }

        if ($this->id_foreman > 0 && $this->id_foreman != $foreman) {
            $this->index($this->id_foreman, $todas);
            return;
        }

        if ($foreman == 0) { // Hay que seleccionar un encargado
            $this->data['title'] = 'Seleccione un encargado de obra';
            $this->data['all_foremen'] = $this->bbdd_model->qry2array("SELECT DISTINCT a.id_worker as id, b.name, b.active FROM `users` a LEFT JOIN `workers` b ON a.id_worker=b.id WHERE id_worker>0 GROUP BY a.id_worker ORDER BY id");
            $this->data['id_foreman'] = $foreman;

            $this->load->view('templates/header', $this->data);
            $this->load->view('obras/selectforeman', $this->data);
            $this->load->view('templates/footer');
        } else {
            $worker = $this->bbdd_model->get_record('workers', $foreman);
            $foremanname = $worker['name'];
            $hoy = date('Y-m-d');
            $data = $this->bbdd_model->get_records('workorders', "id_foreman=$foreman AND '$hoy' BETWEEN date AND end_date", false);
            if ($data) {
                foreach ($data as $key => $value) {
                    $file = $value['id'];

                    $detail[$file] = $this->bbdd_model->get_records('workparts', "id_order=$file", false);
                    $data[$key]['qty'] = $detail[$file] ? count($detail[$file]) : 0;
                }
            }

            //if (isset($detail)) test_array('Detalle',$detail,false);

            $this->data['details'] = isset($detail) ? $detail : null;

            $this->data['title'] = "Obras asignadas a $foremanname";
            $this->data['data'] = $data;
            $this->data['foreman'] = $foreman;
            $this->data['foremanname'] = $foremanname;

            /*
              PENDIENTE

              $detail debe de contener cada parte de obra y cuando se envíe,
              deberá de ponerse un botón para añadir nuevo parte y un listado
              de los partes ya realizados para poderlos consultar.

             */

            $this->load->view('templates/header', $this->data);
            $this->load->view('obras/selectwork', $this->data);
            $this->load->view('templates/footer');

            /*
              $this->edit_table(
              'Gestión de partes de trabajo',
              'workorders',
              array(
              array('name'=>'id', 'label'=>'ID', 'type'=>'number', 'auto'=>true, 'input'=>$this->is_admin),
              array('name'=>'name', 'label'=>'Nombre', 'input'=>$this->is_admin, 'readonly'=>false, 'pattern'=>'.{1,60}', 'title'=>'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter'=>true),
              array('name'=>'id_file', 'label'=>'Expediente', 'type'=>'dbselect', 'table'=>'files', 'input'=>$this->is_admin, 'readonly'=>false),
              array('name'=>'date', 'label'=>'Fecha', 'type'=>'date', 'input'=>$this->is_admin, 'readonly'=>false, 'filter'=>true),
              array('name'=>'active', 'type'=>'checkbox', 'label'=>'Activo', 'input'=>$this->is_admin, 'default'=>true),
              ),
              array(
              'new'=>base_url('/ordenes/orden'),
              'edit'=>base_url('/ordenes/orden/#'),
              'tables'=>array(
              'files'=>$this->bbdd_model->get_table('files', true),
              ),
              )
              );
             */
        }
    }

    public function partid($part = -1)
    {
        if ($parte = $this->bbdd_model->get_record('workparts', $part)) {
            $this->parte($parte['id_foreman'], $parte['id_order'], $part);
        } else {
            $this->index;
        }
    }

    protected function saveimage($part, $_image, $field = 'image', $wc = '')
    {

        if (isset($_FILES[$_image]) && ($_FILES[$_image]['name'] != '')) {
            $image = array_search(
                $_FILES[$_image]['error'],
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif'
                )
            );

            if ($_FILES[$_image]['error'] == UPLOAD_ERR_OK) {
                move_uploaded_file($_FILES[$_image]['tmp_name'], "img/{$wc}{$part}.{$image}");

                //$this->test_array('Guardar', array('id' => $part, $field => "'$image'"));

                $this->bbdd_model->save_data(
                    'workparts',
                    array('id' => $part),
                    array('id' => $part, "$field" => "'$image'")
                );
            } else {
                $message = '<p>Error ' . $_FILES[$_image]['error'] . ' al subir fichero ' . $_FILES[$_image]['name'] . '</p>';
                switch ($_FILES[$_image]['error']) {
                    case 1:
                        $message .= '<p>Fichero demasiado grande: El fichero subido excede la directiva upload_max_filesize de php.ini.</p>';
                        break;
                    case 2:
                        $message .= '<p>Fichero demasiado grande: El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML.</p>';
                        break;
                    case 3:
                        $message .= '<p>El fichero fue sólo parcialmente subido.</p>';
                        break;
                    case 4:
                        $message .= '<p>No se subió ningún fichero.</p>';
                        break;
                    case 6:
                        $message .= '<p>Falta la carpeta temporal.</p>';
                        break;
                    case 7:
                        $message .= '<p>No se pudo escribir el fichero en el disco.</p>';
                        break;
                    case 8:
                        $message .= '<p>Una extensión de PHP detuvo la subida de ficheros.</p>';
                        break;
                }
            }
        }
    }

    public function parte($foreman, $order = null, $part = '')
    {
        if (isset($order)) {
            $_POST['order'] = $order;
        }

        if (isset($_GET['return_to'])) {
            $_POST['return_to'] = $_GET['return_to'];
        }

        if (isset($_POST['return_to'])) {
            //$ret='partes/index/'.$foreman.'/'.$_POST['return_to'];
            $rel = array('id_order' => $_POST['return_to']);
        } else {
            //$ret='ordenes';
        }

        if ($message = $this->session->flashdata('message')) {
            $this->load->vars($message);
        }
        //$order=(isset($_order))?$this->bbdd_model->get_record('orders', $_order):null;

        $part_vehicles = $this->bbdd_model->qry2indexedarray("SELECT * FROM workordervehicles a LEFT JOIN vehicles b ON a.id_vehicle=b.id WHERE id_order='$order'", 'id_vehicle');
        $part_workers = $this->bbdd_model->qry2indexedarray("SELECT * FROM workorderworkers a LEFT JOIN workers b ON a.id_worker=b.id WHERE id_order='$order'", 'id_worker');

        if (isset($_POST['save'])) {
            if ($part == '') {
                $part = $this->obras_model->next_id('workparts');
                $this->bbdd_model->save_data(
                    'workparts',
                    array('id' => $part),
                    array(
                        'id' => $part,
                        'name' => "'" . $_POST['name'] . "'",
                        'id_order' => "'" . $_POST['id_order'] . "'",
                        'id_foreman' => "'" . $foreman . "'",
                        'special_time' => "'" . $_POST['special_time'] . "'",
                        'notes' => "'" . $_POST['notes'] . "'",
                        'date' => "'" . $_POST['date'] . "'",
                    )
                );
            }

            $this->saveimage($part, 'part_image', 'imagen', '');
            $this->saveimage($part, 'fact_image', 'factura', 'f');

            if ($part_vehicles) {
                foreach ($part_vehicles as $key => $value) {
                    if (isset($_POST['v'][$value['id']])) {
                        $this->bbdd_model->save_data(
                            'partvehicles',
                            array('id_part' => $part, 'id_vehicle' => $value['id_vehicle']),
                            array('id_part' => $part, 'id_vehicle' => $value['id_vehicle'])
                        );
                    } else {
                        $this->bbdd_model->runqry("DELETE FROM partvehicles WHERE id_part=$part AND id_vehicle=" . $value['id_vehicle']);
                    }
                }
            }

            if ($part_workers) {
                //test_array('POST',$_POST, false);


                $a['going_start'] = null;
                $a['going_end'] = null;
                $a['back_start'] = null;
                $a['back_end'] = null;
                $a['morning_from'] = null;
                $a['morning_to'] = null;
                $a['afternoon_from'] = null;
                $a['afternoon_to'] = null;
                $a['allowances'] = null;



                foreach ($part_workers as $key => $value) {
                    $res = $idx = array('id_part' => $part, 'id_worker' => $value['id_worker']);

                    foreach ($a as $key2 => $value2) {
                        $v = isset($_POST['w' . $key2][$key]) ? $_POST['w' . $key2][$key] : null;
                        echo "<p>POST[w$key2][$key]=$v</p>";
                        if (!isset($v) || $v == '' /* || $v=='0' */) {
                            $v = $a[$key2];
                        }
                        if (!isset($a[$key2])) {
                            $a[$key2] = $v;
                        }
                        $res[$key2] = "'$v'";
                    }

                    if (isset($_POST['w'][$value['id']])) {
                        $this->bbdd_model->save_data(
                            'partworkers',
                            $idx,
                            $res
                        );
                    } else {
                        $this->bbdd_model->runqry("DELETE FROM partworkers WHERE id_part=$part AND id_worker=" . $value['id']);
                    }
                }
            }

            /*
              if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
              echo "El fichero es válido y se subió con éxito.\n";
              } else {
              echo "¡Posible ataque de subida de ficheros!\n";
              }
             */

            //test_array('Files:',$_FILES,false);
            //test_array('Post:',$_POST, false);

            $this->session->set_flashdata('message', $message);
            redirect(base_url("partes/parte/$foreman/$order/$part"));
        }

        $vehicles = $this->bbdd_model->get_records('partvehicles', "id_part='$part'", false, 'id_vehicle');
        if ($part_vehicles) {
            foreach ($part_vehicles as $key => $value) {
                if (isset($vehicles[$key])) {
                    $part_vehicles[$key]['checked'] = true;
                }
            }
        }

        $workers = $this->bbdd_model->get_records('partworkers', "id_part='$part'", false, 'id_worker');
        if ($part_workers) {
            foreach ($part_workers as $key => $value) {
                if (isset($workers[$key])) {
                    $part_workers[$key]['checked'] = true;
                    foreach (
                        array(
                            'going_start',
                            'going_end',
                            'back_start',
                            'back_end',
                            'morning_from',
                            'morning_to',
                            'afternoon_from',
                            'afternoon_to',
                            'allowances',
                            'active'
                        ) as $value
                    ) {
                        $newvalue = $workers[$key][$value];

                        $part_workers[$key][$value] = $newvalue;
                    }
                }
            }
        }

        if (($foreman != $this->id_foreman) && !$this->is_user && !$this->is_admin) {
            redirect('partes/index/' . $foreman);
        }


        if (isset($order)) {
            $page = 'editpart';

            /*
              if (isset($part)) {   // Editar parte $part
              $this->data['data']=$this->bbdd_model->get_record('workparts', $part);
              } else {          // Nuevo parte
              $this->data['part']=null;
              }
             */
            $this->edit_record(
                'Edición de parte de trabajo',
                'workparts',
                array(
                    array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                    array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true, 'default' => 'Parte ' . date('Y-m-d') . ' de orden ' . $order),
                    array('name' => 'id_order', 'label' => '(Exp) Orden', 'type' => 'dbselect', 'table' => 'workorders', 'input' => $this->is_admin, 'readonly' => true, 'default' => isset($order) ? $order : null),
                    array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                    array('name' => 'special_time', 'label' => 'Montaje especial', 'type' => 'checkbox', 'input' => $this->is_admin, 'readonly' => false),
                    //array('name'=>'image', 'label'=>'Imagen', 'type'=>'text', 'readonly'=>true, 'hidden'=>true),
                    array('name' => 'notes', 'type' => 'textarea', 'label' => 'Anotaciones', 'input' => $this->is_admin, 'param' => 10),
                ),
                $part,
                array(
                    'view' => 'obras/editpart',
                    'return_to' => "partes/index/$foreman",
                    'related_to' => isset($rel) ? $rel : null,
                    'rows' => 10,
                    'message' => (isset($message) ? $message : null),
                    'tables' => array(
                        'workorders' => $this->bbdd_model->qry2array("SELECT id, CONCAT('(',id_file,') ',id,' ',name) AS name, active FROM workorders WHERE id='$order'"),
                    ),
                    'extradata' => array(
                        'part_vehicles' => $part_vehicles,
                        'part_workers' => $part_workers,
                        'vehicles' => $vehicles,
                        'workers' => $workers,
                    ),
                )
            );
        } else {    // Listado de partes vivos del usuario
            if (isset($message)) {
                $this->data['message'] = $message;
            }
            $page = 'showparts';
            $this->data['parts'] = $this->bbdd_model->get_records('workparts', "id_foreman=$foreman AND '$hoy' BETWEEN date AND end_date", false);
        }

        /*
          $this->load->view('templates/header', $this->data);
          $this->load->view('obras/'.$page, $this->data);
          $this->load->view('templates/footer');
         */
    }

    public function orden($id = null)
    {
        if (isset($_GET['return_to'])) {
            $_POST['return_to'] = $_GET['return_to'];
        }

        if (isset($_POST['return_to'])) {
            $ret = 'expedientes/expediente/' . $_POST['return_to'];
            $rel = array('id_file' => $_POST['return_to']);
        } else {
            $ret = 'ordenes';
        }

        $all_vehicles = $this->bbdd_model->get_table('vehicles', false);
        $all_workers = $this->bbdd_model->get_table('workers', false);

        if (isset($_POST['save']) && isset($id)) {
            if ($all_vehicles) {
                foreach ($all_vehicles as $key => $value) {
                    if (isset($_POST['v'][$value['id']])) {
                        $this->bbdd_model->save_data(
                            'workordervehicles',
                            array('id_order' => $id, 'id_vehicle' => $key),
                            array('id_order' => $id, 'id_vehicle' => $key)
                        );
                    } else {
                        $this->bbdd_model->runqry("DELETE FROM workordervehicles WHERE id_order=$id AND id_vehicle=" . $value['id']);
                    }
                }
            }
            if ($all_workers) {
                foreach ($all_workers as $key => $value) {
                    if (isset($_POST['w'][$value['id']])) {
                        $this->bbdd_model->save_data(
                            'workorderworkers',
                            array('id_order' => $id, 'id_worker' => $key),
                            array('id_order' => $id, 'id_worker' => $key)
                        );
                    } else {
                        $this->bbdd_model->runqry("DELETE FROM workorderworkers WHERE id_order=$id AND id_worker=" . $value['id']);
                    }
                }
            }
        }

        $vehicles = $this->bbdd_model->get_records('workordervehicles', "id_order='$id'", false, 'id_vehicle');
        if ($all_vehicles) {
            foreach ($all_vehicles as $key => $value) {
                if (isset($vehicles[$key])) {
                    $all_vehicles[$key]['checked'] = true;
                }
            }
        }

        $workers = $this->bbdd_model->get_records('workorderworkers', "id_order='$id'", false, 'id_worker');
        if ($all_workers) {
            foreach ($all_workers as $key => $value) {
                if (isset($workers[$key])) {
                    $all_workers[$key]['checked'] = true;
                }
            }
        }

        $this->edit_record(
            'Orden de trabajo',
            'workorders',
            array(
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del cliente con un máximo de 60 caracteres.', 'filter' => true),
                array('name' => 'id_file', 'label' => 'Expediente', 'type' => 'dbselect', 'table' => 'files', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'date', 'label' => 'Fecha', 'type' => 'date', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            $id,
            array(
                'view' => 'obras/orden',
                'return_to' => $ret,
                'related_to' => isset($rel) ? $rel : null,
                'tables' => array(
                    'files' => $this->bbdd_model->get_table('files', true),
                ),
                'extradata' => array(
                    'all_vehicles' => $all_vehicles,
                    'all_workers' => $all_workers,
                    //'vehicles'=>$this->bbdd_model->qry2array("SELECT * FROM vehicles WHERE id_order='$id'"),
                    //'workers'=>$this->bbdd_model->qry2array("SELECT * FROM workers WHERE id_order='$id'"),
                    'notes' => $this->bbdd_model->qry2array("SELECT * FROM workordernotes WHERE id_order='$id' ORDER BY id DESC"),
                ),
            )
        );
    }

    public function notas($id)
    {
        $rec = $this->bbdd_model->get_record('orders', $id);
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
