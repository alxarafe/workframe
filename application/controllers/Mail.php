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
require_once APPPATH . 'libraries/sendmail.php';

class Mail extends MY_Dbcontroller
{
    public $mbox;

    // inbox storage and inbox message count
    private $inbox;
    private $msg_cnt;

    // works variables
    private $charset;
    private $htmlmsg;
    private $plainmsg;
    private $attachments;

    // connect to the server and get the inbox emails
    function __construct()
    {
        parent::__construct();

        $this->data['title'] = 'Gestión del correo electrónico';

        $this->load->model('mail_model');
    }

    function getmsg($mid)
    {
        $this->htmlmsg = $this->plainmsg = $this->charset = '';
        $this->attachments = array();

        $header = imap_headerinfo($this->mbox, $mid);
        $subject = (isset($header->subject)) ? imap_utf8($header->subject) : 'Sin asunto';
        $date = (isset($header->udate)) ? date('Y-m-d H:i:s', $header->udate) : 'Sin fecha';
        $from = $header->from[0]->mailbox . '@' . $header->from[0]->host;

        $structure = imap_fetchstructure($this->mbox, $mid);
        if (!$structure->parts) {
            $this->getpart($mid, $structure);
        } else {
            foreach ($structure->parts as $key => $part) {
                $this->getpart($mid, $part, $key + 1);
            }
        }

        return (array(
            'subject' => $subject,
            'date' => $date,
            'from' => $from,
            'charset' => $this->charset,
            'html' => htmlentities(trim($this->htmlmsg)),
            'plain' => trim($this->plainmsg),
            //'attach' => $this->attachments
        ));
    }

    function getpart($mid, $p, $partno = false)
    {
        // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
        //global $htmlmsg,$plainmsg,$charset,$attachments;

        // DECODE DATA
        $data = ($partno) ?
            imap_fetchbody($this->mbox, $mid, $partno) :  // multipart
            imap_body($this->mbox, $mid);  // simple
        // Any part may be encoded, even plain text messages, so check everything.
        if ($p->encoding == 4) {
            $data = quoted_printable_decode($data);
        } elseif ($p->encoding == 3) {
            $data = base64_decode($data);
        }

        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        $params = array();
        if (isset($p->parameters)) {
            foreach ($p->parameters as $x) {
                $params[strtolower($x->attribute)] = $x->value;
            }
        }
        if (isset($p->dparameters)) {
            foreach ($p->dparameters as $x) {
                $params[strtolower($x->attribute)] = $x->value;
            }
        }

        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if (isset($params['filename']) || isset($params['name'])) {
            // filename may be given as 'Filename' or 'Name' or both
            $filename = isset($params['filename']) ? $params['filename'] : $params['name'];
            // filename may be encoded, so see imap_mime_header_decode()
            $this->attachments[$filename] = $data;  // this is a problem if two files have same name
        }

        // TEXT
        if ($p->type == 0 && $data) {
            // Messages may be split in different parts because of inline attachments,
            // so append parts together with blank row.
            if (strtolower($p->subtype) == 'plain') {
                $this->plainmsg .= trim($data) . "\n\n";
            } else {
                $this->htmlmsg .= $data . "<br><br>";
            }
            $this->charset = $params['charset'];  // assume all parts are same charset
        }

        // EMBEDDED MESSAGE
        // Many bounce notifications embed the original message as type 2,
        // but AOL uses type 1 (multipart), which is not handled here.
        // There are no PHP functions to parse embedded messages,
        // so this just appends the raw source to the main message.
        elseif ($p->type == 2 && $data) {
            $this->plainmsg .= $data . "\n\n";
        }

        // SUBPART RECURSION
        if (isset($p->parts)) {
            foreach ($p->parts as $partno0 => $p2) {
                $this->getpart($mid, $p2, $partno . '.' . ($partno0 + 1));  // 1.2, 1.2.1, etc.
            }
        }
    }

    // read the inbox
    function inbox()
    {
        $this->mbox = $this->connect();
        $this->msg_cnt = imap_num_msg($this->mbox);

        $in = array();
        for ($i = 1; $i <= $this->msg_cnt; $i++) {
            $in[] = array_merge(
                array('index' => $i),
                $this->getmsg($i)
            );
        }

        return $in;
    }

    // close the server connection
    function close()
    {
        $this->inbox = array();
        $this->msg_cnt = 0;

        imap_close($this->mbox);
    }

