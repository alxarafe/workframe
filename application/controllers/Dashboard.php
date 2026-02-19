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

// Ya buscaremos la forma de eliminar este requier_once
require_once APPPATH . 'core/MY_Dbcontroller.php';

class Dashboard extends MY_Dbcontroller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_user) {
            redirect('/');
        }
        $this->public_page = false;

        $this->data['title'] = 'Visión general del calendario de obras';
        $this->data['description'] = 'Panel de control';
        $this->data['keywords'] = '';

        $this->load->model(array('bbdd_model', 'obras_model'));

        $this->data['css'][] = 'bower_components/fullcalendar/dist/fullcalendar.min';
        //$this->data['css'][]='bower_components/fullcalendar/dist/fullcalendar.print.min';

        $this->data['js'][] = 'bower_components/moment/moment';
        $this->data['js'][] = 'bower_components/fullcalendar/dist/fullcalendar.min';
        $this->data['js'][] = 'bower_components/fullcalendar/dist/locale/es';
        $this->data['js'][] = 'dashboard';
    }

    protected function getCalendar($_calendar, $_data, $_url)
    {
        $txt = '';
        if ($_data) {
            $txt .= 'var ' . $_calendar . ' = [' . CRLF;
            foreach ($_data as $key => $value) {
                $title = $value['name'];
                $start = explode("-", $value['date']);
                $end = explode("-", $value['end_date']);
                if ($start && count($start) > 2) {
                    $sy = $start[0];
                    $sm = $start[1] - 1;
                    $sd = $start[2];
                }
                if ($end && count($end) > 2) {
                    $ey = $end[0];
                    $em = $end[1] - 1;
                    $ed = $end[2];
                }
                $hour = 0;
                $min = 0;
                $sec = 0;
                if (isset($value['start_hour'])) {
                    $startHour = explode(":", $value['start_hour']);
                    if ($startHour && count($startHour) > 2) {
                        $hour = $startHour[0];
                        $min = $startHour[1];
                        $sec = $startHour[2];
                    }
                }

                $url = base_url($_url . $value['id']);

                $txt .= '{' . CRLF;
                $txt .= "  title: '$title'," . CRLF;
                $txt .= "  start: new Date($sy, $sm, $sd, $hour, $min, $sec)," . CRLF;
                $txt .= "  end: new Date($ey, $em, $ed, 23, 59, 59)," . CRLF;
                $txt .= "  backgroundColor: '#3377ff'," . CRLF;
                $txt .= "  borderColor: '#0000ff'," . CRLF;
                $txt .= "  url: '$url'," . CRLF;
                $txt .= '},' . CRLF;
            }
            $txt .= '];' . CRLF;
        }
        return $txt;
    }

    public function index()
    {
        $date = $_GET['date'] ?? date('Y-m-d', time() - (90 * 24 * 60 * 60));
        $this->data['date'] = $date;

        $page = 'dashboard';
        $data = $this->bbdd_model->qry2array('
			SELECT
				exp.id as id,
			    exp.date as exp_date_order,
			    DATE_FORMAT(exp.date,"%d/%m/%y") as exp_date,
				CONCAT(COALESCE(CONCAT("OT: ",wo.id,". ")),exp.name,"<br />",COALESCE(CONCAT(cl.name,"<br />",wo.name),"")) as description,
				  concat("{",COALESCE(DATE_FORMAT(wo.date,"%w"),""),"}") as dayofweek,
			    wo.date as wo_date_order,
				CONCAT(
					COALESCE(DATE_FORMAT(wo.date,"%d/%m/%y"),""),"<br />",
					COALESCE(DATE_FORMAT(wo.end_date,"%d/%m/%y"),"")
					) as wo_date,
				  COALESCE(DATE_FORMAT(wo.start_hour, "%H:%i"),"") as hour,
				wo.id as workorder,
				CONCAT(cl.contact,"<br />",wo.locality," (",wo.town,")<br />T:",cl.telephone," E:",cl.email) as address,
				wo.date as start_date, wo.end_date as end_date
			FROM files exp 
			LEFT JOIN customers cl ON exp.id_customer=cl.id
			LEFT JOIN workorders wo ON exp.id=wo.id_file'
            . " WHERE wo.end_date>='$date' " .
            'ORDER BY exp_date DESC
		');

        if ($data) {
            foreach ($data as $key => $value) {
                $data[$key]['dayofweek'] = str_replace(
                    ['{}', '{0}', '{1}', '{2}', '{3}', '{4}', '{5}', '{6}'],
                    ['<hr>', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    $value['dayofweek']
                );
                $veh = '';
                $wrk = '';
                if (isset($value['workorder'])) {
                    $f = $this->bbdd_model->qry2array("SELECT b.name FROM workorders a, workers b WHERE a.id_foreman=b.id AND a.id=" . $value['workorder']);

                    if ($f) {
                        $data[$key]['description'] = 'Encargado: <strong>' . $f[0]['name'] . '</strong><br />' .
                            $data[$key]['description'];
                    }

                    $v = $this->bbdd_model->qry2array("SELECT a.id_vehicle as id, b.name FROM workordervehicles a LEFT JOIN vehicles b ON a.id_vehicle=b.id WHERE a.id_order=" . $value['workorder']);
                    $w = $this->bbdd_model->qry2array("SELECT a.id_worker as id, b.name FROM workorderworkers a LEFT JOIN workers b ON a.id_worker=b.id WHERE a.id_order=" . $value['workorder']);

                    if ($v) {
                        foreach ($v as $name) {
                            if ($inci = $this->obras_model->checkvehicle($name['id'], $value['workorder'], $value['start_date'], $value['end_date'])) {
                                $cad = "<span title='$inci' style='color: red;'><span class='glyphicon glyphicon-exclamation-sign'> {$name['name']}</span></span>";
                                $veh .= ($veh == '' ? '' : '<br />') . $cad;
                            } else {
                                $veh .= ($veh == '' ? '' : '<br />') . $name['name'];
                            }
                        }
                    }

                    if ($w) {
                        foreach ($w as $name) {
                            if ($inci = $this->obras_model->checkworker($name['id'], $value['workorder'], $value['start_date'], $value['end_date'])) {
                                $cad = "<span title='$inci' style='color: red;'><span class='glyphicon glyphicon-exclamation-sign'> {$name['name']}</span></span>";
                                $wrk .= ($wrk == '' ? '' : '<br />') . $cad;
                            } else {
                                $wrk .= ($wrk == '' ? '' : '<br />') . $name['name'];
                            }
                        }
                    }
                }
                $data[$key]['vehicles'] = $veh;
                $data[$key]['workers'] = $wrk;

                if (isset($data[$key]['id'])) {
                    $data[$key]['id'] = '<a href="' . base_url('/expedientes/expediente/' . $data[$key]['id']) . '">' . $data[$key]['id'] . '</a>';
                }

                if (isset($data[$key]['description'])) {
                    preg_match('/OT:.+\./', $data[$key]['description'], $res);
                    if ($res[0]) {
                        $cad = trim(substr($res[0], 3), " .");
                    }
                    if ($res) {
                        $data[$key]['description'] = preg_replace(
                            '/OT:.+\./',
                            "OT: <a href='" . base_url("/ordenes/orden/$cad'>") . "$cad</a>.",
                            $data[$key]['description']
                        );
                    }
                }
            }
        }

        //test_array('Data',$data);

        $this->data['fastview'] = $data;

        $txt = '<script>' . CRLF;

        $_wo = $this->bbdd_model->get_table('workorders');
        $txt .= $this->getCalendar('workorders', $_wo, '/ordenes/orden/');

        $_veh = $this->bbdd_model->qry2array(
            'SELECT a.id, a.id_file, a.date, a.end_date, CONCAT(c.name," (",c.license_plate,")") as name, a.start_hour FROM workorders a'
                . ' LEFT JOIN workordervehicles b ON a.id=b.id_order'
                . ' LEFT JOIN vehicles c ON b.id_vehicle=c.id'
        );
        if ($_veh) {
            foreach ($_veh as $index => $value) {
                if ($value['name'] == '') {
                    $_veh[$index]['name'] = '* Sin vehículo asignado *';
                }
            }
        }
        $txt .= $this->getCalendar('vehicles', $_veh, '/ordenes/orden/');

        $_wrk = $this->bbdd_model->qry2array(
            'SELECT a.id, a.id_file, a.date, a.end_date, c.name FROM workorders a'
                . ' LEFT JOIN workorderworkers b ON a.id=b.id_order'
                . ' LEFT JOIN workers c ON b.id_worker=c.id'
        );
        if ($_wrk) {
            foreach ($_wrk as $index => $value) {
                if ($value['name'] == '') {
                    $_wrk[$index]['name'] = '* Sin operarios asignados *';
                }
            }
        }
        $txt .= $this->getCalendar('workers', $_wrk, '/ordenes/orden/');
        $txt .= '</script>' . CRLF;

        $this->data['jsarray'] = $txt;

        $this->load->view('templates/header', $this->data);
        $this->load->view('obras/' . $page, $this->data);
        $this->load->view('templates/footer', $this->data);

        return;
    }
}
