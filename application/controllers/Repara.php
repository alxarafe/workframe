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

class Repara extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->public_page = false;

        $this->load->model('bbdd_model');
    }

    public function index($_crear = false)
    {
        $this->bbdd_model->runqry("UPDATE mails SET html=plain WHERE id=8");
    }
}
