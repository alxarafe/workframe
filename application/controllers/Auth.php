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

define('SENDER_NAME', _APPNAME);
define('SENDER_EMAIL', 'noreply@rsanjoseo.com');

class Auth extends MY_Controller
{
    function __construct()
    {
        $this->isauthpage = true;

        parent::__construct();

        $this->public_page = false; // Esta página no debe de indexarse en los buscadores

        $this->data['title']        = lang('auth_title');
        $this->data['description']  = lang('auth_description');
        $this->data['keywords']     = lang('auth_keywords');

        $this->data['css'][] = 'auth';

        $this->load->model('auth_model');
        $this->load->helper('form');
        $this->load->library(array('session', 'form_validation'));

        $this->form_validation->set_message('required', lang('form_required'));
        $this->form_validation->set_message('is_unique', lang('form_is_unique'));
        $this->form_validation->set_message('alpha_numeric', lang('form_alpha_numeric'));
        $this->form_validation->set_message('numeric', lang('form_numeric'));
        $this->form_validation->set_message('min_length', lang('form_min_length'));
        $this->form_validation->set_message('max_length', lang('form_max_length'));
        $this->form_validation->set_message('matches', lang('form_matches'));
        $this->form_validation->set_message('valid_email', lang('form_valid_email'));
        $this->form_validation->set_message('user_check', lang('form_is_unique'));
        $this->form_validation->set_message('mail_check', lang('form_is_unique'));
        $this->form_validation->set_message('password_check', lang('form_bad_password'));
    }

    public function user_check($str)
    {
        if ($str == $this->data['user']['username']) {  // No se ha cambiado el nombre de usuario, todo ok
            return true;
        } else {    // Se ha cambiado el nombre de usuario
            // Buscamos el usuario con el nuevo nombre solicitado
            $user = $this->auth_model->get_user_by_name($this->auth_model->get_user($str));
            // Si no se ha encontrado, el nuevo nombre vale
            return ($user == false);
        }
    }

    public function mail_check($str)
    {
        if ($str == $this->data['user']['email']) { // No se ha cambiado el email, así que vale
            return true;
        } else {    // Se ha cambiado el email, comprobar que no existe ya
            // Buscamos el usuario con el nuevo nombre solicitado
            $user = $this->auth_model->get_user_by_email($str);
            // Si no se ha encontrado, el nuevo nombre vale
            return ($user == false);
        }
    }

