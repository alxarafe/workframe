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

define('DEFAULT_FORMCLASS', 'form-control');
define('DEFAULT_ROWSXPAGE', 10);

class MY_Dbcontroller extends MY_Controller
{
    /**
     * Array con los datos leídos
     *
     * @var array
     */
    protected $DataTable;

    /**
     * Array con la estructura de la tabla
     *
     * @var array
     */
    protected $TableStructure;

    /**
     * Se inicia un formulario de datos genérico
     */
    function __construct()
    {
        parent::__construct();

        $this->data['ctrl'] = $this;

        // $this->load->helper('dbform');
        $this->load->model('bbdd_model');
    }
    /**
     * Métodos que incluyen elementos visuales.
     * Etiquetados como VIEW.
     * Revisar la posibilidad de extraer a una capa visual.
     */

    /**
     * Para depuración del código. Imprime el array de forma comprensible para
     * el humano (print_r).
     *
     * VIEW: Esta función utiliza elementos html.
     *
     * @param type $nombre es un literal que se pone delante del Array
     * @param type $array es el array a imprimir
     * @param type $die es true (defecto) si se desea detener la ejecución.
     */
    function test_array($nombre, $array, $die = true)
    {
        echo "<h3>$nombre</h3>";
        echo '<pre>' . print_r($array, true) . '</pre>';
        if ($die) {
            die('Fin de ejecución. Añada false a test_array para no detener la ejecución');
        }
    }

    /**
     * Retorna el código html para abrir un formulario.
     *
     * VIEW: Esta función utiliza elementos html.
     *
     * @param array $input es un array con los atributos del formulario
     * @return string código html para abrir el formulario
     */
    function open_form($input)
    {
        $enctype = isset($input['enctype']) ? $input['enctype'] : '';
        $message = isset($input['message']) ? $input['message'] : false;

        $cad = '';
        if ($message) {
            $cad .= '<div class="panel panel-danger">' .
                '<div class="panel-heading">Aviso</div>' .
                '<div class="panel-body">' . $message . '</div>' .
                '</div>';
        }

        $cad .= '<form' .
            $this->get_item($input, 'class', 'form-horizontal') .
            $this->get_item($input, 'action', '') .
            $this->get_item($input, 'method', 'post') .
            ' accept-charset="utf-8"' .
            ($enctype == '' ? '' : $this->get_a_item('enctype', $enctype)) .
            $this->get_item($input, 'class') . '>';

        return $cad;
    }

    /**
     * Retorna el código html paraa pintar la clase de un botón de tipo $type.
     * $type puede ser primary (defecto), success, danger, etc.
     *
     * VIEW: Esta función utiliza elementos html.
     *
     * @param string $type
     * @return string
     */
    function my_class_button($type = 'primary')
    {
        return "btn btn-$type btn-lg";
    }

    /**
     * Retorna el código html para cierre de un formulario.
     *
     * VIEW: Esta función utiliza elementos html.
     *
     * @return string
     */
    function close_form()
    {
        return ('</form>');
    }
    /**
     * Componenetes para el formulario de edición
     */

    /**
     * Muestra un sencillo formulario para editar una tabla simple.
     *
     * Se utiliza para editar todos los registros de la tabla en un único
     * formulario.
     *
     * Se invoca sólo desde el controlador Administracion.
     *
     * @param type $title
     * @param type $tablename
     * @param type $structure
     * @param type $config
     */
    function little_edit_table($title, $tablename, $_structure, $config = null)
    {
        // Si es cancelar, directamente...
        if (isset($_POST['cancel'])) { // Redirigimos al index del controlador
            redirect($this->uri->segments[1]);
        }

        $this->data['title'] = $title;

        $this->DataTable = $this->bbdd_model->get_table($tablename, true);
        $res = $this->TableStructure = $this->normalize_structure($_structure);

        if (isset($_POST['save'])) {
            $res = $this->normalize_structure($_structure);
            if ($res['id']['type'] == 'number') {
                $nextid = $this->bbdd_model->next_id($tablename);
                foreach ($_POST['id'] as $key => $value) {
                    if ($value == '') {
                        $_POST['id'][$key] = $nextid;
                        $nextid++;
                    }
                }
            }

            // Se busca si hay algún id duplicado.
            // Hay que hacerlo AUNQUE NO SEA EDITABLE, pues se puede haber añadido un registro
            $cad = $this->find_duplicates($res);
            if ($cad != '') { // Hay error, así que no se guarda, se reconstruye $data con $_POST y se informa
                $this->data['message'] = $cad;
                unset($this->DataTable);
                foreach ($_POST['id'] as $key => $value) {
                    foreach ($_structure as $column) {
                        $_name = $column['name'];
                        $this->DataTable[$key][$_name] = isset($_POST[$_name][$key]) ? $_POST[$_name][$key] : '';
                    }
                }
            } else {  // Se aplican los cambios con lo que ahora hay en $_POST
                /*

                  Soy consciente de que se produce un error en el caso de cruce de códigos.
                  Si cambias los códigos de os líneas ejemplo 1=2 y 2=1, al intentar cambiar
                  el primero tendríamos 2 registros con código 2 y eso no se podría hacer.
                  Habría que comprobar que ese caso no se diese, o sea, que si esto de produciese
                  se cambiasen todos los registros en lugar del código.

                  Aún así, creo que permitir el cambio de códigos lo voy a dejar de
                  momento desactivado hasta que esto funcione mejor, porque hay otro
                  problema más y es la cascada en aquellas tablas que hagan referencia
                  a la línea de esta tabla cuyo código se ha cambiado.

                  Lo dejaré funcionando asumiendo que no puede pasar 2=1 y 1=2, pero lo
                  desactivaré para que no se pueda modificar el código, de momento.

                 */

                $msg = '';
                foreach ($_POST['oldid'] as $key => $oldid) {
                    //echo "<h1>$key:$oldid</h1>";  // INFO

                    if (isset($_POST['delete']) && isset($_POST['delete'][$key]) && $_POST['delete'][$key]) {
                        $borrado = $this->bbdd_model->delete_record($tablename, $this->DataTable[$key]['id']);
                        if ($borrado === true) {
                            $msg .= '<p>Borrado ' . $this->DataTable[$key]['id'] . '-' . $this->DataTable[$key]['name'] . '</p>';
                        } else {
                            $msg .= '<p>Imposible borrar ' . $this->DataTable[$key]['id'] . '-' . $this->DataTable[$key]['name'] . '. Vinculado a ' . $borrado . ' registros</p>';
                        }
                    } else {
                        $change = null;
                        $new_record = !isset($this->DataTable[$key]); // Sería un nuevo registro
                        $oldid = null;

                        $res = $this->normalize_structure($_structure, (isset($config['default']) ? $config['default'] : null));

                        foreach ($res as $value => $avalue) {
                            if (!isset($avalue['type']) || ($avalue['type'] != 'link')) {
                                $field = (isset($_POST[$value]) && isset($_POST[$value][$key])) ? $_POST[$value][$key] : '0';

                                if (isset($avalue['upper']) && $avalue['upper']) {
                                    $field = strtoupper($field);
                                }

                                if ($value == 'oldid') {
                                    $oldid = array('id' => "'" . ($field == '' ? $_POST['id'][$key] : $field) . "'");
                                } else {
                                    if ($new_record || (/* (!isset($avalue['default']) || $avalue['default']=='') && */($this->DataTable[$key][$value] != $field))) {
                                        $change[$value] = "'" . $field . "'";
                                    }
                                }
                            }
                        }

                        if (isset($change)) {
                            $this->bbdd_model->save_data(
                                $tablename,
                                $oldid,
                                $change
                            );
                        }
                    }
                }

                $this->DataTable = $this->bbdd_model->get_table($tablename, true);
            }
        }

        if (isset($msg) && $msg != '') {
            $this->data['message'] = $msg;
        }

        $this->data['name'] = 'países';
        $this->data['structure'] = $_structure;
        $this->data['config'] = $config;
        $this->data['data'] = $this->DataTable;

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/editlittletable', $this->data);
        $this->load->view('templates/footer');
    }

