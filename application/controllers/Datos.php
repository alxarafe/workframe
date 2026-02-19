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