    /*
    public function password_check($str)
    {
        echo "<h1>Datos de la contraseña $str vs ".$this->data['user']['password']."</h1>";
        var_dump($this->data['user']);
        echo "<p>".md5($str)."</p>";
        //return (md5($str) == $this->user['password']);
        return (md5($str) == $this->data['user']['password']);
    }
*/
    function index()
    {
        $this->login();
    }
    /*
    function sendmail($email, $subject, $body)
    {
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';

        $this->load->library('email');
        $this->email->initialize($config);

        $this->email->from(SENDER_EMAIL, SENDER_NAME);
        $this->email->to($email);
        if (ENVIRONMENT == 'development')
            $this->email->cc('r_sanjose@hotmail.com');

        $this->email->subject($subject);

        $message = '
<!DOCTYPE html>
<html lang="' . /*$this->lang->lang() .* / '">
<head>
<meta charset="utf-8">
<title>' . $subject . '</title>
</head>
<body>' . $body .'
<img src="' . base_url("img/logomail.png") . '" />
<p>' . lang('auth_email_noreply'). '</p>
</body>
</html>';

        $this->email->message($message);
        $result = $this->email->send();

        if (ENVIRONMENT == 'development')
            echo $this->email->print_debugger();

        return $result;
    }

    function send_activation_mail($email, $username, $tokenauth)
    {
        $url = base_url() . /*$this->lang->lang()  .* / "/auth/conf/$username/$tokenauth";
        $subject = lang('auth_email_user_confirmation');
        $message = "
        <p>" . lang('auth_email_user_confirmation2') . "</p>
        <p><a href='$url'>{unwrap}$url{/unwrap}</a></p>
        <p>" . lang('auth_email_user_confirmation3') . "</p>
        ";

        return $this->sendmail($email, $subject, $message);
    }

    function newuser() {    // Alta de usuario
        $pagina='contact';

        $this->load->view('templates/header', $this->data);
        $this->load->view("auth/".$pagina, $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    function reset($user=Null, $auth=Null) {    // Reinicio de contraseña
        if ($user)  // Se ha pasado un usuario, si ok, pedir contraseña
        {
            $pagina = 'newpassword';
            $this->data['user'] = $user;
            $this->data['auth'] = $auth;

            if ($this->auth_model->check_user_token($user, $auth))
            {
                if ($this->form_validation->run()) {
                    $pass = $this->input->post('password');
                    if ($this->auth_model->set_password($user, $pass))
                    {
                        $this->data['message'] = lang('auth_password_changed');
                        $this->auth_model->log_entry(LOG_PASSWORD_RECOVERED, "Password changed for $user");
                    }
                    else
                    {
                        $this->data['message'] = lang('auth_password_chg_error');
                        $this->auth_model->log_entry(LOG_ERROR_MESSAGE, "Error changing password for $user");
                    }
                    $pagina = "login";
                }
            }
            else
            {
                $this->data['message'] = lang('auth_token_error');
                $pagina = 'userpswlost';
                $this->auth_model->log_entry(LOG_PASSWORD_TOKEN_ERROR, "$user password recovery token error");
            }
        }
        else    // Si no se ha pasado usuario, hay que pedir usuario o mail para reenviar enlace
        {
            $this->form_validation->set_rules('mail_name', lang('form_user_or_email'), 'trim|required');
            $pagina = 'userpswlost';

            if($this->form_validation->run() === true)
            {
                $mail_name = $this->input->post('mail_name');
                $tokenauth = md5(rand());

                if ($username = $this->auth_model->get_user($mail_name)) {
                    $email = $this->auth_model->get_email($username);
                }

                if ($username && $email)
                {
                    if ($this->auth_model->user_new_auth($username, $tokenauth))
                    {

                        $to = $email;
                        $url = base_url() . $this->lang->lang()  . "/auth/reset/$username/$tokenauth";
                        $subject = lang('auth_email_password');
                        $message = "
                        <p>" . lang('auth_email_password1'). "</p>
                        <p><a href='$url'>{unwrap}$url{/unwrap}</a></p>
                        <p>" . lang('auth_email_password2'). "</p>
                        <p>" . lang('auth_email_password3'). "</p>
                        ";

                        $this->sendmail($to, $subject, $message);
                        $this->auth_model->log_entry(LOG_PASSWORD_RECOVERY_REQUEST, "Password recovery request for $username");
                    }
                    else
                    {
                        $this->data['message'] = lang('auth_db_error');
                        $this->auth_model->log_entry(LOG_ERROR_MESSAGE, "Error requesting recovery password for $username");
                    }
                }
                else
                {
                    $this->data['message'] = lang('auth_user_not_found');
                    $this->auth_model->log_entry(LOG_LOGIN_USER_NOT_EXIST, "$mail_name don't exist");
                }
            }
        }
        $this->load->view('templates/header', $this->data);
        $this->load->view("auth/".$pagina, $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    function conf($username=Null, $token=Null)
    {
        $pagina = "login";

        if (isset($username) && $this->auth_model->exist_user($username))
        {
            if ($this->auth_model->is_active($username))
            {
                $this->data['message'] = lang('auth_already_activated');
                $this->auth_model->log_entry(LOG_WARNING_MESSAGE, "$mail_name don\'t exist");
            }
            else
            {
                if ($this->auth_model->activate($username, $token))
                {
                    $this->data['message'] = lang('auth_activated');
                    $this->auth_model->log_entry(LOG_USER_ACTIVATED, "$username has been activated");
                }
                else
                {
                    $this->data['message'] = lang('auth_token_error');
                    $this->auth_model->log_entry(LOG_ACTIVATION_TOKEN_ERROR, "$username don\'t has been activated");
                }
            }
        }
        else
        {
            $this->auth_model->log_entry(LOG_LOGIN_USER_NOT_EXIST, "$username don\'t exist");
            $this->data['message'] = lang('auth_user_not_found');
            $pagina='newuser';
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view("auth/".$pagina, $this->data);
        $this->load->view('templates/footer', $this->data);
    }
*/
    function login()
    {
        $page = 'auth/login';
        $ip_status = $this->auth_model->check_ip($this->ip_address);
        if ($ip_status == LOG_LOGIN_IP_IS_BANNED) {
            $this->data['message'] = 'Acceso no permitido ' . LOG_LOGIN_IP_IS_BANNED;
        } else {
            if ($user_id = $this->session->userdata('user_id')) {
                redirect('auth/edit', 'refresh');
            } else {
                $this->form_validation->set_rules('username', lang('form_username'), 'trim|required|min_length[4]|max_length[15]');

                if ($this->form_validation->run()) {
                    $user = strtolower($this->input->post('username'));
                    $pass = $this->input->post('password');

                    $el_user = $this->auth_model->get_user_id($user);

                    if (!$el_user) {
                        $el_user = 0;
                    }

                    $free = $ip_status || !$this->auth_model->check_banned($el_user, $this->ip_address);

                    //echo "<p>user:$user-password:$pass-usuario:$el_user-".($free?"no baneado":"BANEADO")."</p>";

                    if ($el_user && $free) {    // Existe y no está baneado
                        if ($id_user = $this->auth_model->login($user, $pass)) { // Login ok
                            if ($id_user > 0) { // Usuario identificado
                                $this->auth_model->access_user($id_user, $this->ip_address, 1);
                                $this->auth_model->clear_user($id_user, $this->ip_address);

                                $this->user_id = $id_user;

                                $this->session->set_userdata('user_id', $id_user);
                                if ($this->input->post('remember-me')) {
                                    $this->set_cookie(COOKIE_USER_ID, $id_user, 30 * (24 * 60 * 60));
                                } else {
                                    $this->set_cookie(COOKIE_USER_ID, null, 0); // De existir una cookie, se elimina
                                }

                                $this->auth_model->log_entry(LOG_LOGIN_OK, "User $user logged correctly");

                                redirect('/', 'refresh');
                            } elseif ($id_user < 0) {      // Existe, la contraseña es correcta pero no está activado
                                $page = 'auth/login';
                                $this->data['message'] = lang('auth_user_not_activated');
                                $this->auth_model->log_entry(LOG_LOGIN_USER_NOT_ACTIVATED, "Login fail! User $user not activated");
                                redirect('auth/login', 'refresh');
                            }
                        }
                    }
                    $this->data['message'] = lang('auth_user_not_authenticated') . ($free ? '' : ' (*)');

                    if (!$ip_status) {
                        $this->auth_model->access_user($el_user, $this->ip_address, 0);
                    }
                } else {
                    $page = 'auth/login';
                }
            }

            $this->getUser();
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view($page, $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function edit($_username = null)
    {
        $_username = urldecode($_username);

        $page = "edit";

        if (!$_username && isset($this->user)) {
            $_username = $this->user['username'];
        }

        if ($_username && ($this->user['username'] == $_username) || $this->is_admin) {
            $this->data['user'] = $this->auth_model->get_user_by_name($_username);
            if ($this->data['user']['password'] == '') {
                $this->data['user']['password'] = md5('password');
            }
        } else {
            $page = "denied";
        }

        $this->form_validation->set_rules('username', lang('form_username'), "trim|required|alpha_numeric|min_length[4]|max_length[15]|callback_user_check");
        //$this->form_validation->set_rules('password', lang('form_password'), 'trim|required|callback_password_check');
        $this->form_validation->set_rules('newpassword', lang('form_newpassword'), 'trim|matches[passconf]');
        $this->form_validation->set_rules('passconf', lang('form_password2'), 'trim');
        $this->form_validation->set_rules('email', lang('form_email'), 'trim|required|valid_email|callback_mail_check');

        if ($this->form_validation->run()) {
            $username = strtolower($this->input->post('username'));
            $password = $this->input->post('newpassword');

            $email = $this->input->post('email');

            $this->auth_model->chg_user($_username, $username, $password, $email);

            if ($password == "") {
                $this->auth_model->log_entry(LOG_INFO, "$_username (" . $this->user['email'] . ") as $username ($email)");
            } else {
                $this->auth_model->log_entry(LOG_INFO, "ChgPsw. $_username (" . $this->user['email'] . ") as $username ($email)");
            }

            redirect('');
        }


        $this->load->view('templates/header', $this->data);
        $this->load->view('auth/' . $page, $this->data);
        $this->load->view('templates/footer', $this->data);
    }
    /*
    public function edit($_username=null)
    {
        $_username=urldecode($_username);

        $page="edit";

        if (!$_username && isset($this->user)) $_username=$this->user['username'];

        if ($_username && ($this->user['username'] == $_username) || $this->is_admin)
        {
            $this->data['user']=$this->auth_model->get_user_by_name($_username);
            if ($this->data['user']['password']=='') {
                $this->data['user']['password']=md5('password');
            }
        }
        else
        {
            $page="denied";
        }

        $this->form_validation->set_rules('username', lang('form_username'), "trim|required|alpha_numeric|min_length[4]|max_length[15]|callback_user_check");
        $this->form_validation->set_rules('password', lang('form_password'), 'trim|required|callback_password_check');
        $this->form_validation->set_rules('newpassword', lang('form_newpassword'), 'trim|matches[passconf]');
        $this->form_validation->set_rules('passconf', lang('form_password2'), 'trim');
        $this->form_validation->set_rules('email', lang('form_email'), 'trim|required|valid_email|callback_mail_check');

        if ($this->form_validation->run()) {

            $username = strtolower($this->input->post('username'));
            $password = ($this->input->post('newpassword')=="" ? $this->input->post('password') :  $this->input->post('newpassword'));
            $email = $this->input->post('email');

            $this->auth_model->chg_user($_username, $username, $password, $email);

            if ($password == "")
            {
                $this->auth_model->log_entry(LOG_INFO, "$_username (".$this->user['email'].") as $username ($email)");
            }
            else
            {
                $this->auth_model->log_entry(LOG_INFO, "ChgPsw. $_username (".$this->user['email'].") as $username ($email)");
            }

            redirect('');
        }


        $this->load->view('templates/header', $this->data);
        $this->load->view('auth/'.$page, $this->data);
        $this->load->view('templates/footer', $this->data);
    }

    public function log($_username=null)
    {
        $page="log";

        if (!$_username && isset($this->user)) $_username=$this->user['username'];

        if ($_username && ($this->user['username'] == $_username) || $this->is_admin)
        {
            $this->data['user']=$this->auth_model->get_user_by_name($_username);
            $this->data['log']=$this->auth_model->get_last_log($this->user_id);
        }
        else
        {
            $page="denied";
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view("auth/$page", $this->data);
        $this->load->view('templates/footer', $this->data);
    }
*/
    public function logout()
    {
        if (isset($this->user)) {
            $user = $this->user['username'];
            $this->auth_model->log_entry(LOG_LOGOUT, "Logout: $user disconnected!");
            $this->data['message'] = lang('auth_logout');
        }
        $this->set_cookie(COOKIE_USER_ID, null, 0); // De existir una cookie, se elimina
        $this->session->set_userdata('user_id', null);

        $this->user = null;
        $this->is_admin = false;

        $this->load->view('templates/header', $this->data);
        $this->load->view("auth/login", $this->data);
        $this->load->view('templates/footer', $this->data);
    }
}