    /**
     * Muestra una tabla.
     * TODO: Este código puede ser bastante optimizable.
     *
     * @param type $data
     * @param type $structure
     * @param type $config
     * @return string
     */
    function show_table_form($data, $structure, $config)
    {
        $n_items = is_array($data) ? count($data) : 0;
        $rowsxpage = isset($config['rowsxpage']) ? $config['rowsxpage'] : DEFAULT_ROWSXPAGE;

        $candelete = (isset($config) && isset($config['delete'])) ? $config['delete'] : false;
        $default = (isset($config) && isset($config['default'])) ? $config['default'] : array();
        $editrecord = (isset($config) && isset($config['edit'])) ? $config['edit'] : '/#';
        $tables = (isset($config) && isset($config['tables'])) ? $config['tables'] : null;
        $filter = (isset($config) && isset($config['filter'])) ? $config['filter'] : false;

        /*
          DataTables no soporta campos de edición en la tabla.
          Así que en las fasttables vamos a utilizar el código anterior,
          dejando DataTables sólo para tablas de visualización.
         */
        $cad = '';
        $fastedit = isset($config['fastedit']) && $config['fastedit'];
        $code = $fastedit ? 'the_table' : substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
        if ($fastedit) {
            $n_pages = ceil($n_items / $rowsxpage);
            if ($n_pages > 1) {
                $cad .= "<nav class='panel pages'>" . CRLF;
                $cad .= "<ul class='pagination'>" . CRLF;
                $cad .= "<li id='$code-first' class='enabled'><a href='javascript:void(0)' onClick='selectPage(\"$code\",1,$n_pages,$rowsxpage);'>Primera</a></li>" . CRLF;
                for ($i = 1; $i <= $n_pages; $i++) {
                    $cad .= "<li id='$code-$i' class='" . ($i != $n_pages ? 'enabled' : 'active') . "'><a href='javascript:void(0)' onClick='selectPage(\"$code\",$i,$n_pages,$rowsxpage);'>$i</a></li>" . CRLF;
                }
                $cad .= "<li id='$code-last' class='disabled'><a href='javascript:void(0)' onClick='selectPage(\"$code\",$n_pages,$n_pages,$rowsxpage);'>Última</a></li>" . CRLF;
                $cad .= "</ul>" . CRLF;
                $cad .= "</nav>" . CRLF;
            }
        } else {
            $cad .= "<script>if (typeof(tables)=='undefined') {var tables=new Array();}; tables['$code']=$rowsxpage;</script>";
        }


        $res = $this->flatten_structure($structure, isset($config['default']) ? $config['default'] : array());

        $cad .= '<table id="' . $code . '" class="table table-hover" align="center"><thead>' . CRLF;

        $cad .= '<tr>' . CRLF;
        foreach ($structure as $key => $value) {
            if (!isset($value['hidden']) || !$value['hidden']) {
                $cad .= '<th>' . $value['label'] . '</th>' . CRLF;
            }
            // Creamos un array con los dataorders para usarlo posteriormente
            if (isset($value['dataorder'])) {
                $dataorder[$value['name']] = $value['dataorder'];
            }
        }

        if ($candelete && $fastedit) {
            $cad .= '<td>Borrar</td>';
        }
        $cad .= '</tr></thead>' . CRLF;

        // Mostramos la tabla
        $cad .= '<tbody>';
        if (!$data) {
            $cad .= '<tr><td colspan="100%">Aún no hay datos</td></tr>';
        } else {
            $item = 0;
            foreach ($data as $key => $value) {
                $item++;

                //echo "<p>npag para $item=$npag. Comparar con $rowsxpage, no con $n_pages.</p>";
                if ($fastedit) {
                    $npag = ceil($item / $rowsxpage);
                    $cad .= '<tr id="fila' . $key . (isset($value['active']) ? '"' . ($npag != $n_pages ? ' style="display: none"' : '') . ' class="' . $code . 'p' . $npag . ' ' . ($value['active'] > 0 ? ($value['active'] == 1 ? 'success' : 'active') : 'danger') : '') . '">' . CRLF;
                } else {
                    $cad .= '<tr id="fila' . $key . '">' . CRLF;
                }

                /*
                  //$cad.='<tr id="fila'.$key.(isset($value['active'])?'"'.($npag!=$n_pages?' style="display: none"':'').' class="'.$code.'p'.$npag.' '.($value['active']>0?($value['active']==1?'success':'active'):'danger'):'').'">'.CRLF;
                  // Eliminamos el paginado...
                  $cad.='<tr id="fila'.$key.'">'.CRLF;
                 */
                foreach ($res as $_name => $column) {
                    //$_name=$column['name'];
                    //test_array($_name,$column,false);

                    $_type = $column['type'];
                    $_hidden = (isset($column['hidden']) && $column['hidden']) ? true : false;

                    $column_param = null;
                    if ($column['type'] == 'link') {
                        if (isset($column['param']) && isset($data[$key][$column['param']])) {
                            $column_param = $data[$key][$column['param']];
                        }
                    } elseif ($column['type'] == 'dbselect') {
                        if (isset($config['tables'][$column['table']])) {
                            $column_param = $config['tables'][$column['table']];
                        }
                    }

                    $cad .= '<td' . ((isset($dataorder[$_name])) ? ' data-order="' . $value[$dataorder[$_name]] . '"' : '') . ($_hidden ? ' hidden' : '') . '>';

                    if (isset($config['fastedit']) && $config['fastedit']) {
                        $cad .= $this->simple_edit_field($key, $_name, $column, isset($value[$_name]) ? $value[$_name] : (isset($data['default']) ? $data['default'] : ''), $column_param);
                    } else {
                        if ($_name == 'name' && $_type != 'link') {
                            $cad .= '<a href="' . str_replace('#', $key, $config['edit']) . '">' . ($value[$_name] == '' ? '*** No se ha puesto nombre ***' : $value[$_name]) . '</a>';
                        } else {
                            switch ($_type) {
                                case 'link':
                                    $_link = (isset($column['link']) && $column['link']) ? $column['link'] : false;
                                    if ($_link) {
                                        $_link = base_url(str_replace('#', $data[$key]['id'], $_link));
                                    } else {
                                        //test_array('column',$column,false);
                                        $_param = (isset($column['param']) && $column['param']) ? $column['param'] : 'name';
                                        $param = (isset($value[$_param]) && $value[$_param]) ? $value[$_param] : '';

                                        $_value = (isset($column['value']) && $column['value']) ? $column['value'] : '#';
                                        $_link = str_replace('#', $param, $_value);
                                    }
                                    //echo "<p>_link=$_link</p>";
                                    //echo "<p>$_param|$_value|$param</p>";
                                    //test_array('Value',$value,false);
                                    //test_array('Columna',$column,false);
                                    $valor = (isset($_name) && isset($value[$_name]) && $value[$_name] != '') ? $value[$_name] : $column['label'];
                                    if (isset($column['newpage']) && $column['newpage']) {
                                        $url = "'$_link'";
                                        //$cad.='<a href="#" onClick="window.open('.$url.');">'.$column['label'].'</a>';
                                        //$cad.='<a href="javascript:window.open('.$url.');">'.$column['value'].'</a>';
                                        $cad .= '<a href="javascript:window.open(' . $url . ');">' . $valor . '</a>';
                                    } else {
                                        //$cad.='<a href="'.$_link.'">'.$column['value'].'</a>';
                                        $cad .= '<a href="' . $_link . '">' . $valor . '</a>';
                                    }

                                    break;
                                case 'dbselect':
                                    //test_array('column',$column,false);
                                    if (isset($tables) && isset($column['table']) && isset($tables[$column['table']]) && isset($tables[$column['table']][$data[$key][$_name]]['name'])) {
                                        $cad .= $tables[$column['table']][$data[$key][$_name]]['name'];
                                    } else {
                                        $cad .= 'Cliente no asignado';
                                    }
                                    break;
                                case 'time':
                                    $cad .= substr($value[$_name], 0, 5);
                                    break;
                                default:
                                    $cad .= $value[$_name];
                            }
                        }
                    }
                    $cad .= '</td>' . CRLF;
                }

                if ($candelete && $fastedit) {
                    $cad .= '<td><input id="delete' . $key . '" type="checkbox" name="delete[' . $key . ']" > Borrar</input>';
                }

                $cad .= '</tr>' . CRLF;
            }
        }
        $cad .= '</tbody>';
        $cad .= '</table>';

        return $cad;
    }

