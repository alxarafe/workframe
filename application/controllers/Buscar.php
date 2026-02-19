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