    // open the server connection
    // the imap_open function parameters will need to be changed for the particular server
    // these are laid out to connect to a Dreamhost IMAP server
    function connect()
    {
        if (file_exists(EMAILCONFIG)) {
            $file = parse_ini_file('email.ini');
        } else {
            redirect('mail/config');
        }
        $res = imap_open('{' . $file['server'] . ':' . $file['port'] . '}', $file['email'], $file['password']);
        return $res;
    }
    /*
// move the message to a new folder
function move($msg_index, $folder='INBOX.Processed') {
    // move on server
    imap_mail_move($this->conn, $msg_index, $folder);
    imap_expunge($this->conn);

    // re-read the inbox
    $this->inbox();
}

// get a specific message (1 = first email, 2 = second email, etc.)
function get($msg_index=NULL) {
    if (count($this->inbox) <= 0) {
        return array();
    }
    elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) {
        return $this->inbox[$msg_index];
    }

    return $this->inbox[0];
}
*/

    function index()
    {
        //test_array('Post',$_POST,isset($_POST['save']));

        if (isset($_POST['save'])) {
            foreach ($_POST['id_file'] as $key => $value) {
                if ($value > 0) {
                    $this->bbdd_model->save_data(
                        'mails',
                        array('id' => $key),
                        array(
                            'id' => $key,
                            'id_file' => $value
                        )
                    );
                }
            }
            redirect('/mail');
        }

        if (isset($_POST['cancel'])) {
            redirect('/');
        }

        $this->inbox = $this->inbox();

        if ($this->msg_cnt == 1) {
            $this->data['message'] = $this->msg_cnt . ' mensaje nuevo';
        } else {
            $this->data['message'] = $this->msg_cnt . ' mensajes nuevos';
        }


        //test_array('inbox:', $this->inbox, false);

        foreach ($this->inbox as $key => $mail) {
            $this->mail_model->save_mail($mail);
            imap_delete($this->mbox, $mail['index']);
        }
        imap_expunge($this->mbox);  // Elimina definitvamente los mails borrados

        $this->close();
        if ($errors = imap_errors()) {
            foreach ($errors as $value) {
                $this->data['message'] .= ($this->data['message'] == '' ? '' : '<br>') . $value;
            }
        }

        $data = $this->bbdd_model->qry2array('SELECT * FROM mails WHERE id_file=0');

        if (isset($data) && $data) {
            foreach ($data as $key => $value) {
                preg_match('/\(.+\:/', $value['subject'], $res);
                if (isset($res[0])) {
                    $data[$key]['id_file'] = (int)trim($res[0], '(:');
                }
            }
        }

        $this->data['data'] = $data;
        $this->data['all_files'] = $this->bbdd_model->get_table('files');

        $this->load->view('templates/header', $this->data);
        $this->load->view('obras/mails', $this->data);
        $this->load->view('templates/footer');
    }

    function config()
    {
        if (isset($_POST['save'])) {
            $salida = '';
            $salida .= '[mail]' . CRLF;
            $salida .= 'server="' . $_POST['server'] . '"' . CRLF;
            $salida .= 'email="' . $_POST['email'] . '"' . CRLF;
            $salida .= 'password="' . $_POST['password'] . '"' . CRLF;
            $salida .= 'port="' . $_POST['port'] . '"' . CRLF;
            $salida .= 'outserver="' . (isset($_POST['outserver']) && $_POST['outserver'] != '' ? $_POST['outserver'] : $_POST['server']) . '"' . CRLF;
            $salida .= 'outport="' . $_POST['outport'] . '"' . CRLF;
            $salida .= CRLF;
            if ($file = fopen('email.ini', 'w')) {
                fwrite($file, $salida);
                fclose($file);
                redirect(base_url());
            }
        } else {
            $this->data = array_merge($this->data, parse_ini_file('email.ini'));
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view('obras/mailconfig', $this->data);
        $this->load->view('templates/footer');
    }

    function delete($id)
    {
        if (isset($_POST['delete'])) {
            $this->bbdd_model->delete_record('mails', $id);
        }

        if (isset($_POST['delete']) || isset($_POST['exit'])) {
            redirect('/mail');
        }

        $rec = $this->bbdd_model->get_record('mails', $id);
        if (!$rec) {
            redirect(base_url('/mail'));
        }

        $this->data = array_merge($this->data, $rec);

        $this->load->view('templates/header', $this->data);
        $this->load->view('obras/maildelete', $this->data);
        $this->load->view('templates/footer');
    }
}