    /**
     * Muestra un formulario de edición de registro.
     *
     * Permite mostrar un formulario de edición en cualquier sitio.
     * Esta versión es la más reciente y posiblemente pueda sustiutir al resto.
     *
     * VIEW: Esta función utiliza elementos html.
     *
     * Invocado desde las siguientes vistas:
     * obras/cliente: $ctrl->new_record_form('customers', $title, $data, $structure, $id, $config);
     * obras/expediente: $ctrl->new_record_form('files', $title, $data, $structure, $id, $config);
     * obras/orden: $ctrl->new_record_form('workorders', $title, $data, $structure, $id, $config);
     * obras/editpart: $ctrl->new_record_form('editparts', $title, $data, $structure, $id, $config, true, false);
     *
     * @param type $title
     * @param type $data
     * @param type $structure
     * @param type $id
     * @param type $config
     * @param type $horizontal
     * @param type $createform
     * @return string
     */
    function new_record_form($tablename, $title, $data, $structure, $id, $config, $horizontal = true, $createform = true)
    {
        $numero_de_usos = $this->bbdd_model->check_uses($tablename, $id);
        $this->data['candelete'] = ($numero_de_usos === 0);
        $this->data['usos'] = $numero_de_usos === false ? 'No permitido' : "Usos: <strong>$numero_de_usos</strong>";

        $cad = '';
        $alta = !isset($id);

        $res = $this->normalize_structure($structure);
        $flat = $this->flatten_structure($structure);

        /** Esto no debe de ir aquí porque esto MUESTRA un formulario
          if (isset($_POST['save'])) {
          if (!isset($res['id']['value']) || $res['id']['value'] == '') {
          if ($res['id']['auto']) {
          $id = $this->bbdd_model->next_id($tablename);
          $res['id']['value'] = $id;
          unset($_POST['SAVE']);
          redirect(base_url($this->uri->uri_string . '/' . $id));
          die;
          } else {
          echo "<p>Cuidado, que id de $tablename no está puesto a automático</p>";
          }
          }
          }
         */
        $cad .= '<div class="box box-info">';
        $cad .= '<div class="box-header with-border">';
        $cad .= '<h3 class="box-title">' . $title . '</h3>';
        $cad .= '</div>';
        if ($createform) {
            $cad .= '<form ' . ($horizontal ? 'class="form-horizontal"' : 'role="form"') . ' method="post">';
        }
        $cad .= '<div class="box-body">';

        if ($alta) {
            // De momento de fuerza a que 'id' esté dentro del primer grupo.
            // Fallará se no se mete en un grupo, o si se mete en un grupo dentro de otros.
            // De momento lo dejamos así, por aligerar...
            $cad .= $this->new_edit_field('horizontal-form', 'id', $flat['id'], '*');
        } else {
            foreach ($res as $key => $field) {
                $column_param = null;
                if ($field['type'] == 'link' || $field['type'] == 'textarea') {
                    if (isset($field['param'])) {
                        $column_param = $field['param'];
                    }
                } elseif ($field['type'] == 'dbselect') {
                    if (isset($config['tables'][$field['table']])) {
                        $column_param = $config['tables'][$field['table']];
                    }
                }

                if (isset($field['type']) && ($field['type'] == 'group')) {
                    die('edit group se usa!!!');
                    $cad .= $this->edit_group('horizontal-form', $field, $data);
                } else {
                    $cad .= $this->new_edit_field('horizontal-form', $key, $field, isset($_POST[$key]) ? $_POST[$key] : (isset($data) && isset($data[$key]) ? $data[$key] : (isset($res[$key]['default']) ? $res[$key]['default'] : '')), $column_param);
                }
            }
        }

        $cad .= '</div>'; // box-body
        $cad .= '<div class="box-footer">';

        $cad .= '<button name="save" class="btn btn-success" type="submit">Guardar</button>';
        $cad .= '<button name="cancel" class="btn btn-danger pull-right" type="submit" formnovalidate>Salir</button>';
        if ($this->data['candelete']) {
            $cad .= '<button name="delete" class="btn btn-danger pull-right" type="submit">Borrar</button>';
        } else {
            $cad .= '<button name="delete" class="btn btn-danger pull-right" type="submit" disabled>Borrar <span class="badge badge-light"> ' . $numero_de_usos . ' </span></button>';
        }
        $cad .= '&nbsp;&nbsp;';

        $cad .= '</div>'; // box-footer
        $cad .= '</div>'; // box
        if ($createform) {
            $cad .= '</form>';
        }

        return $cad;
    }

