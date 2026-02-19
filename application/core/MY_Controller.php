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

define('DEFAULT_JS', 'common');
define('DEFAULT_CSS', 'common');
define('COOKIE_NAME', _APPNAME);
define('COOKIE_USER_ID', 'USER_ID');

include('includes/miniutils.php');

define("LOG_INFO_MESSAGE", 0);
define("LOG_WARNING_MESSAGE", 1);
define("LOG_ERROR_MESSAGE", 2);
define("LOG_FATAL_MESSAGE", 3);
define("LOG_LOGIN_OK", 200);
define("LOG_LOGOUT", 201);
define("LOG_LOGIN_FAILED", 202);
define("LOG_LOGIN_USER_NOT_ACTIVATED", 203);
define("LOG_LOGIN_USER_NOT_EXIST", 204);
define("LOG_LOGIN_USER_IS_BLOCKED", 205);
define("LOG_LOGIN_USER_IS_BANNED", 206);
define("LOG_LOGIN_IP_IS_BANNED", 207);
define("LOG_USER_NOT_EXIST", 210);
define("LOG_LOGIN_ACTIVATION_MAIL_SEND", 211);
define("LOG_USER_ACTIVATED", 212);
define("LOG_ACTIVATION_TOKEN_ERROR", 213);
define("LOG_PASSWORD_RECOVERY_REQUEST", 214);
define("LOG_PASSWORD_RECOVERED", 215);
define("LOG_PASSWORD_TOKEN_ERROR", 216);

/**
 * Complementa al controlador estándar de CodeIgniter
 */
class MY_Controller extends CI_Controller
{
    public $ip_address, $user_id, $user, $social, $is_admin, $is_user, $public_page, /* $readonly, */ $isauthpage = false;

    /**
     * Retorna las cookies guardadas de la aplicación.
     * En realidad se guarda una única cookie que contiene un array json encriptado.
     *
     * @return array con las cookies guardadas
     */
    function get_all_cookies()
    {
        $cookies = $this->input->cookie(COOKIE_NAME, true);
        return (isset($cookies) ? json_decode($this->encryption->decrypt($cookies), true) : array());
    }

    /**
     * Guarda una nueva cookie.
     * En realidad lo que hace es leer la cookie de la aplicación que  está guardada
     * en un array json, establece el nuevo valor y vuelve a guardar el json encriptado.
     *
     * @param string $name es el nombre de la cookie
     * @param mixed $value es el valor de la cookie
     */
    function set_cookie($name, $value)
    {
        $cookies = $this->get_all_cookies();
        if ($value == null) {
            unset($cookies[$name]);
        } else {
            $cookies[$name] = $value;
        }
        $this->input->set_cookie(COOKIE_NAME, $this->encryption->encrypt(json_encode($cookies)), 30 * (24 * 60 * 60));
    }

    /**
     * Usa get_all_cookies para extraer una única cookie.
     *
     * @param string $name es el nombre de la cookie a extraer
     * @return mixed el valor de la cookie
     */
    function get_cookie($name)
    {
        $cookies = $this->get_all_cookies();
        return (isset($cookies) && isset($cookies[$name]) ? $cookies[$name] : false);
    }

    /**
     * Establece valores para los atributos user_id, user, is_admin e is_user si
     * el usuario ha sido correctamente identificado.
     * El código de usuario queda almacenado en session y en la cookie user_id de
     * la aplicación (véanse métodos set_cookie y get_cookie)
     */
    function getUser()
    {
        // Si no existe al tabla de usuarios es que es la primera vez que se ejecuta, así que hay que generar las tablas
        if (!$this->auth_model->table_exists('users')) {
            $this->auth_model->checkTables(true);
            redirect('/');
        }

        $this->user_id = $this->session->userdata('user_id');
        if (!$this->user_id) {
            $this->user_id = $this->get_cookie(COOKIE_USER_ID);
            if ($this->user_id) {
                $this->session->set_userdata('user_id', $this->user_id);
            }
        }
        if (!$this->user_id) {
            $this->user_id = 0;
        }

        $this->user = $this->auth_model->get_user_by_id($this->user_id);

        $this->is_admin = isset($this->user['username']) && $this->auth_model->check_if_user_is($this->user['username'], USER_IS_ADMINISTRATOR);
        $this->is_user = $this->is_admin || (isset($this->user['username']) && $this->auth_model->check_if_user_is($this->user['username'], USER_IS_USER));
    }

    function __construct()
    {
        parent::__construct();

        $this->lang->load('auth');
        $this->lang->load('header');

        $this->ip_address = $this->input->ip_address();

        $this->load->model("auth_model");

        $this->getUser();
        $this->load->helper('security');

        $this->data['title'] = '';
        $this->data['description'] = '';

        $this->data['image'] = '';

        $this->data['css'][] = "bootstrap-select";
        $this->data['css'][] = "bower_components/bootstrap/dist/css/bootstrap.min";
        $this->data['css'][] = "bower_components/font-awesome/css/font-awesome.min";
        $this->data['css'][] = "bower_components/Ionicons/css/ionicons.min";
        $this->data['css'][] = "dist/css/AdminLTE.min";
        $this->data['css'][] = "dist/css/skins/skin-blue.min";
        $this->data['css'][] = DEFAULT_CSS;

        $this->data['js'][] = "bootstrap-select";
        $this->data['js'][] = "bower_components/datatables.net/js/jquery.dataTables.min";
        $this->data['js'][] = "bower_components/datatables.net-bs/js/dataTables.bootstrap.min";
        $this->data['js'][] = "bower_components/jquery-slimscroll/jquery.slimscroll.min";
        $this->data['js'][] = "bower_components/fastclick/lib/fastclick";
        $this->data['js'][] = "dist/js/demo";
        $this->data['js'][] = DEFAULT_JS;

        $this->public_page = true;

        //if (!$this->user_id && !$this->isauthpage) redirect("/auth");
    }
}

/**
 * DEPRECATED: Se dejó de usar cuando se incluyó DataTables para mostrar los resultados.
 * Muestra un array paginado.
 */
class MY_Page_Controller extends MY_Controller
{
    public $items, $offset, $page, $total_pages;

    function __construct()
    {
        parent::__construct();

        $this->data['js'][] = 'responsive-paginate';

        $this->items = (isset($this->items) ? $this->items : 12);
        $this->page = (isset($_GET['page']) ? $_GET['page'] : 1);
    }

    public function view()
    {
        if (!isset($this->data['query'])) {
            die("Debe de asignarse a query el resultado de la consulta antes de llamar al view de MY_Page_Controller.");
        }

        $this->offset = ($this->page - 1) * $this->items;
        $this->total_pages = ceil(count($this->data['query']) / $this->items);

        return array_slice($this->data['query'], $this->offset, $this->items, true);
    }
}
