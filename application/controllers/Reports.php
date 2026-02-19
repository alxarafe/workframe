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

class Reports extends MY_Dbcontroller
{
    public $pdf;

    function __construct()
    {

        parent::__construct();

        if (!$this->is_user) {
            redirect(base_url());
        }

        $this->load->library('Pdf');
        $this->load->model('bbdd_model');

        /* https://tcpdf.org/docs/srcdoc/tcpdf/class-TCPDF/

    __construct(
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
        $unicode = true,
        $encoding = 'UTF-8',
        $diskcache = false,
        $pdfa = false ) */

        $this->pdf = new PdfReport();
    }

    function index()
    {

        redirect(base_url());

        // Lo que viene a continuación me sirve para testear report.
        $files = array(
            array(
                'id' => 1,
                'name' => 'Expediente número 1', // es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es es
                'orders' => array(
                    array(
                        'id' => 1,
                        'name' => 'Orden número 1',
                        'parts' => array(
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 2,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 2',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 9,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 2,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 2',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 1,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 1,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 1',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            )
                        )
                    ),
                    array(
                        'id' => 2,
                        'name' => 'Orden número 2',
                        'parts' => array(
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 10,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 10',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            )
                        )
                    )
                )
            ),
            array(
                'id' => 2,
                'name' => 'Expediente número 2',
                'orders' => array(
                    array(
                        'id' => 5,
                        'name' => 'Orden número 5',
                        'parts' => array(
                            array(
                                'id' => 50,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 50',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            )
                        )
                    ),
                    array(
                        'id' => 10,
                        'name' => 'Orden número 10',
                        'parts' => array(
                            array(
                                'id' => 100,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 100',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 100,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 100',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 100,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 100',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            ),
                            array(
                                'id' => 100,
                                'fecha' => '2017/12/01',
                                'name' => 'Parte número 100',
                                'festivo' => 0,
                                'ruta' => 'Ruta de pruebas',
                                'horario' => 'Su horario',
                                'horas' => 10,
                                'dietas' => 'No'
                            )
                        )
                    )
                )
            )
        );

        $this->report(
            $files,
            array(
                'breaks' => array('orders', 'parts'),
                'totals' => array('horas' => 'T'),
                'orientation' => 'L',
                'names' => array(
                    array('ID', 'Expediente'),
                    array('ID', 'Parte de obra'),
                    array('ID', 'Fecha', 'Parte de trabajo', 'Festivo', 'Ruta', 'Horario', 'Horas', 'Dietas')
                ),
                /*
            'widths'=>array(
                array(10, 60),
                array(10, 60),
                array(10, 30, 50, 30, 30, 30, 30, 30)
            )
            */
            )
        );
    }

    public function operarios($id = null)
    {
        if (!isset($id) || $id == '#') { // Listado de operarios
            $this->report(
                'SELECT 
				a.id,
				a.name,
				b.name as workcenter,
				c.name as category,
				a.active
			FROM workers a
			LEFT JOIN workcenters b ON a.id_workcenter=b.id
			LEFT JOIN categories c  ON a.id_category=c.id
			ORDER BY name',
                array('id', 'Operario', 'Centro de trabajo', 'Categoría', 'Activo')
            );
        } else {                        // Listado del operario seleccionado
            if (!$worker = $this->bbdd_model->get_record('workers', $id)) {
                redirect(base_url('reports/operarios'));
                return;
            }

            $this->data['title'] = 'Informe de (' . $id . ') ' . $worker['name'];

            if (isset($_POST['report'])) {
                $from = "'" . $_POST['from'] . "'";
                $to = "'" . $_POST['to'] . "'";
                /*
            $parts=$this->bbdd_model->qry2array("
                SELECT
                    d.id as file_id, d.name as file,
                    c.id as workorder_id, c.name as workorder,
                    b.id as workpart_id, b.name as workpart, b.special_time,
                    b.date, a.going_start, a.going_end, a.back_start, a.back_end,
                    a.morning_from, a.morning_to, a.afternoon_from, a.afternoon_to, a.allowances
                FROM
                    workparts b,
                    partworkers a
                    LEFT JOIN workorders c ON a.id_part=c.id
                    LEFT JOIN files d ON c.id_file=d.id
                WHERE
                    a.id_part=b.id AND
                    a.id_worker=$id AND
                    b.date BETWEEN $from AND $to
                ORDER BY file_id, workorder_id, date
            ");
            */
                $parts = $this->bbdd_model->qry2array("
SELECT
	d.id as file_id, d.name as file,
	c.id as workorder_id, c.name as workorder,
	b.id as workpart_id, b.name as workpart, b.special_time,
	b.date, a.going_start, a.going_end, a.back_start, a.back_end,
	a.morning_from, a.morning_to, 
	a.afternoon_from, a.afternoon_to, a.allowances
FROM 
	workparts b
	LEFT JOIN partworkers a ON a.id_part=b.id
	LEFT JOIN workorders c ON b.id_order=c.id
	LEFT JOIN files d ON c.id_file=d.id
WHERE 
	b.id=a.id_part AND 
	a.id_worker=$id AND 
	b.date BETWEEN $from AND $to
			");

                //test_array('Partes',$parts,false);

                if ($parts) {
                    $afileid = '';
                    $aorderid = '';
                    foreach ($parts as $value) {
                        if ($afileid != $value['file_id']) {
                            if (isset($rec)) {
                                $files[] = $rec;
                                unset($rec);
                            }

                            $afileid = $value['file_id'];
                            $aorderid = '';

                            $rec['file_id'] = $afileid;
                            $rec['file'] = $value['file'];
                        }

                        if ($aorderid != $value['workorder_id']) {
                            $aorderid = $value['workorder_id'];
                            $rec['orders'][$aorderid]['orderid'] = $value['workorder_id'];
                            $rec['orders'][$aorderid]['order'] = $value['workorder'];
                        }

                        $part_id = $value['workpart_id'];

                        $going_start = $value['going_start'];
                        $going_end = $value['going_end'];
                        $back_start = $value['back_start'];
                        $back_end = $value['back_end'];
                        $morning_from = $value['morning_from'];
                        $morning_to = $value['morning_to'];
                        $afternoon_from = $value['afternoon_from'];
                        $afternoon_to = $value['afternoon_to'];

                        $rec['orders'][$aorderid]['parts'][$part_id]['id'] = $value['workpart_id'];
                        $rec['orders'][$aorderid]['parts'][$part_id]['date'] = ymd2dmy($value['date']);
                        $rec['orders'][$aorderid]['parts'][$part_id]['name'] = $value['workpart'];
                        $rec['orders'][$aorderid]['parts'][$part_id]['special_time'] = $value['special_time'] ? 'Festivo' : 'Laboral';
                        $rec['orders'][$aorderid]['parts'][$part_id]['going'] = "$going_start-$going_end";
                        $rec['orders'][$aorderid]['parts'][$part_id]['back'] = "$back_start-$back_end";
                        $rec['orders'][$aorderid]['parts'][$part_id]['desplazamientos'] = (int)/*60**/(time_dif($going_start, $going_end) + time_dif($back_start, $back_end)) / 60;
                        $rec['orders'][$aorderid]['parts'][$part_id]['morning'] = "$morning_from-$morning_to";
                        $rec['orders'][$aorderid]['parts'][$part_id]['afternoon'] = "$afternoon_from-$afternoon_to";
                        $rec['orders'][$aorderid]['parts'][$part_id]['trabajo'] = (int)(time_dif($morning_from, $morning_to) + time_dif($afternoon_from, $afternoon_to)) / 60;
                        $rec['orders'][$aorderid]['parts'][$part_id]['allowances'] = $value['allowances'];
                    }
                    $files[] = $rec;
                } else {
                    $files = false;
                }

                //test_array('Files',$files);

                $this->report(
                    $files,
                    array(
                        'breaks' => array('orders', 'parts'),
                        'totals' => array(
                            'desplazamientos' => 'T',
                            'trabajo' => 'T'
                        ),
                        'orientation' => 'L',
                        'names' => array(
                            array('ID', 'Expediente'),
                            array('ID', 'Parte de obra'),
                            array('ID', 'Fecha', 'Parte de trabajo', 'Festivo', 'Ida', 'Vuelta', 'Min', 'Mañana', 'Tarde', 'Min', 'Dietas')
                        ),
                        /*
                    'widths'=>array(
                        array(10, 60),
                        array(10, 60),
                        array(10, 30, 50, 30, 30, 30, 30, 30)
                    )
                    */
                        'title' => $this->data['title'],
                        'subtitle' => "Del $from al $to",
                    )
                );

                return;
            }

            $this->load->view('templates/header', $this->data);
            $this->load->view('templates/fromto', $this->data);
            $this->load->view('templates/footer');
        }
    }

    public function clientes($id = null)
    {
        if (!isset($id) || $id == '#') {    // Listado de clientes
            $this->report(
                'SELECT 
				id, 
				CONCAT(name,"\n",contact) as cliente,
				CONCAT(address,"\n",zip," ",locality," (",town,")") as direccion,
				CONCAT("Tef: ",telephone,"\nemail: ",email) as contacto,
				active
			FROM customers ORDER BY cliente',
                array(
                    'names' => array('id', 'Cliente', 'Dirección', 'Contacto', 'Activo'),
                    'title' => 'Listado de clientes',
                    'subtitle' => ''
                )
            );
        } else {                        // Listado del cliente $id
            //var_dump($_POST);
            if (!$customer = $this->bbdd_model->get_record('customers', $id)) {
                redirect(base_url('reports/clientes'));
                return;
            }

            $this->data['title'] = 'Informe de (' . $id . ') ' . $customer['name'];

            if (isset($_POST['report'])) {
                $from = "'" . $_POST['from'] . "'";
                $to = "'" . $_POST['to'] . "'";
                $files = $this->bbdd_model->qry2array("SELECT * FROM files WHERE id_customer=$id ORDER BY date");
                if (isset($files) && $files) {
                    foreach ($files as $key => $value) {
                        $id_file = $value['id'];
                        $files[$key]['workorders'] = $this->bbdd_model->qry2array("SELECT * FROM workorders WHERE id_file=$id_file ORDER BY date");
                        if (isset($files[$key]['workorders']) && $files[$key]['workorders']) {
                            foreach ($files[$key]['workorders'] as $key2 => $value2) {
                                $id_order = $value2['id'];
                                $files[$key]['workorders'][$key2]['workparts'] = $this->bbdd_model->qry2array("SELECT * FROM workparts WHERE id_order=$id_order AND date BETWEEN $from AND $to ORDER BY date");
                            }
                        }
                    }
                    //test_array('Files', $files, false);

                    foreach ($files as $key => $value) {
                        //echo '<tr><td>E</td><td>'.$value['date'].'</td><td></td><td></td><td></td><td>'.$value['id'].'</td><td>'.$value['name'].'</td></tr>';
                        $res[] = array(
                            'doc1' => $value['id'],
                            'doc2' => '',
                            'doc3' => '',
                            'fecha' => $value['date'],
                            'fecha2' => '',
                            'festivo' => '',
                            'name' => $value['name']
                        );
                        if ($value['workorders']) {
                            foreach ($value['workorders'] as $wokey => $wovalue) {
                                //echo '<tr><td>O</td><td>'.$wovalue['date'].'</td><td>'.$wovalue['end_date'].'</td><td></td><td></td><td>'.$wovalue['id'].'</td><td>'.$wovalue['name'].'</td></tr>';
                                $res[] = array(
                                    'doc1' => '',
                                    'doc2' => $wovalue['id'],
                                    'doc3' => '',
                                    'fecha' => $wovalue['date'],
                                    'fecha2' => $wovalue['end_date'],
                                    'festivo' => '',
                                    'name' => $wovalue['name']
                                );
                                if ($wovalue['workparts']) {
                                    foreach ($wovalue['workparts'] as $wpkey => $wpvalue) {
                                        //echo '<tr><td>P</td><td>'.$wpvalue['date'].'</td><td></td><td>'.($wpvalue['special_time']?'Festivo':'').'</td><td></td><td>'.$wpvalue['id'].'</td><td>'.$wpvalue['name'].'</td></tr>';
                                        $res[] = array(
                                            'doc1' => '',
                                            'doc2' => '',
                                            'doc3' => $wpvalue['id'],
                                            'fecha' => $wpvalue['date'],
                                            'fecha2' => '',
                                            'festivo' => ($wpvalue['special_time'] ? 'Festivo' : ''),
                                            'name' => $wpvalue['name']
                                        );
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $res = null;
                }

                //test_array('Res',$res);
                $this->report(
                    $res,
                    array(
                        'names' => array('Exp', 'Orden', 'Parte', 'Fecha', 'Fin', 'Festivo', 'Nombre'),
                        'title' => $this->data['title'],
                        'subtitle' => "Del $from al $to",
                    )
                );
                return;
            }

            $this->load->view('templates/header', $this->data);
            $this->load->view('templates/fromto', $this->data);
            $this->load->view('templates/footer');
        }
    }
}