    // little_table_form lo que hace es generar el código html necesario para mostrar una tabla con los datos recopilados según los parámetros que se le pasen
    // A diferencia de table_form, little_table_form pretende utilizarse con tablas de pocos registros y ser autosuficiente (editar en la misma tabla)
    // También es más sencillo, ya que no requiere buscador ni nada, Todos los datos aparecen en pantalla.

    function little_table_form($data, $structure, $config)
    {
        // Asignar aquí los valores a config que sean necesarios
        $config['fastedit'] = true;

        $cad = $this->show_table_form($data, $structure, $config);

        $newrecord = (isset($config) && isset($config['new'])) ? $config['new'] : '/';
        $nl = '';

        /**
         * Este bloque genera el código para añadir un nuevo registro en blanco cuando
         * se pulsa el botón añadir.
         */
        $res = $this->normalize_structure($structure, isset($config['default']) ? $config['default'] : array());
        foreach ($res as $_name => $column) {
            $_hidden = (isset($column['hidden']) && $column['hidden']) ? true : false;
            $_type = (isset($column['type']) && $column['type']) ? $column['type'] : 'text';
            $_label = (isset($column['label']) && $column['label']) ? $column['label'] : $_name;
            $_default = (isset($column['default']) && $column['default']) ? $column['default'] : '';
            $nl .= '<td' . ($_hidden ? ' hidden' : '') . '>';
            if ($_type == 'dbselect') {
                $tmp = $this->simple_edit_field('#', $_name, $column, $_default, $config['tables'][$column['table']]);
            } else {
                $tmp = $this->simple_edit_field('#', $_name, $column, $_default);
            }
            $tmp = str_replace('\'', '`', $tmp);
            $nl .= $tmp;
            $nl .= '</td>';
        }

        $cad .= '</tr>' . CRLF;
        $cad .= '<span><button class="' . $this->my_class_button('primary') . '" onClick="addLine(';
        $cad .= "'" . $nl . "'";
        $cad .= ');" type="button">Añadir</button></span>' . CRLF;

        if (isset($config['report_url'])) {
            $url = $config['report_url'];
            $url = "'$url'";
            $cad .= '<span><button class="' . $this->my_class_button('primary') . '" onClick="window.open(' . $url . ');" type="button">Listar</button></span>' . CRLF;
        }

        return $cad;
    }

    /// Revisado hasta aquí

    function find_duplicates($res)
    {
        $check = array('id');
        foreach ($res as $key => $value) {
            if (($key != 'id') && isset($value['unique']) && $value['unique']) {
                $check[] = $key;
            }
        }

        $cad = '';
        foreach ($check as $name) {
            $arr = $_POST[$name];
            if (isset($res[$name]['auto']) && $res[$name]['auto']) {
                $arr = array_filter($arr); // Se eliminan los registro vacíos
            }

            $res = array_diff($arr, array_diff(array_unique($arr), array_diff_assoc($arr, array_unique($arr))));
            foreach (array_unique($res) as $v) {
                // Se buscan los duplicados, pero la línea empieza por 0, así que hay que sumar 1
                $duplis = array_keys($res, $v);
                if (is_array($duplis)) {
                    foreach ($duplis as $key => $value) {
                        $duplis[$key] = $value + 1;
                    }
                }
                $cad .= "<p>Duplicado $v en la posición " . implode(', ', $duplis) . '.</p>';
            }
        }

        if ($cad != '') {
            $cad .= '<p>Los cambios no se han guardado</p>';
        }

        return $cad;
    }

