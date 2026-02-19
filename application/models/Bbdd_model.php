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

class Bbdd_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function loadStructure()
    {
        $this->bbddStructure = NULL;
    }

    /**
     * Obtiene los elementos de una tabla.
     * Para poder usar el parámetro fecha, se asume la existencia de los campos
     * 'temporary_from' y 'temporary_to', y la fecha debe de estar como cadena
     * en formato japonés 'yyyy-mm-dd'.
     * 
     * @param string $tablename es la tabla que deseamos recuperar
     * @param boolean $todos si también queremos los inactivos ($active>0)
     * @param string|null $fecha si queremos restringir entre fechas.
     * @return boolean
     */
    public function get_table($tablename, $todos = false, $fecha = null)
    {
        $where = '';
        if (!$todos) {
            $where .= 'active>0 ';
        }
        if (isset($fecha)) {
            if ($where !== '') {
                $where .= 'AND ';
            }
            $where .= "NOT '$fecha' BETWEEN temporary_from AND temporary_to ";
        }
        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }
        $sql = "SELECT *, id as oldid FROM {$tablename} {$where}ORDER BY id";
        //echo "<p>$sql</p>";
        $res = $this->qry2array($sql);
        if ($res) {
            foreach ($res as $value) {
                $ret[$value['id']] = $value;
            }
        } else {
            $ret = false;
        }
        return $ret;
    }

    public function get_filtered_table($tablename, $filter, $todos = false)
    {
        $cad1 = '';
        if (isset($filter) && is_array($filter)) {
            foreach ($filter as $key => $value) {
                $cad1 .= ($cad1 == '' ? '' : ' && ') . $key . ' LIKE "%' . $value . '%"';
            }
        }
        $cad2 = ($todos ? '' : 'active>0');

        if ($cad1 == '' && $cad2 == '') {
            $cad = '';
        } else {
            $cad = ' WHERE ';
            if ($cad2 == '') {
                $cad .= $cad1;
            } else {
                $cad .= $cad2 . ($cad1 == '' ? '' : ' && ' . $cad1);
            }
        }

        $cad = "SELECT *, id as oldid FROM $tablename" . $cad . ' ORDER BY id';
        //echo "<p>$cad</p>";

        $res = $this->qry2array($cad);

        if ($res) {
            foreach ($res as $value) {
                $ret[$value['id']] = $value;
            }
        } else {
            $ret = false;
        }
        return $ret;
    }

    public function get_record($tablename, $id, $index = 'id')
    {
        $res = $this->qry2array("SELECT *, id as oldid FROM $tablename WHERE $index='$id'");
        return $res && count($res) > 0 ? $res[0] : false;
    }

    public function get_records($tablename, $condition, $useoldid = true, $indexfield = 'id')
    {
        $res = $this->qry2array("SELECT *" . ($useoldid ? ", $indexfield as oldid" : "") . " FROM $tablename WHERE $condition ORDER BY $indexfield");
        if ($res) {
            foreach ($res as $value) {
                $ret[$value[$indexfield]] = $value;
            }
        } else {
            $ret = false;
        }
        return $ret;
    }

    public function qry2indexedarray($qry, $indexfield = 'id')
    {
        $res = $this->qry2array($qry);
        if ($res) {
            foreach ($res as $value) {
                $ret[$value[$indexfield]] = $value;
            }
        } else {
            $ret = false;
        }
        return $ret;
    }

    public function check_uses($tablename, $id)
    {
        $count = 0;
        switch ($tablename) {
            case 'customers':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM customernotes WHERE id_customer='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM files WHERE id_customer='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'files':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM filenotes WHERE id_file='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workorders WHERE id_file='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'workorders':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workordernotes WHERE id_order='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workparts WHERE id_order='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id_worker) AS num FROM workorderworkers WHERE id_order='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id_vehicle) AS num FROM workordervehicles WHERE id_order='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'workers':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workorders WHERE id_foreman='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workparts WHERE id_foreman='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id_order) AS num FROM workorderworkers WHERE id_worker='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id_part) AS num FROM partworkers WHERE id_worker='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'roles':
                $_count = $this->qry2array("SELECT COUNT(role_id) AS num FROM user_roles WHERE role_id='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'orderstatus':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workorders WHERE status='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'workcenters':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workers WHERE id_workcenter='$id'");
                $count += $_count[0]['num'];
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM vehicles WHERE id_workcenter='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'categories':
                $_count = $this->qry2array("SELECT COUNT(id) AS num FROM workers WHERE id_category='$id'");
                $count += $_count[0]['num'];
                return $count;
            case 'vehicles':
                $_count = $this->qry2array("SELECT COUNT(id_vehicle) AS num FROM partvehicles WHERE id_vehicle='$id'");
                $count += $_count[0]['num'];
                return $count;
        }
        return false;
    }

    public function delete_record($tablename, $id)
    {
        $uses = $this->check_uses($tablename, $id);
        if ($uses === 0) {
            return $this->runqry("DELETE FROM $tablename WHERE id='$id'");
        }
        return $uses;
    }
}
