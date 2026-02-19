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

if (!defined('FIRSTDAY')) define('FIRSTDAY', '1970-01-01');
if (!defined('LASTDAY')) define('LASTDAY', '9999-12-31');

if (!defined('LOG_CHG_DATA')) define('LOG_CHG_DATA', 10);

if (!function_exists('to_float')) {
    function to_float($dato)
    {
        return str_replace(",", ".", preg_replace("/[^0-9.,]/", "", $dato));
    }
}

if (!function_exists('ymd2dmy')) {
    function ymd2dmy($date)
    {
        return date("d-m-Y", strtotime($date));
    }
}

if (!function_exists('time_dif')) {
    function time_dif($inicio, $fin)
    {
        return (strtotime($fin) - strtotime($inicio));
    }
}

if (!function_exists('DiasFecha')) {
    function DiasFecha($fecha, $dias)
    {
        $nuevafecha = strtotime($dias . " day", strtotime($fecha));
        $nuevafecha = date('j-m-Y', $nuevafecha); //formatea nueva fecha
        return $nuevafecha; //retorna valor de la fecha
    }
}

if (!function_exists('get_date')) {
    function get_date($_fecha)
    {
        if ($_fecha == LASTDAY) {
            $ret = LASTDAY;
        } else {
            if (strpos($_fecha, "/") == 2) { // Si está en formato dd/mm/aa o dd/mm/aaaa...
                $_fecha = implode("-", array_reverse(explode("/", $_fecha)));
            }
            $ret = date('Y-m-d', strtotime(str_replace("/", "-", date('Y-m-d', strtotime(str_replace("/", "-", $_fecha))))));
        }
        return $ret;
    }
}

if (!function_exists('str2date')) {
    function str2date($fecha)
    {
        return ($fecha == LASTDAY ? '31/12/9999' : date('d/m/Y', strtotime($fecha)));
    }
}

if (!function_exists('str2money')) {
    function str2money($money)
    {
        return number_format($money, 2) . ' €';
    }
}

abstract class MY_Model extends CI_Model
{

    public $bbddStructure;

    abstract protected function loadStructure();

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    function database_exists($database)
    {
        $result = $this->qry2array("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$database'");
        return (count($result) > 0);
    }