    function report($sql, $format = null)
    {
        $this->load->library('Pdf');
        $pdf = new PdfReport();
        if (!isset($sql) || !$sql) {
            echo "No hay datos";
        } else {
            if (is_array($sql)) {
                $data = $sql;
            } else {
                $data = $this->bbdd_model->qry2array($sql);
            }
            //echo "<p>$sql</p>"; test_array('Data',$data);
            $pdf->PrintDocument($data, $format);
            $pdf->Output('filename.pdf', 'I');
        }
    }

    /**
     *
     * @param type $title
     * @param type $tablename
     * @param type $structure
     * @param type $id
     * @param type $config
     * @return type
     */
    function edit_record($title, $tablename, $structure, $id, $config = null)
    {
        $numero_de_usos = ($id == 0) ? false : $this->bbdd_model->check_uses($tablename, $id);
        $this->data['candelete'] = ($numero_de_usos === 0);
        $this->data['usos'] = $numero_de_usos === false ? 'No permitido' : "Usos: <strong>$numero_de_usos</strong>";

        $return_to = base_url(isset($config) && isset($config['return_to']) ? $config['return_to'] : $this->uri->segments[1]);
        $related_to = isset($config['related_to']) ? $config['related_to'] : null;
        $view = (isset($config) && isset($config['view'])) ? $config['view'] : 'templates/editrecord';
        $message = (isset($config) && isset($config['message'])) ? $config['message'] : null;

        $res = $this->flatten_structure($structure);

        if (isset($_POST['delete'])) {
            redirect(base_url($this->uri->uri_string . '/delete'));
        }

        if (isset($_POST['cancel'])) { // Redirigimos al index del controlador
            redirect($return_to);
        }

        if (isset($_POST['save']) && ($id == 0)) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            }
            if ($id == 0) {
                $id = $this->bbdd_model->next_id($tablename);
            }
            $ret = $this->uri->segments[1] . '/' . $this->uri->segments[2] . '/' . $id;
            if (isset($_POST['return_to'])) {
                $ret .= '?return_to=' . $_POST['return_to'];
            }
            redirect(base_url($ret));
        }

        if (isset($_POST['save']) && isset($_POST['name'])) {
            unset($_POST['save']); // Que no intente guardarse save que no es un campo...
            $id = $_POST['id'];
            if ($id == '' && $res['id']['auto']) {
                $id = $this->bbdd_model->next_id($tablename);
                $return_to = base_url($this->uri->segments[1] . '/' . $this->uri->segments[2] . '/' . $id);
            }
            $change = array('id' => "'$id'");

            $registro = $this->bbdd_model->get_record($tablename, $id);
            foreach ($res as $name => $value) {
                if ($name != 'id' && !isset($value['type']) || ($value['type'] != 'link')) {
                    $field = (isset($_POST[$name]) && isset($_POST[$name])) ? $_POST[$name] : '0';
                    if ($value['upper']) {
                        $field = strtoupper($field);
                    }

                    if ($registro[$name] != $field) {
                        // echo "<p>Cambiado '" . $registro[$name] . "' por '$field'</p>";
                        $change[$name] = "'" . $field . "'";
                    }
                }
            }

            if (isset($change)) {
                $this->bbdd_model->save_data(
                    $tablename,
                    array('id' => "'$id'"),
                    $change
                );
            }
            /**
             * Se elimina este redirect, porque el cliente pide que se guarden los
             * cambios cuando se pulse en cualquier botón que redirija a otra pantalla.
             * Creo que lo mejor sería que abriese en otra pestaña, pero esta puede
             * ser una solución temporal. De todas formas el redirect no lo habían pedido.
             * TODO: Revisar en el próximo entorno.
             */
            //redirect($return_to);
        }

        $this->data['id'] = $id;
        $this->data['title'] = $title;

        $this->data['exists'] = false;
        if ($id > 0) {
            $data = $this->bbdd_model->get_record($tablename, $id);
            $this->data['exists'] = $data != false;
            if (isset($related_to)) {
                foreach ($related_to as $key => $value) {
                    if (!isset($data[$key])) {
                        $data[$key] = $value;
                    }
                }
            }
            if (isset($message)) {
                $this->data['message'] = $message;
            }
            $data['id'] = $id;
        } else {
            $data = null;
            $this->data['message'] = (isset($message) ? "<p>$message</p>" : '') .
                '<p>Creación/edición de registro. Deje el código en blanco para crear un nuevo registro.</p>' .
                '<p>Si introduce un código se editará si existe. Si no existe, se creará un nuevo registro con ese código.</p>';
        }

        //$this->data['name']='países';
        $this->data['structure'] = $structure;
        $this->data['config'] = $config;
        $this->data['data'] = $data;
        if (isset($config['extradata'])) {
            foreach ($config['extradata'] as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view($view, $this->data);
        $this->load->view('templates/footer');

        return;
    }

    function edit_table($title, $tablename, $structure, $config = null)
    {
        $this->data['title'] = $title;

        if (isset($_POST['cancel'])) {
            redirect('/');
            redirect($this->uri->segments[1]);
        }

        /** Filters no se usan...
         *
          if (isset($_POST['unfilter'])) {
          unset($_POST['filters']);
          }

          $fl = Null;
          if (isset($_POST['filters'])) {
          foreach ($_POST['filters'] as $key => $value) {
          if (trim($value) != '') {
          $fl[$key] = trim($value);
          }
          }
          }

          if (isset($fl)) {
          $data = $this->bbdd_model->get_filtered_table($tablename, $fl, true);
          } else {
          $data = $this->bbdd_model->get_table($tablename, true);
          }
         */
        $data = $this->bbdd_model->get_table($tablename, true);
        if (isset($msg) && $msg != '') {
            $this->data['message'] = $msg;
        }

        //$this->data['name']='países';
        $this->data['structure'] = $structure;
        $this->data['config'] = $config;
        $this->data['data'] = $data;

        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/edittable', $this->data);
        $this->load->view('templates/footer');
    }
    /**
     * Métodos del Helper.
     *
     * Anteriormente se usaban los que están en helpers mediante:
     * - $this->load->helper('dbform');
     */

    /**
     * Se usa para definir un atributo de un elemento html.
     * Datos un item y un valor, retorna el literal: item='valor'.
     * Si valor es '', retorna lo que se haya puesto por defecto en $default.
     *
     * @param string $item es el nombre del atributo
     * @param string $value es el valor del atributo
     * @param string $default es lo que se pone si no tiene valor (lo normal
     * sería que si no tiene valor, no se ponga el atributo, por eso por defecto
     * es la cadena vacía.
     *
     * @return string
     */
    function get_a_item($item, $value = '', $default = '')
    {
        return $value != '' ? " $item='$value'" : $default;
    }

    /**
     * Extrae el valor de un atributo de un array asociativo.
     * Es similar a get_a_item, pero lo extrae del array $data.
     *
     * @param array $data es un array con los atributos.
     * @param string $item es el nombre del atributo
     * @param string $default es lo que se retorna si no existe el atributo.
     * @return string retorna una cadena al estilo item='valor' o '' si no existe
     */
    function get_item($data, $item, $default = null)
    {
        $res = isset($data[$item]) ? $data[$item] : $default;
        return isset($res) ? " $item='$res'" : '';
    }

    /**
     * Visualiza la tabla en un datatable, permitiendo seleccionar un registro
     * para editar.
     *
     * @param type $data
     * @param type $structure
     * @param array $config
     * @return string
     */
    function table_form($data, $structure, $config)
    {
        //$config['filter'] = true;

        $cad = $this->show_table_form($data, $structure, $config);

        $newrecord = (isset($config) && isset($config['new'])) ? $config['new'] : '/';

        $cad .= '</tr>' . CRLF;
        $cad .= '<span><a href="' . $newrecord . '"><button class="' . $this->my_class_button('primary') . '" type="button">Añadir</button></a></span>' . CRLF;

        if (isset($config['report_url'])) {
            $url = $config['report_url'];
            $url = "'$url'";
            $cad .= '<span><button class="' . $this->my_class_button('primary') . '" onClick="window.open(' . $url . ');" type="button">Listar</button></span>' . CRLF;
        }

        return $cad;
    }

    /**
     * Aplana una estructura de definición de formularios con subgrupos.
     * Hay puntos donde hay que analizar todos los elementos de un formulario,
     * por lo que ésta función retorna todos en un único nivel normalizándolos.
     *
     * La normalización garantiza que todos los elementos contengan todos los
     * atributos necesarios para evitar errores y comprobaciones posteriores
     * en el procesamiento.
     *
     * @param array $structure
     * @param array $default son campos ocultos cuyo valor tiene que ser
     * enviado y retornado por el formulario sin ser modificados. Véase como
     * ejemplo el formulario de usuarios en Administracion, cuyo password se
     * pone como oculto 'default' => array('password' => md5('password'))
     *
     * @return type
     */
    function flatten_structure($structure, $default = array())
    {
        $ret = null;

        foreach ($structure as $input) {
            if (isset($input['type']) && ($input['type'] == 'group')) {
                $res = flatten_group($input['data']);
                foreach ($res as $key => $value) {
                    $ret[$key] = $value;
                }
            } else {
                $ret[$input['name']] = $this->normalize_field($input);
            }
        }

        // Este default sólo se aplica a los registros que no están en edición
        // Para registros en edición, default es un atributo más.
        if (isset($default)) {
            foreach ($default as $key => $value) {
                $ret[$key]['default'] = $value;
                $ret[$key]['hidden'] = true;
                $ret[$key]['type'] = 'text';
            }
        }

        return $ret;
    }

    /**
     * Garantiza que el array de atributos contenga todos los necesarios, de no
     * existir alguno, lo añade con su valor por defecto.
     *
     * @param array $data
     * @return array
     */
    function normalize_field($data)
    {
        $key = $data['name'];

        $ret['class'] = isset($data['class']) ? $data['class'] : DEFAULT_FORMCLASS;
        $ret['type'] = isset($data['type']) ? $data['type'] : (isset($data['link']) ? 'link' : 'text');
        $ret['label'] = isset($data['label']) ? $data['label'] : $key;
        //$ret['placeholder']=isset($data['placeholder'])?$data['placeholder']:'';
        $ret['value'] = isset($data['value']) ? $data['value'] : null;
        $ret['readonly'] = isset($data['readonly']) && $data['readonly'] ? true : false;
        $ret['hidden'] = isset($data['hidden']) && $data['hidden'] ? true : false;
        $ret['unique'] = isset($data['unique']) && $data['unique'] ? true : false;
        $ret['newpage'] = isset($data['newpage']) && $data['newpage'] ? true : false;
        $ret['boolean'] = isset($data['boolean']) && $data['boolean'] ? true : false;
        $ret['filter'] = isset($data['filter']) && $data['filter'] ? true : false;
        $ret['auto'] = isset($data['auto']) && $data['auto'] ? true : false;
        $ret['upper'] = isset($data['upper']) && $data['upper'] ? true : false;
        $ret['required'] = isset($data['required']) && $data['required'] ? true : false;
        $ret['pattern'] = isset($data['pattern']) && $data['pattern'] ? $data['pattern'] : null;
        $ret['title'] = isset($data['title']) && $data['title'] ? $data['title'] : null;
        $ret['format'] = isset($data['format']) && $data['format'] ? $data['format'] : 'horizontal-form';
        $ret['default'] = isset($data['default']) && $data['default'] ? $data['default'] : '';

        if (isset($data['param'])) {
            $ret['param'] = $data['param'];
        }
        if (isset($data['table'])) {
            $ret['table'] = $data['table'];
        }
        if (isset($data['link'])) {
            $ret['link'] = $data['link'];
        }

        if ($ret['boolean']) {
            if (!isset($data['values'])) {
                $ret['values'] = array('1' => 'Sí', '0' => 'No');
            }
        }

        return $ret;
    }

    /**
     *
     * @param array $structure
     * @param array $default
     * @return array
     */
    function normalize_structure($structure, $default = array())
    {
        $ret = null;

        foreach ($structure as $input) {
            $ret[$input['name']] = $this->normalize_field($input);
        }

        // Este default sólo se aplica a los registros que no están en edición
        // Para registros en edición, default es un atributo más.
        if (isset($default)) {
            foreach ($default as $key => $value) {
                $ret[$key]['default'] = $value;
                $ret[$key]['hidden'] = true;
                $ret[$key]['type'] = 'text';
            }
        }

        return $ret;
    }

    /**
     * Retorna el código html para editar un campo.
     * TODO: Es muy posible que se pueda fusionar son simple_edit_field.
     *
     * @param string $format
     * @param type $key
     * @param type $data
     * @param type $value
     * @param type $param
     * @return string
     */
    function edit_field($format, $key, $data, $value = '', $param = '')
    {
        $crlf = ($format == "#" ? '' : CRLF);

        if ($key == 'id') {
            if ($value == '*') {
                $value = '';
            } elseif ($value == '' && !$data['auto']) {
                $data['readonly'] = false;
            } else {
                $data['readonly'] = true;
            }
        }

        if ($data['type'] == 'date' && $value == '') {
            $value = ''; //date("Y-m-d");
        }

        if ($data['type'] == 'datetime' && $value == '') {
            $value = ''; //date("Y-m-d H:i");
        }

        if ($data['type'] == 'time' && $value == '') {
            $value = date("H:i");
        }

        if (is_int($format) || ($format == '#')) { // Si $format es un número, se trata de una lista y el número es la fila
            $name = $key . '[' . $format . ']';
            $key .= $format;
            $format = 'list';
        } else {
            $name = $key;
        }


        $_type = $data['type'];
        $_label = (isset($data['label']) ? $data['label'] : '');
        $_value = ($_type == 'checkbox' ? 1 : $value);

        $items = " name='$name'" .
            $this->get_item($data, 'type') .
            $this->get_item($data, 'pattern') .
            $this->get_item($data, 'placeholder') .
            $this->get_item($data, 'id') .
            $this->get_item($data, 'title') .
            ($_type == 'checkbox' ? '' : $this->get_item($data, 'class')) .
            (" value='$_value'") .
            (isset($data['readonly']) && $data['readonly'] ? ' readonly' : '') .
            (isset($data['required']) && $data['required'] ? ' required' : '');

        $cad = "<div class='form-group'>" . $crlf;

        if ($format == 'list' || $_label == '') {
            $cad .= '<div>' . $crlf;
        } else {
            $cad .= "<div class='col-sm-3'>" . $crlf .
                "<label for='$_label' class='control-label'>$_label:</label>" . $crlf .
                "</div>" . $crlf .
                "<div class='col-sm-9'>" . $crlf;
        }

        switch ($_type) {
            case 'number':
            case 'text':
            case 'date':
            case 'datetime':
            case 'email':
                $cad .= "<input id='$key' $items />" . $crlf;
                break;
            case 'checkbox':
                $cad .= "<input id='$key' $items " . ($value == 0 ? '' : 'checked') . "> $_label</input>" . $crlf;
                break;
            case 'dbselect':
                //test_array('$param',$param);
                if (isset($data['readonly']) && $data['readonly']) {
                    //$cad.="<input id='$key' $items />".$crlf;
                    $cad .= "<input id='$key' name='$key' type='text' value='$value' hidden />";
                    $cad .= "<span class='form-control' readonly>" . $param[0]['name'] . "</span>";
                } else {
                    $cad .= "<select id='$key' class='selectpicker " . (isset($data['class']) && $data['class'] != '' ? $data['class'] : DEFAULT_FORMCLASS) . "' name='$name' data-live-search='true'>";
                    $cad .= "<option value='0'" . ($value == 0 ? ' checked' : '') . ">Seleccione una opción</option>";
                    if (isset($param) && is_array($param)) {
                        foreach ($param as $tf) {
                            $inactive = isset($tf['active']) && !$tf['active'];
                            $cad .= "<option value='" . $tf['id'] . "'" . ($inactive ? " disabled" : "") . ($value == $tf['id'] ? " selected" : "") . ">" . $tf['name'] . "</option>";
                        }
                    }
                    $cad .= "</select>";
                }
                break;
            case 'textarea':
            case 'text':
            case 'blob':
            case 'data':
                $rows = $param == '' ? 4 : $param;
                $cad .= "<textarea id='$key' $items rows='$rows'>" . $value . "</textarea>" . $crlf;
                break;
            case 'link':
                $_value = (isset($data['value']) && $data['value']) ? $data['value'] : '#';
                $_param = str_replace('#', $param, $_value);

                if ($crlf == '') {
                    $cad .= "<span class='btn btn-default' disabled='disabled'>$_label</span>";
                } else {
                    $url = "'$_param'";
                    if (isset($data['newpage']) && $data['newpage']) {
                        $cad .= "<a href=\"javascript:window.open($url);\" class='btn btn-info'>$_label</a>";
                    } else {
                        $cad .= "<a href=$url class='btn btn-info'>$_label</a>";
                    }
                }
                break;
            default:
                $cad .= "<input type='" . $data['type'] . "' name='$key' value='$value' id='$key' placeholder='" . $data['placeholder'] . "' class='form-control'" . (isset($data['readonly']) && $data['readonly'] ? ' readonly' : '') . " />" . $crlf;
        }
        $cad .= '</div>' . $crlf;
        $cad .= '</div>' . $crlf; // form_group
        return $cad;
    }

    function simple_edit_field($format, $key, $data, $value = '', $param = '')
    {
        $crlf = ($format == "#" ? '' : CRLF);

        if ($key == 'id') {
            if ($value == '*') {
                $value = '';
            } elseif ($value == '' && !$data['auto']) {
                $data['readonly'] = false;
            } else {
                $data['readonly'] = true;
            }
        }

        if ($data['type'] == 'date' && $value == '') {
            $value = date("Y-m-d");
        }

        if ($data['type'] == 'datetime' && $value == '') {
            $value = date("Y-m-d H:i");
        }

        if ($data['type'] == 'time' && $value == '') {
            $value = date("H:i");
        }

        if (is_int($format) || ($format == '#')) { // Si $format es un número, se trata de una lista y el número es la fila
            $name = $key . '[' . $format . ']';
            $key .= $format;
            $format = 'list';
        } else {
            $name = $key;
        }

        $_type = $data['type'];
        $_label = (isset($data['label']) ? $data['label'] : '');
        $_value = ($_type == 'checkbox' ? 1 : $value);

        $items = " name='$name'" .
            $this->get_item($data, 'type') .
            $this->get_item($data, 'pattern') .
            $this->get_item($data, 'placeholder') .
            $this->get_item($data, 'id') .
            $this->get_item($data, 'title') .
            ($_type == 'checkbox' ? '' : $this->get_item($data, 'class')) .
            (" value='$_value'") .
            (isset($data['readonly']) && $data['readonly'] ? ' readonly' : '') .
            (isset($data['required']) && $data['required'] ? ' required' : '');

        $cad = '';

        switch ($_type) {
            case 'number':
            case 'text':
            case 'date':
            case 'datetime':
            case 'time':
            case 'email':
                $cad .= "<input id='$key' $items />" . $crlf;
                break;
            case 'checkbox':
                $cad .= "<input id='$key' $items " . ($value == 0 ? '' : 'checked') . "> $_label</input>" . $crlf;
                break;
            case 'dbselect':
                if (isset($data['readonly']) && $data['readonly']) {
                    //$cad.="<input id='$key' $items />".$crlf;
                    $cad .= "<input id='$key' name='$key' type='text' value='$value' hidden />";
                    $cad .= "<span class='form-control' readonly>" . $param[0]['name'] . "</span>";
                } else {
                    $cad .= "<select id='$key' class='selectpicker " . (isset($data['class']) && $data['class'] != '' ? $data['class'] : DEFAULT_FORMCLASS) . "' name='$name' data-live-search='true'>";
                    $cad .= "<option value='0'" . ($value == 0 ? ' checked' : '') . ">Seleccione una opción</option>";
                    if (isset($param) && is_array($param)) {
                        foreach ($param as $tf) {
                            $inactive = isset($tf['active']) && !$tf['active'];
                            $cad .= "<option value='" . $tf['id'] . "'" . ($inactive ? " disabled" : "") . ($value == $tf['id'] ? " selected" : "") . ">" . $tf['name'] . "</option>";
                        }
                    }
                    $cad .= "</select>";
                }
                break;
            case 'textarea':
            case 'text':
            case 'blob':
            case 'data':
                $rows = $param == '' ? 4 : $param;
                $cad .= "<textarea id='$key' $items rows='$rows'>" . $value . "</textarea>" . $crlf;
                break;
            case 'link':
                $_value = (isset($data['value']) && $data['value']) ? $data['value'] : '#';
                $_param = str_replace('#', $param, $_value);

                if ($crlf == '') {
                    $cad .= "<span class='btn btn-default' disabled='disabled'>$_label</span>";
                } else {
                    $url = "'$_param'";
                    if (isset($data['newpage']) && $data['newpage']) {
                        $cad .= "<a href=\"javascript:window.open($url);\" class='btn btn-default'>$_label</a>";
                    } else {
                        $cad .= "<a href=$url class='btn btn-default'>$_label</a>";
                    }
                }
                break;
            default:
                $cad .= "<input type='" . $data['type'] . "' name='$key' value='$value' id='$key' placeholder='" . $data['placeholder'] . "' class='form-control'" . (isset($data['readonly']) && $data['readonly'] ? ' readonly' : '') . " />" . $crlf;
        }
        return $cad;
    }

    /**
     * Esta es la versión más reciente y sencilla de edit_field.
     * Es muy posible que con esta sobre para todos y pueda sustituir en un
     * futuro a simple_edit_field y a edit_field.
     *
     * @param type $format
     * @param type $key
     * @param type $data
     * @param type $value
     * @param type $param
     * @return string
     */
    function new_edit_field($format, $key, $data, $value = '', $param = '')
    {
        $cad = '';
        $label = (isset($data['label']) ? $data['label'] : '');

        $cad .= '<div class="form-group">' . CRLF;
        $cad .= '<label for="' . $key . '" class="col-sm-3 control-label">' . $label . '</label>' . CRLF;
        $cad .= '<div class="col-sm-9">' . CRLF;
        $cad .= $this->simple_edit_field($format, $key, $data, $value, $param) . CRLF;
        $cad .= '</div>' . CRLF; // col-sm-(edit field)
        $cad .= '</div>' . CRLF; // form-group

        return $cad;
    }

    /**
     * table_form lo que hace es generar el código html necesario para mostrar
     * una tabla con los datos recopilados según los parámetros que se le pasen
     *
     * Si id es null, permite introducir un código o dejarlo en blanco.
     * Si se deja en blanco se toma el siguiente libre, si no se toma el introducido.
     * Una vez metido el código se bloquea y se permite editar el resto de campos.
     *
     * @param type $data
     * @param type $structure
     * @param type $id
     * @param type $config
     * @return type
     */
    function record_form($data, $structure, $id, $config)
    {
        $cad = '';
        $alta = ($id == 0);

        $res = $this->normalize_structure($structure);
        $flat = $this->flatten_structure($structure);

        if ($alta) {
            // De momento de fuerza a que 'id' esté dentro del primer grupo.
            // Fallará se no se mete en un grupo, o si se mete en un grupo dentro de otros.
            // De momento lo dejamos así, por aligerar...
            $cad .= $this->edit_field('horizontal-form', 'id', $flat['id'], '*');
        } else {
            foreach ($res as $key => $field) {
                $column_param = null;
                if ($field['type'] == 'link' || $field['type'] == 'textarea') {
                    if (isset($field['param'])) {
                        $column_param = $field['param'];
                    }
                } elseif ($field['type'] == 'dbselect') {
                    if (isset($config['tables'][$field['table']])) {
                        $column_param = $config['tables'][$field['table']];
                    }
                }

                if (isset($field['type']) && ($field['type'] == 'group')) {
                    $cad .= $this->edit_group('horizontal-form', $field, $data);
                } else {
                    $cad .= $this->edit_field('horizontal-form', $key, $field, isset($_POST[$key]) ? $_POST[$key] : (isset($data) && isset($data[$key]) ? $data[$key] : (isset($res[$key]['default']) ? $res[$key]['default'] : '')), $column_param);
                }
            }
        }

        //$cad.='<div> <!- container ->'.CRLF;

        return $cad;
    }
}
