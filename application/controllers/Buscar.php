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

class Buscar extends MY_Dbcontroller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_user) {
            redirect(base_url('/'));
        }

        $this->public_page = false;

        $this->lang->load('buscar');

        $this->data['title']        = lang('buscar_title');
        $this->data['description']  = lang('buscar_description');
        $this->data['keywords']     = lang('buscar_keywords');

        //$this->data['js'][]='docsearch';

        $this->load->model("buscar_model");
    }

    public function index()
    {
        $cad = $_GET['cad'];
        foreach ($this->buscar_model->buscar($cad) as $key => $value) {
            $this->data[$key] = $value;
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view("pages/buscar", $this->data);
        $this->load->view('templates/footer', $this->data);
    }
}
