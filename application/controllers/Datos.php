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

class Datos extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->public_page = false;

        $this->load->model('bbdd_model');
    }

    public function index($_crear = false)
    {
        $crear = isset($_crear) && ($_crear == "actualizar");

        echo "<p>Modificación de la base de datos: <strong>" . ($crear ? "Sí" : "No") . "</strong>.</p>";
        if ($this->is_admin && !$_crear) {
            echo '<p><em>Para actualizar la base de datos, use: <strong>' . site_url('/datos/index/actualizar') . '</strong></em></p>';
        }

        $database = DATABASE;

        if ($this->bbdd_model->database_exists($database)) {
            $this->load->model('auth_model');
            $this->auth_model->checkTables($crear);
            $this->load->model('bbdd_model');
            $this->bbdd_model->checkTables($crear);
            $this->load->model('obras_model');
            $this->obras_model->checkTables($crear);
            $this->load->model('mail_model');
            $this->mail_model->checkTables($crear);
            die("Proceso finalizado");
        } else {
            die("No existe la base de datos $database");
        }
    }
}
