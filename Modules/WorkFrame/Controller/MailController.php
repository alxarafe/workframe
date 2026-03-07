<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Illuminate\Database\Capsule\Manager as DB;
use Alxarafe\Lib\Messages;

/**
 * Mail controller — IMAP email reader.
 */
#[Menu(menu: 'main_menu', label: 'Correo electrónico', icon: 'fas fa-envelope', order: 60, permission: 'WorkFrame.Mail', route: 'WorkFrame.Mail')]
class MailController extends \Alxarafe\Base\Controller\Controller
{
    private $mailbox = null;
    private array $mailConfig = [];

    #[Menu(menu: 'main_menu', label: 'Procesar correo', icon: 'fas fa-inbox', parent: 'WorkFrame.Mail', order: 10, permission: 'WorkFrame.Mail')]
    public function doIndex(): bool
    {
        $this->mailConfig = $this->loadMailConfig();

        if (empty($this->mailConfig['host'])) {
            Messages::addError('Mail configuration is not set. Please configure it first.');
            $this->setDefaultTemplate('mail_config');
            $this->addVariable('config', $this->mailConfig);
            return true;
        }

        $this->connect();
        $emails = $this->getInbox();

        $this->addVariable('emails', $emails);
        $this->addVariable('config', $this->mailConfig);
        $this->setDefaultTemplate('mail_index');
        return true;
    }

    #[Menu(menu: 'main_menu', label: 'Configurar', icon: 'fas fa-cog', parent: 'WorkFrame.Mail', order: 20, permission: 'WorkFrame.Mail')]
    public function doConfig(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'host' => $_POST['host'] ?? '',
                'port' => (int) ($_POST['port'] ?? 993),
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? '',
                'ssl' => isset($_POST['ssl']),
            ];

            DB::table('mail_configs')->updateOrInsert(['id' => 1], $data);
            Messages::addMessage('Mail configuration saved.');
        }

        $config = $this->loadMailConfig();
        $this->addVariable('config', $config);
        $this->setDefaultTemplate('mail_config');
        return true;
    }

    public function doDelete(): bool
    {
        $id = (int) ($_GET['mail_id'] ?? 0);
        if ($id > 0) {
            $this->connect();
            if ($this->mailbox) {
                imap_delete($this->mailbox, (string) $id);
                imap_expunge($this->mailbox);
                Messages::addMessage('Email deleted.');
            }
        }

        return $this->doIndex();
    }

    private function loadMailConfig(): array
    {
        $row = DB::table('mail_configs')->where('id', 1)->first();
        if ($row) {
            return (array) $row;
        }
        return ['host' => '', 'port' => 993, 'username' => '', 'password' => '', 'ssl' => true];
    }

    private function connect(): void
    {
        if (!function_exists('imap_open')) {
            Messages::addError('IMAP extension is not available.');
            return;
        }

        $host = $this->mailConfig['host'] ?? '';
        $port = $this->mailConfig['port'] ?? 993;
        $ssl = $this->mailConfig['ssl'] ?? true;

        $mailbox = '{' . $host . ':' . $port . '/imap' . ($ssl ? '/ssl' : '') . '}INBOX';

        $this->mailbox = @imap_open(
            $mailbox,
            $this->mailConfig['username'] ?? '',
            $this->mailConfig['password'] ?? ''
        );

        if (!$this->mailbox) {
            Messages::addError('Failed to connect to mail server: ' . imap_last_error());
        }
    }

    private function getInbox(): array
    {
        if (!$this->mailbox) {
            return [];
        }

        $emails = [];
        $numMessages = imap_num_msg($this->mailbox);

        for ($i = $numMessages; $i > max(0, $numMessages - 50); $i--) {
            $header = imap_headerinfo($this->mailbox, $i);
            $body = imap_fetchbody($this->mailbox, $i, '1');

            $emails[] = [
                'id' => $i,
                'from' => $header->fromaddress ?? '',
                'subject' => $header->subject ?? '',
                'date' => $header->date ?? '',
                'body' => quoted_printable_decode($body),
            ];
        }

        return $emails;
    }

    public function __destruct()
    {
        if ($this->mailbox) {
            imap_close($this->mailbox);
        }
    }
}
