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

class Administracion extends MY_Dbcontroller
{
    /**
     * Gestión de ficheros maestros, controlador 'administracion'
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_admin) {
            redirect('/');
        }
        $this->public_page = false;

        $this->data['title'] = 'Configuración de la aplicación';
        $this->data['description'] = 'Configuración de la aplicación';
        $this->data['keywords'] = '';

        $this->load->model(array('bbdd_model'));
    }

    /**
     * Método por defecto
     */
    public function index()
    {
        $page = 'index';

        $this->load->view('templates/header', $this->data);
        $this->load->view('admin/' . $page, $this->data);
        $this->load->view('templates/footer');
    }

    /**
     * Gestión de usuarios. Si se le pasa el parámetro report invoca al gestor de
     * informes de usuarios.
     *
     * @param type $report
     */
    public function users($report = null)
    {
        if ($report == 'report') {
            $this->report(
                'SELECT a.id,a.username,a.email,b.name FROM users a LEFT JOIN workers b ON a.id_worker=b.id WHERE a.active=true ORDER BY a.username',
                array(
                    'names' => array('id', 'Usuario', 'Correo electrónico', 'Operario'),
                    'title' => 'Listado de usuarios',
                    'subtitle' => ''
                )
            );
        }

        $this->little_edit_table(
            'Gestión de usuarios',
            'users',
            array(
                array('name' => 'oldid', /* 'label'=>'oldid', 'input'=>$this->is_admin, 'readonly'=>true, */ 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'username', 'label' => 'Nombre', 'input' => $this->is_admin, 'unique' => true, 'readonly' => false, 'pattern' => '[a-zA-Z0-9-]{4,15}', 'title' => 'Use letras números y guiones entre 4 y 15 caracteres.'),
                array('name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                array('name' => 'id_worker', 'label' => 'Operario', 'type' => 'dbselect', 'table' => 'workers', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
                array('name' => 'roles', 'type' => 'link', 'label' => 'Roles', 'param' => 'username', 'value' => base_url('/administracion/editroles/#')),
                array('name' => 'edit', 'type' => 'link', 'label' => 'Editar', 'param' => 'username', 'value' => base_url('/auth/edit/#'))
            ),
            array(
                'delete' => false,
                'default' => array('password' => md5('password')),
                'tables' => array(
                    'workers' => $this->bbdd_model->get_table('workers', true),
                ),
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    /**
     * Gestión de roles
     *
     * @param type $report
     */
    public function roles($report = null)
    {
        if ($report == 'report') {
            $this->report(
                'SELECT a.id, a.name FROM roles a WHERE active=1 ORDER BY a.name',
                array(
                    'names' => array('id', 'name'),
                    'title' => 'Listado de roles',
                    'subtitle' => '',
                )
            );
        }

        $this->little_edit_table(
            'Gestión de roles',
            'roles',
            array(
                array('name' => 'oldid', 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,15}', 'title' => 'Escriba cómo identificar el rol con un máximo de 25 caracteres.'),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            array(
                'delete' => true,
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    /**
     * Gestión de roles
     *
     * @param type $report
     */
    public function orderStatus($report = null)
    {
        if ($report == 'report') {
            $this->report(
                'SELECT a.id, a.name FROM orderstatus a WHERE active=1 ORDER BY a.name',
                array(
                    'names' => array('id', 'name'),
                    'title' => 'Listado de estados de órdenes',
                    'subtitle' => '',
                )
            );
        }

        $this->little_edit_table(
            'Estados de órdenes',
            'orderstatus',
            array(
                array('name' => 'oldid', 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,15}', 'title' => 'Escriba cómo identificar el rol con un máximo de 25 caracteres.'),
                array('name' => 'visible', 'type' => 'checkbox', 'label' => 'Mostrar', 'input' => $this->is_admin, 'default' => true),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            array(
                'delete' => true,
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    /**
     * Asigna los roles al usuario $id
     *
     * @param type $id
     */
    function editroles($id = null)
    {
        if ($id == null) {
            redirect(base_url('administracion/users'));
        }

        $user_id = $this->auth_model->get_user_id($id);

        if (!$user_id || isset($_POST['cancel'])) {
            redirect(base_url('administracion/users'));
        }

        if (isset($_POST['save'])) {
            $this->auth_model->runqry("DELETE FROM user_roles WHERE user_id=$user_id");
            foreach ($_POST['name'] as $key => $value) {
                $this->auth_model->save_data(
                    'user_roles',
                    array(
                        'user_id' => $user_id,
                        'role_id' => $key,
                    ),
                    array(
                        'user_id' => $user_id,
                        'role_id' => $key,
                    )
                );
            }
            redirect(base_url('administracion/users'));
        }

        $this->data['roles'] = $this->auth_model->get_roles();
        $this->data['usuario'] = $this->auth_model->get_user_and_roles($id);

        $page = 'userroles';

        $this->load->view('templates/header', $this->data);
        $this->load->view('admin/' . $page, $this->data);
        $this->load->view('templates/footer');
    }

    public function sections($report = null)
    {
        if ($report == 'report') {
            $this->report(
                'SELECT a.id, a.name FROM sections a WHERE active=1 ORDER BY a.name',
                array(
                    'names' => array('id', 'name'),
                    'title' => 'Listado de secciones',
                    'subtitle' => '',
                )
            );
        }

        $this->little_edit_table(
            'Tabla de secciones',
            'sections',
            array(
                array('name' => 'oldid', 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba cómo identificar el rol con un máximo de 60 caracteres.'),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            array(
                'delete' => false,
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    public function categories($report = null)
    {
        if ($report == 'report') {
            $this->report(
                'SELECT a.id, a.name FROM categories a WHERE active=1 ORDER BY a.name',
                array(
                    'names' => array('id', 'name'),
                    'title' => 'Listado de categorias',
                    'subtitle' => '',
                )
            );
        }

        $this->little_edit_table(
            'Tabla de categorías',
            'categories',
            array(
                array('name' => 'oldid', 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba cómo identificar el rol con un máximo de 60 caracteres.'),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            array(
                'delete' => true,
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    public function workcenters($report = null)
    {
        var_dump($_GET);
        if ($report == 'report') {
            $this->report(
                'SELECT a.id, a.name FROM workcenters a WHERE active=1 ORDER BY a.name',
                array(
                    'names' => array('id', 'name'),
                    'title' => 'Listado de delegaciones',
                    'subtitle' => '',
                )
            );
        }

        $this->little_edit_table(
            'Tabla de centros de trabajo',
            'workcenters',
            array(
                array('name' => 'oldid', 'hidden' => true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba cómo identificar el centro de trabajo con un máximo de 60 caracteres.'),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            array(
                'delete' => true,
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    public function workers($id = null, $delete = '')
    {
        if ($delete == 'delete') {
            $this->bbdd_model->delete_record('workers', $id);
            redirect(base_url('administracion/workers'));
        }

        if ($id == 'report') {
            $this->report(
                'SELECT a.id, a.name, b.name as workcenter, c.name as category, a.email as email, a.active FROM workers a LEFT JOIN workcenters b ON a.id_workcenter=b.id LEFT JOIN categories c ON a.id_category=c.id WHERE a.active=true ORDER BY a.name',
                array(
                    'names' => array('id', 'Operario', 'Delegación', 'Categoría', 'Activo'),
                    'title' => 'Listado de empleados',
                    'subtitle' => '',
                )
            );
            return;
        }

        if ($id == null) {
            $this->edit_table(
                'Tabla de operarios',
                'workers',
                array(
                    //array('name'=>'oldid', 'hidden'=>true),
                    array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                    array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del trabajador con un máximo de 60 caracteres.'),
                    array('name' => 'id_workcenter', 'label' => 'Delegación', 'type' => 'dbselect', 'table' => 'workcenters', 'input' => $this->is_admin, 'readonly' => false),
                    array('name' => 'id_category', 'label' => 'Categoría', 'type' => 'dbselect', 'table' => 'categories', 'input' => $this->is_admin, 'readonly' => false),
                    array('name' => 'list', 'type' => 'link', 'label' => 'Informe', 'param' => 'id', 'value' => base_url('/reports/operarios/#'), 'newpage' => true),
                    array('name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                    array('name' => 'temporary_from', 'label' => 'Baja desde', 'type' => 'date', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                    array('name' => 'temporary_to', 'label' => 'Baja hasta', 'type' => 'date', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                    array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
                ),
                array(
                    'delete' => false,
                    'new' => base_url('/' . $this->uri->uri_string . '/0'),
                    'edit' => base_url('/' . $this->uri->uri_string . '/#'),
                    'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
                    'tables' => array(
                        'workcenters' => $this->bbdd_model->get_table('workcenters', true),
                        'categories' => $this->bbdd_model->get_table('categories', true),
                    ),
                    'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
                )
            );
            return;
        }

        $this->edit_record(
            'Tabla de operarios',
            'workers',
            array(
                //array('name'=>'oldid', 'hidden'=>true),
                array('name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin),
                array('name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del trabajador con un máximo de 60 caracteres.'),
                array('name' => 'id_workcenter', 'label' => 'Delegación', 'type' => 'dbselect', 'table' => 'workcenters', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'id_category', 'label' => 'Categoría', 'type' => 'dbselect', 'table' => 'categories', 'input' => $this->is_admin, 'readonly' => false),
                array('name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                array('name' => 'list', 'type' => 'link', 'label' => 'Informe', 'param' => 'id', 'value' => base_url('/reports/operarios/' . $id), 'newpage' => true),
                array('name' => 'temporary_from', 'label' => 'Baja desde', 'type' => 'date', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                array('name' => 'temporary_to', 'label' => 'Baja hasta', 'type' => 'date', 'input' => $this->is_admin, 'unique' => false, 'readonly' => false),
                array('name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true),
            ),
            $id,
            array(
                'return_to' => '/administracion/workers',
                'delete' => false,
                'tables' => array(
                    'workcenters' => $this->bbdd_model->get_table('workcenters', true),
                    'categories' => $this->bbdd_model->get_table('categories', true),
                ),
                'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
            )
        );
    }

    public function vehicles($id = null, $delete = '')
    {
        if ($delete == 'delete') {
            $this->bbdd_model->delete_record('vehicles', $id);
            redirect(base_url('administracion/vehicles'));
        }

        if ($id == 'report') {
            $this->report(
                'SELECT a.id,a.name,b.name as workcenter,a.license_plate FROM vehicles a LEFT JOIN workcenters b ON a.id_workcenter=b.id WHERE a.active=true ORDER BY b.name, a.name',
                array(
                    'names' => array('id', 'nombre', 'delegación', 'matrícula'),
                    'title' => 'Listado de vehículos',
                    'subtitle' => '',
                )
            );
            return;
        }

        if ($id == null) {
            $this->edit_table(
                'Tabla de vehículos',
                'vehicles',
                [
                    //array('name'=>'oldid', 'hidden'=>true),
                    ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin],
                    ['name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del vehiculo con un máximo de 60 caracteres.', 'filter' => true],
                    ['name' => 'id_workcenter', 'label' => 'Delegación', 'type' => 'dbselect', 'table' => 'workcenters', 'input' => $this->is_admin, 'readonly' => false, 'filter' => true],
                    ['name' => 'license_plate', 'label' => 'Matrícula', 'type' => 'text', 'input' => $this->is_admin, 'readonly' => false],
                    ['name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true],
                ],
                [
                    'delete' => true,
                    'new' => base_url('/' . $this->uri->uri_string . '/0'),
                    'edit' => base_url('/' . $this->uri->uri_string . '/#'),
                    'report_url' => base_url('/' . $this->uri->uri_string . '/report'),
                    'tables' => [
                        'workcenters' => $this->bbdd_model->get_table('workcenters', true),
                    ]
                ]
            );
            return;
        }

        $veh = $this->bbdd_model->get_record('vehicles', $id);
        $this->edit_record(
            'Tabla de vehículos',
            'vehicles',
            [
                ['name' => 'id', 'label' => 'ID', 'type' => 'number', 'auto' => true, 'input' => $this->is_admin],
                ['name' => 'name', 'label' => 'Nombre', 'input' => $this->is_admin, 'readonly' => false, 'pattern' => '.{1,60}', 'title' => 'Escriba el nombre del vehiculo con un máximo de 60 caracteres.'],
                ['name' => 'id_workcenter', 'label' => 'Delegación', 'type' => 'dbselect', 'table' => 'workcenters', 'input' => $this->is_admin, 'readonly' => false],
                ['name' => 'license_plate', 'label' => 'Matrícula', 'type' => 'text', 'input' => $this->is_admin, 'readonly' => false],
                ['name' => 'active', 'type' => 'checkbox', 'label' => 'Activo', 'input' => $this->is_admin, 'default' => true],
            ],
            $id,
            [
                'return_to' => '/administracion/vehicles',
                'tables' => [
                    'workcenters' => $this->bbdd_model->qry2array("SELECT * FROM workcenters"),
                ],
            ]
        );
    }
}