    function table_exists($table)
    {
        $database = DATABASE;
        $result = $this->qry2array("
			SELECT *
			FROM information_schema.tables 
			WHERE table_schema = '$database' AND table_name = '$table'");

        return ($result != false);
    }

    function crear_tabla($nombre, $tabla)
    {
        echo "Creando tabla $nombre; ";

        $consulta = "CREATE TABLE " . $nombre . " ( ";
        foreach ($tabla['fields'] as $index => $col) {
            if ($col['Type'] == 'serial')
                $consulta .= '`' . $index . '` INT NOT NULL AUTO_INCREMENT';
            else {
                $consulta .= '`' . $index . '` ' . $col['Type'];

                if (isset($col['Size'])) {
                    $size = str_replace(".", ",", $col['Size']);
                    $consulta .= "(" . $size . ")";
                }

                if (isset($col['Extra']) && ($col['Extra'] == 'auto_increment'))
                    $consulta .= " AUTO_INCREMENT";
                if (isset($col['Key']) && ($col['Key'] == 'PRI'))
                    $consulta .= " PRIMARY KEY";

                if (!isset($col['Null']))
                    $col['Null'] = "NO";

                if ($col['Null'] == 'YES')
                    $consulta .= " NULL";
                else
                    $consulta .= " NOT NULL";

                $defecto = isset($col['Default']) ? $col['Default'] : Null;
                if (!($defecto === null)) {
                    // echo "<pre>$index en $table_name no es null</pre>";
                    if (in_array($col['Type'], array("varchar", "text", "date", "time"))) // Si hay valor por defecto y es un literal
                        $defecto = "'$defecto'";           // Lo encerramos entre comillas
                }
                // else echo "<pre>$index en $table_name SI es null</pre>";

                if (isset($col['Default']))
                    $consulta .= " DEFAULT " . $defecto; /*
                      else if($col['null'] == 'YES')
                      $consulta .= " DEFAULT NULL"; */

                $consulta .= ', ';
            }
        }

        // Agregamos clave primaria
        $tablePKeys = isset($tabla['keys']) ? $tabla['keys'] : Null;
        if ($tablePKeys === Null) {
            $consulta = substr($consulta, 0, -2); // Quitamos la coma y el espacio del final
        } else {
            $consulta .= " INDEX (";
            foreach ($tablePKeys as $index) {
                $consulta .= $index . ", ";
            }
            $consulta = substr($consulta, 0, -2) . ") "; // Quitamos la coma y el espacio del final y cerramos el paréntesis
        }
        $consulta .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;';

        echo "<p>Ejecutando [$consulta]</p>";

        $this->runqry($consulta);

        $values = isset($tabla['values']) ? $tabla['values'] : Null;
        if (isset($values)) {
            echo "<p>Agregando valores por defecto</p>";
            $consulta = "INSERT INTO $nombre ";
            $header = true;
            foreach ($values as $value) {
                $fields = "(";
                $datos = "(";
                foreach ($value as $fname => $fvalue) {
                    $fields .= $fname . ", ";
                    $datos .= $fvalue . ", ";
                }
                $fields = substr($fields, 0, -2) . ") ";
                $datos = substr($datos, 0, -2) . "), ";

                if ($header) {
                    $consulta .= $fields . " VALUES ";
                    $header = false;
                }

                $consulta .= $datos;
            }

            $consulta = substr($consulta, 0, -2);

            echo "<p>Ejecutando [$consulta]</p>";

            $this->runqry($consulta);
        }
    }

    public function getColumns($tableName)
    {
        return $this->qry2array("SHOW COLUMNS FROM $tableName;");
    }

    public function compareColumn($FieldName, $inStructure, $inTable)
    {
        $type = $inStructure['Type'];
        /*
          if (isset($inStructure['Size'])) {
          $size = str_replace(".",",",$inStructure['Size']);
          $type .= "(".$size.")";
          }
         */

        /*
          echo "<p>$FieldName</p>";
          var_dump($inStructure);
         */

        if (!isset($inStructure['Default']))
            $inStructure['Default'] = Null;
        if (!isset($inStructure['Extra']))
            $inStructure['Extra'] = '';
        if (!isset($inStructure['Key']))
            $inStructure['Key'] = '';
        if (!isset($inStructure['Null']))
            $inStructure['Null'] = 'NO';

        $cad = "";
        if ($FieldName != $inTable['Field'])
            $cad .= "<p>Nombre: $FieldName != " . $inTable['Field'] . "</p>";
        if ($type != $inTable['Type'])
            $cad .= "<p>Tipo: $type != " . $inTable['Type'] . "</p>";
        if ($inStructure['Null'] != $inTable['Null'])
            $cad .= "<p>Null: " . $inStructure['Null'] . " != " . $inTable['Null'] . "</p>";
        if ($inStructure['Default'] != $inTable['Default'])
            $cad .= "<p>Default: " . $inStructure['Default'] . " != " . $inTable['Default'] . "</p>";
        if ($inStructure['Extra'] != $inTable['Extra'])
            $cad .= "<p>Extra: " . $inStructure['Extra'] . " != " . $inTable['Extra'] . "</p>";

        $result = ($cad == "");
        if (!$result) {
            echo "<pre><p>Corregir campo <strong>$FieldName</strong></p>$cad</pre>";
        }

        return $result;
    }

    public function changeColumn($tableName, $fieldName, $inStructure, $inTable)
    { // No estoy muy seguro de que haga todo lo que tiene que hacer
        echo "<pre>";
        print_r($inTable);
        echo "</pre>";

        $_null = (isset($inStructure['Null'])) ? $inStructure['Null'] : "NO";
        $_key = (isset($inStructure['Key'])) ? $inStructure['Key'] : "";
        $_def = (isset($inStructure['Default'])) ? $inStructure['Default'] : "";
        $_extra = (isset($inStructure['Extra'])) ? $inStructure['Extra'] : "";

        $result = "ALTER TABLE $tableName MODIFY $fieldName ";

        $result .= (isset($inStructure['Type']) ? $inStructure['Type'] : $inTable['Type']) . " ";
        if (isset($inStructure['Key']) && $inStructure['Key'] != $inTable['Key'])
            $result .= ($_key == 'PRI' ? 'PRIMARY KEY ' : '');
        $result .= ($_null == 'NO' ? "NOT " : "") . "NULL ";
        if ($_def != "") {
            $result .= "DEFAULT " . (($_def == 'CURRENT_TIMESTAMP' || $_def == 'NULL' || $_def == 'Null') ? $_def : "'$_def'") . " ";
        }
        $result .= $_extra;


        /*
          if ((isset($inStructure['Default'])) && (!$this->compare_defaults($inTable['Default'], $inStructure['Default']))) {
          if (is_null($inTable['Default']))
          $result .= 'ALTER TABLE ' . $tableName . ' ALTER `' . $fieldName. '` DROP DEFAULT;';
          else {
          if( strtolower(substr($inStructure['Defecto'], 0, 9)) == "nextval('" ) { // nextval es para postgresql
          if($inTable['extra'] != 'auto_increment') {
          $result .= 'ALTER TABLE ' . $tableName . ' MODIFY `' . $inStructure['Field'] . '` ' . $inTable['Type'];

          if($inTable['Null'] == 'YES')
          $result .= ' NULL AUTO_INCREMENT;';
          else
          $result .= ' NOT NULL AUTO_INCREMENT;';
          }
          }
          else
          $result .= 'ALTER TABLE ' . $tableName . ' ALTER `' . $fieldName . '` SET DEFAULT ' . $inStructure['Default'].";";

          if($inStructure['Null'] != $inTable['Null']) {
          if(strtolower($inStructure['Null']) == 'NO')
          $result .= 'ALTER TABLE ' . $tableName . ' MODIFY `' . $fieldName . '` ' . $inStructure['Type'] . ' NOT NULL;';
          else
          $result .= 'ALTER TABLE ' . $tableName . ' MODIFY `' . $fieldName . '` ' . $inStructure['Type'] . ' NULL;';
          }
          }
          }
         */
        // echo "<pre>[[[$result]]]</pre>";
        return $result;
    }

    public function addColumn($tableName, $fieldName, $inStructure)
    {

        echo "<pre>";
        print_r($inStructure);
        echo "</pre>";

        $consulta = "ALTER TABLE $tableName ADD COLUMN $fieldName ";

        $col = $inStructure;

        $consulta .= $col['Type'];

        if (isset($col['Size'])) {
            $size = str_replace(".", ",", $col['Size']);
            $consulta .= "(" . $size . ")";
        }

        if (isset($col['Extra']) && ($col['Extra'] == 'auto_increment'))
            $consulta .= " AUTO_INCREMENT";
        if (isset($col['Key']) && ($col['Key'] == 'PRI'))
            $consulta .= " PRIMARY KEY";

        if (!isset($col['Null']))
            $col['Null'] = "NO";

        if ($col['Null'] == 'YES')
            $consulta .= " NULL";
        else
            $consulta .= " NOT NULL";

        $defecto = isset($col['Default']) ? $col['Default'] : Null;
        if (!($defecto === null)) {
            // echo "<pre>$index en $table_name no es null</pre>";
            if (in_array($col['Type'], array("varchar", "text"))) // Si hay valor por defecto y es un literal
                $defecto = "'$defecto'";           // Lo encerramos entre comillas
        }
        // else echo "<pre>$index en $table_name SI es null</pre>";

        if (isset($col['Default'])) {
            $defecto = $col['Default'];
            $consulta .= " DEFAULT " . (($defecto == 'CURRENT_TIMESTAMP' || $defecto == 'NULL' || $defecto == 'Null') ? $defecto : "'$defecto'");
        }

        $result = $consulta;


        /*

          $_null	= (isset($inStructure['Null']))?$inStructure['Null']:$res_null=$inTable['Null'];
          $_key	= (isset($inStructure['Key']))?$inStructure['Key']:$res_null=$inTable['Key'];
          $_def	= (isset($inStructure['Default']))?$inStructure['Null']:$res_null=$inTable['Default'];
          $_extra	= (isset($inStructure['Extra']))?$inStructure['Extra']:$res_null=$inTable['Extra'];

          $result = "ALTER TABLE $tableName ADD COLUMN $fieldName ";

          $result.=(isset($inStructure['Type'])?		$inStructure['Type']:		$inTable['Type'])." ";
          if ($inStructure['Key']!=$inTable['Key']) $result.=($_key=='PRI'?'PRIMARY KEY ':'');
          $result.=($_null=='NO'?"NOT ":"")."NULL ";
          $result.=($_def!=""?"DEFAULT $_def ":"");
          $result.=$_extra;
         */

        /*
          $result = 'ALTER TABLE ' . $tableName . ' ADD `' . $fieldName . '` ';

          if($inStructure['Type'] == 'SERIAL')
          $result .= '`' . '` INT NOT NULL AUTO_INCREMENT;'; // El campo de tipo serial, de momento no lo he probado
          else {
          $result .= $inStructure['Type'];

          if (isset($inStructure['Size'])) {
          $result .= '('.$inStructure['Size'].')';
          }

          if($inStructure['Null'] == 'NO')
          $result .= " NOT NULL";
          else
          $result .= " NULL";

          if($inStructure['Default'])
          $result .= " DEFAULT ".$inStructure['Default'].";";
          else if($inStructure['Null'] == 'YES')
          $result .= " DEFAULT NULL;";
          }
         */

        return $result;
    }

    function checkTable($tablename, $table, $crear)
    {
        if ($this->table_exists($tablename)) {
            echo "<p>Procesando <strong>$tablename</strong>:</p>";
            $columnas = $this->getColumns($tablename);
            foreach ($table['fields'] as $index => $col) {
                // echo "<p>$index</p>";
                $encontrada = false;
                foreach ($columnas as $inTable) {
                    if (!$encontrada) {
                        if ($encontrada = ($inTable['Field'] == $index)) { // Encontrada, hay que comprobar si es igual o ha cambiado
                            if (!$this->compareColumn($index, $col, $inTable)) { // No coinciden, hay que cambiar la estructura y cruzar los dedos
                                $consulta = $this->changeColumn($tablename, $index, $col, $inTable);
                                echo ($crear ? "Ejecutando" : "Necesita ejecutar") . " [$consulta]. ";
                                if ($crear) {
                                    $this->runqry($consulta);
                                }
                            }
                        }
                    }
                }

                if (!$encontrada) { // La columna no existe en la tabla y hay que crearla
                    $qry = $this->addColumn($tablename, $index, $col);
                    echo ($crear ? "Ejecutando" : "Necesita ejecutar") . " [$qry]. ";
                    if ($crear) {
                        $this->runqry($qry);
                    }
                }
            }
        } elseif ($crear) {
            $this->crear_tabla($tablename, $table);
        } else {
            die("La tabla $tablename no existe");
        }
    }

    function checkTables($crear)
    {
        $this->loadStructure();

        if (isset($this->bbddStructure))
            foreach ($this->bbddStructure as $key => $table)
                $this->checkTable($key, $table, $crear);
    }

    function runqry($qry)
    {
        return $this->db->query($qry);
    }

    function qry2array($qry)
    {
        $query = $this->db->query($qry);
        $result = $query->result_array();
        return (count($result) > 0 ? $result : false);
    }

    function record_exists($fichero, $buscar, $mas = null)
    {
        $ret = Null;
        $consulta = "";
        foreach ($buscar as $key => $value) {
            $consulta .= ($consulta == "" ? "$key = $value" : " AND $key = $value");
        }

        if (isset($mas))
            $consulta .= $mas;

        if ($consulta != "") {
            if ($result = $this->db->query("SELECT id FROM $fichero WHERE $consulta")) {
                if ($result->num_rows > 0 || $result->result_id->num_rows > 0) {
                    $row = $result->result_array();
                    $ret = $row[0]['id'];
                }
            }
        }
        return $ret;
    }

    function search_in($fichero, $buscar, $create = false)
    {
        $ret = Null;
        $claves = '';
        $valores = '';
        $consulta = '';
        foreach ($buscar as $key => $value) {
            $claves .= ($claves == "" ? $key : ", " . $key);
            $valores .= ($valores == "" ? $value : ", " . $value);
            $consulta .= ($consulta == "" ? "$key = $value" : " AND $key = $value");
        }
        if ($claves != "") {
            if ($result = $this->db->query("SELECT id FROM $fichero WHERE $consulta")) {
                if ($result->num_rows > 0) {
                    $row = $result->result_array();
                    $ret = $row[0]['id'];
                } else if ($create) {
                    $sql = "INSERT INTO $fichero ($claves) VALUES ($valores)";
                    if ($result = $this->db->query($sql)) {
                        $ret = $this->db->insert_id();
                    }
                }
            }
        }
        return $ret;
    }

    function get_it($fichero, $_dato, $create = false, $campo = 'name')
    {
        $ret = Null;
        $_dato = trim($_dato);

        $sql = "SELECT id FROM $fichero WHERE $campo='$_dato'";

        if ($_dato != "") {
            if ($result = $this->qry2array($sql)) {
                if ($result) {
                    $ret = $result[0]['id'];
                } else if ($create) {
                    $sql = "INSERT INTO $fichero ($campo) VALUES ('$_dato')";
                    if ($result = $this->db->query($sql)) {
                        $ret = $this->db->insert_id();
                    }
                }
            }
        }
        return $ret;
    }

    function escape_array($data)
    {
        $ret = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Habría que quitar el 2º parámetro de trim, pero antes
                // asegurarnos de que ningún save_array meta los parámetros
                // entre comillas. Ahora quitaría las comillas del principoi
                // o final de la cadena y eso es un error abc' o 'abc se
                // quedaria como abc: $_value=addslashes(trim($value));
                // También es cierto que debería de usarse mysqli_real_escape_string()
                $_value = addslashes(trim($value, " \'\t\n\r\0\x0B"));
                $ret[$key] = "'$_value'";
            }
        }
        return $ret;
    }

    function save_data($fichero, $_keys, $_fields, $_infoname = null) // _infoname sera el nombre que aparecerá en el log, por ejemplo el nombre del cliente o del dominio
    {
        $_fields = $this->escape_array($_fields);

        $kfields = "";
        $kvalues = "";
        $kquery = "";
        $iname = "";
        foreach ($_keys as $key => $value) {
            $kfields .= ($kfields == "" ? $key : ", " . $key);
            $kvalues .= ($kvalues == "" ? $value : ", " . $value);
            $kquery .= ($kquery == "" ? "$key = $value" : " AND $key = $value");
            $iname .= ($iname == "" ? '#' . $value : ":$value");
        }

        $infoname = (isset($_infoname) ? $_infoname : $iname);

        $ffields = "";
        $fvalues = "";
        $fquery = "";
        foreach ($_fields as $key => $value) {
            $ffields .= ($ffields == "" ? $key : ", " . $key);
            $fvalues .= ($fvalues == "" ? $value : ", " . $value);
            $fquery .= ($fquery == "" ? "$key = $value" : ", $key = $value");
        }

        $ret = Null;
        if ($kfields != "") {
            $sql = "SELECT $kfields FROM $fichero WHERE $kquery";
            $sql = "SELECT * FROM $fichero WHERE $kquery";
            //echo "<p>[[$sql]]</p>";
            //var_dump($this->qry2array($sql));
            if ($result = $this->db->query($sql)) {
                /*
                  var_dump($result);
                  var_dump($result->result_id);
                  echo ($result->result_id->num_rows);  // Antes era $result->num_rows
                 */
                if ($result->num_rows > 0 || $result->result_id->num_rows > 0) {
                    $sql = "UPDATE $fichero SET $fquery WHERE $kquery";
                    //echo "<p>[$sql]</p>";

                    $_checkdata = $result->result_array();
                    $checkdata = $_checkdata[0];
                    foreach ($_fields as $key => $thefield) {
                        $field = strtolower(trim($thefield, ' \'\"'));
                        if ($field != strtolower($checkdata[$key]))
                            $this->auth_model->log_entry(LOG_CHG_DATA, addslashes("$infoname: $fichero.$key, de $checkdata[$key] a $field"));
                    }

                    $result = $this->db->query($sql);
                    if (isset($_keys['id'])) {
                        $ret = $_keys['id'];
                    } else {
                        $sql = "SELECT * FROM $fichero WHERE $kquery";
                        // echo "<p>[$sql]</p>";
                        $ret = false;
                        if (($result = $this->db->query($sql)) && ($result->num_rows > 0)) {
                            $res = $result->result_array();
                            if ((count($res) > 0) && (isset($res[0]['id'])))
                                $ret = $res[0]['id'];
                        }
                    }
                } else {
                    $sql = "INSERT INTO $fichero ($ffields) VALUES ($fvalues)";

                    foreach ($_fields as $key => $thefield) {
                        $field = strtolower(trim($thefield, ' \'\"'));
                        $this->auth_model->log_entry(LOG_CHG_DATA, "$infoname: $fichero.$key, alta $field");
                    }


                    // echo "<p>[$sql]</p>";
                    if ($result = $this->db->query($sql)) {
                        $ret = $this->db->insert_id();
                    }
                }
            }
            // var_dump($result);
        }
        return $ret;
    }

    function delete_data($fichero, $_keys)
    {
        $kquery = "";
        foreach ($_keys as $key => $value) {
            $kquery .= ($kquery == "" ? "$key = $value" : " AND $key = $value");
        }

        $ret = Null;
        if ($kquery != "") {
            $sql = "DELETE FROM $fichero WHERE $kquery";
            $ret = $this->db->query($sql);
        }
        return $ret;
    }

    function next_id($fichero, $key = 'id')
    {
        $result = $this->qry2array("SELECT max($key) as id FROM $fichero");
        return (count($result)) > 0 ? $result[0]['id'] + 1 : 1;
    }
}
