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

class Index extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->is_user) {
            redirect('/dashboard');
        }
        redirect('/auth');
        die;

        $this->data['title'] = 'Identificación de usuarios';
        $this->data['description'] = "this->description";
        $this->load->helper('form');
    }

    public function index()
    {
        $page = 'main';

        $this->load->view('templates/header', $this->data);
        $this->load->view('pages/' . $page, $this->data);
        $this->load->view('templates/footer', $this->data);
    }
}
