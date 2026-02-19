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
